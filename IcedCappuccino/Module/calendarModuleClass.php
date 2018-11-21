<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/11/2
 * Time: 15:20
 */

namespace IcedCappuccino\M;

if(isset($_POST['sessionid'])){
    session_id($_POST['sessionid']);
}
session_start();

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
            "openid"=>$_SESSION['openId']
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
        if (empty($table->table_name)){
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
        $sql = "INSERT INTO Calendar(tname, Mon, Tue, Wed, Thu, Fri, Sat, Sun, openid, ttime) VALUES (:tname,:Mon,:Tue,:Wed,:Thu,:Fri,:Sat,:Sun,:openid,:ttime)";
        $where = [
            ":tname"=>$table->table_name,
            ":openid"=>$_SESSION["openId"],
            ":ttime" => time()
        ];
//        var_dump($paths);
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

    public function viewTable(){
        $tid = $_POST['tid'];
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
        $arr_days = [
            ["Mon"=>!($_POST['Mon']=="false" ? false : $_POST['Mon'])],
            ["Tue"=>!($_POST['Tue']=="false" ? false : $_POST['Tue'])],
            ["Wed"=>!($_POST['Wed']=="false" ? false : $_POST['Wed'])],
            ["Thu"=>!($_POST['Thu']=="false" ? false : $_POST['Thu'])],
            ["Fri"=>!($_POST['Fri']=="false" ? false : $_POST['Fri'])],
            ["Sat"=>!($_POST['Sat']=="false" ? false : $_POST['Sat'])],
            ["Sun"=>!($_POST['Sun']=="false" ? false : $_POST['Sun'])],
        ];
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
                $path['Sat'] = $str_path.$row['Sat'];
                $path['Sun'] = $str_path.$row['Sun'];
            }
            foreach ($arr_days as $key=>$value){
                if ((bool)$value){
                    $file = fopen($path[$key]."JSON","w+");
                    if (!fwrite($file,$value))
                        $this->setJSON("isOK",false);
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
}