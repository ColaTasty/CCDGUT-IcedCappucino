<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/11/15
 * Time: 13:42
 */

namespace IcedCappuccino\Module;

use IcedCappuccino\Controllor\Router;
use IcedCappuccino\DB;
use IcedCappuccino\ModuleAbstract;
DB::connect();

class wxBusinessModuleClass extends ModuleAbstract
{
    private $app_dbcount = "";
    public function __construct()
    {
        $sql = "SELECT accountId FROM WeChatAccount WHERE appId = :id AND appSecret = :secret";
        $where = [
            ':id' => __APP_ID__,
            ':secret' => __APP_SECRET__
        ];
        $stmt = DB::executeSQL($sql,$where);
        foreach ($stmt as $row){
            $this->app_dbcount = $row['accountId'];
        }
    }

    public function getAccessToken(){
        $callBack = null;
        $callBack = $this->isAccessTokenValid();
        if ($callBack)
            return $callBack;
        else{
            $callBack = $this->callWechat("accessToken");
        }
        return $callBack;
    }

    public function getOpenId(){
        var_dump($this->callWechat("login"));
        return $this->getCallBack();
    }

    /**
     *@param null
     *@return boolean|string
     */
    private function isAccessTokenValid(){
        $callBack = false;
        $sql = "SELECT * FROM WeChatAccessToken WHERE accountId=:id";
        $where = [":id"=>$this->app_dbcount];
        $stmt = DB::executeSQL($sql,$where);
        foreach ($stmt as $row){
            if (strtotime($row['expireTime']) > time()){
                $callBack = $row['accessToken'];
            }
        }
        return $callBack;
    }

    public function callWechat($action){
        $callBack = null;
        switch ($action){
            default:
                $this->setException("No such \$action=$action in wxBusiness::callWechat()");
                break;
            case "login":
                if (!$_POST['code']) {
                    $this->setException("There is no code to login");
                    break;
                }
                $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".__APP_ID__."&secret=".__APP_SECRET__."&js_code=".$_POST['code']."&grant_type=authorization_code";
                $callBack = Router::httpGet($url)['res'];
                break;
            case "accessToken":
                break;
        }
        return $callBack;
    }
}