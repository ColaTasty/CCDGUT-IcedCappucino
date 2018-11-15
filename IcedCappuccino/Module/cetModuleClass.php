<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/10/14
 * Time: 23:19
 */

namespace IcedCappuccino\M;

use IcedCappuccino\C\Router;
use IcedCappuccino\ModuleAbstract;

if (isset($_POST['cet_sessionid']))
    session_id($_POST['cet_sessionid']);
session_start();
class cetModuleClass extends ModuleAbstract
{
//    private static $dd = [];

    public function __construct()
    {

    }

    public function verify(){
        $_SESSION['cookie'] = [];

        if (!isset($_POST['i'])){
            $this->setJSON("isOK",false);
            $this->setJSON("msg","没有输入准考证号");
            return $this->getCallBack();
        }
        $data = [
            "c" => "CET",
            "ik" => $_POST["i"],
            "t" => random_int(1,100),
        ];
        $header = [
            "Referer" => "http://cet.neea.edu.cn/cet/"
        ];

        $res = Router::httpGet("http://cache.neea.edu.cn/Imgs.do?",$data,$header,null,true);
        $this->setJSON("isOK",$res['header']["http_code"]==200);
        if ($this->getJSON()['isOK']){
            $index_setcookie = strpos($res['res'],"Set-Cookie:");
            $index_resultimg = strpos($res['res'],"result.imgs(\"");
            $str_cookies = substr($res['res'],$index_setcookie,$index_resultimg-$index_setcookie);
            $str_result = substr($res['res'],$index_resultimg);
            $str_cookies = str_replace(" ","",$str_cookies);
            $str_result = str_replace(" ","",$str_result);
            $res_url = substr($str_result,strlen("result.imgs(\""),strlen($str_result)-strpos($str_result,"http")-strlen("\");"));
            $res_cookies = substr($str_cookies,strlen("Set-Cookie:"),strpos($str_cookies,"path=/")-strlen("Set-Cookie:"));
            $arr_cookies = explode(";",$res_cookies);
            foreach ($arr_cookies as $val){
                if (empty($val))
                    continue;
                $arr_tmp = explode("=",$val);
                $_SESSION['cookie'][$arr_tmp[0]] = $arr_tmp[1];
            }
            $this->setJSON("request_url",$res_url);
            $this->setJSON("cet_sessionid",session_id());
            $this->setJSON("cookie",$_SESSION['cookie']);
        }else{
            $this->setJSON("msg","无法连接成绩服务器");
        }
        $this->setJSON("test",$res['url']);

        return $this->getCallBack();
    }

    public function getDd(){
        $res = Router::httpGet("http://cet.neea.edu.cn/cet/js/data.js");
        $str_tmp = str_replace("var dq=","",$res['res']);
        $this->setJSON("isOK",$res['header']["http_code"]==200);
        $this->setJSON("dd",json_decode(str_replace(";","",$str_tmp)));
        return $this->getCallBack();
    }

    public function query(){
        $query_data = [
            "t" => $_POST['t'],
            "z" => $_POST["z"],
            "n" => $_POST["n"],
            "v" => $_POST["v"]
        ];

        foreach ($query_data as $item){
            if (empty($item)){
                $this->setView("toView");
                return 0;
            }
        }

        $post_data = [
            "data"=>"{$query_data["t"]},{$query_data['z']},{$query_data['n']}",
            "v"=>$query_data['v']
        ];

        $header = [
            "Referer" => "http://cet.neea.edu.cn/cet/"
        ];

        $res = Router::httpPost("http://cache.neea.edu.cn/cet/query",$post_data,$header,$_SESSION['cookie']);

        $arr_res = [];
        if ($res['header']['http_code'] == 200){
            $str_result_tmp = str_replace("<script>document.domain='neea.edu.cn';</script><script>parent.result.callback(\"{","",$res['res']);
            $str_result_tmp = str_replace("}\");</script>","",$str_result_tmp);
            $str_result_tmp = str_replace(",",";",$str_result_tmp);
            $str_result_tmp = str_replace(":","=",$str_result_tmp);
            $str_result_tmp = str_replace("'","",$str_result_tmp);
            $arr_tmp = explode(";",$str_result_tmp);
            foreach ($arr_tmp as $str_tmp){
                $a = explode("=",$str_tmp);
                $arr_res[$a[0]] = $a[1];
            }
        }

        $this->setJSON("isOK",!isset($arr_res['error']));
        if (!$this->json['isOK'])
            $this->setJSON("msg","查询信息错误！");
        $this->setJSON("res",$arr_res);

        return $this->getCallBack();
    }

    public function unsetSession(){
        session_destroy();
        if (isset($_SESSION['cookie'])){
            $this->setJSON("isOK",true);
            $this->setJSON("msg","cet_session已被销毁");
        }else{
            $this->setJSON("isOK",false);
            $this->setJSON("msg","cet_session销毁失败");
            $this->setJSON("test",$_SESSION['cookie']);
        }
        return $this->getCallBack();
    }
}