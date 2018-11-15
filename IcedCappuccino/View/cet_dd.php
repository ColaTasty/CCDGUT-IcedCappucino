<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/11/15
 * Time: 18:08
 */
$res = \IcedCappuccino\C\Router::httpGet("http://cet.neea.edu.cn/cet/js/data.js");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="application/json" charset="UTF-8"/>
    <script src="http://cet.neea.edu.cn/cet/js/data.js" charset="UTF-8">
    </script>
</head>
<body>
{"isOK":<?php echo ($res['header']['http_code']==200)? "true":"false" ?>,"dd":
<script>document.write(JSON.stringify(dq));</script>
}
</body>
</html>
