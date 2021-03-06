<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://pic.taodianke.com//static/Touch/css/body.css"/>
    <title>直播间</title>
</head>
<body style="background-image:url('https://pic.taodianke.com//static/Touch/images/bg_ground.png'); width:100%;height:100%;position:fixed;background-repeat:no-repeat;">

<!-- 用户头像&人数 -->
<div class="live-user clearfix">
    <div class="user-imgs clearfix">

    </div>
    <div class="user-total" id="usertotal"></div>
</div>

<!--商品内容-->
<div id="wrapper">
    <div id="scroller">
        <div class="pullDown"></div>
        <div class="live-content">

        </div>
        <div id="to_bottom"></div>
    </div>
</div>

<a class="live_more" href="<?php echo U('Index/index', array('uid' => $proxy_id));?>">更多宝贝</a>

<script type="text/javascript" src="http://aaa.wgzapp.net/cached/compile/ffdf1923a125a455.js"></script>
<script type="text/javascript" src="https://pic.taodianke.com//static/Touch/js/iscroll-probe.js"></script>
<script type="text/javascript">

    var zbj_data = '';

    //随机数
    function GetRandomNum(Min, Max) {
        var Range = Max - Min;
        var Rand = Math.random();
        return (Min + Math.round(Rand * Range));
    }

    var usertotal = GetRandomNum(2800, 3200);

    function UserTotal() {
        usertotal += GetRandomNum(-1, 2);
        $('#usertotal').text(usertotal + '人');
    }


    var myScroll, //对象
        loading = false, //下拉加载显示
        loadflg = true,//下拉加载判断
        gids = [], //商品id
        time = 0;

    function getRandom(min, max){
        var r = Math.random() * (max - min);
        var re = Math.round(r + min);
        re = Math.max(Math.min(re, max), min)
        return re;
    }

    function success_goods() {
        var data = zbj_data;
        var logo = 'http://pic.taodianke.com/static/Touch/images/mao.jpg';
        var len = data.length;
        var temp = '';
        var num = getRandom(0, len-1);
        console.log(num);
        temp += set_goods_template(data[num], logo);
        $('.live-content').append(temp);
        myScroll.refresh();
        //跳转最后
        var to = document.querySelector("#to_bottom");
        myScroll.scrollToElement(to, 300, 0, true);


    }


    //设置商品模版
    function set_goods_template(data, logo) {
        var temp = '';
        var myDate = new Date();//获取系统当前时间
        var mytime = myDate.toLocaleTimeString(); //获取当前时间
        temp += '<div class="content-item">';
        temp += '<input type="hidden" id="' + data.id + '" value="' + data.id + '"/>';
        temp += '<div class="item-time"><span class="time-style">' + mytime + '</span></div>';
        temp += '<div class="item-goodsimg clearfix">';
        temp += '<div class="item-user-image" style="background-image: url(' + logo + ');"></div>';
        temp += '<div class="goodsimg-img">';
        temp += '<a href="' + data.url + '"><img src="' + data.pic_url + '"></a>';
        temp += '</div>';
        temp += '</div>';
        temp += '<div class="item-goodstext clearfix">';
        temp += '<div class="item-user-image" style="background-image: url(' + logo + ');"></div>';
        temp += '<div class="goodstext-triangle"></div>';
        temp += '<div class="goodstext-text">';
        temp += '<div class="text-desc">' + data.title + '<br>【原价】' + data.price + '<br>【券后价】' + data.coupon_price + '</div>';
        temp += '<div class="text-btn"><a href="' + data.url + '">领劵购买</a></div>';
        temp += '</div>';
        temp += '</div>';
        temp += '</div>';
        return temp;
    }

    //请求
    function get_goods_content() {
        var get_url = "<?php echo U('Index/getHotGoods');?>";
        $.get(get_url, function (data) {
            if (data.code == 0) {
                zbj_data = data.data;
                success_goods();
                setInterval("success_goods()", 3000);
            }
        })
    }

    var success_user = function (data) {
        if (data.code == 0) {
            var temp = '';
            temp += set_user_template(data.user);
            $('.user-imgs').html(temp);

        }
    }

    //设置商品模版
    function set_user_template(data) {
        var temp = '';
        temp += '<div class="imgs-item">';
        temp += '<div class="item-head" style="background-image: url(' + data.headimgurl + ')"  ></div>';
        temp += '<div class="item-name" >' + data.nickname + '</div>';
        temp += '</div>';
        return temp;
    }

    //请求
    function get_user_content() {
        $.ajax({
            type: 'get',
            url: "<?php echo U('Index/getUser');?>",
            contentType: 'application/x-www-form-urlencoded',
            timeout: 6000,
            data: {},
            success: success_user
        });
    }

    //滑动
    function isPassive() {
        var supportsPassiveOption = false;
        try {
            addEventListener("test", null, Object.defineProperty({}, 'passive', {
                get: function () {
                    supportsPassiveOption = true;
                }
            }));
        } catch (e) {
        }
        return supportsPassiveOption;
    }

    document.addEventListener('touchmove', function (e) {
        e.preventDefault();
    }, isPassive() ? {
        capture: false,
        passive: false
    } : false);


    myScroll = new IScroll('#wrapper', {
        scrollbars: false,
        mouseWheel: true,
        interactiveScrollbars: false,
        shrinkScrollbars: 'scale',
        fadeScrollbars: true,
        click: true,
        probeType: 3
    });
    //监听滚动事件
    myScroll.on('scroll', function () {
        if (this.y < myScroll.maxScrollY) {
            myScroll.refresh();
        }
        if (this.y > 40 && !loading) {
            $('.pullDown').text('松手加载更多');
            loading = true;
        } else if (this.y < 40 && loading) {
            $('.pullDown').text('');
        }
        time = 0;
    });

    //下拉松手后开始加载
    myScroll.on('scrollEnd', function () {
        if (loading) {
            loading = false;//重置pullDown
            $('.pullDown').text('');
            if (loadflg) {
                loadflg = false;
                setTimeout("get_goods_content()", 1000)
            }
        }
    });

    var touch = true;//用来判断用户手指是不是在做浏览商品的操作
    var delayt;
    var wrapperBox = document.getElementById("wrapper");

    function delaytrue() {
        delayt = setTimeout("touch=true", 4000);
    }

    wrapperBox.addEventListener('touchstart', function () {
        touch = false;
        clearTimeout(delayt);
    }, false);
    wrapperBox.addEventListener('touchmove', function () {
        touch = false;
    }, false);
    wrapperBox.addEventListener('touchend', function () {
        delaytrue();
    }, false);


    $(document).ready(function () {
        get_goods_content();
        get_user_content();
        UserTotal();
        setInterval("UserTotal()", 5 * 1000);
        //计时
        setInterval(function () {
            time++;
        }, 1000);
    });


</script>
</body>
</html>