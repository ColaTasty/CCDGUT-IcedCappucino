<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/11/28
 * Time: 22:21
 */

namespace IcedCappuccino\Module;

use IcedCappuccino\DB;
use IcedCappuccino\ModuleAbstract;

DB::connect();

if (isset($_POST['ssid'])){
    session_id($_POST['ssid']);
    session_start();
}

class plazaModuleClass extends ModuleAbstract
{
    const USTATUS = [
        "FREEZON" => 0,     //未激活
        "NORMAL" => 1,      //正常
        "BANNED" => 2,      //封禁中
        "HAD_BANNED" => 3,  //被封禁过
        "AD" => 4           //广告用户
    ];

    const AD_KEY = "6f8ca57ad6d1";  //激活成为广告用户的密匙

    public function init(){
        $sql = "SELECT * FROM PlazaUser WHERE openId=:openId";
        $where = [
            ":openId" => $_SESSION['openId']
        ];
        $stmt = DB::executeSQL($sql,$where);
        $this->setJSON("isOK",$stmt->rowCount() > 0);
        if ($this->json["isOK"]){
            foreach ($stmt as $row){
                $this->setJSON("sid",$row['sid']);
                $this->setJSON("uname",$row['uname']);
                $this->setJSON("utime",date("Y-m-d H:i:s",$row['utime']));
                $this->setJSON("ustatus",$row['ustatus']);
                if ($row['ustatus'] == self::USTATUS['BANNED'] || $row['ustatus'] == self::USTATUS['HAD_BANNED']){
                    $this->setJSON("bantime",date("Y-m-d H:i:s",$row['bantime']));
                    $this->setJSON("banlimite",$row['banlimite']);
                }
            }
        }
    }

    public function getTz(){
        $start = isset($_POST['page']) ? $_POST['page']-1 : 0;
        $sql = "SELECT * FROM PlazaMessages ORDER BY mid DESC LIMIT {$start},10";
        $stmt = DB::executeSQL($sql);
        if ($stmt->rowCount() > 0){
            foreach ($stmt  as $row){

            }
        }
    }
}