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
//        $this->setJSON("test",$_SESSION['openId']);
        return $this->getCallBack();
    }

    public function buildUp(){
        $table = json_decode($_POST['table']);
        $sql = "INSERT INTO Calendar(tname, Mon, Tue, Wed, Thu, Fri, Sat, Sun, openid, ttime) VALUES (:tname,:Mon,:Tue,:Wed,:Thu,:Fri,:Sat,:Sun,:openid,:ttime)";
        $where = [
            ":tname"=>$table->table_name,
            ":Mon" => json_encode($table->Mon),
            ":Tue" => json_encode($table->Tue),
            ":Wed" =>json_encode($table->Wed),
            ":Thu" =>json_encode($table->Thu),
            ":Fri" =>json_encode($table->Fri),
            ":Sat" =>json_encode($table->Sat),
            ":Sun" =>json_encode($table->Sun),
            ":openid"=>$_SESSION["openId"],
            ":ttime" => time()
        ];
        if (!empty($where[':tname'])){$stmt = DB::executeSQL($sql,$where);
            if ($stmt->rowCount() > 0)
                $this->setJSON("isOK",true);
            else {
                $this->setJSON("isOK", false);
            }
        }else{
            $this->setJSON("isOK",false);
        }
        return $this->getCallBack();
    }

    public function viewTable(){
        $tid = $_POST['tid'];
        $sql = "SELECT * FROM Calendar WHERE tid=:tid";
        $where = [
            ":tid" => $tid
        ];
        $stmt = DB::executeSQL($sql,$where);
        if ($stmt->rowCount() > 0){
            $this->setJSON("isOK",true);
            foreach ($stmt as $row){
                $this->setJSON("tid",$row['tid']);
                $this->setJSON("tname",$row['tname']);
                $this->setJSON("Mon",$row['Mon']);
                $this->setJSON("Tue",$row['Tue']);
                $this->setJSON("Wed",$row['Wed']);
                $this->setJSON("Thu",$row['Thu']);
                $this->setJSON("Fri",$row['Fri']);
                $this->setJSON("Sat",$row['Sat']);
                $this->setJSON("Sun",$row['Sun']);
                $this->setJSON("ttime",date("Y-m-d H:i:s",$row['ttime']));
            }
        }else{
            $this->setJSON("isOK",false);
            $this->setJSON("msg","未找到对应周程表");
        }
        return $this->getCallBack();
    }

    public function reBuild(){
        $tid = $_POST['tid'];
        $sql = "UPDATE Calendar SET ";
        $arr_days = [
            ["Mon"=>($_POST['Mon']=="false" ? false : $_POST['Mon'])],
            ["Tue"=>($_POST['Tue']=="false" ? false : $_POST['Tue'])],
            ["Wed"=>($_POST['Wed']=="false" ? false : $_POST['Wed'])],
            ["Thu"=>($_POST['Thu']=="false" ? false : $_POST['Thu'])],
            ["Fri"=>($_POST['Fri']=="false" ? false : $_POST['Fri'])],
            ["Sat"=>($_POST['Sat']=="false" ? false : $_POST['Sat'])],
            ["Sun"=>($_POST['Sun']=="false" ? false : $_POST['Sun'])],
        ];
        $stack = new Stack($arr_days);
        $arr_tmp = [];
        while (!$stack->isEmpty()){
            foreach ($stack->pop(true) as $key => $value) {
                if ((boolean)$value) {
                    $arr_tmp[":" . $key] = $value;
                }
            }
        }
        end($arr_tmp);
        $last_key = key($arr_tmp);
        foreach ($arr_tmp as $key => $value){
            $sql .= str_replace(":","",$key)."=$key";
            if ($key != $last_key){
                $sql .= ",";
            }
        }
        $sql .= " WHERE tid=:tid";
        $where = $arr_tmp;
        $where[':tid'] = $tid;
        $stmt = DB::executeSQL($sql,$where);
        if ($stmt->rowCount() > 0){
            $this->setJSON("isOK",true);
        }else{
            $this->setJSON("isOK",false);
            $this->setJSON("msg",$stmt->errorInfo());
        }

        return $this->getCallBack();
    }

    public function delet(){
        $tid = $_POST['tid'];
        $sql = "DELETE FROM Calendar WHERE tid = :tid";
        $where = [
            ":tid" => $tid
        ];
        $stmt = DB::executeSQL($sql,$where);
        if ($stmt->rowCount() > 0){
            $this->setJSON("isOK",true);
        }else{
            $this->setJSON("isOK",true);
            $this->setJSON("msg","删除失败了，服务器的原因，再试试吧");
        }
        return $this->getCallBack();
    }
}