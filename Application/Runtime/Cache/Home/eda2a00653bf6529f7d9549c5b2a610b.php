<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>淘店客</title>
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/common.css?<?php echo C('V');?>" />
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/item.css?<?php echo C('V');?>" />
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/login.css?<?php echo C('V');?>" />

</head>
<body>
<header>
    <div class="header">
        <div class="content">
            <div class="top fl">
                <ul>
                    <?php if(session('uid') > 0): ?><li><a href="javascript:void(0)">欢迎您：
                            <?php echo session('user')['mobile']; ?>
                        </a></li>
                        <li><a href="<?php echo U('Public/logout');?>">退出</a></li>
                        <?php else: ?>
                        <li><a href="<?php echo U('Public/login');?>">登录</a></li>
                        <li><a href="<?php echo U('Public/register');?>">注册</a></li><?php endif; ?>
                </ul>
            </div>
            <div class="top fr">
                <ul>
                    <li><a href="http://m.taodianke.com" target="_blank">手机站</a></li>
                    <li>|</li>
                    <li>
                        <a href="tencent://message/?uin=125789730&Site=http://www.taodianke.com/&Menu=yes" target="_blank">联系客服</a>
                    </li>
                </ul>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="content">
        <div class="twopart">
            <a title="淘店客" href="<?php echo U('/');?>">
                <div class="logo fl">
                    <img src="/Public/Home/img/logo.jpg" width="244px" height="44px">
                </div>
            </a>
            <div class="searchDiv">
                <form class="search-form fl" name="search" action="<?php echo U('Item/search');?>" method="get">
                    <input type="text" class="search-inputbox" name="keyword" id="keyword" placeholder="输入您要的商品" value="<?php echo ($keyword); ?>">
                    <button id="search"></button>
                </form>
                <div class="clear"></div>
                <div class="search-keywords">
                    <ul>
                        <li><a href="<?php echo U('Item/search',array('keyword'=>'耳机'));?>" target="_blank">
                            <span class="tdk-color">耳机</span> </a></li>
                        <li><a href="<?php echo U('Item/search',array('keyword'=>'面膜'));?>" target="_blank"> <span>面膜</span> </a>
                        </li>
                        <li><a href="<?php echo U('Item/search',array('keyword'=>'女装'));?>" target="_blank">
                            <span class="tdk-color">女装</span> </a></li>
                        <li><a href="<?php echo U('Item/search',array('keyword'=>'行李箱'));?>" target="_blank"> <span>行李箱</span>
                        </a></li>
                        <li><a href="<?php echo U('Item/search',array('keyword'=>'口红'));?>" target="_blank"> <span>口红</span> </a>
                        </li>
                        <li><a href="<?php echo U('Item/search',array('keyword'=>'充电宝'));?>" target="_blank"> <span>充电宝</span>
                        </a></li>
                        <li><a href="<?php echo U('Item/search',array('keyword'=>'洗面奶'));?>" target="_blank"> <span>洗面奶</span>
                        </a></li>
                        <li><a href="<?php echo U('Item/search',array('keyword'=>'手机壳'));?>" target="_blank">
                            <span class="tdk-color">手机壳</span> </a></li>
                        <li><a href="<?php echo U('Item/search',array('keyword'=>'抽纸'));?>" target="_blank"> <span>抽纸</span> </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
</header>
<div class="content" id="body-content">
    
    <div class="main"
    <?php if(!empty($img)): ?>style="background: url(<?php echo ($img); ?>) no-repeat"<?php endif; ?>
    >
    <div class="wrap">
        <div class="loginBox">
            <div class="tit">手机号登录</div>
            <input type="text" class="input_l" name="account" placeholder="账号" id="mobile">
            <input type="password" class="input_l" name="pwd" placeholder="密码" id="password">
            <p class="info"></p>
            <div>
                <button class="login_btn" id="login_btn">登录</button>
                <a class="reg_btn" href="<?php echo U('Public/register');?>">注册</a>
            </div>
        </div>
    </div>
    </div>

</div>
<footer>
    <div class="footer">
        <ul>
            <li><a href="javascript:void(0)"><i class="i_abus"></i>关于我们</a></li>
            <li><a href="javascript:void(0)"><i class="i_coopr"></i>商家合作</a></li>
            <li><a href="javascript:void(0)"><i class="i_shopsafe"></i>联系我们</a></li>
            <li><a href="javascript:void(0)"><i class="i_helpcnter"></i>常见问题</a></li>
        </ul>
        <pre><a href="javascript:void(0)" target="_blank">陕ICP备16008812号</a> Copyright © 2010 - 2017 http://www.taodianke.com/ All Rights Reserved  统计代码 版权所有：陕西三多网络科技有限公司 </pre>
    </div>
</footer>
<div id="back-to-top" class="iton-top-index">
    <a href="#"></a>
</div>
<script type="text/javascript">
    $('#search').click(function () {
        var keyword = $('#keyword').val();
        if (!keyword) {
            return false;
        }
    });

    $(function () {
        $("#back-to-top").hide();
        $(function () {
            $(window).scroll(function () {
                if ($(window).scrollTop() > 100) {
                    $("#back-to-top").fadeIn(1500);
                } else {
                    $("#back-to-top").fadeOut(1500);
                }
            });
            //当点击跳转链接后，回到页面顶部位置
            $("#back-to-top").click(function () {
                $('body,html').animate({scrollTop: 0}, 500);
                return false;
            });
        });
    });
</script>

    <script type="text/javascript">
        (function () {
            $('#login_btn').click(function () {
                var url = "<?php echo U('Public/doLogin');?>";
                var mobile = $('#mobile').val();
                var pwd = $('#password').val();
                if (!mobile) {
                    $('.info').text('请输入手机号码！');
                    return false;
                }
                if (!pwd) {
                    $('.info').text('请输入密码！');
                    return false;
                }
                $.post(url, {mobile: mobile, password: pwd}, function (res) {
                    if (res.status == 1) {
                        window.location.href = res.url;
                    } else {
                        $('.info').text(res.info);
                    }
                });
            });
            $('.input_l').focus(function () {
                $('.info').text('');
            })
        })();
    </script>

</body>
</html>