<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>淘店客</title>
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/common.css?<?php echo C('V');?>" />
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/item.css?<?php echo C('V');?>" />
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/detail.css?<?php echo C('V');?>"/>

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
    
    <div class="goods-detail">
        <div class="posi">
            <?php if($cate_name): ?><a href="<?php echo U('Item/index',array('cate_id'=>$info['cate_id']));?>"><?php echo ($cate_name); ?></a> - &gt; 商品详情
                <?php else: ?>
                <a href="<?php echo U('Item/index');?>">全部</a> - &gt; 商品详情<?php endif; ?>
        </div>
        <a href="<?php echo ($info["url"]); ?>" target="_blank">
            <img src="<?php echo ($info["pic_url"]); ?>" width="400px" height="400px">
        </a>
        <div class="goods-info">
            <div class="tit"><?php echo ($info["title"]); ?></div>
            <div class="intro">
                <?php echo ($info["intro"]); ?>
            </div>
            <div class="data">
                <div class="price">
                    <span class="price_at">￥<?php echo ($info["coupon_price"]); ?></span>
                    <span class="price_bf">￥<?php echo ($info["price"]); ?></span>
                </div>
                <div class="num">
                    <span><div><?php echo ($info['volume']); ?></div>销量</span>
                </div>
            </div>
            <div class="quan">
                <a href="<?php echo ($info["url"]); ?>" target="_blank">
                    <div class="quan_value">券 <span>￥<?php echo ($info["quan"]); ?></span></div>
                    <div class="link">立即领券</div>
                    <div class="clear"></div>
                </a>
            </div>
        </div>
    </div>
    <div class="goods-share-item content">
        <div class="goods-share">
            <div class="promote-top">
                <div class="promote-tit">
                    <span><i></i>营销模板</span>
                    <?php if($_SESSION['uid'] AND ($row['type'] != 'chuchujie')): ?><button class="mo-ban-btn share-click active">
                            强烈建议
                        </button>
                        <button class="mo-ban-btn share-click">
                            建议
                        </button>
                        <button class="mo-ban-btn share-click">
                            备选
                        </button>
                        <button class="update-mo-ban" id="update-mo-ban">
                            编辑模板
                        </button><?php endif; ?>
                </div>
            </div>
            <div class="share">
                <div class="tui-content" id="copy">
                    <img src="<?php echo (str_ireplace('https','http',$info["pic_url"])); ?>" width="114"/>
                    <br>
                    <?php if($_SESSION['uid'] AND ($info['type'] != 'chuchujie')): ?><div class="share-mo-ban active">
                            <?php echo ($info['template'][0]); ?>
                        </div>
                        <div class="share-mo-ban">
                            <?php echo ($info['template'][1]); ?>
                        </div>
                        <div class="share-mo-ban">
                            <?php echo ($info['template'][2]); ?>
                        </div>
                        <?php else: ?>
                        <?php echo ($info["title"]); ?><br>
                        【在售价】<?php echo ($info["price"]); ?><br>
                        【券后价】<?php echo ($info["coupon_price"]); ?><br>
                        <?php if(($type) == "chuchujie"): ?>领券下单链接<span>【<?php echo ($info['kou_ling']); ?>】</span>
                            <?php else: ?>
                            复制这条信息【<?php echo ($info['kou_ling']); ?>】，打开☞手机淘宝☜即可查看并下单！<?php endif; endif; ?>
                </div>
                <!-- qq复制内容区域 -->
                <div class="copy">一键复制</div>
                <?php if($_SESSION['uid'] AND ($info['type'] != 'chuchujie')): ?><div class="zone-box">
                        <?php if(is_array($zone)): $i = 0; $__LIST__ = $zone;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><button data-pid="<?php echo ($row["pid"]); ?>"
                            <?php if(($row["is_default"]) == "1"): ?>class="zone active"
                                <?php else: ?>
                                class="zone"<?php endif; ?>
                            ><?php echo ($row["zone_name"]); ?></button><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div><?php endif; ?>
            </div>
            <div class="share-mask"></div>
        </div>
        <div class="item-list goods-item">
            <div class="promote-top">
                <div class="promote-tit">
                    <span><i></i>精品推荐</span>
                </div>
                <div class="promote-more">
                    <a href="<?php echo U('Item/index',array('cate_id'=>$info['cate_id']));?>">查看更多&gt;</a>
                </div>
            </div>
            <ul style="margin-top: 50px">
                <?php if(is_array($more_data)): $i = 0; $__LIST__ = $more_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 3 );++$i;?><li
                    <?php if(($mod) == "2"): ?>class="item-content-box no-mr"
                        <?php else: ?>
                        class="item-content-box"<?php endif; ?>
                    >
                    <div class="goods-img">
                        <a href="<?php echo U('Item/detail',array('num_iid'=>$row['num_iid'],'type'=>$row['goods_type']));?>">
                            <img src="<?php echo ($row["pic_url"]); ?>" alt="<?php echo ($row["title"]); ?>">
                        </a>
                    </div>
                    <div class="goods-info">
                        <a class="goods-tit">
                            <?php echo ($row["title"]); ?>
                        </a>
                        <div class="goods-slider">
                            <span class="slider"><em data-width="0%" style="width: 0%;"></em></span>
                        </div>
                        <div class="goods-price" title="<?php echo ($row["title"]); ?>">
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
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div id="mask">
        <div class="content">
            <div class="close">
                <img src="/Public/Home/img/close.png" alt="" width="50" style="cursor: pointer;border-radius:50% " id="close">
            </div>
            <div class="mo-ban">
                <div class="mo-ban-left">
                    <div class="header">
                        <h3>模板使用说明</h3>
                        <p>模板：原价<span class="default-color">#原价#</span>元，领券<span
                                class="default-color">#券金额#</span>元，只要<span class="default-color">#券后价#</span>元</p>
                        <p>效果：原价<span class="default-color">249</span>元，领券<span class="default-color">50</span>元，只要<span
                                class="default-color">199</span>元</p>
                    </div>
                    <div class="tag">
                        <h3 class="default-color">标签</h3>
                        <div class="tag-left">
                            <p>#标题#</p>
                            <p>#券后价#</p>
                            <p>#原价#</p>
                            <p>#券金额#</p>
                            <p>#领券链接#</p>
                            <p>#销量#</p>
                            <p>#文案#</p>
                            <p>#淘口令#</p>
                        </div>
                        <div class="tag-center">
                            <p>商品短标题</p>
                            <p>商品用券后价格</p>
                            <p>商品原价</p>
                            <p>优惠券金额</p>
                            <p>商品领券链接地址</p>
                            <p>当前累计月销量</p>
                            <p>商品推广文案</p>
                            <p>商品淘口令</p>
                        </div>
                        <div class="tag-right">
                            <p>&nbsp;</p>
                            <p>如：199</p>
                            <p>如：249</p>
                            <p>如：50</p>
                            <p>&nbsp;</p>
                            <p>如：1000</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="mo-ban-right">
                    <div class="header">
                        <button class="mo-ban-btn active" style="width: 25%;text-align: center">强烈建议</button>
                        <button class="mo-ban-btn" style="width: 25%;text-align: center">建议</button>
                        <button class="mo-ban-btn" style="width: 25%;text-align: center">备选</button>
                    </div>
                    <div class="save-mo-ban">
                        <div class="textarea show">
                            <textarea class="text"><?php echo ($template[0]); ?></textarea>
                        </div>
                        <div class="textarea">
                            <textarea class="text"><?php echo ($template[1]); ?></textarea>
                        </div>
                        <div class="textarea">
                            <textarea class="text"><?php echo ($template[2]); ?></textarea>
                        </div>
                        <button class="save-btn">保存</button>
                    </div>
                    <p class="default-color error-tip" style="margin-top: 30px;font-size: 14px;"></p>
                </div>
                <div class="clear"></div>
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

    <script src="https://cdn.bootcss.com/clipboard.js/1.7.1/clipboard.min.js" type="text/javascript"
            charset="utf-8"></script>
    <script type="text/javascript">
        $(function () {
            var btn_index = 0;
            var change_index = 0;
            var ClipboardSupport = 0;
            if (typeof Clipboard != "undefined") {
                ClipboardSupport = 1;
            } else {
                ClipboardSupport = 0;
            }
            $('.copy').click(function (e) {
                copyFunction('.copy', "复制成功");
            });
            var copyFunction = function (copyBtn, copyMsg) {
                if (ClipboardSupport == 0) {
                    $('.copy').css({
                        'background': '#999',
                        'font-size': '10px',
                        'box-shadow': '0.5px 0.5px 7px 0.5px #999'
                    }).text('浏览器版本过低，请升级或更换浏览器后重新复制');
                    setTimeout(function () {
                        $('.copy').css({
                            'background': '#FF420F',
                            'font-size': '15px',
                            'box-shadow': '0.5px 0.5px 7px 0.5px #FF420F'
                        }).text('一键复制');
                    }, 5000)
                } else {
                    var clipboard = new Clipboard(copyBtn, {
                        target: function () {
                            return document.getElementById('copy');
                        }
                    });
                    clipboard.on('success', function (e) {
                        $('.copy').css({
                            'background': '#999',
                            'box-shadow': '0.5px 0.5px 7px 0.5px #999'
                        }).text(copyMsg);
                        setTimeout(function () {
                            $('.copy').css({
                                'background': '#FF420F',
                                'box-shadow': '0.5px 0.5px 7px 0.5px #FF420F'
                            }).text('一键复制');
                        }, 5000)
                        e.clearSelection();
                    });
                    clipboard.on('error', function (e) {
                        $('.copy').css({
                            'background': '#999',
                            'font-size': '10px',
                            'box-shadow': '0.5px 0.5px 7px 0.5px #999'
                        }).text('复制失败，请升级或更换浏览器后重新复制');
                        setTimeout(function () {
                            $('.copy').css({
                                'background': '#FF420F',
                                'font-size': '15px',
                                'box-shadow': '0.5px 0.5px 7px 0.5px #FF420F'
                            }).text('一键复制');
                        }, 5000)
                        e.clearSelection();
                    });
                }
            }
            $('#close').click(function () {
                $('#mask').hide();
            });
            $('#update-mo-ban').click(function () {
                $('#mask').show();
            });
            $('.goods-share .share-click').click(function () {
                change_index = $(this).index() - 1;
                $(this).addClass('active').siblings().removeClass('active');
                $('.share .share-mo-ban').eq(change_index).addClass('active').siblings().removeClass('active');
            });

            $('#mask .mo-ban-btn').click(function () {
                btn_index = $(this).index();
                $(this).addClass('active').siblings().removeClass('active');
                $('#mask .textarea').eq(btn_index).addClass('show').siblings().removeClass('show');
            });

            $('.zone').click(function () {
                var _this = $(this);
                if ($(this).hasClass('active')) {
                    return false;
                }
                var item_id = "<?php echo ($info["num_iid"]); ?>";
                var type = "<?php echo ($info["type"]); ?>";
                var template_key = change_index;
                var pid = $(this).data('pid');
                $('.share-mask').text('正在努力请求中……').show();
                $.post("<?php echo U('getTemplate');?>", {
                    item_id: item_id,
                    type: type,
                    template_key: template_key,
                    pid: pid
                }, function (res) {
                    if (res.status == 1) {
                        $('.share .share-mo-ban.active').html(res.info);
                        _this.addClass('active').siblings().removeClass('active');
                        $('.share-mask').text('请求成功');
                    } else {
                        $('.share-mask').text(res.info);
                    }
                    setTimeout(function () {
                        $('.share-mask').hide();
                    }, 1000)
                });
            });

            $('textarea').focus(function () {
                $('.error-tip').text('');
            });

            /**
             * 修改模板信息
             */
            $('.save-btn').click(function () {
                var content = $('#mask .textarea').eq(btn_index).find('textarea').val();
                if (!content) {
                    $('.error-tip').text('请输入推广模板内容');
                    return false;
                }
                $.post("<?php echo U('setTemplate');?>", {key: btn_index, content: content}, function (res) {
                    if (res.status == 1) {
                        window.location.reload();
                    } else {
                        $('.error-tip').text(res.info);
                    }
                });

            });
        });
    </script>

</body>
</html>