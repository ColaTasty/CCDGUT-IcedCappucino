<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/10/3
 * Time: 12:27
 */

namespace IcedCappuccino\C;


use IcedCappuccino\Config;
use IcedCappuccino\Filter\Stack;

class Router
{

    /**
     * @access public
     * @param null
     * @return void
     */
    public static function routeRun(){
        $arr = explode("/",$_SERVER["REQUEST_URI"]);
        if (in_array("IcedCappuccino",$arr)){
            @$req = [
                $_SERVER['REQUEST_METHOD'],
                $arr[2],
                $arr[3],
                $arr[4]
            ];
        }else{
            @$req = [
                $_SERVER['REQUEST_METHOD'],
                $arr[1],
                $arr[2],
                $arr[3]
            ];
        }
        $req[3] = explode("?",$arr[count($arr)-1])[0];
        self::routeStart($req);
    }

    /**
     * @access private
     * @param array | $arr_action[0] = "ControllorNname" ,$arr_action[1] = "ModuleNname" ,$arr_action[2] = "MethodNname"
     * @return void
     */
    private static function routeStart($arr_action){
        try{
            if (Config::isControllor($arr_action[1])){
                $str_controllor = "IcedCappuccino\C\\$arr_action[1]ControllorClass";
                @$str_module = $arr_action[2];
                @$str_method = $arr_action[3];
                include_once __DIR__."/$arr_action[1]ControllorClass.php";
                @$obj = new $str_controllor($str_module,$str_method);
                @$obj->run();
                exit();
            }else{
                throw new \Exception("<br>There is no $arr_action[1]ControllorClass!!");
            }}
        catch (\Exception $exception){
            exit($exception->getMessage());
        }
    }

    /**
     * @access private
     * @param string
     * @param array
     * @param array
     * @param array
     * @param boolean
     * @return array
     */
    public static function httpGet($url, $data = null, $header = [],$cookie = [], $need_header = false){
        $str_date = "";
        $curl = curl_init();

        //数据排序，转换成get数据格式（https://example.com?key=val&key=val）
        if (isset($data)){
//            if (strpos($url,"?") != -1)
//                $str_date .= "&";
//            else
//                $str_date .= "?";
            $str_date .= (strpos($url,"?") > -1) ? "&":"?";
            $stack = new Stack();
            foreach ($data as $key => $val){
                $stack->push([$key=>$val]);
            }

            while (!$stack->isEmpty()){
                foreach ($stack->pop(true) as $key => $val){
                    $str_date .= "$key=$val";
                }
                if (!$stack->isEmpty())
                    $str_date .= "&";
            }
            $stack = null;
            $url .= $str_date;
        }
//        var_dump($url);

        //设置curl句柄
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl,CURLOPT_ENCODING,"gzip");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, $need_header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//绕过ssl验证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        //设置header
        $arr_header_tmp = [];
        if (!empty($header)){
            $i = 0;
            foreach ($header as $key => $value){
                $arr_header_tmp[$i] = "$key:$value";
                $i++;
            }
        }
        curl_setopt($curl,CURLOPT_HTTPHEADER,$arr_header_tmp);

        //设置cookie
        if (!empty($cookie)){
            $str_cookie_tmp = "";
            foreach ($cookie as $key => $value){
                $str_cookie_tmp .= "$key=$value;";
            }
            curl_setopt($curl,CURLOPT_COOKIE,$str_cookie_tmp);
        }

        //执行curl
        $res = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        return [
            "url" => $url,
            "header"=> $info,
            "res" => $res,
        ];
    }

    public static function httpPost($url, $data = null, $header = [],$cookie = [],$need_header = false){
        $curl = curl_init();
        $str_date = "";

        //数据排序，转换成post数据格式（key=val&key=val）
        if (isset($data)){
            $stack = new Stack();
            foreach ($data as $key => $val){
                $stack->push([$key=>$val]);
            }

            while (!$stack->isEmpty()){
                foreach ($stack->pop(true) as $key => $val){
                    $str_date .= "$key=$val";
                }
                if (!$stack->isEmpty())
                    $str_date .= "&";
            }
            $stack = null;
        }

        //设置curl句柄
        curl_setopt($curl,CURLOPT_URL, $url);
        curl_setopt($curl,CURLOPT_ENCODING,"gzip");
        curl_setopt($curl,CURLOPT_HEADER,$need_header);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//绕过ssl验证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 100);
        curl_setopt($curl,CURLOPT_POSTFIELDS, $str_date);

        //设置header
        $arr_header_tmp = [];
        if (!empty($header)){
            $i = 0;
            foreach ($header as $key => $value){
                $arr_header_tmp[$i] = "$key:$value";
                $i++;
            }
        }
        if (!isset($header['Content-type']) || !isset($header['content-type']))
            array_push($arr_header_tmp,'Content-type:application/x-www-form-urlencoded;charset=utf-8');
        curl_setopt($curl,CURLOPT_HTTPHEADER,$arr_header_tmp);

        //设置cookie
        if (!empty($cookie)){
            $str_cookie_tmp = "";
            foreach ($cookie as $key => $value){
                $str_cookie_tmp .= "$key=$value;";
            }
            curl_setopt($curl,CURLOPT_COOKIE,$str_cookie_tmp);
        }

        //执行curl
        $res = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        return [
            "url" => $url,
            "res" => $res,
            "header" => $info
        ];

    }
}