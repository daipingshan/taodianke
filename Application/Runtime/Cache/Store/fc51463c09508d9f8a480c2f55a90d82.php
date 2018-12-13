<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="keywords" content="宅喵生活-会员中心">
    <meta name="description" content="宅喵生活-会员中心">
    <link href="/Public/Touch/css/index.css?v=<?php echo time();?>" rel="stylesheet" type="text/css"/>
    <link href="https://pic.taodianke.com//static/Touch/css/reset.css" rel="stylesheet" type="text/css"/>
    <title>宅喵生活-会员中心</title>
    <script src="https://pic.taodianke.com//static/Touch/js/rem.js"></script>
</head>
<body>
<!--tab-->
<div class="header-user">
    <img src="/Public/Touch/images/user-card.png">
    <h3 class="name"><?php echo ($info['username']); ?></h3>
    <h3 class="mobile"><?php echo ($info['mobile']); ?></h3>
</div>
<div class="content-user-box">
    <div class="icon-box cl">
        <div class="icon">
            <a href="<?php echo U('index/index', array('uid'=>$uid));?>"><img src="/Public/Touch/images/icon-user.png" alt=""></a>
            <h3>个人商城</h3>
        </div>
        <div class="icon">
            <a href="<?php echo U('Index/getCateList', array('cate'=>'28','uid'=>$uid));?>"><img src="/Public/Touch/images/icon-9.9.png" alt=""></a>
            <h3>9.9包邮</h3>
        </div>
        <div class="icon">
            <a href="<?php echo U('Index/getCateList', array('cate'=>'1000','uid'=>$uid));?>"><img src="/Public/Touch/images/icon-hot.png" alt=""></a>
            <h3>热销宝贝</h3>
        </div>
        <div class="icon">
            <a href="<?php echo U('Coupon/index');?>"><img src="/Public/Touch/images/icon-package.png" alt=""></a>
            <h3>我的红包</h3>
        </div>
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