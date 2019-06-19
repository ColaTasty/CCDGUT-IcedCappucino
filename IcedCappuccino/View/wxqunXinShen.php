<?php
/**
 * @author Makia98 https://github.com/ColaTasty
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>【城院贴吧】新生微信群</title>
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
<div id="content-box">
    <div class="content-item">
        <p id="comment">添加机器人微信，获取更多好玩的的姿势^_^</p>
        <img src="https://ccdgut.yuninter.net/IcedCappuccino/lib/lib/jpg?jpg=AI" alt="AI机器人微信"
             onclick="img_onClick(this)" id="qr-code" />
    </div>
    <!--    <div class="content-item">-->
    <!--        <img id="qr-code" src="https://ccdgut.yuninter.net/IcedCappuccino/lib/lib/jpg?jpg=qun1" alt="新生群①"/>-->
    <!--        <p id="comment">已满100人，请加【城院贴吧AI机器人】邀请进群</p>-->
    <!--        <p id="comment">新生咨询群①</p>-->
    <!--    </div>-->
    <hr>
    <div class="content-item">
        <a href="https://jq.qq.com/?_wv=1027&k=5VW1ESc" class="location">点我加入2019级新生咨询【QQ】群</a>
        <p id="comment">👆进QQ群哦👆</p>
    </div>
    <hr>
    <div class="content-item">
        <img id="qr-code" src="https://ccdgut.yuninter.net/IcedCappuccino/lib/lib/png?png=qun2"
             onclick="img_onClick(this)" alt="新生群②"/>
        <p id="comment">新生咨询群②</p>
    </div>
    <div class="content-item">
        <img id="qr-code" src="https://ccdgut.yuninter.net/IcedCappuccino/lib/lib/png?png=qun3"
             onclick="img_onClick(this)" alt="新生群③"/>
        <p id="comment">新生咨询群③</p>
    </div>
    <div class="content-item">
        <img id="qr-code" src="https://ccdgut.yuninter.net/IcedCappuccino/lib/lib/jpg?jpg=qun4"
             onclick="img_onClick(this)" alt="新生群④"/>
        <p id="comment">新生咨询群④</p>
    </div>
</div>
<script>
    let img_onClick = function (e) {
        location.href = e.getAttribute("src");
    }
</script>
</body>
</html>
