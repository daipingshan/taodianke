<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>淘店客</title>
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/common.css?<?php echo C('V');?>" />
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/item.css?<?php echo C('V');?>" />
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/list.css?<?php echo C('V');?>" />

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
    
        <div class="main-nav">
            <div class="content">
                <?php if(is_array($menu)): $k = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($k % 2 );++$k;?><a href="<?php echo ($row["url"]); ?>"
                    <?php if($act == $row['act']): ?>class="<?php echo ($row["class"]); ?> nav-cur"
                        <?php else: ?>
                        class="<?php echo ($row["class"]); ?>"<?php endif; ?>
                    ><?php echo ($row["name"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
    
</header>
<div class="content" id="body-content">
    
    <div class="goods-type-filter">
        <div class="goods-type">
            <span class="main-top-tit">商品分类：</span>
            <div class="goods-type-main">
                <span data-id="0" data-type="cate" <?php if(($cate_id) == "0"): ?>class="search cur"<?php else: ?>class="search"<?php endif; ?>><a href="javascript:void(0)">全部<b> (<?php echo ($count); ?>) </b></a></span>
                <?php if(is_array($cate)): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><span data-id="<?php echo ($row["cate_id"]); ?>" data-type="cate" <?php if(($cate_id) == $row['cate_id']): ?>class="search cur"<?php else: ?>class="search"<?php endif; ?>><a href="javascript:void(0)"><?php echo ($row["name"]); ?><b> (<?php echo ($row["num"]); ?>) </b></a></span><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
        <div class="goods-filter">
            <form id="search_goods_form">
                <span class="main-top-tit">高级筛选：</span>
                <div class="goods-filter-main">
                    <div class="price-area">
                        价格
                        <input class="need_positive_float" id="start_price" type="number" step="0.01" min="0" placeholder="￥" value="<?php echo ($start_price); ?>">-<input class="need_positive_float" id="end_price" type="number" step="0.01" min="0" placeholder="￥" value="<?php echo ($end_price); ?>">
                    </div>
                    <div class="yj-area">
                        佣金比例 ≥ <input class="need_positive_float" id="commission" type="number" min="0" placeholder="%" value="<?php echo ($commission); ?>">
                    </div>
                    <div class="sale-area">
                        销量 ≥ <input class="need_positive_float" id="sale_num" type="number" value="<?php echo ($sale_num); ?>" min="0">
                    </div>
                    <button type="button" class="filter-submit search" data-type="num">筛选</button>
                </div>
            </form>
        </div>
    </div>
    <div class="goods-pages">
        <span <?php if($sort == 0): ?>class="pxclick search cur"<?php else: ?>class="pxclick search"<?php endif; ?> data-id="0" data-type="sort"><a href="javascript:void(0);">综合</a></span>
        <span <?php if($sort == 1): ?>class="pxclick search cur"<?php else: ?>class="pxclick search"<?php endif; ?> data-id="1" data-type="sort"><a href="javascript:void(0);">最新</a></span>
        <span <?php if($sort == 3): ?>class="pxclick search cur"<?php else: ?>class="pxclick search"<?php endif; ?> data-id="3" data-type="sort"><a href="javascript:void(0);">销量</a></span>
        <span <?php if($sort == 2): ?>class="pxclick search cur"<?php else: ?>class="pxclick search"<?php endif; ?> data-id="2" data-type="sort"><a href="javascript:void(0);">价格</a></span>
    </div>
    <div class="item-list content">
        <?php if(empty($data)): ?><div class="no-item">
                <h2>没有找到符合条件商品！</h2>
            </div>
            <?php else: ?>
            <ul>
                <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 5 );++$i;?><li
                    <?php if(($mod) == "4"): ?>class="item-content-box no-mr"
                        <?php else: ?>
                        class="item-content-box"<?php endif; ?>
                    >
                    <div class="goods-img">
                        <a href="<?php echo U('Item/detail',array('num_iid'=>$row['num_iid'],'type'=>$row['goods_type']));?>">
                            <img src="<?php echo ($row["pic_url"]); ?>" alt="<?php echo ($row["title"]); ?>">
                        </a>
                        <div class="shop-type">
                            <?php echo ($row['type']); ?>
                        </div>
                    </div>
                    <div class="goods-info">
                        <a class="goods-tit" href="<?php echo U('Item/detail',array('num_iid'=>$row['num_iid'],'type'=>$row['goods_type']));?>">
                            <?php echo ($row["title"]); ?>
                        </a>
                        <div class="goods-slider">
                            <span class="slider"><em data-width="0%" style="width: 0%;"></em></span>
                        </div>
                        <div class="goods-price" title="佣金：<?php echo ($row["commission"]); ?>元">
                            <p>券后价</p>
                            <p><b>￥<?php echo ($row["coupon_price"]); ?></b></p>
                        </div>
                        <div class="goods-sale">
                            <div class="goods-quan fl">
                                <p>券<b>￥<?php echo ($row["quan"]); ?></b></p>
                            </div>
                            <div class="goods-num fr">
                                <p>销量<b><?php echo ($row["volume"]); ?></b></p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                <div class="clear"></div>
            </ul><?php endif; ?>

    </div>
    <div class="page">
        <?php echo ($page); ?>
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
        $(function () {
            $('.search').click(function () {
                var type = $(this).data('type');
                var cate_id = sort = 0;
                if(type == 'cate'){
                    cate_id = $(this).data('id');
                }else{
                    cate_id = $('.goods-type-main span.cur').data('id');
                }
                if(type == 'sort'){
                    sort = $(this).data('id');
                }else{
                    sort = $('.goods-pages span.cur').data('id');
                }
                var start_price = $('#start_price').val() ? $('#start_price').val() : 0;
                var end_price = $('#end_price').val() ? $('#end_price').val() : 0;
                var commission = $('#commission').val() ? $('#commission').val() : 0;
                var sale_num = $('#sale_num').val() ? $('#sale_num').val() : 0;
                var url = "<?php echo U('Item/index');?>";
                url = url + '?cate_id=' + cate_id + '&sort=' + sort + '&search=' + start_price + '-' + end_price + '-' + commission + '-' + sale_num;
                window.location.href = url;
            })
        });
    </script>

</body>
</html>