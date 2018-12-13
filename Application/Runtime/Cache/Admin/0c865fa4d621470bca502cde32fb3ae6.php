<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>主页--管理后台</title>
    <!-- Bootstrap Core CSS -->
    <link href="http://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="http://cdn.bootcss.com/metisMenu/1.1.3/metisMenu.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="/Public/plugins/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <!-- <link href="/Public/plugins/bower_components/datatables-responsive/css/dataTables.responsive.css" rel="stylesheet"> -->
    <!-- Timeline CSS -->
    <link href="/Public/Admin/css/timeline.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/Public/Admin/css/common.css" rel="stylesheet">
    <!-- Morris Charts CSS -->
    <link href="http://cdn.bootcss.com/morris.js/0.5.0/morris.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="http://cdn.bootcss.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="http://cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

     <!-- jQuery -->
    <script src="/Public/Admin/js/jquery.min.js"></script>

</head>
<body>
    <div id="wrapper">
<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.html">管理后台</a>
        <!--div style="float: left;">
            <marquee style="WIDTH: 900px;" scrollamount="5" direction="left" >
                <h5 style="color:red;"><?php echo ($user['name']); ?>加油！！！离双11还差<?php echo ($guonian); ?>天，
                    <?php if($user['name'] == '仰宗虎'): ?>想要月入过万，你你必须每天第一个来，最后一个走<?php endif; ?>
                    双11即将到来，错过这一次需要等一年。</h5>
            </marquee>
        </div-->

    </div>
    <!-- /.navbar-header -->

    <ul class="nav navbar-top-links navbar-right">
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="#"><i class="fa fa-user fa-fw"></i> 个人中心</a>
                </li>
                <li><a href="#"><i class="fa fa-gear fa-fw"></i> 设置</a>
                </li>
                <li><a href="<?php echo U('Index/updatepass');?>"><i class="fa fa-gear fa-fw"></i> 修改密码</a>
                </li>
                <li class="divider"></li>
                <li><a href="<?php echo U('Public/logout');?>"><i class="fa fa-sign-out fa-fw"></i> 退出</a>
                </li>
            </ul>
            <!-- /.dropdown-user -->
        </li>
        <!-- /.dropdown -->
    </ul>
    <!-- /.navbar-top-links -->

    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
                <?php if(is_array($menu_list)): $i = 0; $__LIST__ = $menu_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><li>
                        <a href="javascript:;"><i class="fa fa-users fa-fw"></i> <?php echo ($row["menu_name"]); ?><span
                                class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <?php if(is_array($row["son_menu"])): $i = 0; $__LIST__ = $row["son_menu"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$son): $mod = ($i % 2 );++$i;?><li>
                                    <a href="<?php echo U($son['url']);?>"><?php echo ($son["menu_name"]); ?></a>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>

    <!-- /.navbar-static-side -->
</nav>  <!-- /.navbar-static-side -->
</nav>
<link rel="stylesheet" href="/Public/Char/css/bootstrap.min.css">
<link rel="stylesheet" href="/Public/Char/css/style.css">
<style>@import url("//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc2/css/bootstrap-glyphicons.css");</style>
<link rel="stylesheet" href="/Public/Admin/css/top_items.css?v=<?php echo time();?>">
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<script src="/Public/Char/js/bootstrap.min.js"></script>
<script src="/Public/Char/js/jquery.pin.min.js"></script>
<script src="/Public/Char/js/raphael-min.js"></script>
<script src="/Public/Char/js/morris.js"></script>
<Script src="/Public/Char/js/moment.js"></Script>
<script src="/Public/Char/js/daterangepicker.js"></script>
<div id="page-wrapper">
    <div id='message-tip'>
    <?php if($error != ''): ?><div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo ($error); ?>
        </div><?php endif; ?>
    <?php if($success != ''): ?><div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo ($success); ?>
        </div><?php endif; ?>
</div>

<script id="message-tip-tmpl" type="text/x-jquery-tmpl">
    {{if error}}
    <div class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      ${error}
    </div>
    {{/if}}
    {{if success}}
    <div class="alert alert-success" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ${success}
    </div>
    {{/if}}
</script>

<script>
// js 显示错误信息
function show_message_tip($obj){
    $('div#message-tip').html('');
    $('div#message-tip').html($('#message-tip-tmpl').tmpl($obj));
    return false;
}
</script>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">选品库列表</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <ul class="cate">
                <?php $temp_param = $url_param; ?>
                <?php if(is_array($cate)): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; unset($url_param['page']); $url_param['uid'] = $vo['uid'] ?>
                    <a href="<?php echo U('itemsList');?>?<?php echo http_build_query($url_param);?>">
                        <li
                        <?php if($temp_param['uid'] == $vo['uid']): ?>class="cate active"
                            <?php else: ?>
                            class="cate"<?php endif; ?>
                        ><?php echo ($vo['name']); ?></li>
                    </a><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
            <form>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name='keyword' value="<?php echo ($url_param['keyword']); ?>"
                           placeholder="宝贝查询">
                </div>
                <div class="col-lg-2">
                    <select name="source" class="form-control">
                        <option value="">请选择天猫，淘宝，放心购</option>
                        <option value="1"
                        <?php if($url_param['source'] == 1): ?>selected<?php endif; ?>
                        >天猫</option>
                        <option value="2"
                        <?php if($url_param['source'] == 2): ?>selected<?php endif; ?>
                        >淘宝</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <input type="text" readonly name="time" id="reservation" class="form-control" value="<?php echo ($time); ?>"/>
                </div>
                <div>
                    <button type="submit" class="btn btn-danger">宝贝查询</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><div class="col-lg-12" style="margin-top: 20px">
                <?php if(is_array($row)): $i = 0; $__LIST__ = $row;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-lg-3 box">
                        <div class="img">
                            <img class="lazy" data-original="<?php echo ($vo["img"]); ?>">
                            <div class="intro">
                                <?php echo ($vo["describe_info"]); ?>
                            </div>
                        </div>
                        <div class="content">
                            <a href="<?php echo ($vo["temai_id"]); ?>" target="_blank"><h5><?php echo ($vo["temai_title"]); ?></h5></a>
                            <a href="<?php echo ($vo["tmall_url"]); ?>" target="_blank"><p><?php echo ($vo["title"]); ?></p></a>
                            <div class="col-lg-12 no-padding type-time">
                                <div class="col-lg-8 no-padding time"><?php echo (date("Y-m-d H:i:s",$vo['behot_time'])); ?></div>
                                <div class="col-lg-4 type">
                                    <?php if($vo['type'] == '淘宝'): ?><button class="btn btn-success btn-xs"><?php echo ($vo['type']); ?></button>
                                        <?php elseif($vo['type'] == '天猫'): ?>
                                        <button class="btn btn-danger btn-xs"><?php echo ($vo['type']); ?></button>
                                        <?php else: ?>
                                        <button class="btn btn-warning btn-xs"><?php echo ($vo['type']); ?></button><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-lg-12 no-padding">
                                <div class="col-lg-6 no-padding" style="font-size: 18px;color: #FF420F;">
                                    <b>￥<?php echo ($vo["price"]); ?></b>
                                </div>
                            </div>
                            <div class="col-lg-12" style="margin: 20px 0">
                                <?php if(($vo['is_add']) == "1"): ?><button class="btn btn-success" style="width: 100%">已添加至选品库</button>
                                    <?php else: ?>
                                    <button class="btn btn-danger addItemCache" style="width: 100%"
                                            data-id="<?php echo ($vo["id"]); ?>" data-val='<?php echo ($vo["post_data"]); ?>' data-placement="top"
                                            data-toggle="popover">一键添加
                                    </button><?php endif; ?>
                            </div>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <div class="row" style="margin: 20px 0 50px 0">
        <ul class="pagination" style="width: 100%">
            <?php echo ($page); ?>
        </ul>
    </div>
    <div class="bottom-position">
        <div class="col-lg-3">
            <div class="cart" id="cart" onclick="openCart()">
                <a href="javascript:;" class="cart-icon"></a>
                <a href="javascript:;" class="cart-num"><?php echo ($cart_count); ?></a>
            </div>
        </div>
        <div class="col-lg-3">
            <button class="btn btn-success" style="margin: 25px 30%;width: 40%" onclick="saveCart()">一键保存</button>
        </div>
        <div class="col-lg-3">
            <button class="btn btn-danger" style="margin: 25px 30%;width: 40%" onclick="openCart()">一键预览</button>
        </div>
        <div class="col-lg-3">
            <div id="back-to-top" class="iton-top-index" style="margin: 25px auto">
                <a href="#"></a>
            </div>
        </div>
    </div>
</div>

    </div>
    <!-- /#wrapper -->
    <!-- Bootstrap Core JavaScript -->
    <script src="http://cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="http://cdn.bootcss.com/metisMenu/1.1.3/metisMenu.min.js"></script>
    <!-- Morris Charts JavaScript -->
    <script src="http://cdn.bootcss.com/raphael/2.1.4/raphael-min.js"></script>
    <script src="http://cdn.bootcss.com/morris.js/0.5.0/morris.min.js"></script>
    <script src="/Public/Admin/js/jquery.tmpl.min.js"></script>
    <script src="/Public/plugins/bower_components/layer/layer.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="/Public/Admin/js/common.js?v=12"></script>
</body>
</html>

<script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="/Public/Admin/js/jquery.lazyload.js"></script>
<script src="/Public/Admin/js/jquery-ui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var is_add = true
        $("img.lazy").lazyload({effect: "fadeIn"});
        $('#reservation').daterangepicker(null, function (start, end, label) {
        });
        $('.addItemCache').click(function () {
            if (is_add === false) {
                layer.msg('正在请求中...，操作太频繁，请稍后再试！');
                return false;
            }
            var item_num = parseInt($('#cart .cart-num').text());
            if (item_num > 10) {
                layer.msg('最多只能添加10件商品！');
                return false;
            }
            var _this = $(this);
            var item_id = $(this).data('id');
            var post_data = $(this).data('val');
            var url = "<?php echo U('addItemCache');?>";
            is_add = false;
            $.post(url, {id: item_id, post_data: post_data}, function (res) {
                $('#modal .modal-body').html(res.info);
                $('#modal').modal('show');
                if (res.status == 1) {
                    var img = _this.parents('.box').find('.img img');
                    flyCart(img);
                    $('#cart .cart-num').text(item_num + 1);
                    _this.removeClass('addItemCache btn-danger').addClass('btn-success').text('已添加至选品库');
                    _this.unbind();
                } else {
                    _this.text(res.info);
                    setTimeout(function () {
                        _this.text('一键添加');
                    }, 3000)
                }
                is_add = true;
            });
        });

        function flyCart(imgtodrag) {
            var cart = $('#cart');
            if (imgtodrag) {
                var imgclone = imgtodrag.clone()
                        .offset({
                            top: imgtodrag.offset().top,
                            left: imgtodrag.offset().left
                        })
                        .css({
                            'opacity': '0.5',
                            'position': 'absolute',
                            'height': '150px',
                            'width': '150px',
                            'z-index': '100'
                        })
                        .appendTo($('body'))
                        .animate({
                            'top': cart.offset().top + 10,
                            'left': cart.offset().left + 10,
                            'width': 75,
                            'height': 75
                        }, 1000, 'easeInOutExpo');

                imgclone.animate({
                    'width': 0,
                    'height': 0
                }, function () {
                    $(this).detach()
                });
            }
        }
    });

    function openCart() {
        var num = parseInt($('#cart .cart-num').text());
        if (num == 0) {
            layer.msg('请先添加商品后再预览！');
            return false;
        }
        layer.open({
            title: '预览文章',
            type: 2,
            area: ['800px', '670px'],
            fix: true,
            content: "<?php echo U('cartList');?>",
        });
    }

    function saveCart() {
        var num = parseInt($('#cart .cart-num').text());
        if (num == 0) {
            layer.msg('请先添加商品后再保存！');
            return false;
        }
        layer.open({
            title: '保存文章',
            type: 2,
            area: ['1000px', '670px'],
            fix: true,
            content: "<?php echo U('saveCart');?>",
        });
    }
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