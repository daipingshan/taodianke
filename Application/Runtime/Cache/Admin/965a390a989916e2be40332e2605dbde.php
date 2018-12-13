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
        <div class="col-lg-8">
            <h1 class="page-header">特卖账号列表</h1>
        </div>
        <div class="col-lg-4" style="margin-top: 45px">
            <button class="btn btn-danger" data-toggle="modal" data-target="#add-modal">添加账号</button>
        </div>
        <!-- /.col-lg-12 -->
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
                                <th>账号名称</th>
                                <th>添加时间</th>
                                <th>修改时间</th>
                                <th style='width:300px;'><i class="fa fa-gear fa-fw"></i></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                    <td><?php echo ($vo["username"]); ?></td>
                                    <td><?php echo (date("Y-m-d H:i:s",$vo["add_time"])); ?></td>
                                    <td><?php echo (date("Y-m-d H:i:s",$vo["update_time"])); ?></td>
                                    <td>
                                        <a class="btn btn-warning update-top"
                                           href="javascript:;" data-id="<?php echo ($vo["id"]); ?>"
                                           data-name="<?php echo ($vo["username"]); ?>" data-cookie='<?php echo ($vo["cookie"]); ?>'>修改</a>
                                        <a class="btn btn-danger del-top" href="javascript:;"
                                           data-url="<?php echo U('deleteAccount');?>" data-id="<?php echo ($vo["id"]); ?>">删除</a>
                                        <a class="btn btn-info open-info" href="javascript:;"
                                           data-url="<?php echo U('openInfo',array('id'=>$vo['id']));?>" data-id="<?php echo ($vo["id"]); ?>">查看</a>
                                        <a class="btn btn-info open-info" href="javascript:;"
                                           data-url="<?php echo U('GetFxgOrder',array('id'=>$vo['id']));?>" data-id="<?php echo ($vo["id"]); ?>">更新订单</a>
                                    </td>
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
<!-- 按钮触发模态框 -->
<!-- 模态框（Modal） -->
<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">添加特卖账号</h4>
            </div>
            <form class="bs-example bs-example-form" role="form">
                <div class="modal-body">
                    <div class="input-group">
                        <span class="input-group-addon">特卖账号名称&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <input type="text" name="username" id="username" class="form-control" placeholder="请输入账号名称">
                    </div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon">特卖账号cookie</span>
                        <textarea name="cookie" class="form-control" id="cookie" rows="10"
                                  placeholder="请输入头条账号cookie"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" id="add">添加</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<div class="modal fade" id="update-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">修改特卖账号</h4>
            </div>
            <form class="bs-example bs-example-form" role="form">
                <div class="modal-body">
                    <div class="input-group">
                        <span class="input-group-addon">特卖账号名称&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <input type="text" name="username" id="update_username" class="form-control"
                               placeholder="请输入账号名称">
                    </div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon">特卖账号cookie</span>
                        <textarea name="cookie" class="form-control" id="update_cookie" rows="10"
                                  placeholder="请输入头条账号cookie"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="update_id" id="update_id"/>
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
        $('#add').click(function () {
            var _this = $(this);
            var username = $('#username').val();
            var cookie = $('#cookie').val();
            if (!username) {
                layer.msg('请输入头条账号名称');
                return false;
            }
            if (!cookie) {
                layer.msg('请输入头条账号cookie');
                return false;
            }
            _this.attr('disabled', true);
            var url = "<?php echo U('addAccount');?>";
            $.post(url, {username: username, cookie: cookie}, function (res) {
                layer.msg(res.info);
                if (res.status == 1) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 3000)
                } else {
                    _this.removeAttr('disabled');
                }
            })
        })

        $('.update-top').click(function () {
            $('#update_username').val($(this).data('name'));
            $('#update_cookie').val($(this).data('cookie'));
            $('#update_id').val($(this).data('id'));
            $('#update-modal').modal('show');
        })

        $('#update').click(function () {
            var _this = $(this);
            var username = $('#update_username').val();
            var cookie = $('#update_cookie').val();
            var id = $('#update_id').val();
            if (!username) {
                layer.msg('请输入头条账号名称');
                return false;
            }
            if (!cookie) {
                layer.msg('请输入头条账号cookie');
                return false;
            }
            _this.attr('disabled', true);
            var url = "<?php echo U('updateAccount');?>";
            $.post(url, {username: username, cookie: cookie, id: id}, function (res) {
                layer.msg(res.info);
                if (res.status == 1) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 3000)
                } else {
                    _this.removeAttr('disabled');
                }
            })
        })

        $('.del-top').click(function () {
            var _this = $(this);
            var url = $(this).data('url');
            var id = $(this).data('id');
            layer.confirm('确认要删除该账号吗？', {
                btn: ['确定', '取消']//按钮
            }, function () {
                _this.attr('disabled', true);
                $.post(url, {id: id}, function (res) {
                    layer.msg(res.info);
                    if (res.status == 1) {
                        setTimeout(function () {
                            window.location.reload();
                        }, 3000)
                    } else {
                        _this.removeAttr('disabled');
                    }
                })
            });
        })

        $('.open-info').click(function () {
            var url = $(this).data('url');
            $.get(url, {}, function (res) {
                layer.open({
                    title: '账号信息',
                    content: res.info //这里content是一个普通的String
                });
            })
        })
    })
</script>