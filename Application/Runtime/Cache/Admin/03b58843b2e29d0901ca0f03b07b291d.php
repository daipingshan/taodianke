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
            <h1 class="page-header">天猫订单管理</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12 form-group">
            <div class="row">
                <h4><?php echo ($sdate); ?>前三甲</h4>
                <p>状元：<?php echo ($cout[0]['username']); ?> 成绩：<?php echo ($cout[0]['cout']); ?>
                榜眼：<?php echo ($cout[1]['username']); ?> 成绩：<?php echo ($cout[1]['cout']); ?>
                探花：<?php echo ($cout[2]['username']); ?> 成绩：<?php echo ($cout[2]['cout']); ?></p>
                <div>未上榜的同学继续努力，相信下次就是你</div>
                <p><a href="GetYsdOrder.html" target="_blank" style="color:red; ">查看昨天订单情况</a></p>
                <h4><?php echo ($nwtime); ?>实时前三甲 刷新哦说不定你就上榜了</h4>
                <p>状元：<?php echo ($tadaycou[0]['username']); ?> 成绩：<?php echo ($tadaycou[0]['cout']); ?>
                榜眼：<?php echo ($tadaycou[1]['username']); ?> 成绩：<?php echo ($tadaycou[1]['cout']); ?>
                探花：<?php echo ($tadaycou[2]['username']); ?> 成绩：<?php echo ($tadaycou[2]['cout']); ?></p>
            </div>
            <p style="">
                <?php echo ($user['name']); ?>今天订单金额：<?php echo ($user['money']); ?>
                本月预估业绩：<?php echo ($user['mmoney']); ?>
                预估排名第<?php echo ($user['num']); ?>名
                <?php if($user['mmoney2'] && $user['mmoney1']): ?>你比<?php echo ($user['num']-1); ?>名少<?php echo ($user['mmoney1']); ?>
                    比<?php echo ($user['num']+1); ?>名多<?php echo ($user['mmoney2']); ?>
                    <?php else: ?>
                    比<?php echo ($user['num']+1); ?>名多<?php echo ($user['mmoney2']); endif; ?>
                <span style="display: block;"><a href="GetmyOrder.html">查看我的订单</a></span>
            </p>
        </div>
        <?php if($uid == '0'): ?><div id="pp" style="position:absolute; right: 10px; top: 52px; ">
              <div style="float: left; margin-right:50px;margin-top: 45px;  height: 50px;width: 330px; ">
                  今天业绩：<?php echo ($tadaycoutsum); ?>——本月业绩：<?php echo ($monthcoutsum); ?>
              </div>
              <div style="float: left; margin-right:10px;  height: 310px;overflow-y:auto;width: 230px; ">
                  <?php if(is_array($tadaycou)): $i = 0; $__LIST__ = $tadaycou;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dd): $mod = ($i % 2 );++$i;?><p><?php echo ($dd["username"]); ?>--成绩--<?php echo ($dd["cout"]); ?></p><?php endforeach; endif; else: echo "" ;endif; ?>
              </div>
              <div style="float: right;margin-right: 10px; height: 310px;overflow-y:auto;width: 220px;">
                  <?php if(is_array($monthcout)): $i = 0; $__LIST__ = $monthcout;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ddd): $mod = ($i % 2 );++$i;?><p><?php echo ($ddd["username"]); ?>--成绩--<?php echo ($ddd["cout"]); ?></p><?php endforeach; endif; else: echo "" ;endif; ?>
              </div>
           <div style="clear: both;"></div>
          </div>
            <?php elseif($uid == '14' || $uid == '2' ): ?>
            <div id="ppp" style="position:absolute; right: 10px; top: 52px; ">
                <div style="float: left; margin-right:5px;  height: 310px;overflow-y:auto;width: 180px; ">
                    <p>本月排名</p>
                    <p><?php echo ($monthcout[0]['username']); ?>--成绩--<?php echo ($monthcout[0]['cout']); ?></p>
                    <p><?php echo ($monthcout[1]['username']); ?>--成绩--<?php echo ($monthcout[1]['cout']); ?></p>
                    <p><?php echo ($monthcout[2]['username']); ?>--成绩--<?php echo ($monthcout[2]['cout']); ?></p>
                    <p><?php echo ($monthcout[3]['username']); ?>--成绩--<?php echo ($monthcout[3]['cout']); ?></p>
                    <p><?php echo ($monthcout[4]['username']); ?>--成绩--<?php echo ($monthcout[4]['cout']); ?></p>
                    <p><?php echo ($monthcout[5]['username']); ?>--成绩--<?php echo ($monthcout[5]['cout']); ?></p>
                    <p><?php echo ($monthcout[6]['username']); ?>--成绩--<?php echo ($monthcout[6]['cout']); ?></p>
                    <p><?php echo ($monthcout[7]['username']); ?>--成绩--<?php echo ($monthcout[7]['cout']); ?></p>
                    <p><?php echo ($monthcout[8]['username']); ?>--成绩--<?php echo ($monthcout[8]['cout']); ?></p>
                    <p><?php echo ($monthcout[9]['username']); ?>--成绩--<?php echo ($monthcout[9]['cout']); ?></p>
                    <p><?php echo ($monthcout[10]['username']); ?>--成绩--<?php echo ($monthcout[10]['cout']); ?></p>
                    <p><?php echo ($monthcout[11]['username']); ?>--成绩--<?php echo ($monthcout[11]['cout']); ?></p>
                </div>
                <div style="float: left; margin-right:5px;  height: 310px;overflow-y:auto;width: 180px; ">
                    <p><?php echo ($sdate); ?>排名</p>
                    <p><?php echo ($cout[0]['username']); ?>--成绩--<?php echo ($cout[0]['cout']); ?></p>
                    <p><?php echo ($cout[1]['username']); ?>--成绩--<?php echo ($cout[1]['cout']); ?></p>
                    <p><?php echo ($cout[2]['username']); ?>--成绩--<?php echo ($cout[2]['cout']); ?></p>
                    <p><?php echo ($cout[3]['username']); ?>--成绩--<?php echo ($cout[3]['cout']); ?></p>
                    <p><?php echo ($cout[4]['username']); ?>--成绩--<?php echo ($cout[4]['cout']); ?></p>
                    <p><?php echo ($cout[5]['username']); ?>--成绩--<?php echo ($cout[5]['cout']); ?></p>
                    <p><?php echo ($cout[6]['username']); ?>--成绩--<?php echo ($cout[6]['cout']); ?></p>
                    <p><?php echo ($cout[7]['username']); ?>--成绩--<?php echo ($cout[7]['cout']); ?></p>
                    <p><?php echo ($cout[8]['username']); ?>--成绩--<?php echo ($cout[8]['cout']); ?></p>
                    <p><?php echo ($cout[9]['username']); ?>--成绩--<?php echo ($cout[9]['cout']); ?></p>
                    <p><?php echo ($cout[10]['username']); ?>--成绩--<?php echo ($cout[10]['cout']); ?></p>
                    <p><?php echo ($cout[11]['username']); ?>--成绩--<?php echo ($cout[11]['cout']); ?></p>
                </div>
                <div style="float: right;margin-right: 5px; height: 310px;overflow-y:auto;width: 180px;">
                    <p><?php echo ($nwtime); ?>排名</p>
                    <p><?php echo ($tadaycou[0]['username']); ?>--成绩--<?php echo ($tadaycou[0]['cout']); ?></p>
                    <p><?php echo ($tadaycou[1]['username']); ?>--成绩--<?php echo ($tadaycou[1]['cout']); ?></p>
                    <p><?php echo ($tadaycou[2]['username']); ?>--成绩--<?php echo ($tadaycou[2]['cout']); ?></p>
                    <p><?php echo ($tadaycou[3]['username']); ?>--成绩--<?php echo ($tadaycou[3]['cout']); ?></p>
                    <p><?php echo ($tadaycou[4]['username']); ?>--成绩--<?php echo ($tadaycou[4]['cout']); ?></p>
                    <p><?php echo ($tadaycou[5]['username']); ?>--成绩--<?php echo ($tadaycou[5]['cout']); ?></p>
                    <p><?php echo ($tadaycou[6]['username']); ?>--成绩--<?php echo ($tadaycou[6]['cout']); ?></p>
                    <p><?php echo ($tadaycou[7]['username']); ?>--成绩--<?php echo ($tadaycou[7]['cout']); ?></p>
                    <p><?php echo ($tadaycou[8]['username']); ?>--成绩--<?php echo ($tadaycou[8]['cout']); ?></p>
                    <p><?php echo ($tadaycou[9]['username']); ?>--成绩--<?php echo ($tadaycou[9]['cout']); ?></p>
                    <p><?php echo ($tadaycou[10]['username']); ?>--成绩--<?php echo ($tadaycou[10]['cout']); ?></p>
                    <p><?php echo ($tadaycou[11]['username']); ?>--成绩--<?php echo ($tadaycou[11]['cout']); ?></p>
                </div>
                <div style="clear: both;"></div>
            </div>
            <?php else: ?>
            <div id="pppp" style="position:absolute; right: 10px; top: 52px; ">
                <div style="float: left; margin-right:5px;  height: 310px;overflow-y:auto;width: 180px; ">
                    <p><?php echo ($sdate); ?>排名</p>
                    <p><?php echo ($cout[0]['username']); ?>--成绩--<?php echo ($cout[0]['cout']); ?></p>
                    <p><?php echo ($cout[1]['username']); ?>--成绩--<?php echo ($cout[1]['cout']); ?></p>
                    <p><?php echo ($cout[2]['username']); ?>--成绩--<?php echo ($cout[2]['cout']); ?></p>
                    <p><?php echo ($cout[3]['username']); ?>--成绩--<?php echo ($cout[3]['cout']); ?></p>
                    <p><?php echo ($cout[4]['username']); ?>--成绩--<?php echo ($cout[4]['cout']); ?></p>
                    <p><?php echo ($cout[5]['username']); ?>--成绩--<?php echo ($cout[5]['cout']); ?></p>
                    <p><?php echo ($cout[6]['username']); ?>--成绩--<?php echo ($cout[6]['cout']); ?></p>
                    <p><?php echo ($cout[7]['username']); ?>--成绩--<?php echo ($cout[7]['cout']); ?></p>
                    <p><?php echo ($cout[8]['username']); ?>--成绩--<?php echo ($cout[8]['cout']); ?></p>
                    <p><?php echo ($cout[9]['username']); ?>--成绩--<?php echo ($cout[9]['cout']); ?></p>
                    <p><?php echo ($cout[10]['username']); ?>--成绩--<?php echo ($cout[10]['cout']); ?></p>
                    <p><?php echo ($cout[11]['username']); ?>--成绩--<?php echo ($cout[11]['cout']); ?></p>
                </div>
                <div style="float: right;margin-right: 5px; height: 310px;overflow-y:auto;width: 180px;">
                    <p><?php echo ($nwtime); ?>排名</p>
                    <p><?php echo ($tadaycou[0]['username']); ?>--成绩--<?php echo ($tadaycou[0]['cout']); ?></p>
                    <p><?php echo ($tadaycou[1]['username']); ?>--成绩--<?php echo ($tadaycou[1]['cout']); ?></p>
                    <p><?php echo ($tadaycou[2]['username']); ?>--成绩--<?php echo ($tadaycou[2]['cout']); ?></p>
                    <p><?php echo ($tadaycou[3]['username']); ?>--成绩--<?php echo ($tadaycou[3]['cout']); ?></p>
                    <p><?php echo ($tadaycou[4]['username']); ?>--成绩--<?php echo ($tadaycou[4]['cout']); ?></p>
                    <p><?php echo ($tadaycou[5]['username']); ?>--成绩--<?php echo ($tadaycou[5]['cout']); ?></p>
                    <p><?php echo ($tadaycou[6]['username']); ?>--成绩--<?php echo ($tadaycou[6]['cout']); ?></p>
                    <p><?php echo ($tadaycou[7]['username']); ?>--成绩--<?php echo ($tadaycou[7]['cout']); ?></p>
                    <p><?php echo ($tadaycou[8]['username']); ?>--成绩--<?php echo ($tadaycou[8]['cout']); ?></p>
                    <p><?php echo ($tadaycou[9]['username']); ?>--成绩--<?php echo ($tadaycou[9]['cout']); ?></p>
                    <p><?php echo ($tadaycou[10]['username']); ?>--成绩--<?php echo ($tadaycou[10]['cout']); ?></p>
                    <p><?php echo ($tadaycou[11]['username']); ?>--成绩--<?php echo ($tadaycou[11]['cout']); ?></p>
                </div>
                <div style="clear: both;"></div>
            </div><?php endif; ?>
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
                                    <td>点击时间</td>
                                    <td width="25%">商品信息</td>
                                    <td>商品数</td>
                                    <td>商品单价</td>
                                    <td>订单状态</td>
                                    <td>收入比率</td>
                                    <td>付款金额</td>
                                    <td>预估收入</td>
                                    <td>订单来源</td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dd): $mod = ($i % 2 );++$i;?><tr>
                                    <td><?php echo ($dd["create_time"]); ?></td>
                                    <td><?php echo ($dd["click_time"]); ?></td>
                                    <td><a href="<?php echo ($dd["auctionurl"]); ?>" target="_blank"><?php echo ($dd["title"]); ?></a></td>
                                    <td><?php echo ($dd["number"]); ?></td>
                                    <td><?php echo ($dd["price"]); ?></td>
                                    <td><?php echo ($dd["paystatus"]); ?></td>
                                    <td><?php echo ($dd["discount_rate"]); ?></td>

                                    <td><?php echo ($dd["total_fee"]); ?></td>
                                    <td style="color: red;"><?php echo ($dd["fee"]); ?></td>
                                    <td><?php echo ($dd["username"]); ?></td>
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