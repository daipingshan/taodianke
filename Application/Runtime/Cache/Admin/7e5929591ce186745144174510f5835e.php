<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!-- saved from url=(0098)http://kolplatform.jinritemai.com/index/myarticle/previewarticle?id=4891607&sig=1512525770338.7427 -->
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta id="viewport" name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,minimal-ui">
    <!--页面title-->
    <title>预览文章</title>

    <script src="/Public/Admin/js/jquery.min.js"></script>
    <script src="/Public/Admin/js/swipe.js"></script>
    <script src="/Public/plugins/bower_components/layer/layer.js"></script>

    <!--预览页面PC -->
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/previewgallery.css?v=<?php echo time();?>">
    <link rel="stylesheet" href="/Public/Admin/css/layer.css">
</head>
<body>
<div id="simulator">
    <div id="main">
        <div id="gallery" class="swipe" style="visibility: visible;">
            <div class="swipe-wrap">
                <!--figcaption为图片描述，控制在200字以内-->
                <!--img标签width和height属性须事先给出，由于懒加载的需要，必须把"src"改写为"alt-src" -->
                <?php if(is_array($data)): $k = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div class="swiper-slide" data-index="0">
                        <figure>
                            <div class="img-wrap">
                                <img src="<?php echo ($vo["img"]); ?>">
                                <div class="pswp__price_tag position_left" style="top:20%;left:60%;">
                                    <span>¥<?php echo ($vo["price"]); ?></span>
                                    <i class="dot-con">
                                        <i class="dot-animate"></i>
                                        <i class="dot"></i>
                                    </i>
                                </div>
                            </div>
                            <div class="bottom-bar">
                                <h2>
                                    <?php echo ($vo["title"]); ?>
                                    <a class="direct-link"
                                       href="<?php echo ($vo["tmall_url"]); ?>"
                                       target="_blank">直达链接</a>
                                </h2>
                                <figcaption>
                                    <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong> <?php echo ($vo["describe_info"]); ?>
                                </figcaption>
                            </div>
                        </figure>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>

        <div class="btn-container">
            <span class="prevBtn" onclick="mySwipe.prev();"></span>
            <span class="nextBtn" onclick="mySwipe.next();"></span>
        </div>
    </div>
</div>

<script type="text/javascript">
    var num = "<?php echo count($data);?>";
    $(function () {
        if ($('#gallery').find('.swipe-wrap').children().length) {
            window.mySwipe = $('#gallery').Swipe({
                startSlide: 0,
                // speed: 400,
                // auto: 10000,
                continuous: false,
                disableScroll: false,
                stopPropagation: false,
                callback: function (index, elem) {
                },
                transitionEnd: function (index, elem) {
                }
            }).data('Swipe');
        }

        $('#gallery').hover(function (e) {
            // e.preventDefault();

        }, function (e) {
            // e.preventDefault();

        });
    });
</script>