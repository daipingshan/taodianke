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
                <?php if($_SESSION['AdminInfo']['id'] != 30): ?><li>
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
                                <a target="_blank" href="<?php echo U('Order/Fxg');?>">放心购文章</a>
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
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:;"><i class="fa fa-users fa-fw"></i> 今日头条<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="<?php echo U('TopLine/index');?>">账号管理</a>
                            </li>
                            <li>
                                <a href="<?php echo U('TopLine/itemsList');?>">选品管理</a>
                            </li>
                            <li>
                                <a href="<?php echo U('TopLine/itemsMoreList');?>">更多选品管理</a>
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
                                </li><?php endif; ?>
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
</nav>
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
            <h1 class="page-header">修改密码</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="panel-body">
        <form role="form" id="form-login" method="post" action="">
            <fieldset>
                <div class="form-group">
                    <label>原始密码：</label>
                    <input class="form-control" id='password' placeholder="原始密码" name="password" type="text" autofocus>
                </div>
                <div class="form-group">
                    <label>新密码：</label>
                    <input class="form-control" id='npassword' placeholder="新密码" name="npassword" type="password" value="">
                </div>
                <div class="form-group">
                    <label>确认新密码：</label>
                    <input class="form-control" id='qpassword' placeholder="确认新密码" name="qpassword" type="password" value="">
                </div>
                <!-- 使用时删除 “a”标签 input 取消注释 -->
                <a href="javascript:void(0);" id="btn-login" class="btn btn-lg btn-success btn-block">修改</a>
            </fieldset>
        </form>
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
    $(function(){
        // 点击登录按钮
        $('#btn-login').click(function(){

            var $this = $(this);
            var $form = $this.parents('form#form-login');
            var password = $form.find('input#password').val();
            var npassword = $form.find('input#npassword').val();
            var qpassword = $form.find('input#qpassword').val();
            if(!$.trim(password)){
                show_message_tip({error:'请输入密码'});
                return false;
            }
            if(npassword!=qpassword){
                show_message_tip({error:'两次输入新密码不对'});
                return false;
            }
            if($this.hasClass('disabled')){
                return false;
            }
            $this.addClass('disabled');
            $this.html('正在修改密码请稍后...');
            window.setTimeout(function() {
                $form.submit();
            }, 1500);
            return false;
        });
    })
</script>