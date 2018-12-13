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
            <h1 class="page-header">头条文章列表</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <?php if(!empty($user_data)): ?><div class="row" style="margin-bottom: 20px;margin-left: -30px">
            <div class="col-lg-12">
                <form>
                    <div class="col-lg-3">
                        <select name="user_id" class="form-control">
                            <option value="">请选择用户</option>
                            <?php if(is_array($user_data)): foreach($user_data as $k=>$row): ?><option value="<?php echo ($row["id"]); ?>"
                                <?php if(I('get.user_id') == $row['id']): ?>selected<?php endif; ?>
                                ><?php echo ($row["name"]); ?></option><?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-danger">查询</button>
                    </div>
                </form>
            </div>
        </div><?php endif; ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th width="30%">文章标题</th>
                                <th width="8%">文章数量</th>
                                <th width="12%">发布账号</th>
                                <th width="12%">发布人</th>
                                <th width="15%">时间</th>
                                <th><i class="fa fa-gear fa-fw"></i></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                    <td>
                                        <?php if(($vo['is_send'] == 0) AND ($vo['is_save'] == 0)): ?><a href="javascript:;" class="update-news-info" data-id="<?php echo ($vo["id"]); ?>" data-info="<?php echo ($vo["title"]); ?>"><?php echo ($vo["title"]); ?></a>
                                            <?php else: ?>
                                            <a href="javascript:;" class="news-info" data-url="<?php echo U('newsInfo',array('id'=>$vo['id']));?>"><?php echo ($vo["title"]); ?></a><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo count(json_decode($vo['content'])); ?>
                                    </td>
                                    <td><?php echo ($account[$vo['account_id']]['username']); ?></td>
                                    <td><?php echo ($vo["username"]); ?></td>
                                    <td>
                                        <?php if($vo['send_time']): echo ($vo["send_time"]); ?><br>
                                            <?php else: ?>
                                            未设置定时发布<br><?php endif; ?>
                                        <?php echo (date("Y-m-d H:i:s",$vo["add_time"])); ?>
                                    </td>
                                    <td>
                                        <?php if($vo['is_send'] == 0): if(($vo['is_save']) == "1"): ?><a class="btn btn-warning" disabled="disabled">已存稿</a>
                                                <?php else: ?>
                                                <a class="btn btn-warning figure"
                                                   data-url="<?php echo U('figure',array('id'=>$vo['id']));?>"
                                                   href="javascript:;">存草稿</a><?php endif; endif; ?>
                                        <?php if($vo['is_save'] == 0): if(($vo['is_send']) == "1"): ?><a class="btn btn-success" disabled="disabled"
                                                   href="javascript:;">已发表</a>
                                                <?php else: ?>
                                                <a class="btn btn-success publish" href="javascript:;"
                                                   data-url="<?php echo U('publish',array('id'=>$vo['id']));?>">发表</a><?php endif; endif; ?>
                                        <a class="btn btn-info news-info" href="javascript:;"
                                           data-url="<?php echo U('newsInfo',array('id'=>$vo['id']));?>">预览</a>
                                        <?php if(($vo['is_send'] == 0) AND ($vo['is_save'] == 0)): ?><a class="btn btn-danger news-del" href="javascript:;"
                                               data-url="<?php echo U('newsDel',array('id'=>$vo['id']));?>">删除</a><?php endif; ?>
                                    </td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <ul class="pagination" style="width: 100%">
                        <?php echo ($pages); ?>
                    </ul>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-6 -->
    </div>
</div>
<!-- 按钮触发模态框 -->
<!-- 按钮触发模态框 -->
<div class="modal fade" id="update-news-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">编辑文章标题</h4>
            </div>
            <form class="bs-example bs-example-form" role="form">
                <div class="modal-body">
                    <div class="input-group">
                        <span class="input-group-addon">文章标题</span>
                        <textarea type="text" class="form-control" name="name" id="update-info"
                                  placeholder="请输入文章标题" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="update_id" id="update-id"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" id="update">修改</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
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
    $(function () {
        $('.news-info').click(function () {
            layer.open({
                title: '预览文章',
                type: 2,
                area: ['800px', '670px'],
                fix: true,
                content: $(this).data('url'),
            });
        });


        $('.update-news-info').click(function () {
            $('#update-info').val($(this).data('info'));
            $('#update-id').val($(this).data('id'));
            $('#update-news-info').modal('show');
        });

        $('#update').click(function () {
            var _this = $(this);
            var title = $('#update-info').val();
            var id = $('#update-id').val();
            if (!title) {
                layer.msg('请输入文章标题');
                return false;
            }
            _this.attr('disabled', true);
            $.post("<?php echo U('updateNews');?>", {id: id, title: title}, function (res) {
                layer.msg(res.info);
                if (res.status == 1) {
                    setTimeout(function () {
                        location.reload();
                    }, 2000)
                } else {
                    _this.removeAttr('disabled');
                }
            });
        })
        $('.figure').click(function () {
            var is_click = 1;
            var _this = $(this);
            var url = $(this).data('url');
            layer.confirm('确认要将此文章存草稿吗？', {
                btn: ['确定', '取消']//按钮
            }, function () {
                if (is_click == 0) {
                    layer.msg('正在请求中……，请勿重复点击！');
                } else {
                    is_click = 0;
                    _this.attr('disabled', true);
                    $.get(url, {}, function (res) {
                        layer.msg(res.info);
                        if (res.status == 1) {
                            setTimeout(function () {
                                window.location.reload();
                            }, 3000)
                        } else {
                            is_click = 1;
                            _this.removeAttr('disabled');
                        }
                    })
                }
            });
        })
        $('.publish').click(function () {
            var is_click = 1;
            var _this = $(this);
            var url = $(this).data('url');
            layer.confirm('确认要将此文章发布吗？', {
                btn: ['确定', '取消']//按钮
            }, function () {
                if (is_click == 0) {
                    layer.msg('正在请求中……，请勿重复点击！');
                } else {
                    is_click = 0;
                    _this.attr('disabled', true);
                    $.get(url, {}, function (res) {
                        layer.msg(res.info);
                        if (res.status == 1) {
                            setTimeout(function () {
                                window.location.reload();
                            }, 3000)
                        } else {
                            is_click = 1;
                            _this.removeAttr('disabled');
                        }
                    })
                }
            });
        })
        $('.news-del').click(function () {
            var is_click = 1;
            var _this = $(this);
            var url = $(this).data('url');
            layer.confirm('确认要将此文章删除吗？', {
                btn: ['确定', '取消']//按钮
            }, function () {
                if (is_click == 0) {
                    layer.msg('正在请求中……，请勿重复点击！');
                    return false;
                } else {
                    is_click = 0;
                    _this.attr('disabled', true);
                    $.get(url, {}, function (res) {
                        layer.msg(res.info);
                        if (res.status == 1) {
                            setTimeout(function () {
                                window.location.reload();
                            }, 3000)
                        } else {
                            is_click = 1;
                            _this.removeAttr('disabled');
                        }
                    })
                }
            });
        })
    })
</script>