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
            <h1 class="page-header">微头条选品库</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name='keyword' value="<?php echo I('get.keyword');?>"
                           placeholder="宝贝查询">
                </div>
                <div class="col-lg-4">
                    <select name="sort" class="form-control">
                        <option value="0">请选择排序方式</option>
                        <option value="1"
                        <?php if(I('get.sort') == 1): ?>selected<?php endif; ?>
                        >按照价格从高到低</option>
                        <option value="2"
                        <?php if(I('get.sort') == 2): ?>selected<?php endif; ?>
                        >按照价格从低到高</option>
                        <option value="3"
                        <?php if(I('get.sort') == 3): ?>selected<?php endif; ?>
                        >按照佣金比率从高到低</option>
                        <option value="4"
                        <?php if(I('get.sort') == 4): ?>selected<?php endif; ?>
                        >按照佣金比率从低到高</option>
                    </select>
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
                            <img class="lazy" data-original="<?php echo ($vo["pic"]); ?>">
                            <div class="intro">
                                <?php echo ($vo["desc"]); ?>
                            </div>
                        </div>
                        <div class="content">
                            <a href="<?php echo ($vo["coupon_url"]); ?>" target="_blank"><h5><?php echo ($vo["title"]); ?></h5></a>
                            <a href="<?php echo ($vo["coupon_url"]); ?>" target="_blank"><p><?php echo ($vo["short_title"]); ?></p></a>
                            <div class="col-lg-12 no-padding">
                                <div class="col-lg-4 no-padding" style="font-size: 18px;color: #FF420F;">
                                    <b>￥<?php echo ($vo["coupon_price"]); ?></b>
                                </div>
                                <div class="col-lg-4 no-padding" style="font-size: 14px;color: #999;">
                                    <i>￥<?php echo ($vo["price"]); ?></i>
                                </div>
                                <div class="col-lg-4 no-padding" style="font-size: 14px;color: #FF420F;">
                                    <b><?php echo ($vo["commission_rate"]); ?>%</b>
                                </div>
                            </div>
                            <div class="col-lg-12" style="margin: 20px 0">
                                <?php if(($vo['is_send']) == "Y"): ?><button class="btn btn-success" style="width: 100%">已发送</button>
                                    <?php else: ?>
                                    <button class="btn btn-danger sendItem" style="width: 100%"
                                            data-id="<?php echo ($vo["id"]); ?>">一键发送
                                    </button><?php endif; ?>
                            </div>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>
<div id="back-to-top" class="iton-top-index" style="position: fixed;bottom: 20px;right: 20px">
    <a href="#"></a>
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
        $('.sendItem').click(function () {
            if (is_add === false) {
                layer.msg('正在请求中...，操作太频繁，请稍后再试！');
                return false;
            }
            var _this = $(this);
            var id = $(this).data('id');
            var url = "<?php echo U('sendGoods');?>";
            is_add = false;
            $.post(url, {id: id}, function (res) {
                if (res.status == 1) {
                    _this.removeClass('sendItem btn-danger').addClass('btn-success').text('已发送');
                } else {
                    _this.text(res.info);
                    setTimeout(function () {
                        _this.text('一键发送');
                    }, 3000)
                }
                is_add = true;
            });
        });
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