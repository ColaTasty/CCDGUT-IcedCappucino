<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/12/9
 * Time: 0:12
 */

$row = ["一","二","三","四","五","六","七","八","九","十",];
$tables = $this->response["ttype"]==="week" ? [
    "Mon"=>json_decode($this->response["Mon"]),
    "Tue"=>json_decode($this->response["Tue"]),
    "Wed"=>json_decode($this->response["Wed"]),
    "Thu"=>json_decode($this->response["Thu"]),
    "Fri"=>json_decode($this->response["Fri"]),
    "Sat"=>json_decode($this->response["Sat"]),
    "Sun"=>json_decode($this->response["Sun"])
] : [
    "Mon"=>json_decode($this->response["Mon"]),
    "Tue"=>json_decode($this->response["Tue"]),
    "Wed"=>json_decode($this->response["Wed"]),
    "Thu"=>json_decode($this->response["Thu"]),
    "Fri"=>json_decode($this->response["Fri"]),];
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $this->response["tname"] ?></title>
    <meta content="text/html" http-equiv="Content-Type" charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="/IcedCappuccino/lib/lib/css?css=calendar">
</head>
<body>
<h3 id="tname"><?php echo $this->response["tname"] ?></h3>
<table id="values">
    <tr>
        <th style="width: 20px">时间点</th>
        <?php
            if ($this->response['ttype'] == "week"){
                echo "<th>星期一</th>".
                     "<th>星期二</th>".
                     "<th>星期三</th>".
                     "<th>星期四</th>".
                     "<th>星期五</th>".
                     "<th>星期六</th>".
                     "<th>星期日</th>";
            }elseif ($this->response['ttype'] == "class"){
                echo "<th>星期一</th>".
                     "<th>星期二</th>".
                     "<th>星期三</th>".
                     "<th>星期四</th>".
                     "<th>星期五</th>";
            }
        ?>
    </tr>
    <?php
    if($this->response['ttype'] == "week") {
        for ($ridx = 0; $ridx < count($row); $ridx++) {
            if ($ridx % 2 == 0) {
                echo "<tr id='rows' style='background-color: rgba(169,170,168,0.6)'><td>$row[$ridx]</td>";
            } else {
                echo "<tr id='rows'><td>$row[$ridx]</td>";
            }
            foreach ($tables as $key => $value) {
                if ($value[$ridx]->value === null) {
                    echo "<td></td>";
                } else {
                    echo "<td id=\"value\">" .
                        "<p>" .
                        $value[$ridx]->start_time->hour . ":" . $value[$ridx]->start_time->minute .
                        "</p>" .
                        "<p>至</p>" .
                        "<p>" .
                        $value[$ridx]->end_time->hour . ":" . $value[$ridx]->end_time->minute .
                        "</p>" .
                        "<p>" .
                        $value[$ridx]->value .
                        "</p>" .
                        "</td>";
                }
            }
            echo "</tr>";
        }
    }
    elseif ($this->response["ttype"] == "class") {
        for ($ridx = 0; $ridx < count($row); $ridx++) {
            echo "<tr id='rows'><td>$row[$ridx]</td>";
            foreach ($tables as $key => $value) {
                if ($value[$ridx]->start_time === $ridx) {
                    echo "<td id=\"value\" rowspan=\"" . (((int)$value[$ridx]->end_time - (int)$value[$ridx]->start_time)+1) . "\">" .
                        "<p>" . $value[$ridx]->value->className . "</p>" .
                        "<p>" . $value[$ridx]->value->classTeacher . "</p>" .
                        "<p>" . $value[$ridx]->value->classRoom . "</p>" .
                        "</td>";
                }
                elseif(is_null($value[$ridx]->start_time)){
                    echo "<td></td>";
                }
            }
            echo "</tr>";
        }
    }
    ?>
</table>
<div id="footer">
    <p><?php echo date("Y")?> &copy; 城院贴吧小助手</p>
</div>
</body>
</html>
