<?php
/**
 * Author 黎江 Makia98
 * Create By IDEA
 * On 2019-07-03 23:46
 */
ini_set("display_errors", 'On');
$page = isset($_GET["page"]) ? $_GET["page"] : 1;
if ($page > 1)
    $start = (($page - 1) * 10) + 1;
else
    $start = $page - 1;
$dsn = "mysql:host=localhost;dbname=we7;charset=utf8";
$pdo = new PDO($dsn, "root", "Air9WF0svH06m1jJsbaUDK8oYeePnUQT");
$sql = "SELECT * FROM ims_collect_name ORDER BY time ASC LIMIT {$start},10";
$stmt = $pdo->prepare($sql);
$stmt->execute();
if (!$stmt) {
    exit("<h1>不好意思，出错了！！</h1>");
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="initial-scale=1, maximum-scale=3, minimum-scale=1, user-scalable=no"/>
    <title>已收录的名字</title>
    <link href="/makia/lib/lib/css?css=bootstrap.min" rel="stylesheet">
    <style>
        th, td {
            text-align: center;
        }

        tr:hover td {
            background-color: #b9bbbe !important;
        }

        tr th {
            font-size: 15px;
        }

        .th-name {
            width: 40%;
        }

        .th-contact {
            width: 40%;
        }

        .th-time {
            width: 20%;
        }

        .cytb-header {
            width: 100%;
            height: 150px;
            background-color: #3d3d3d;
        }

        .cytb-header h2 {
            color: #f9f9f9;
            text-align: center;
        }

        .cytb-pager {
            width: 80%;
            height: 50px;
            margin: 0 auto;
            border: 1px solid green;
            padding: 0;
            position: absolute;
        }

        .cytb-pager a,
        .cytb-pager p {
            display: block;
            position: relative;
        }

        .cytb-pager p {
            width: 40%;
        }
    </style>
</head>
<body>
<div class="cytb-header">
    <br>
    <h2>城院贴吧</h2>
    <h2>新公众号名字征集</h2>
</div>
<table class="table table-striped">
    <tr>
        <th class="th-name"><span style="display: block;padding-top: 6%">名字</span></th>
        <th class="th-contact"><span style="display: block;padding-top: 6%">联系方式</span></th>
        <th class="th-time">提交<br>时间</th>
    </tr>
    <?php
    foreach ($stmt as $row) {
        ?>
        <tr>
            <td><?php echo $row["content"]; ?></td>
            <td><?php echo $row["wxid"]; ?></td>
            <td><?php $time = $row["time"];
                echo date("m-d", $time);
                echo "<br>";
                echo date("H:i", $time); ?></td>
        </tr>
        <?php
    }
    ?>
</table>
<?php
$stmt = $pdo->prepare("SELECT COUNT(rank) AS total FROM ims_collect_name");
$stmt->execute();
if (!$stmt)
    exit("<h1>页码找错了！！</h1>");
else {
    foreach ($stmt as $row) {
        $total = $row["total"];
    }
    $tmp = $total % 10;
    $total_page = $tmp == 0 ? $total / 10 : ($total-$tmp)/10 + 1;
}
?>
<ul class="pagination justify-content-center" id="bottom">
    <?php if ($page - 1 <= 0): ?>
        <li class="page-item disabled"><a class="page-link"
                                          href="javascript:void(0)">&laquo;</a>
        </li>
    <?php else: ?>
        <li class="page-item"><a class="page-link"
                                 href="https://ccdgut.yuninter.net/IcedCappuccino/wxapp/msg/tieba_new_name?page=<?php echo $page-1;?>">&laquo;</a>
        </li>
    <?php endif; ?>
    <li class="page-item"><a class="page-link" href="javascript:void(0)"><?php echo $page; ?>
            &nbsp;/&nbsp;<?php echo $total_page; ?></a></li>
    <?php if ($page == $total_page): ?>
        <li class="page-item disabled"><a class="page-link"
                                          href="javascript:void(0)">&laquo;</a>
        </li>
    <?php else: ?>
        <li class="page-item"><a class="page-link"
                                 href="https://ccdgut.yuninter.net/IcedCappuccino/wxapp/msg/tieba_new_name?page=<?php echo $page+1;?>">&raquo;</a>
        </li>
    <?php endif; ?>
</ul>
<script src="/makia/lib/lib/js?js=jquery-3.3.1"></script>
<script src="/makia/lib/lib/js?js=bootstrap.min"></script>
</body>
</html>
