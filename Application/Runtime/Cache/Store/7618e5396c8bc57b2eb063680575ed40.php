<?php if (!defined('THINK_PATH')) exit();?><html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo ($item['title']); ?>--商品详情</title>
    <link rel="stylesheet" type="text/css" href="https://pic.taodianke.com//static/Touch/css/style_v3.css"/>
    <link href="https://pic.taodianke.com//static/Touch/css/main.css?version=<?php echo C('version');?>" rel="stylesheet">
    <link href="https://pic.taodianke.com//static/Touch/css/detail.css?version=<?php echo C('version');?>" rel="stylesheet">
    <link rel="stylesheet" href="https://pic.taodianke.com//static/Touch/css/layer.css?version=<?php echo C('version');?>" id="layui_layer_skinlayercss">
</head>
<body>

<!--固定的-->
<div class="tiger_nav1" id="head_seach">
    <div class="seach_nav">
        <div class="seach_1" onclick="javascript:history.go(-1);return false;"></div>
        <div class="seach_2">
            <span class="t-index">商品详情</span>
        </div>
        <div class="seach_3"  data-url="<?php echo U('Index/index',array('uid'=>$uid));?>" onclick="window.location.href=$(this).data('url')"></div>
    </div>
</div>
<!--固定的结束-->

<div id="pull" class=" detail-product">
    <div class="scroll-box">
        <div class="content detail-product-head">
            <div class="detail-product-img" onclick="openTips();">
                <img src="<?php echo ($item['pic_url']); ?>" alt="<?php echo ($item["title"]); ?>">
            </div>
            <!--商品信息-->
            <div class="media-list line-btm">
                <div class="media-list-title line-top"><?php echo ($item["title"]); ?></div>
                <?php if(!empty($$item["intro"])): ?><div class="media-list-subtext"><b>小编推荐：</b><?php echo ($item["intro"]); ?></div><?php endif; ?>
                <div class="media-list-info flex-center oh">
                    <div class="flex-col">
                        <i class="rmb media-arial fl">¥</i>
                        <span class="media-arial media-price fl"> <?php echo ($item["coupon_price"]); ?></span>
                        <div class="fl coupon-txt">
                            <p class="old-price media-arial"><i class="fl">¥</i> <?php echo ($item["price"]); ?></p>
                            <p>券后价</p>
                        </div>
                        <span class="fl coupon-price">
                            <b class="price flex-center">
                                <i class="media-arial">¥</i>
                                <i class="media-arial"><?php echo ($item["quan"]); ?></i></b>
                            <img src="https://pic.taodianke.com//static/Touch/images/quan_03.png" alt="">
                        </span>
                    </div>
                    <div class="sales">
                        <img src="https://pic.taodianke.com//static/Touch/images/head_06.png" alt="">
                        <i class="media-arial"><?php echo ($item["volume"]); ?></i> 已买
                    </div>
                </div>
            </div>


            <div class="copy-tao-words">
                <?php if($item["shop_type"] != 'J'): ?><a class="copy-tao-words-btn" data-taowords="<?php echo ($item["tao_kou_ling"]); ?>" style="background-color:#f8285c;">
                        一键复制下方淘口令
                    </a><?php endif; ?>
                <!-- <div class="copy-tao-words-txt">点击复制后，请打开【手机淘宝】购买！</div> -->
            </div>
            <?php if($item["shop_type"] != 'J'): ?><!--淘口令-->
                <div class="media-list media-tkl text-center" style="display: block;">
                    <div class="detail-command">
                        <div class="detail-command-box">
                            <span id="code1_ios" style="display: inline;"><?php echo ($item["tao_kou_ling"]); ?></span>
                            <input type="text" value="<?php echo ($item["tao_kou_ling"]); ?>" onfocus="iptNum(this, true);" oninput="iptNum(this, false);" style="display: none;">
                        </div>
                    </div>
                    <p class="tkl-txt">长按复制上方淘口令，打开手机淘宝购买</p>
                </div><?php endif; ?>
        </div>

        <?php if($item["shop_type"] != 'J'): ?><!--加载失败-->
        <div class="detail-load-failed">
            <a href="javascript:;">
                <span> <b class="fl">查看图文详情</b> <img src="https://pic.taodianke.com//static/Touch/images/arrow_03.png" alt=""></span>
            </a>
        </div><?php endif; ?>
        <!--内容-->
        <div id="content" class="detail-content line-top line-btm"></div>
        <input id="wxGid" type="hidden" value="<?php echo ($item["num_iid"]); ?>">
    </div>
</div>

<!--底总按钮-->
<div class="pr detail-footer-bar line-top">
    <a class="detail-coupon-after detail-ft-bar text-left" href="javascript:;">
        <p class="price-tit">
            <span class="fl">券后</span><i class="fl media-arial" style="color: #f8285c">¥</i>
        </p>
        <b class="media-arial" style="color: #f8285c;"><?php echo ($item["coupon_price"]); ?></b>
    </a>

    <?php if(($tou == 'weixin') AND ($item['shop_type'] != 'J') ): ?><a class="detail-coupon detail-ft-bar flex-center" href="javascript:;" onclick="openTips();">
            <p>优惠券</p>
            <p><b class="media-arial"><?php echo ($item["quan"]); ?></b> 元</p>
        </a>
        <a class="detail-coupon-buy" href="javascript:;" onclick="openTips();" style="background-color:#f8285c">
            <b>领券购买</b>
        </a>
    <?php else: ?>
        <a class="detail-coupon detail-ft-bar flex-center" href="<?php echo ($item["high_commision_url"]); ?>">
            <p>优惠券</p>
            <p><b class="media-arial"><?php echo ($item["quan"]); ?></b> 元</p>
        </a>
        <a href="<?php echo ($item["high_commision_url"]); ?>" target="_self" class="detail-coupon-buy" rel="nofollow">
            <b>领券购买</b>
        </a><?php endif; ?>

</div>

<!--口令复制模版-->
<div class="dialog">
    <div class="detail-mask"></div>
    <div class="detail-mask-content">
        <div class="detail-mask-allow">
            <img src="https://pic.taodianke.com//static/Touch/images/allow-top.png" alt="">
        </div>
        <div class="detail-mask-command">
            <div class="detail-mask-command-head">
                <div class="detail-mask-command-ios">
                    <p><span>请点击右上角</span> <img src="https://pic.taodianke.com//static/Touch/images/browser-allow.png" alt=""></p>
                    <p><span>并选择<i>在<i class="media-arial">Safari</i>中打开</i></span> <img src="https://pic.taodianke.com//static/Touch/images/browser.png" alt=""></p>
                </div>
                <div class="detail-mask-command-android">
                    <p><span>请点击右上角</span> <img class="android-img" src="https://pic.taodianke.com//static/Touch/images/browser-allow.png" alt=""></p>
                    <p><span>并选择<i> 在浏览器中打开</i></span></p>
                </div>
                <p><span>就可以到淘宝下单啦！</span></p>
            </div>
            <div class="detail-mask-con">
                <div class="detail-mask-con-title">
                    <div class="detail-mask-title-box">
                        <p>或者</p>
                    </div>
                    <div class="detail-mask-line"></div>
                </div>
                <div class="self-copy-area">
                    <p class="detail-mask-tips">长按复制下方淘口令，打开手机淘宝购买</p>
                    <div class="btn detail-mask-command-box">
                        <span id="code2_ios" class="media-arial"><?php echo ($item["tao_kou_ling"]); ?></span>
                        <textarea class="media-arial" onfocus="iptNum(this, true);" oninput="iptNum(this, false);"><?php echo ($item["tao_kou_ling"]); ?></textarea>
                    </div>
                </div>
                <div class="copy-tao-words">
                    <a class="copy-tao-words-btn" data-taowords="<?php echo ($item["tao_kou_ling"]); ?>">一键复制</a>
                    <div class="copy-tao-words-txt">点击复制后，请打开【手机淘宝】购买！</div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://pic.taodianke.com//static/Touch/js/jquery.2.1.4.js"></script>
<script src="https://pic.taodianke.com//static/Touch/js/layer.js?version=<?php echo C('version');?>"></script>
<script src="https://pic.taodianke.com//static/Touch/js/imgLazy.v1.js?version=<?php echo C('version');?>"></script>
<script src="https://pic.taodianke.com//static/Touch/js/clip-board.min.js"></script>
<script src="https://pic.taodianke.com//static/Touch/js/detail.js?version=<?php echo C('version');?>"></script>

</body>
</html>