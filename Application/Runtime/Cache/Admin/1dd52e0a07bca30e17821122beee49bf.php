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
<link rel="stylesheet" href="/Public/Char/css/bootstrap.min.css">
<link rel="stylesheet" href="/Public/Char/css/style.css">
<link rel="stylesheet" href="/Public/Admin/css/top_items.css?v=<?php echo time();?>">
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">高阅读量文章</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form>
                <div class="col-lg-3">
                    <input type="text" name="keyword" class="form-control" value="<?php echo I('get.keyword');?>"
                           placeholder="文章关键字"/>
                </div>
                <div class="col-lg-3">
                    <input type="text" readonly name="time" id="reservation" class="form-control" value="<?php echo ($time); ?>"/>
                </div>
                <div class="col-lg-2">
                    <input type="number" class="form-control" name='read_num' value="<?php echo I('get.read_num');?>"
                           placeholder="阅读量大于">
                </div>
                <div class="col-lg-2">
                    <input type="number" class="form-control" name='create_user_id' value="<?php echo I('get.create_user_id');?>"
                           placeholder="文章作者ID">
                </div>
                <div>
                    <button type="submit" class="btn btn-danger">文章查询</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row" style="margin-top: 20px; margin-left: 3px">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>文章标题</th>
                                <th width="5%">评论</th>
                                <th width="5%">阅读</th>
                                <th width="8%">类型</th>
                                <th width="5%">平台</th>
                                <th width="10%">发布作者ID</th>
                                <th width="15%">发布时间</th>
                                <th width="10%">商品链接</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                    <td><a href="https://www.toutiao.com/a<?php echo ($vo["article_id"]); ?>"
                                           target="_blank"><?php echo ($vo["title"]); ?></a>
                                    </td>
                                    <td><?php echo ($vo["comments_count"]); ?></td>
                                    <td><?php echo ($vo["go_detail_count"]); ?></td>
                                    <td>
                                        <?php if($vo["check_str"] == '淘宝'): ?><button class="btn btn-success btn-small"><?php echo ($vo["check_str"]); ?></button>
                                            <?php elseif($vo["check_str"] == '天猫'): ?>
                                            <button class="btn btn-danger btn-small"><?php echo ($vo["check_str"]); ?></button>
                                            <?php elseif($vo["check_str"] == '放心购'): ?>
                                            <button class="btn btn-warning btn-small"><?php echo ($vo["check_str"]); ?></button>
                                            <?php else: ?>
                                            <button class="btn btn-info btn-small">未知</button><?php endif; ?>
                                    </td>
                                    <td><?php echo ($vo["type"]); ?></td>
                                    <td><?php echo ($vo["create_user_id"]); ?><span style="color: red">【<?php echo ($vo['num']); ?>】</span></td>
                                    <td><?php echo (date("Y-m-d H:i:s",$vo["behot_time"])); ?></td>
                                    <td>
                                        <a class="btn btn-info"
                                           href="http://www.51taojinge.com/jinri/temai_content_article.php?id=<?php echo ($vo["news_id"]); ?>"
                                           target="_blank">查看头条商品</a>
                                        <br>
                                        <a style="margin-top: 5px" class="btn btn-warning"
                                           href="<?php echo U('highReadItemsList');?>?article_id=<?php echo ($vo["article_id"]); ?>"
                                           target="_blank">查看商品</a>
                                    </td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row" style="margin: 20px 0 50px 0">
                        <ul class="pagination" style="width: 100%">
                            <?php echo ($page); ?>
                        </ul>
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
<script src="/Public/Char/js/morris.js"></script>
<Script src="/Public/Char/js/moment.js"></Script>
<script src="/Public/Char/js/daterangepicker.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#reservation').daterangepicker(null, function (start, end, label) {
        });
    });
</script>