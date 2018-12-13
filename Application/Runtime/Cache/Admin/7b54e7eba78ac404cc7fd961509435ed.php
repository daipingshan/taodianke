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
                        <?php if($row['son_menu']): ?><a href="javascript:;"><i class="fa fa-users fa-fw"></i> <?php echo ($row["menu_name"]); ?><span
                                    class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <?php if(is_array($row["son_menu"])): $i = 0; $__LIST__ = $row["son_menu"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$son): $mod = ($i % 2 );++$i;?><li>
                                        <a href="<?php echo U($son['url']);?>"><?php echo ($son["menu_name"]); ?></a>
                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                            <?php else: ?>
                            <a href="<?php echo U($row['url']);?>"><i class="fa fa-users fa-fw"></i> <?php echo ($row["menu_name"]); ?></a><?php endif; ?>

                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>

    <!-- /.navbar-static-side -->
</nav>  <!-- /.navbar-static-side -->
</nav>
<link rel="stylesheet" href="/Public/Char/css/style.css?v=<?php echo time();?>">
<link rel="stylesheet" href="/Public/Admin/css/top_items.css?v=<?php echo time();?>">
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<script src="/Public/Char/js/morris.js"></script>
<Script src="/Public/Char/js/moment.js"></Script>
<script src="/Public/Char/js/daterangepicker.js"></script>
<style>
    body {
        padding-bottom: 0;
    }

    .col-lg-3 {
        width: 23%
    }

    #page div > span {
        display: inline-block;
        line-height: 70px;
    }
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">特卖商品库</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form>
                <div class="col-lg-2">
                    <input type="text" class="form-control" name='title' value="<?php echo I('get.title');?>"
                           placeholder="商品关键字">
                </div>
                <div class="col-lg-2">
                    <input type="text" class="form-control" name='shop_name' value="<?php echo I('get.shop_name');?>"
                           placeholder="商铺名称">
                </div>
                <div class="col-lg-2">
                    <input type="text" class="form-control" name='shop_goods_id' value="<?php echo I('get.shop_goods_id');?>"
                           placeholder="商品id">
                </div>
                <div class="col-lg-3">
                    <input type="text" readonly name="time" id="reservation" class="form-control" value="<?php echo ($time); ?>"
                           placeholder="商品上线时间范围"/>
                </div>
                <div class="col-lg-3">
                    <select name="sort" class="form-control">
                        <option value="0">默认排序</option>
                        <option value="1"
                        <?php if(I('get.sort') == 1): ?>selected<?php endif; ?>
                        >热度从高到低</option>
                        <option value="2"
                        <?php if(I('get.sort') == 2): ?>selected<?php endif; ?>
                        >热度从低到高</option>
                        <option value="3"
                        <?php if(I('get.sort') == 3): ?>selected<?php endif; ?>
                        >销量从高到低</option>
                        <option value="4"
                        <?php if(I('get.sort') == 4): ?>selected<?php endif; ?>
                        >销量从低到高</option>
                        <option value="5"
                        <?php if(I('get.sort') == 5): ?>selected<?php endif; ?>
                        >佣金从高到低</option>
                        <option value="6"
                        <?php if(I('get.sort') == 6): ?>selected<?php endif; ?>
                        >佣金从低到高</option>
                        <option value="7"
                        <?php if(I('get.sort') == 7): ?>selected<?php endif; ?>
                        >价格从高到低</option>
                        <option value="8"
                        <?php if(I('get.sort') == 8): ?>selected<?php endif; ?>
                        >价格从低到高</option>
                    </select>
                </div>
                <br>
                <br>
                <div class="col-lg-2" style="position: relative">
                    <input type="number" name="cos_ratio_up" class="form-control" value="<?php echo I('get.cos_ratio_up');?>"
                           placeholder="佣金起始值"><span
                        style="position: absolute;top: 0;right: 0;line-height: 34px">%</span>
                </div>
                <div class="col-lg-2" style="position: relative">
                    <input type="number" name="cos_ratio_down" class="form-control"
                           value="<?php echo I('get.cos_ratio_down');?>" placeholder="佣金结束值"><span
                        style="position: absolute;top: 0;right: 0;line-height: 34px">%</span>
                </div>
                <div class="col-lg-2" style="position: relative">
                    <input type="number" name="sku_price_up" class="form-control" value="<?php echo I('get.sku_price_up');?>"
                           placeholder="价格起始值"><span
                        style="position: absolute;top: 0;right: 0;line-height: 34px">元</span>
                </div>
                <div class="col-lg-2" style="position: relative">
                    <input type="number" name="sku_price_down" class="form-control"
                           value="<?php echo I('get.sku_price_down');?>" placeholder="价格结束值"><span
                        style="position: absolute;top: 0;right: 0;line-height: 34px">元</span>
                </div>
                <div class="col-lg-4" style="text-align: center">
                    <button type="submit" class="btn btn-danger">宝贝查询</button>
                    <?php if($_SESSION['AdminInfo']['id'] == 0): ?><button type="button" class="btn btn-success" data-toggle="modal" data-target="#add-modal">采集商品
                        </button><?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    <div id="content">
        <div class="row">
            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><div class="col-lg-12" style="margin-top: 20px">
                    <?php if(is_array($row)): $i = 0; $__LIST__ = $row;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-lg-3 box">
                            <div class="img">
                                <img src="<?php echo ($vo["figure"]); ?>" style="margin: 0 10%;width: 80%">
                                <a href="<?php echo ($vo["sku_url"]); ?>" target="_blank">
                                    <div class="intro">
                                        <?php echo ($vo["sku_title"]); ?>
                                    </div>
                                </a>
                                <?php if(($vo["is_new"]) == "1"): ?><button class="btn btn-success"
                                            style="position: absolute;top: 0;right: 0;border-radius: 0">
                                        新品
                                    </button><?php endif; ?>
                            </div>
                            <div class="content" style="margin: 0 5%">
                                <h6 style="margin: 10px 0"><?php echo ($vo['shop_name']); ?></h6>
                                <div class="col-lg-12 no-padding type-time">
                                    <div class="col-lg-5 no-padding" style="font-size: 16px;color: #FF420F;">
                                        <b>￥<?php echo ($vo["sku_price"]); ?></b>
                                    </div>
                                    <div class="col-lg-7 no-padding time" style="font-size: 12px; !important;">
                                        热度：<b style="color: #FF420F"><?php echo ($vo['hotrank']); ?></b>
                                    </div>
                                </div>
                                <div class="col-lg-12 no-padding type-time">
                                    <div class="col-lg-5 no-padding" style="font-size: 12px;">
                                        佣金比率：<b style="color: #FF420F"><?php echo ($vo['cos_ratio']*100); ?>%</b>
                                    </div>
                                    <div class="col-lg-7 no-padding time" style="font-size: 12px; !important;">
                                        预估：<b style="color: #FF420F">￥<?php echo ($vo['cos_fee']); ?></b>
                                    </div>
                                </div>
                                <div class="col-lg-12 no-padding type-time" style="font-size: 12px">
                                    商品编号：<a href="<?php echo U('Sale/itemsList');?>?shop_goods_id=<?php echo ($vo['platform_sku_id']); ?>"
                                            target="_blank"
                                            id="copy-<?php echo ($vo["platform_sku_id"]); ?>"><?php echo ($vo["platform_sku_id"]); ?></a>
                                </div>

                                <div class="col-lg-12 add-btn" style="margin: 20px 0;position: relative">
                                    <button class="btn btn-danger copy copy-<?php echo ($vo["platform_sku_id"]); ?>" style="width: 100%"
                                            data-class=".copy-<?php echo ($vo["platform_sku_id"]); ?>"
                                            data-id="copy-<?php echo ($vo["platform_sku_id"]); ?>">复制商品编号
                                    </button>
                                </div>
                            </div>
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <div id="page">
            <hr/>
            <div style="width: 80%;margin: 0 auto">
                <ul style="width: 100%;">
                    <?php echo ($page); ?>
                    <div style="clear: both"></div>
                </ul>
            </div>
        </div>
    </div>
    <!-- 模态框（Modal） -->
    <div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">商品采集器</h4>
                </div>
                <form class="bs-example bs-example-form" role="form">
                    <div class="modal-body">
                        <div style="width: 100%;height: 200px;border: 1px solid #ccc;padding: 10px 20px;overflow: auto"
                             id="info">
                            显示商品采集过程中的交互信息
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" id="add">立即采集</button>
                        <button type="button" class="btn btn-danger" id="stop">停止</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
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
<script src="https://cdn.bootcss.com/clipboard.js/1.7.1/clipboard.min.js" type="text/javascript"
        charset="utf-8"></script>
<script>
    $('#reservation').daterangepicker(null, function (start, end, label) {
    });
    // 定义一个新的复制对象
    var ClipboardSupport = 0;
    if (typeof Clipboard != "undefined") {
        ClipboardSupport = 1;
    } else {
        ClipboardSupport = 0;
    }
    $('.copy').click(function (e) {
        copyFunction($(this).data('class'), $(this));
    });
    var status = 0;
    var num = 0;
    var url = "<?php echo U('ajaxCollectionItem');?>";
    var page = 1;
    $('#add').click(function () {
        status = 1;
        $(this).attr('disabled', true).text('正在采集中……');
        getItems();
    });
    function getItems() {
        if (status == 1) {
            $.get(url, {page: page}, function (res) {
                if (page == 1 && num == 0) {
                    $('#info').html("<p>" + res.info.info + "<p>");
                } else {
                    $('#info').append("<p>" + res.info.info + "<p>");
                    $('#info').scrollTop($('#info')[0].scrollHeight);
                }

                if (res.info.code == 1) {
                    page++;
                    getItems();
                } else {
                    if (res.info.code == 0 && num < 3) {
                        num++;
                        getItems();
                    } else {
                        status = 0;
                        $('#add').removeAttr('disabled').text('立即采集');
                    }
                }
            });
        } else {
            $('#info').append("<br>已停止");
            $('#info').scrollTop($('#info')[0].scrollHeight);
        }
    }
    $('#stop').click(function () {
        if (status == 1) {
            status = 0;
            $('#add').removeAttr('disabled').text('立即采集');
        }
    });
    var copyFunction = function (copyBtn, obj) {
        if (ClipboardSupport == 0) {
            obj.css({
                'background': '#999',
                'font-size': '10px',
            }).text('浏览器版本过低，请升级或更换浏览器后重新复制');
            setTimeout(function () {
                obj.css({
                    'background': '#d9534f',
                    'font-size': '15px',
                }).text('复制商品编号');
            }, 5000)
        } else {
            var clipboard = new Clipboard(copyBtn, {
                target: function () {
                    return document.getElementById(obj.data('id'));
                }
            });
            clipboard.on('success', function (e) {
                obj.css({
                    'background': '#449d44',
                }).text('复制成功');
                setTimeout(function () {
                    obj.css({
                        'background': '#d9534f',
                        'font-size': '15px',
                    }).text('复制商品编号');
                }, 5000)
                e.clearSelection();
            });
            clipboard.on('error', function (e) {
                obj.css({
                    'background': '#999',
                    'font-size': '10px',
                }).text('复制失败，请升级或更换浏览器后重新复制');
                setTimeout(function () {
                    obj.css({
                        'background': '#d9534f',
                        'font-size': '15px',
                    }).text('复制商品编号');
                }, 5000)
                e.clearSelection();
            });
        }
    }


</script>