<?php if (!defined('THINK_PATH')) exit();?><html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>商品详情</title>
    <link href="https://pic.taodianke.com//static/Touch/css/detail.css" rel="stylesheet">
</head>
<body>

<!--固定的-->
<div class="tiger_nav1" id="head_seach">
    <div class="seach_nav">
        <div class="seach_1" onclick="javascript:history.go(-1);return false;"></div>
        <div class="seach_2">
            <span class="t-index">商品详情</span>
        </div>
        <div class="seach_3"  data-url="<?php echo ($jumpUrl); ?>" onclick="window.location.href=$(this).data('url')"></div>
    </div>
</div>
<!--固定的结束-->
<p style="width: 100%;
    text-align: center;
    font-size: 1.5rem;
    color: #FF420F;margin: 100px 0 15px 0;">该商品已下线！
</p>
<p style="text-align: center;">
    页面自动 <a id="href" href="<?php echo ($jumpUrl); ?>">跳转</a> 等待时间：
    <b id="wait"><?php echo ($waitSecond); ?></b>
</p>
<script type="text/javascript">
    (function () {
        var wait = document.getElementById('wait'), href = document.getElementById('href').href;
        var interval = setInterval(function () {
            var time = --wait.innerHTML;
            if (time <= 0) {
                location.href = href;
                clearInterval(interval);
            }
        }, 1000);
    })();
</script>
</body>
</html>