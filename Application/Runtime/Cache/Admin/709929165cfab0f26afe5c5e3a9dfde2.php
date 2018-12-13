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
                <?php if($_SESSION['AdminInfo']['id'] != 30 && $_SESSION['AdminInfo']['id'] != 33 && $_SESSION['AdminInfo']['id'] != 34 && $_SESSION['AdminInfo']['id'] != 35 && $_SESSION['AdminInfo']['id'] != 36 && $_SESSION['AdminInfo']['id'] != 37 && $_SESSION['AdminInfo']['id'] != 38 && $_SESSION['AdminInfo']['id'] != 39): ?><li>
                        <a href="javascript:;"><i class="fa fa-users fa-fw"></i> 文章管理<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a target="_blank" href="<?php echo U('TopLine/topNewsList');?>">头条文章</a>
                            </li>
                            <li>
                                <a href="<?php echo U('News/index');?>">文章列表</a>
                            </li>
                            <li>
                                <a href="<?php echo U('News/add');?>">登记文章</a>
                            </li>
                            <li>
                                <a target="_blank" href="<?php echo U('Order/GetNews');?>">商品查看</a>
                            </li>

                        </ul>
                    </li>
                    <li>
                        <a href="javascript:;"><i class="fa fa-shopping-cart fa-fw"></i>订单管理<span
                                class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="<?php echo U('Order/index');?>">所有订单</a>
                            </li>
                            <li>
                                <a target="_blank" href="<?php echo U('Order/Fxg');?>">放心购订单</a>
                            </li>
                            <li>
                                <a target="" href="<?php echo U('Order/Renlin');?>">认领订单</a>
                            </li>
                            <li>
                                <a target="" href="<?php echo U('Order/product');?>">放心购商品库</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                    <li>
                        <a href="javascript:;"><i class="fa fa-users fa-fw"></i> 商品管理<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="<?php echo U('News/searsch');?>">商品列表</a>
                            </li>
                        </ul>
                    </li><?php endif; ?>
                <?php if($_SESSION['AdminInfo']['id'] == 0): ?><li>
                        <a href="javascript:;"><i class="fa fa-users fa-fw"></i> 用户管理<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="<?php echo U('User/userList');?>">用户列表</a>
                            </li>
                            <li>
                                <a href="<?php echo U('User/authList');?>">权限管理</a>
                            </li>
                            <li>
                                <a href="<?php echo U('User/authGroup');?>">权限组管理</a>
                            </li>
                        </ul>
                    </li><?php endif; ?>
                <?php if($_SESSION['AdminInfo']['top_line_account_ids']): ?><li>
                        <a href="javascript:;"><i class="fa fa-users fa-fw"></i> 今日头条<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <?php if($_SESSION['AdminInfo']['id'] == 0): ?><li>
                                    <a href="<?php echo U('TopLine/index');?>">账号管理</a>
                                </li><?php endif; ?>
                            <li>
                                <a href="<?php echo U('TopLine/newItemsList');?>">选品管理</a>
                            </li>
                            <li>
                                <a href="<?php echo U('TopLine/itemsMoreList');?>">更多选品管理</a>
                            </li>
                            <li>
                                <a href="<?php echo U('TopLine/itemsUrlList');?>">链接选品</a>
                            </li>
                            <li>
                                <a href="<?php echo U('TopLine/newsList');?>">文章管理</a>
                            </li>
                        </ul>
                    </li><?php endif; ?>
                <?php if($_SESSION['AdminInfo']['sale_account_ids']): ?><li>
                        <a href="javascript:;"><i class="fa fa-users fa-fw"></i> 特卖达人<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <?php if($_SESSION['AdminInfo']['id'] == 0): ?><li>
                                    <a href="<?php echo U('Sale/index');?>">账号管理</a>
                                </li>
                                <li>
                                    <a href="<?php echo U('Sale/highReadNewsList');?>">高阅读量文章</a>
                                </li>
                                <li>
                                    <a href="<?php echo U('Sale/highReadItemsList');?>">高阅读量商品</a>
                                </li><?php endif; ?>
                            <li>
                                <a href="<?php echo U('Sale/newItems');?>">特卖新品库</a>
                            </li>
                            <li>
                                <a href="<?php echo U('Sale/imgList');?>">非商品图集</a>
                            </li>
                            <li>
                                <a href="<?php echo U('Sale/itemsList');?>">选品管理</a>
                            </li>
                            <li>
                                <a href="<?php echo U('Sale/itemsUrlList');?>">链接选品</a>
                            </li>
                            <li>
                                <a href="<?php echo U('Sale/newsList');?>">文章管理</a>
                            </li>
                        </ul>
                    </li><?php endif; ?>
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>

    <!-- /.navbar-static-side -->
</nav>  <!-- /.navbar-static-side -->
</nav>
        <style>
            #pp p{ margin: 0px; line-height: 15px;font-size: 15px}
            #ppp p{ margin: 0px; line-height: 18px;font-size: 15px}
            #pppp p{ margin: 0px; line-height: 18px;font-size: 15px}
        </style>
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
            <h1 class="page-header">订单认领</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">

    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td>创建时间</td>
                                    <td width="25%">文章来源</td>
                                    <td width="25%">商品信息</td>
                                    <td>商品单价</td>
                                    <td>订单状态</td>
                                    <td>收入比率</td>
                                    <td>付款金额</td>
                                    <td>预估收入</td>
                                    <td>所属账号</td>
                                    <td>订单操作</td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dd): $mod = ($i % 2 );++$i;?><tr>
                                    <td><?php echo ($dd["pay_time"]); ?></td>
                                    <td><a href="<?php echo ($dd["order_source"]); ?>" target="_blank"><?php echo ($dd["article_title"]); ?></a></td>
                                    <td><a href="<?php echo ($dd["commodity_info"]); ?>" target="_blank"><?php echo ($dd["commodity_name"]); ?></a></td>
                                    <td><?php echo ($dd["order_money"]); ?></td>
                                    <td><?php echo ($dd["order_status"]); ?></td>
                                    <td><?php echo ($dd["profit_percent"]); ?></td>

                                    <td><?php echo ($dd["order_money"]); ?></td>
                                    <td style="color: red;"><?php echo ($dd["income"]); ?></td>
                                    <td><?php echo ($dd["author_id"]); ?></td>
                                    <td><a href="<?php echo U('Order/renl',array('gid'=>$dd['id']));?>" confirm_tip='确定这个商品是你的' onclick='return ajax_operation(this);'>认领</a></td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-6 -->
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
<script>
    
</script>