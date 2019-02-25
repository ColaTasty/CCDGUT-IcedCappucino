<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/11/2
 * Time: 15:20
 */

namespace IcedCappuccino\Module;

/**
 * SESSION = [
 * "sessionKey",
 * "expireTime",
 * "openId",
 * "uid"]
 */
if(isset($_POST['sessionid'])){
    session_id($_POST['sessionid']);
    session_start();
}

use IcedCappuccino\DB;
use IcedCappuccino\Filter\Stack;
use IcedCappuccino\ModuleAbstract;
DB::connect();

class calendarModuleClass extends ModuleAbstract
{
    const TABLES_PATH = __DIR__."/../../CalendarTables/";

    public function hello(){
        $this->setJSON("isOK",true);
        $this->setJSON("msg","hello world");
        return $this->getCallBack();
    }

    public function init(){
        $sql = "SELECT tid,tname,ttime FROM CYTB_WeChat.Calendar WHERE openid=:openid";
        $where = [
            ":openid"=>$_SESSION['openId']
        ];
        $stmt = DB::executeSQL($sql,$where);
        if ($stmt->rowCount() > 0){
            $arr_tmp = [];
            $i = 0;
            foreach ($stmt as $row){
                $arr_tmp[$i]['tid'] = $row['tid'];
                $arr_tmp[$i]['tname'] = $row['tname'];
                $arr_tmp[$i]['ttime'] = date("Y-m-d H:i:s",$row['ttime']);
                $i++;
            }
            $this->setJSON("isOK",true);
            $this->setJSON("tables",$arr_tmp);
        }else{
            $this->setJSON("isOK",false);
            $this->setJSON("msg","你还没有创建周程表噢，快新建一个吧!");
        }
        return $this->getCallBack();
    }

    public function buildUp(){
        $table = json_decode($_POST['table']);
        if (empty($table->table_name) || !isset($table->table_name)){
            $this->setJSON("isOK", false);
            return $this->getJSON();
        }
        $t = time();
        $str_path = self::TABLES_PATH.$_SESSION["openId"];
        $days = [
            "Mon" => json_encode($table->Mon),
            "Tue" => json_encode($table->Tue),
            "Wed" => json_encode($table->Wed),
            "Thu" => json_encode($table->Thu),
            "Fri" => json_encode($table->Fri),
            "Sat" => json_encode($table->Sat),
            "Sun" => json_encode($table->Sun),
        ];
        $paths = [
            "Mon" => md5($t."Mon".$_SESSION["openId"]),
            "Tue" => md5($t."Tue".$_SESSION["openId"]),
            "Wed" => md5($t."Wed".$_SESSION["openId"]),
            "Thu" => md5($t."Thu".$_SESSION["openId"]),
            "Fri" => md5($t."Fri".$_SESSION["openId"]),
            "Sat" => md5($t."Sat".$_SESSION["openId"]),
            "Sun" => md5($t."Sun".$_SESSION["openId"])
        ];
        $sql = "INSERT INTO Calendar(tname, Mon, Tue, Wed, Thu, Fri, Sat, Sun, openid, ttime,`type`) VALUES (:tname,:Mon,:Tue,:Wed,:Thu,:Fri,:Sat,:Sun,:openid,:ttime,:type)";
        $where = [
            ":tname"=>$table->table_name,
            ":openid"=>$_SESSION["openId"],
            ":ttime" => time(),
            ":type" =>$table->table_type
        ];
        if (!file_exists(self::TABLES_PATH))
            mkdir(self::TABLES_PATH,0777);
        if (!file_exists($str_path))
            mkdir($str_path,0777);
        foreach ($days as $key=>$value) {
            $file = fopen($str_path."/".$paths[$key].".JSON","w");
            if (!fwrite($file,$value)){
                $this->setCallBack("File is writing unsuccessful");
                exit($this->getCallBack());
            }
            chmod($str_path."/".$paths[$key].".JSON",0777);
            fclose($file);
            $where[":".$key] = $paths[$key];
        }
        $stmt = DB::executeSQL($sql,$where);
        if ($stmt->rowCount() > 0)
            $this->setJSON("isOK",true);
        else {
            $this->setJSON("isOK", false);
        }
        return $this->getCallBack();
    }

    /**
     * @return array|null
     */
    public function viewTable(){
        if ($_POST["isEditing"] != 1 && $_GET["isEditing"] != 1){
            $this->setView("viewCalendarTable");
        }
        if (!isset($_GET['tid']) && !isset($_POST['tid'])) {
            return null;
        }
        elseif (isset($_POST['tid'])){
            $tid = $_POST['tid'];
        }else{
            $tid = $_GET['tid'];
        }
        $sql = "SELECT * FROM Calendar WHERE tid=:tid";
        $where = [
            ":tid" => $tid
        ];
        $arr_files = [];
        $stmt = DB::executeSQL($sql,$where);
        if ($stmt->rowCount() > 0){
            $this->setJSON("isOK",true);
            foreach ($stmt as $row){
                $file_path = self::TABLES_PATH.$row['openid']."/";
                $arr_files = [
                    'Mon' => $file_path.$row['Mon'].".JSON",
                    'Tue' => $file_path.$row['Tue'].".JSON",
                    'Wed' => $file_path.$row['Wed'].".JSON",
                    'Thu' => $file_path.$row['Thu'].".JSON",
                    'Fri' => $file_path.$row['Fri'].".JSON",
                    'Sat' => $file_path.$row['Sat'].".JSON",
                    'Sun' => $file_path.$row['Sun'].".JSON",
                ];
                $this->setJSON("ttype",$row["type"]);
                $this->setJSON("tid",$row['tid']);
                $this->setJSON("tname",$row['tname']);
                $this->setJSON("ttime",date("Y-m-d H:i:s",$row['ttime']));
            }
            foreach ($arr_files as $key => $path) {
                $file = fopen($path,"r");
                $this->setJSON($key,fread($file,filesize($path)));
                fclose($file);
            }
        }else{
            $this->setJSON("isOK",false);
            $this->setJSON("msg","未找到对应周程表");
        }
        return $this->getCallBack();
    }

    public function reBuild(){
        $tid = $_POST['tid'];
        $type = $_POST['ttype'];
        $arr_days = [
            "Mon"=>$_POST['Mon'],
            "Tue"=>$_POST['Tue'],
            "Wed"=>$_POST['Wed'],
            "Thu"=>$_POST['Thu'],
            "Fri"=>$_POST['Fri']
        ];
        if ($type == "week"){
            $arr_days["Sat"] = $_POST['Sat'];
            $arr_days["Sun"] = $_POST['Sun'];
        }
        $sql = "SELECT * FROM Calendar WHERE tid=:tid";
        $where = [
            ":tid" => $tid
        ];
        $str_path = self::TABLES_PATH;
        $path = [];
        $stmt = DB::executeSQL($sql,$where);
        if ($stmt->rowCount() > 0){
            foreach ($stmt as $row) {
                $str_path .= $row['openid']."/";
                $path['Mon'] = $str_path.$row['Mon'];
                $path['Tue'] = $str_path.$row['Tue'];
                $path['Wed'] = $str_path.$row['Wed'];
                $path['Thu'] = $str_path.$row['Thu'];
                $path['Fri'] = $str_path.$row['Fri'];
                if ($type == "week"){
                    $path['Sat'] = $str_path.$row['Sat'];
                    $path['Sun'] = $str_path.$row['Sun'];
                }
            }
            foreach ($arr_days as $key=>$value){
                if ($_POST[$key] != "0"){
                    $file = fopen($path[$key].".JSON","w");
                    if (!fwrite($file,$value)){
                        $this->setJSON("isOK",false);
                        return $this->getCallBack();
                    }
                    fclose($file);
                }
            }
        }
        $this->setJSON("isOK",true);
        return $this->getCallBack();
    }

    public function delet(){
        $tid = $_POST['tid'];
        $sql = "SELECT * FROM Calendar WHERE tid=:tid";
        $where = [
            ":tid" => $tid
        ];
        $stmt = DB::executeSQL($sql,$where);
        if ($stmt->rowCount() > 0){
            foreach ($stmt as $row) {
                $str_path = self::TABLES_PATH.$row['openid'];
                unlink($str_path."/".$row['Mon'].".JSON");
                unlink($str_path."/".$row['Tue'].".JSON");
                unlink($str_path."/".$row['Wed'].".JSON");
                unlink($str_path."/".$row['Thu'].".JSON");
                unlink($str_path."/".$row['Fri'].".JSON");
                unlink($str_path."/".$row['Sat'].".JSON");
                unlink($str_path."/".$row['Sun'].".JSON");
            }
            $sql = "DELETE FROM Calendar WHERE tid = :tid";
            $where = [
                ":tid" => $tid
            ];
            $this->setJSON("isOK",true);
            if (!(DB::executeSQL($sql,$where)->rowCount()>0))
                $this->setJSON("isOK",false);
        }
        return $this->getCallBack();
    }

    public function sharingTable(){
        $tid = $_POST['tid'];
        if(isset($_SESSION['openId'])){
            $openId = $_SESSION["openId"];
        }else{
            $this->setJSON("isOK",false);
            $this->setJSON("msg","sessionId is NULL!");
            return $this->getCallBack();
        }
        $sql = "SELECT tid,tname,openid,type,ttime,COUNT(sharebytid) FROM Calendar WHERE tid=:tid";
        $where = [":tid"=>$tid];
        $stmt = DB::executeSQL($sql,$where);
        if (!($stmt->rowCount() > 0)){
            $this->setJSON("isOK",false);
            $this->setJSON("msg","未查到信息!");
             return $this->getCallBack();
        }
        foreach ($stmt as $row){
            $this->setJSON("table",[
                "tid"=>$row['tid'],
                "tname"=>$row['tname'],
                "ttime"=>date("Y-m-d H:i:s",$row["ttime"]),
                "type"=>$row['type'],
                "sharedCount"=>$this->getSharingCount($tid)
                //  ↑isOK in this function complete
                ]);
            $this->setJSON("isSelf",$row['openid']==$openId);
        }
        return $this->getCallBack();
    }

    private function getSharingCount()
    {
        $tid = $_POST['tid'];
        $count = 0;
        $sql = "SELECT COUNT('sharebyid') AS total FROM Calendar WHERE sharebytid=:tid";
        $stmt = DB::executeSQL($sql, [":tid" => $tid]);
        if (!($stmt)) {
            $this->setJSON("isOK", false);
            $this->setJSON("msg", "查找保存总数失败！请重试！");
            return 0;
        }
        foreach ($stmt as $row) {
            $count = $row['total'];
        }
        $this->setJSON("isOK", true);
        return $count;
    }

    public function getTableCreator(){
        $tid = $_POST['tid'];
        $openId = null;
        $stmt = DB::executeSQL("SELECT openid FROM Calendar WHERE tid=:tid",[":tid"=>$tid]);
        if (!($stmt->rowCount() > 0)){
            $this->setJSON("isOK",false);
            $this->setJSON("msg","未查到创建者用户 error:01");
            return $this->getCallBack();
        }
        foreach ($stmt as $row){
            $openId = $row['openid'];
        }
        $sql = "SELECT openId,nickName,avatarUrl FROM WeChatUser WHERE openId=:openId";
        $stmt = DB::executeSQL($sql,[":openId"=>$openId]);
        if (!($stmt->rowCount() > 0)){
            $this->setJSON("isOK",false);
            $this->setJSON("msg","未查到创建者用户 error:02");
            return $this->getCallBack();
        }
        foreach ($stmt as $row){
            $this->setJSON("tableCreator",[
                "nickName"=>$row['nickName'],
                "avatarUrl"=>$row['avatarUrl']
            ]);
            $this->setJSON("isOK",true);
        }
        return $this->getCallBack();
    }

    public function saveSharingTable(){
        $tid = $_POST['tid'];
        if(isset($_SESSION['openId'])){
            $openId = $_SESSION["openId"];
        }else {
            $this->setJSON("isOK", false);
            $this->setJSON("msg", "请再试一次!");
            return $this->getCallBack();
        }
        /**
         * start verify table is NULL
         */
        $sql = "SELECT sharebytid FROM Calendar WHERE sharebytid=:sharebytid AND openid=:openid";
        $stmt = DB::executeSQL($sql,[":sharebytid"=>$tid,":openid"=>$openId]);
        if ($stmt->rowCount() > 0){
            $this->setJSON("isOK",false);
            $this->setJSON("msg","你已经保存过一份了");
            return $this->getCallBack();
        }
        /**
         * start file open & copy
         */
        $sql = "SELECT * FROM Calendar WHERE tid=:tid";
        $stmt = DB::executeSQL($sql,[":tid"=>$tid]);
        foreach ($stmt as $row){
            $old_dir = self::TABLES_PATH.$row['openid']."/";
            $values = [
                ":tname"=>$row['tname'],
                ":Mon"=>$row['Mon'],
                ":Tue"=>$row['Tue'],
                ":Wed"=>$row['Wed'],
                ":Thu"=>$row['Thu'],
                ":Fri"=>$row['Fri'],
                ":Sat"=>$row['Sat'],
                ":Sun"=>$row['Sun'],
                ":openid"=>$openId,
                ":ttime"=>time(),
                ":type"=>$row['type'],
                ":sharebytid"=>$tid
            ];
        }
        $new_dir = self::TABLES_PATH.$openId."/";
        $old_files = [
            "Mon"=>$values[':Mon'],
            "Tue"=>$values[':Tue'],
            "Wed"=>$values[':Wed'],
            "Thu"=>$values[':Thu'],
            "Fri"=>$values[':Fri'],
            "Sat"=>$values[':Sat'],
            "Sun"=>$values[':Sun']
        ];
        $new_files = [
            "Mon"=>md5(time().$openId."Mon"),
            "Tue"=>md5(time().$openId."Tue"),
            "Wed"=>md5(time().$openId."Wed"),
            "Thu"=>md5(time().$openId."Thu"),
            "Fri"=>md5(time().$openId."Fri"),
            "Sat"=>md5(time().$openId."Sat"),
            "Sun"=>md5(time().$openId."Sun")
        ];
        if(!file_exists($new_dir)){
            mkdir($new_dir);
        }
        foreach ($old_files as $day => $value){
            $old_file = fopen($old_dir.$value.".JSON","r");
            $new_file = fopen($new_dir.$new_files[$day].".JSON","w");
            chmod($new_dir.$new_files[$day].".JSON",0777);
            if (!fwrite($new_file,fread($old_file,filesize($old_dir.$value.".JSON")))){
                $this->setJSON("isOK",false);
                $this->setJSON("msg","周程表保存失败: ".$day);
                fclose($old_file);
                fclose($new_file);
                return $this->getCallBack();
            }
            fclose($old_file);
            fclose($new_file);
        }
        /**
         * start insert informations into DB
         */
        foreach ($new_files as $key => $value){
            $values[":".$key] = $value;
        }
        $sql = "INSERT INTO Calendar(tname,Mon,Tue,Wed,Thu,Fri,Sat,Sun,openid,ttime,type,sharebytid) VALUES(:tname,:Mon,:Tue,:Wed,:Thu,:Fri,:Sat,:Sun,:openid,:ttime,:type,:sharebytid)";
        $stmt = DB::executeSQL($sql,$values);
        if (!($stmt->rowCount() > 0)){
            $this->setJSON("isOK",false);
            $this->setJSON("msg","添加周程表失败");
            return $this->getCallBack();
        }
        $this->setJSON("isOK",true);
        return $this->getCallBack();
    }
}