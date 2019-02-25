<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/10/2
 * Time: 20:57
 */

$test_openid = "oxGL90JqywvPzfwAhe-DNiRa6i6c";
header("content-type:text/json");
if (isset($_GET["ssid"])){
    session_id($_GET['ssid']);
    session_start();
    var_dump($_SESSION);
    exit();
}else{
    echo(json_encode(["openid"=>$test_openid]));
    exit();
}
?>
<!--<!DOCTYPE html>-->
<!--<html lang="en">-->
<!--<head>-->
<!--    <meta content="text/javascript" charset="UTF-8"/>-->
<!--    <script src="http://cet.neea.edu.cn/cet/js/data.js" charset="UTF-8"></script>-->
<!--</head>-->
<!--<body>-->
<!--<script>document.write(JSON.stringify(dq));</script>-->
<!--</body>-->
<!--</html>-->