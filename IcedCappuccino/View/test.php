<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/10/2
 * Time: 20:57
 */

//$tmp = substr($_SERVER["REQUEST_URI"],1);
//$segments = explode("/",$tmp);
//print_r($segments);
$uri = substr($_SERVER["REQUEST_URI"],1,strpos($_SERVER["REQUEST_URI"],"?")-1);
$segments = explode("/", $uri);
print_r($segments);
?>