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
            <h1 class="page-header">添加文章</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="index.html">文章管理</a></li>
            </ol>
        </div>
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-10">
                            <form role="form" id='check-form' method='post' action="<?php echo U('News/add');?>">
                                <div class="form-group">
                                    <label>标题</label>
                                    <input class="form-control" name='article[title]' type="text">
                                </div>
                                <div class="form-group">
                                    <label>文章类型</label>
                                    <div class="col-lg-12">
                                        <div class="col-lg-3">
                                            <select class="form-control" name='article[newstype]' >
                                                <option value='专辑'>专辑</option>
                                                <option value='图集'>图集</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>写作平台</label>
                                    <div class="col-lg-12">
                                        <div class="col-lg-3">
                                            <select class="form-control" name='article[platform]' >
                                                <option value='头条'>头条</option>
                                                <option value='特卖'>特卖</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>审核状态</label>
                                    <div class="col-lg-12">
                                        <div class="col-lg-3">
                                            <select class="form-control" name='article[shenhe]' >
                                                <option value='审核中'>审核中</option>
                                                <option value='已审核'>已审核</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>文章类型</label>
                                    <div class="col-lg-12"> 
                                        <div class="col-lg-3">                                  
                                        <select class="form-control" name='article[type]' >
                                            <option value='特卖文章'>特卖文章</option>
                                            <option value='放心购文章'>放心购文章</option>
                                        </select>                                    
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>领域</label>
                                    <div class="col-lg-12">
                                        <div class="col-lg-3">
                                            <select class="form-control" name='article[lingyu]' >
                                                <option value='潮女搭配师'>潮女搭配师</option>
                                                <option value='型男塑造师'>型男塑造师</option>
                                                <option value='居家巧匠'>居家巧匠</option>
                                                <option value='母婴大人'>母婴大人</option>
                                                <option value='美妆老师'>美妆老师</option>
                                                <option value='数码极客'>数码极客</option>
                                                <option value='文娱先锋'>文娱先锋</option>
                                                <option value='时尚车主'>时尚车主</option>
                                                <option value='户外'>户外</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="button" onclick="return ajax_check_submit_from(this);" check_url="<?php echo U('News/check_add_edit_data');?>" id="btn-add-edit-goods"  class="btn btn-primary" value="提交"/>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-1"></div>
                    </div>
                    <!-- /.row (nested) -->
                </div>
                <!-- /.panel-body -->
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

<script>
// 图片上传
var UPLOAD_URL = "<?php echo U('Goods/uploadImg',array('type'=>'DDHomeMerchantImage'));?>";

$(function() {
    // 商家团单列表 根据选择的城市 返回商家
    $('select.merchant-category-op').change(function() {
        var $this = $(this);
        var fid = $this.val();
        var href = $this.attr('load_href');
        var op_obj_id = $this.attr('load_select_id');
        var $op_obj = $(op_obj_id);
        var $tip = $('#merchant-category-load-tip');

        if (!fid) {
            return false;
        }
        if (!href) {
            return false;
        }
        if (!op_obj_id) {
            return false;
        }
        if ($this.hasClass('disabled')) {
            return false;
        }
        $this.addClass('disabled');
        $this.attr('disabled', true);
        $tip.html('正在加载子分类,请稍后...');
        $op_obj.html("<option value=''>--请选择--</option>");

        get_data($this,$op_obj,href,{fid: fid},$tip);

        var agent_href = $this.attr('load_agent_href');
        var $agent_op_obj = $($this.attr('load_agent_select_id'));

        if (agent_href && $agent_op_obj) {
            get_data($this,$agent_op_obj,agent_href,{cid: fid});
        }
        return false;

        function get_data(obj,op_obj,href,params,tip=null) {
            $.post(href, params, function(res) {
                obj.removeClass('disabled');
                obj.attr('disabled', false);
                if (tip) {
                    tip.html('');
                }
                if (res.code == 0 && res.data) {
                    var op_select_value = op_obj.attr('select_value');
                    var option_arr = [];
                    option_arr.push("<option value=''>--请选择--</option>");
                    for (var i = 0; i < res.data.length; i++) {
                        var option_str = "<option value='" + res.data[i].id + "'>" + res.data[i].name + "</option>";
                        if (op_select_value && op_select_value == res.data[i].id) {
                            option_str = "<option value='" + res.data[i].id + "' selected='selected'>" + res.data[i].name + "</option>";
                        }
                        option_arr.push(option_str);
                    }
                    op_obj.html(option_arr.join(''));
                    op_obj.change();
                    return false;
                }
                return false;
            }, 'json');
        }
    });
    // $('#merchant-province-id').change();
});
</script>