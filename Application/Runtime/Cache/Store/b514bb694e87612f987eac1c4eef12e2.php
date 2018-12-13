<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="[describe]" />
    <meta name="format-detection" content="telephone=no" />
    <title><?php echo ($name); ?>--优惠券大放送了</title>
    <link href="https://pic.taodianke.com//static/Touch/css/style.css" rel="stylesheet" />
    <link href="https://pic.taodianke.com//static/Touch/css/swipper.css" rel="stylesheet" />
    <link href="https://pic.taodianke.com//static/Touch/css/preload.css" rel="stylesheet" />
    <link href="https://pic.taodianke.com//static/Touch/css/loading.css" rel="stylesheet" />
    <link href="https://pic.taodianke.com//static/Touch/css/nav.css" rel="stylesheet"/>
    <script src="https://pic.taodianke.com//static/Touch/js/jquery.2.1.4.js"></script>
    <script src="https://pic.taodianke.com//static/Touch/js/nav.js"></script>
    <script>
        var deviceWidth = document.documentElement.clientWidth;
        if (deviceWidth > 750) deviceWidth = 750;
        document.documentElement.style.fontSize = deviceWidth / 7.5 + "px";
        document.documentElement.style.width = "100%";
    </script>
</head>
<body>


<div id="containter" class="container">

    <!--固定的-->
    <div class="tiger_nav1" id="head_seach">
        <div class="seach_nav">
            <div class="seach_1" onclick="javascript:history.go(-1);return false;"></div>
            <div class="seach_2">
                <span class="t-index"><?php echo ($name); ?></span>
            </div>
            <div class="seach_3" data-url="<?php echo U('Index/index',array('uid'=>$uid));?>" onclick="window.location.href=$(this).data('url')"></div>
        </div>
    </div>
    <!--固定的结束-->

    <!--浮动导航-->
    <div class="tiger_nav" id="pf_seach" style="display:none;height: 50px;">
        <div class="seach_nav">
            <div class="seach_1" onclick="javascript:history.go(-1);return false;"></div>
            <div class="seach_2">
                <form id="search-form" action="<?php echo U('Item/search');?>" method="get">
                    <input type="hidden" name="uid" value="<?php echo ($uid); ?>">
                    <input type="text" id="keyword" name="keyword" value="" class="tige_sear" placeholder="请输入您要找的商品"/>
                    <button id="tiger_search-submit" type="submit" onclick="searchan()">
                        <img src="https://pic.taodianke.com//static/Touch/images/search.png"/>
                    </button>
                </form>
            </div>
            <div class="seach_3" data-url="<?php echo U('Index/index',array('uid'=>$uid));?>" onclick="window.location.href=$(this).data('url')"></div>
        </div>
    </div>

    <!--浮动导航结束-->


    <div class="goods_list index_goodslist">
        <section class="goods" id="pageCon">
            <ul id="list_box" class="list_box">
                <link rel="stylesheet" href="https://pic.taodianke.com//static/Touch/css/dropload.css">
<script src="https://pic.taodianke.com//static/Touch/js/dropload.min.js"></script>

<script>
    $(function (){
        var p = 2;
        var ajaxurl = "<?php echo ($ajaxurl); ?>";
        var maxpage = "<?php echo ($maxpage); ?>";
        var uid = "<?php echo ($uid); ?>";
        var cate = "<?php echo ($cate); ?>";
        var btop = $(".loading1").offset().top;
        var loading = $("#list_more").data("on", false);
        $(window).scroll(function(){
            if(loading.data("on")) return;
            if(btop<$(window).height()+$(document).scrollTop()){
                loading.data("on", true).fadeIn();
                $.get(ajaxurl,{p:p,uid:uid,cate:cate},function(res){
                    var sqlJson = eval(res.data);
                    (function(sqlJson){
                        if(p>maxpage){
                            $("#list_more").show();
                            $(".loading1").appendTo("<span>加载完成</span>");
                        }else{
                            var content="";
                            for(var i in sqlJson){
                                content += '<li class="relative">';

                                content += '<a href = " ' + sqlJson[i]['item_url'] + ' ">';
                                content += '<div class="goods_pic">';
                                content += '<div class="allpreContainer">';
                                content += '<div class="inoutbg" style="background-image: url(' + sqlJson[i]['pic_url'] + '_240x240.jpg); background-size: cover; background-position: 50% 50%; background-repeat: no-repeat;">';
                                content += '</div>';
                                content += '<div class="DSbg" style="background-size: cover; background-position: 50% 50%; background-repeat: no-repeat;">';
                                content += '</div>';
                                content += '</div>';
                                content += '</div>';
                                content += '</a>';

                                content += '<div class="goods_bottom">';

                                content += '<div>';
                                content += '<a class="goods_text" href="' + sqlJson[i]['item_url'] +'">' + sqlJson[i]['title'] + '</a>';
                                content += '</div>';

                                content += '<a href = " ' + sqlJson[i]['item_url'] + ' ">';
                                if (sqlJson[i]['shop_type'] == 'C') {
                                    content += '<div class="comefrom">天猫</div>';
                                } else if (sqlJson[i]['shop_type'] == 'J'){
                                    content += '<div class="comefrom" style="background: url(https://pic.taodianke.com//static/Touch/images/chuchu.png) no-repeat left center/15px;">楚楚街</div>';
                                }else {
                                    content += '<div class="comefrom" style="background: url(https://pic.taodianke.com//static/Touch/images/taobao.png) no-repeat left center/15px;">淘宝</div>';
                                }
                                content += '<div style=" position: absolute;top:60px;left: 10px;font-size:12px;">销量：' + sqlJson[i]['volume'] + '</div>';
                                content += '<div style=" position: absolute;top:73px;left: 10px;font-size:12px;">原价：&yen;' + sqlJson[i]['price'] + '</div>';
                                content += '</a>';

                                content += '<a href = " ' + sqlJson[i]['item_url'] + ' ">';
                                content += '<div class="goodspc">';

                                content += '<div class="goods_price">';
                                content += '<span style="font-size:12px;color:#969696">券后价</span><span style="font-size:12px">¥</span>¥<span>' + sqlJson[i]['coupon_price'] + '</span>';
                                content += '</div>';

                                content += '<div class="new-coupon">';
                                content += '<span>马上领劵</span><span>立减<em class="ljmoney">' + sqlJson[i]['quan'] + '</em>元</span>';
                                content += '</div>';
                                content += '</div>';
                                content += '</a>';

                                content += '</div>';
                                content += '</li>';
                            }
                            $('.list_box').append(content);
                            loading.data("on",false).fadeIn(500);
                        }
                        p++;
                    })(sqlJson);
                    loading.fadeOut();
                });
            }
        });
    });
</script>

<?php if(is_array($items_list)): $i = 0; $__LIST__ = $items_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><li class="relative">
        <a href="<?php echo ($item['item_url']); ?>">
            <div class="goods_pic">
                <div class="allpreContainer">
                    <div class="inoutbg" style="opacity: 0; background-image: url(<?php echo ($item['pic_url']); ?>); background-size: cover; background-position: 50% 50%; background-repeat: no-repeat;"></div>
                    <div class="DSbg" style="opacity: 1; background-image: url(<?php echo ($item['pic_url']); ?>); background-size: cover; background-position: 50% 50%; background-repeat: no-repeat;"></div>
                </div>
            </div>
        </a>

        <div class="goods_bottom">
            <div><a class="goods_text" href="<?php echo ($item['item_url']); ?>"><?php echo ($item['title']); ?></a></div>
            <a href="<?php echo ($item['item_url']); ?>">
                <?php if($item['shop_type'] == 'C'): ?><div class="comefrom">天猫</div>
                <?php elseif($item['shop_type'] == 'J'): ?>
                    <div class="comefrom" style="background: url(https://pic.taodianke.com//static/Touch/images/chuchu.png) no-repeat left center/15px;">楚楚街</div>
                <?php else: ?>
                    <div class="comefrom" style="background: url(https://pic.taodianke.com//static/Touch/images/taobao.png) no-repeat left center/15px;">淘宝</div><?php endif; ?>

                <div style=" position: absolute;top:60px;left: 10px;font-size:12px;">
                    销量：<?php echo ($item['volume']); ?>
                </div>
                <div style=" position: absolute;top:73px;left: 10px;font-size:12px;">
                    原价：¥<?php echo ($item['price']); ?>
                </div>
            </a>
            <a href="<?php echo ($item['item_url']); ?>">
                <div class="goodspc">
                    <div class="goods_price">
                        <span style="font-size:12px;color: #969696">券后价</span>
                        <span style="font-size:12px;">¥</span><span><?php echo ($item['coupon_price']); ?></span>
                    </div>
                    <div class="new-coupon">
                        <span>马上领劵</span><span>立减<em class="ljmoney"><?php echo ($item['quan']); ?></em>元</span>
                    </div>
                </div>
            </a>
        </div>
    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </section>
        <div id="list_more" class="loading1" style="margin-top:10px;text-align:center">
            <span onclick="get_list(0);">查看更多</span>
        </div>
    </div>

</div>

<script type="text/javascript">
    document.getElementById("pf_seach").style.display = "none";
    window.onscroll = function () {
        if (document.documentElement.scrollTop + document.body.scrollTop > 100) {
            document.getElementById("pf_seach").style.display = "block";
        }
        else {
            document.getElementById("pf_seach").style.display = "none";
        }
    }
</script>

<script src="https://pic.taodianke.com//static/Touch/js/idangerous.swiper.min.js"></script>
<script src="https://pic.taodianke.com//static/Touch/js/common_phone.js"></script>
<script src="https://pic.taodianke.com//static/Touch/js/layer.js"></script>
<!--底部菜单开始-->
<div id="menu">
    <ul>
        <li <?php if(($act) == "one"): ?>class="relative active"<?php else: ?>class="relative"<?php endif; ?>>
            <a href="<?php echo U('Index/index',array('uid'=>$uid));?>" class="link-hover"></a>
            <div class="menu-inside">
                <span class="icon_n1"></span>
                <font>首页</font>
            </div>
        </li>
        <li <?php if(($act) == "two"): ?>class="relative active"<?php else: ?>class="relative"<?php endif; ?>>
            <a href="<?php echo U('Index/getCateList', array('cate'=>'28','uid'=>$uid));?>" class="link-hover"></a>
            <div class="menu-inside">
                <span class="icon_n2"></span>
                <font>9.9</font>
            </div>
        </li>
        <li <?php if(($act) == "three"): ?>class="relative active"<?php else: ?>class="relative"<?php endif; ?>>
            <a href="<?php echo U('Index/getCateList', array('cate'=>'1000','uid'=>$uid));?>" class="link-hover"></a>
            <div class="menu-inside">
                <span class="icon_n3"></span>
                <font>热销</font>
            </div>
        </li>

    </ul>
</div>
<!--底部菜单结束-->

</body>
</html>