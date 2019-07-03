<?php
/**
 * @author Makia98 https://github.com/ColaTasty
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>【大学四年】2019新生咨询群</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta content="width=device-width,user-scalable=no" name="viewport">
    <style>
        #content-box {
            width: 100%;
            min-height: 500px;
        }

        #content-box .content-item {
            width: 100%;
            min-height: 200px;
            padding-top: 10px;
        }

        #content-box .content-item a.location {
            display: block;
            text-align: center;
        }

        #content-box .content-item a:hover,
        #content-box .content-item a:link,
        #content-box .content-item a:visited,
        #content-box .content-item a:active{
            color: #150df7;
            text-decoration: none;
        }

        #content-box .content-item #qr-code {
            display: block;
            width: auto;
            height: 200px;
            margin: 0 auto;
            border: 2px solid #aaa;
            border-radius: 20px;
        }

        #content-box .content-item #comment {
            display: block;
            text-align: center;
        }
    </style>
</head>
<body>
<input type="text" style="position: fixed;top: -50px;left: -50px;" id="tmp-copy"/>
<div id="content-box">
    <div class="content-item">
        <p id="comment" style="color: red;">新生咨询群1、2、3、4已满100人</p>
        <p id="comment">请添加机器人微信，它能拉你进更多群！</p>
        <a href="javascript:void(0)" class="location" account="cytb666666" onclick="wx_onClick(this)">点我复制机器人微信【cytb666666】</a>
        <img src="https://ccdgut.yuninter.net/IcedCappuccino/lib/lib/jpg?jpg=AI" alt="AI机器人微信"
             onclick="img_onClick(this)" id="qr-code" />
    </div>
    <hr>
    <div class="content-item">
        <a href="https://jq.qq.com/?_wv=1027&k=5VW1ESc" class="location">点我加入2019级新生咨询【QQ】群</a>
        <img src="https://ccdgut.yuninter.net/IcedCappuccino/lib/lib/png?png=QQqun" alt="QQ群" id="qr-code" onclick="img_onClick(this)">
        <a href="javascript:void(0)" class="location" id="qq-copy" account="770341598" onclick="qq_onClick(this)">点我复制QQ群号【770341598】</a>
    </div>
</div>
<script>
    let img_onClick = function (e) {
        location.href = e.getAttribute("src");
    };
    let qq_onClick = function (e) {
        let tmp = document.getElementById("tmp-copy");
        let qq = e.getAttribute("account");
        tmp.value = qq;
        tmp.focus();
        tmp.select();
        document.execCommand("Copy");
        alert("复制成功！快去QQ粘贴加群吧！");
    };
    let wx_onClick = function (e) {
        let tmp = document.getElementById("tmp-copy");
        let qq = e.getAttribute("account");
        tmp.value = qq;
        tmp.focus();
        tmp.select();
        document.execCommand("Copy");
        alert("复制成功！快去微信粘贴添加吧！");
    };
</script>
</body>
</html>
