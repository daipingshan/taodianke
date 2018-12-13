<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="keywords" content="宅喵生活-绑定手机号">
    <meta name="description" content="宅喵生活-绑定手机号">
    <link href="/Public/Touch/css/index.css?v=<?php echo time();?>" rel="stylesheet" type="text/css"/>
    <link href="https://pic.taodianke.com//static/Touch/css/reset.css" rel="stylesheet" type="text/css"/>
    <title>宅喵生活-绑定手机号</title>
    <script src="https://pic.taodianke.com//static/Touch/js/rem.js"></script>
</head>
<body>
<!--tab-->
<div class="header">
    <img src="/Public/Touch/images/bind-mobile-header.png">
</div>
<div class="content-box">
    <div class="input-group cl">
        <div class="input-left">
            <i class="username-logo"></i>
        </div>
        <div class="input-right input-style">
            <input type="text" name="username" placeholder="请输入会员姓名！">
        </div>
    </div>
    <div class="input-group cl">
        <div class="input-left">
            <i class="mobile-logo"></i>
        </div>
        <div class="input-right input-style">
            <input type="tel" name="mobile" placeholder="输入会员手机号码！">
        </div>
    </div>
    <div class="input-group cl">
        <div class="input-left">
            <i class="code-logo"></i>
        </div>
        <div class="input-right input-style input-code">
            <input type="number" name="code" placeholder="请输入验证码！">
        </div>
        <div class="input-btn">
            <button class="get-code" type="button">获取验证码</button>
        </div>
    </div>
    <div class="input-group btn-box">
        <button type="submit">提交信息，成为会员</button>
    </div>

    <div class="footer-box">
        <h3>会员权益</h3>
        <p>1：领取红包，宅喵生活店消费无门槛抵扣现金券</p>
        <p>2：宅喵生活店消费折扣优惠</p>
    </div>
</div>


<script src="https://pic.taodianke.com//static/Touch/js/jquery-1.11.0.min.js" type="text/javascript"></script>
</body>
</html>