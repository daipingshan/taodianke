<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="[describe]" />
    <meta name="format-detection" content="telephone=no" />
    <title>宅喵生活</title>
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
<style>
    #day-sign {
        height: 42px;
        width: 50px;
        position: fixed;
        top: 230px;
        left: 80%;
        z-index: 99;
    }

    #mask {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 99998;
        display: none;
    }

    #mask #day-sign-ok {
        margin: 10% auto;
        width: 90%;
        height: 70%;
        background-image: url(https://pic.taodianke.com//static/Touch/images/day-sign-ok-img.png);
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }

    #mask #money {
        position: absolute;
        top: 45%;
        left: 25%;
        width: 18%;
        height: 10%;
        font-size: 40px;
        font-weight: 900;
        color: #fff600;
    }

    #mask #limit-money {
        position: absolute;
        top: 53%;
        left: 56%;
        width: 20%;
        height: 20px;
        font-size: 12px;
        line-height: 20px;
        text-align: center;
        color: #545454;
    }

    #mask #close {
        position: absolute;
        bottom: 10%;
        left: 0;
        width: 50px;
        height: 50px;
        margin: 0 44%;
        background-image: url(https://pic.taodianke.com//static/Touch/images/close.png);
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }


</style>

<div id="containter" class="container">
    <!--固定的-->
    <div class="tiger_nav1" id="head_seach">
        <div class="seach_nav">
            <div class="seach_2" style="width:68%;">
                <form id="search-form" action="<?php echo U('Item/search');?>" method="get">
                    <input type="hidden" name="uid" value="<?php echo ($uid); ?>">
                    <input type="text" id="keyword" name="keyword" value="" class="tige_sear" placeholder="请输入您要找的商品"/>
                    <button id="tiger_search-submit" type="submit" onclick="searchan()">
                        <img src="https://pic.taodianke.com//static/Touch/images/search.png"/>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!--固定的结束-->
<input type="hidden" id="uid" value="<?php echo ($uid); ?>">


<div class="indexnavlist" style="margin: 3%">
    <?php if(is_array($cate)): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo U('Index/getCateList', array('cate'=>$vo['cate_id'],'uid'=>$uid));?>"
           class="indexnaveach indexnaveach1"
           style="background-image: url(<?php echo ($vo["img"]); ?>);background-size : 70%;width: 16.66%">
            <span style="font-size: 0.24rem;"><?php echo ($vo["name"]); ?></span>
        </a><?php endforeach; endif; else: echo "" ;endif; ?>
</div>


<div class="goods_list index_goodslist">
    <div class="index_list_title">
        <span class="intro_title"><i>爆品推荐</i> <em>总有一款属于你</em></span>
    </div>

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
        var btop = $(".loading1").offset().top;
        var loading = $("#list_more").data("on", false);
        $(window).scroll(function(){
            if(loading.data("on")) return;
            if(btop<$(window).height()+$(document).scrollTop()){
                loading.data("on", true).fadeIn();
                $.get(ajaxurl,{p:p,uid:uid},function(res){
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
                                content += '<span style="font-size:12px;color:#969696">券后价</span><span style="font-size:12px">¥</span><span>' + sqlJson[i]['coupon_price'] + '</span>';
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
                    <div class="inoutbg" style="opacity: 0; background-image: url(<?php echo ($item['pic_url']); ?>_240x240.jpg); background-size: cover; background-position: 50% 50%; background-repeat: no-repeat;"></div>
                    <div class="DSbg" style="opacity: 1; background-image: url(<?php echo ($item['pic_url']); ?>_240x240.jpg); background-size: cover; background-position: 50% 50%; background-repeat: no-repeat;"></div>
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
                <div style=" position: absolute;top:60px;left: 10px;font-size:12px;">销量：<?php echo ($item['volume']); ?></div>
                <div style=" position: absolute;top:73px;left: 10px;font-size:12px;">原价：¥<?php echo ($item['price']); ?></div>
            </a>
            <a href="<?php echo ($item['item_url']); ?>" >
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
<?php if(($is_day_sign) == "0"): ?><div id="day-sign" onclick="day_sign()">
        <img src="https://pic.taodianke.com//static/Touch/images/red-package.png">
    </div><?php endif; ?>
<div id="mask">
    <div id="day-sign-ok" onclick="javascript:window.location.href=$(this).data('url')"
         data-url="<?php echo U('Coupon/index');?>?status=not_use">
        <div id="money"></div>
        <div id="limit-money">满<span></span>元可用</div>
    </div>
    <div id="close" onclick="close_sign()"></div>
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
<script>

    /**
     * 签到
     */
    function day_sign() {
        if (isWeiXin()) {
            $.get("<?php echo U('daySign');?>", {}, function (res) {
                if (res.status == 1) {
                    $('#day-sign').hide();
                    $('#mask').show();
                    $('#money').text(res.info.money).css('line-height', $('#money').height() + 'px');
                    $('#limit-money span').text(res.info.limit_money);
                } else {
                    alert(res.info);
                }
            });
        } else {
            alert('该功能仅支持淘店客公众号下使用！');
        }
    }

    /**
     * 关闭签到
     */
    function close_sign() {
        $('#mask').hide();
    }

    function isWeiXin() {
        var ua = window.navigator.userAgent.toLowerCase();
        if (ua.match(/MicroMessenger/i) == 'micromessenger') {
            return true;
        } else {
            return false;
        }
    }
</script>
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