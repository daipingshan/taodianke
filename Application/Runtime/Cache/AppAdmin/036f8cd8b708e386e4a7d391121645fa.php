<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="Xenon Boostrap Admin Panel" />
	<meta name="author" content="" />
	
	<title>用户管理</title>

	<!-- <link rel="stylesheet" href="http://fonts.useso.com/css?family=Arimo:400,700,400italic">-->

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

    
	<link rel="stylesheet" href="/Public/Assets/css/fonts/linecons/css/linecons.css">
	<link rel="stylesheet" href="/Public/Assets/css/fonts/fontawesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="/Public/Assets/css/bootstrap.css">
	<link rel="stylesheet" href="/Public/Assets/css/xenon-core.css">
	<link rel="stylesheet" href="/Public/Assets/css/xenon-forms.css">
	<link rel="stylesheet" href="/Public/Assets/css/xenon-components.css">
	<link rel="stylesheet" href="/Public/Assets/css/xenon-skins.css">
	<link rel="stylesheet" href="/Public/Assets/css/custom.css">
    <link rel="stylesheet" href="/Public/Assets/css/style.css">

	<script src="/Public/Assets/js/jquery-1.11.1.min.js"></script>

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	<style type="text/css">
		.page{
			width: 100%;
		}
		.pagination{
			width: 100%;
			margin: 10px auto;
		}
		input::-webkit-outer-spin-button,input::-webkit-inner-spin-button{
        	-webkit-appearance:none;
		}
		input[type="number"]{
		    -moz-appearance:none;
		}
		.panel .panel-body {
		    color: #000000;
		}
		.iton-top-index {
		    width: 42px;
		    height: 36px;
		    background-image: url('/Public/Assets/images/icon.png');
		    background-position: -1529px -33px;
		    display: block;
		    position: fixed;
		    bottom: 20px;
		    right: 20px;
		    z-index: 1000;
		}

		.iton-top-index:hover {
		    width: 42px;
		    height: 36px;
		    background-image: url('/Public/Assets/images/icon.png');
		    background-position: -1604px -33px;
		    cursor: pointer;
		}

	</style>
</head>
<div class="page-container">
    <div class="sidebar-menu toggle-others fixed">
	<div class="sidebar-menu-inner">
		<header class="logo-env">
			<!-- logo -->
			<div class="logo">
				<a href="/AppAdmin/index" class="logo-expanded">
					<img src="/Public/Assets/images/logo@2x.png" width="80" alt="" />
				</a>
				<a href="/AppAdmin/index" class="logo-collapsed">
					<img src="/Public/Assets/images/logo-collapsed@2x.png" width="40" alt="" />
				</a>
			</div>
		</header>

		<ul id="main-menu" class="main-menu">
			<!-- add class "multiple-expanded" to allow multiple submenus to open -->
			<!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
			<?php if(is_array($nav_list_cache)): $i = 0; $__LIST__ = $nav_list_cache;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['child_nav']): ?><li <?php if($menu_pid == $vo['id']): ?>class="has-sub expanded active"<?php endif; ?>>
						<a href="/AppAdmin/<?php echo ($vo["module_name"]); ?>/<?php echo ($vo["action_name"]); ?>/<?php echo ($vo["data"]); ?>">
							<i class="<?php echo ($vo["ico"]); ?>"></i>
							<span class="title"><?php echo ($vo["name"]); ?></span>
						</a>
						<?php if($vo['child_nav']): ?><ul <?php if($menu_pid == $vo['id']): ?>style="display:block"<?php endif; ?>>
								<?php if(is_array($vo["child_nav"])): foreach($vo["child_nav"] as $key=>$ve): ?><li <?php if($ve["module_name"] == CONTROLLER_NAME AND $ve["action_name"] == ACTION_NAME): ?>class="active"<?php endif; ?>>
										<a href="/AppAdmin/<?php echo ($ve["module_name"]); ?>/<?php echo ($ve["action_name"]); ?>/<?php echo ($ve["data"]); ?>">
											<span class="title"><?php echo ($ve["name"]); ?></span>
										</a>
									</li><?php endforeach; endif; ?>
							</ul><?php endif; ?>
					</li>
				<?php else: ?>
					<li <?php if($menu_pid == $vo['id']): ?>class="active"<?php endif; ?>>
						<a href="/AppAdmin/<?php echo ($vo["module_name"]); ?>/<?php echo ($vo["action_name"]); ?>/<?php echo ($vo["data"]); ?>">
							<i class="<?php echo ($vo["ico"]); ?>"></i>
							<span class="title"><?php echo ($vo["name"]); ?></span>
						</a>
					</li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div>
</div>
    <div class="main-content">
        <!-- User Info, Notifications and Menu Bar -->
        <nav class="navbar user-info-navbar" role="navigation">
	<ul class="user-info-menu left-links list-inline list-unstyled">
		<li class="hidden-sm hidden-xs">
			<a href="#" data-toggle="sidebar">
				<i class="fa-bars"></i>
			</a>
		</li>
	</ul>

	<ul class="user-info-menu right-links list-inline list-unstyled">
		<li class="dropdown user-profile">
			<a href="#" data-toggle="dropdown">
				<img src="/Public/Assets/images/user-4.png" alt="user-image" class="img-circle img-inline userpic-32" width="28" />
				<span><?php echo ($username); ?><i class="fa-angle-down"></i></span>
			</a>

			<ul class="dropdown-menu user-profile-menu list-unstyled">
             	<li>
					<a href="javascript:;" onclick="edit(<?php echo ($uid); ?>);" >
						<i class="fa-info"></i>修改密码
					</a>
				</li>

				<li class="last">
					<a href="/AppAdmin/Auth/logout">
						<i class="fa-lock"></i>退出登录
					</a>
				</li>
			</ul>
		</li>
	</ul>
</nav>
        <div class="page-title">
            <div class="title-env">
                <h1 class="title">用户管理</h1>
                <p class="description">显示所有用户信息</p>
            </div>
            <div class="breadcrumb-env">
                <ol class="breadcrumb bc-1">
                    <li><a href="/AppAdmin/"><i class="fa-home"></i>主页</a></li>
                    <li class="active"><strong>用户管理 </strong></li>

                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success" style="display: none" id="success">
                    <strong></strong>
                </div>
            </div>
            <div class="col-md-12">
                <div class="alert alert-danger" id="error" style="display:none">
                    <strong></strong>
                </div>
            </div>
        </div>
        <style>
    .col-xs-2{
        width:14%;
    }
</style>
<div class="row">
    <div class="panel panel-default col-sm-12">
        <div class="panel-heading">
            <h3 class="panel-title">搜索用户</h3>
            <div class="panel-options">
                <a href="#" data-toggle="panel">
                    <span class="collapse-icon">–</span>
                    <span class="expand-icon">+</span>
                </a>
                <a href="#" data-toggle="remove">×</a>
            </div>
        </div>
        <form method="get" action="<?php echo U('User/index');?>">
            <div class="panel-body">
                <div class="row col-margin">
                    <div class="col-xs-2">
                        <input type="text" name="pid" class="form-control" placeholder="用户pid"
                        <?php if(I('get.pid') != '' ): ?>value='<?php echo ($data['pid']); ?>'<?php endif; ?>
                        />
                    </div>

                    <div class="col-xs-2">
                        <input type="text" name="dwxk_adsense_id" class="form-control" placeholder="大微信客推广位"
                        <?php if(I('get.dwxk_adsense_id') != '' ): ?>value='<?php echo ($data['dwxk_adsense_id']); ?>'<?php endif; ?>
                        />
                    </div>
                    <div class="col-xs-2">
                        <input type="text" name="real_name" class="form-control" placeholder="姓名"
                        <?php if(I('get.real_name') != '' ): ?>value='<?php echo ($data['real_name']); ?>'<?php endif; ?>
                        />
                    </div>
                    <div class="col-xs-2">
                        <input type="text" name="tel" class="form-control" placeholder="电话号码"
                        <?php if(I('get.tel') != '' ): ?>value='<?php echo ($data['tel']); ?>'<?php endif; ?>
                        />
                    </div>
                    <div class="col-xs-2">
                        <input type="text" class="form-control datepicker" name="zhuce_time" placeholder="请输入注册时间"
                        <?php if(I('get.zhuce_time') != '' ): ?>value='<?php echo ($data['reg_time']); ?>'<?php endif; ?>
                        >
                    </div>
                    <div class="col-xs-2">
                        <select name="proxy_type" id="board_id" class="form-control" style="width: 100%;">
                            <option value="">请选择代理级别</option>
                            <option value="0"
                            <?php if(I('get.proxy_type') == '0'): ?>selected<?php endif; ?>
                            >普通代理</option>
                            <option value="1"
                            <?php if(I('get.proxy_type') == '1'): ?>selected<?php endif; ?>
                            >企业级代理</option>
                            <option value="3"
                            <?php if(I('get.proxy_type') == '3'): ?>selected<?php endif; ?>
                            >一级代理</option>
                        </select>
                        <input type="hidden" id="parent_id" name="parent_id" value="<?php echo ($data["parent_id"]); ?>">
                    </div>
                    <div class="col-xs-2">
                        <input type="submit"  class="btn btn-secondary btn-single" value="搜索" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="/Public/Assets/js/datepicker/bootstrap-datepicker.js"></script>


        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">用户管理</h3>
                        <div class="panel-options">
                            <a href="#"> <i class="linecons-cog"></i> </a>
                            <a href="#" data-toggle="panel">
                                <span class="collapse-icon">&ndash;</span>
                                <span class="expand-icon">+</span> </a>
                            <a href="#" data-toggle="reload"> <i class="fa-rotate-right"></i> </a>
                            <a href="#" data-toggle="remove"> &times; </a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive" data-pattern="priority-columns" data-focus-btn-icon="fa-asterisk"
                             data-sticky-table-header="true" data-add-display-all-btn="true" data-add-focus-btn="true">
                            <table cellspacing="0" class="table table-small-font table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th width="8%">id</th>
                                    <th width="18%">个人基本信息</th>
                                    <th width="18%">账号信息</th>
                                    <th width="18%">我的推广位</th>
                                    <th width="20%">代理信息</th>
                                    <th width="12%">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                                        <th><?php echo ($vo["id"]); ?></th>
                                        <td>
                                            姓名：<?php echo ($vo["real_name"]); ?> <br/>
                                            电话：<?php echo ($vo["mobile"]); ?><br/>
                                            代理级别：
                                            <?php if($vo["proxy_type"] == '0' ): ?>普通代理
                                                <?php elseif($vo["proxy_type"] == '1'): ?>
                                                企业级代理
                                                <?php elseif($vo["proxy_type"] == '3'): ?>
                                                一级代理<?php endif; ?>
                                            <br/>
                                            是否有微信营销：<?php echo ($vo["is_wechat_marketing"]); ?>
                                            <br>
                                            用户身份：
                                            <?php if($vo['store_type'] == 0 ): ?>非线下门店用户
                                                <?php elseif($vo['store_type'] == 1): ?>
                                                线下门店店主
                                                <?php elseif($vo['store_type'] == 2): ?>
                                                线下们店收银员<?php endif; ?>
                                        </td>
                                        <td>
                                            pid：<?php echo ($vo["pid"]); ?><br/>
                                            大微信客推广：<?php echo ($vo["dwxk_adsense_id"]); ?><br/>
                                            支付宝账号：<?php echo ($vo["bank_account"]); ?>
                                        </td>
                                        <td>
                                            <?php if(is_array($zone[$vo['id']])): foreach($zone[$vo['id']] as $key=>$zo): ?><a href="javascript:;" title="PID:<?php echo ($zo["pid"]); ?> 大微信客 <?php echo ($zo["dwxk_adsense_id"]); ?>"
                                                   onclick="edit_pid('<?php echo ($zo["zone_name"]); ?>', '<?php echo ($zo["pid"]); ?>', '<?php echo ($zo["dwxk_adsense_id"]); ?>', '<?php echo ($zo["is_default"]); ?>', '<?php echo ($zo["id"]); ?>', '<?php echo ($vo["id"]); ?>')">
                                                    <?php echo ($zo["zone_name"]); ?>
                                                    <?php if($zo["is_default"] == '1'): ?>(默认)<?php endif; ?>
                                                </a><br><?php endforeach; endif; ?>
                                        </td>
                                        <td>
                                            注册时间：<?php echo (date("Y-m-d H:i",$vo["reg_time"])); ?><br/>
                                            代理时间：
                                            <?php if($vo["join_agent_time"] > 0): echo (date("Y-m-d",$vo["join_agent_time"])); endif; ?>
                                            <br/>
                                            推荐人:
                                            <?php if($vo["parentid"] == '0' ): ?>暂无上级
                                                <?php else: ?>
                                                <?php echo ($user[$vo['parentid']]['real_name']); endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo U('User/edit',array('id'=>$vo['id']));?>" class="ibutton">
                                                <i class="fa-edit"></i>
                                            </a> |
                                            <a href="" class="ibutton">
                                                <i class="fa-trash"></i>
                                            </a><br>
                                            <a href="javascript:;" onclick="qr_code_url(<?php echo ($vo["id"]); ?>)"
                                               class="btn btn-primary btn-single btn-sm">查看二维码</a><br>
                                            <?php if($vo["proxy_type"] == '3' ): ?><a href="<?php echo U('User/index',array('parent_id'=>$vo['id']));?>"
                                                   class="btn btn-primary btn-single btn-sm">查看下级代理</a><br><?php endif; ?>
                                            <a href="javascript:;" onclick="add_more_pid(<?php echo ($vo["id"]); ?>)"
                                               class="btn btn-primary btn-single btn-sm"
                                               style="background-color: #1ab7ea">添加推广位</a>
                                            <br>
                                            <?php if($vo['store_type'] == 1): ?><a href="javascript:;" class="btn btn-danger btn-sm del-store"
                                                   data-id="<?php echo ($vo["id"]); ?>">取消店主</a>
                                                <?php elseif($vo['store_type'] == 2): ?>
                                                <a href="javascript:;" class="btn btn-danger btn-sm">收银员</a>
                                                <?php else: ?>
                                                <a href="javascript:;" class="btn btn-danger btn-sm set-store"
                                                   data-id="<?php echo ($vo["id"]); ?>" data-address="<?php echo ($vo["address"]); ?>"
                                                   data-remark="<?php echo ($vo["user_remark"]); ?>" data-name="<?php echo ($vo["real_name"]); ?>">设置店主</a><?php endif; ?>
                                        </td>
                                    </tr><?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="page">
                            <ul class="pagination"><?php echo ($page); ?></ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <footer class="main-footer sticky footer-type-1">
	<div class="footer-inner">
		<!-- Add your copyright text here -->
		<div class="footer-text">
			&copy; 2017
			<strong>罗文网络科技有限公司</strong>
			Technical Support <a href="Tel:15991606450" target="_blank" title="">Guonan</a> - EMail <a title="" target="_blank">4396988@qq.com</a>
		</div>
		<!-- Go to Top Link, just add rel="go-top" to any link to add this functionality -->
		<div class="go-up">
			<a href="#" rel="go-top">
				<i class="fa-angle-up"></i>
			</a>
		</div>
	</div>
</footer>
	
	

    </div>

    <!-- Modal 4 (Confirm)-->
    <div class="modal fade" id="modal-4" data-backdrop="static">
        <div class="modal-dialog" style="width: 400px;height: 500px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">查看二维码</h4>
                </div>
                <div class="modal-body" id="images" style="height:400px;text-align:center">
                    正在加载....
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 添加推广位 -->
    <div class="modal fade" id="set-store">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">设置店主</h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="user_id" value="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">真实姓名</label>
                                <input type="text" name="realname" id="realname" class="form-control"
                                       placeholder="请输入真实姓名">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">店铺名称</label>
                                <input type="text" name="user_remark" id="user_remark" class="form-control"
                                       placeholder="请输入店铺名称">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">店铺地址</label>
                                <input type="text" name="address" id="address" class="form-control"
                                       placeholder="请输入店铺地址">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label error-tip" style="color: red"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-info" id="set-store-btn">确认</button>
                </div>

            </div>
        </div>
    </div>

    <!-- 添加推广位 -->
    <div class="modal fade" id="modal_mix">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">添加推广位</h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="uid" value="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">推广位名称</label>
                                <input type="text" name="zone_name" id="zone_name" class="form-control tuiguanwei"
                                       placeholder="请输入推广位名称">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">推广位pid</label>
                                <input type="text" name="zone_pid" id="zone_pid" class="form-control tuiguanwei"
                                       placeholder="请输入推广位pid">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">大微信客推广位（选填）</label>
                                <input type="text" name="dwxk_adsense_id" id="dwxk_adsense_id"
                                       class="form-control tuiguanwei" placeholder="请输入大微信客推广位">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">是否默认</label>
                                </div>
                                <div class="col-sm-10">
                                    <div class="radio">
                                        <label><input type="radio" name="is_default" value="1"/>是 </label>
                                        <label><input type="radio" name="is_default" value="0" checked="checked"/>否
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-info" onclick="do_add_more_pid()">确认</button>
                </div>

            </div>
        </div>
    </div>

    <!-- 编辑推广位 -->
    <div class="modal fade" id="modal_edit">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">编辑推广位</h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="update_id" value="">
                    <input type="hidden" name="uid" id="update_uid" value="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">推广位名称</label>
                                <input type="text" name="zone_name" id="update_zone_name"
                                       class="form-control tuiguanwei" placeholder="请输入推广位" value="">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">推广位pid</label>
                                <input type="text" name="zone_pid" id="update_zone_pid" class="form-control tuiguanwei"
                                       placeholder="请输入推广位pid" value="">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">大微信客推广位（选填）</label>
                                <input type="text" name="dwxk_adsense_id" id="update_dwxk_adsense_id"
                                       class="form-control tuiguanwei" placeholder="请输入大微信客推广位" value="">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">是否默认</label>
                            </div>
                            <div class="col-sm-10">
                                <div class="radio">
                                    <label><input type="radio" name="edit_is_default" value="1"/>是 </label>
                                    <label><input type="radio" name="edit_is_default" value="0">否 </label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-white" onclick="do_delete_pid()">删除</button>
                    <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-info" onclick="do_edit_pid()">确认</button>
                </div>

            </div>
        </div>
    </div>
    <style>
        .tuiguanwei {
            width: 110% !important;
        }
    </style>
    <script>
        $('.set-store').click(function () {
            $('#user_id').val($(this).data('id'));
            $('#realname').val($(this).data('name'));
            $('#address').val($(this).data('address'));
            $('#user_remark').val($(this).data('remark'));
            jQuery('#set-store').modal('show', {backdrop: 'static'});
        });

        $('#realname,#address,#user_remark').focus(function () {
            $('.error-tip').text('');
        });

        $('#set-store-btn').click(function () {
            var id = $('#user_id').val();
            var realname = $('#realname').val();
            var user_remark = $('#user_remark').val();
            var address = $('#address').val();
            if (!realname) {
                $('.error-tip').text('请输入真实姓名！');
                return false;
            }
            if (!user_remark) {
                $('.error-tip').text('请输入店铺名称！');
                return false;
            }
            if (!address) {
                $('.error-tip').text('请输入店铺地址！');
                return false;
            }
            $.post("<?php echo U('setStore');?>", {
                id: id,
                realname: realname,
                user_remark: user_remark,
                address: address
            }, function (res) {
                $('.error-tip').text(res.info);
                if (res.status == 1) {
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                }
            })
        })

        $('.del-store').click(function () {
            var id = $(this).data('id');
            var status = confirm('你确定要将该用户取消店主身份？');
            if (status) {
                $.post("<?php echo U('delStore');?>", {
                    id: id,
                }, function (res) {
                    if (res.status == 1) {
                        $('.alert-success strong').text(res.info);
                        $('.alert-success').show();
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    } else {
                        $('.alert-danger strong').text(res.info);
                        $('.alert-danger').show();
                    }
                })
            }

        })
        function qr_code_url(id) {
            jQuery('#modal-4').modal('show', {backdrop: 'static'});
            var url = "<?php echo U('User/getWechatQrcode');?>";
            $.get(url, {id: id}, function (res) {
                var l = "";
                if (res.code == 0) {
                    l += "<img src='" + res.data + "' width='200' height='350'>";
                    $('#images').html(l);
                } else if (res.code == -1) {
                    l += res.msg;
                    $('#images').html(l);
                }
            })
        }
        function add_more_pid(id) {
            jQuery('#modal_mix').modal('show', {backdrop: 'static'});
            jQuery('#uid').val(id);
        }
        function do_add_more_pid() {
            var zone_pid = $("#zone_pid").val();
            var zone_name = $("#zone_name").val();
            var dwxk_adsense_id = $("#dwxk_adsense_id").val();
            var is_default = $("#modal_mix input[name='is_default']:checked").val();
            var uid = $("#uid").val();
            if (!zone_pid || !zone_name) {
                alert('推广位名称或推广位pid不能为空');
                return false;
            }

            var get_url = "<?php echo U('User/addMorePid');?>";
            $.get(get_url, {
                zone_pid: zone_pid,
                zone_name: zone_name,
                uid: uid,
                dwxk_adsense_id: dwxk_adsense_id,
                is_default: is_default
            }, function (res) {
                alert(res.msg);
                if (res.code == 0) {
                    window.location.href = window.location.href;
                }
            });
        }

        function edit_pid(zone_name, pid, dwxk_adsense_id, is_default, id, uid) {
            jQuery('#update_zone_name').val(zone_name);
            jQuery('#update_zone_pid').val(pid);
            jQuery('#update_dwxk_adsense_id').val(dwxk_adsense_id);
            jQuery('#update_id').val(id);
            jQuery('#update_uid').val(uid);
            $("#modal_edit input[name='edit_is_default']").prop("checked", false);
            $("#modal_edit input[name='edit_is_default'][value=" + is_default + "]").prop("checked", "checked");
            jQuery('#modal_edit').modal('show', {backdrop: 'static'});
        }

        function do_edit_pid() {
            var id = $("#update_id").val();
            var uid = $("#update_uid").val();
            var zone_name = $("#update_zone_name").val();
            var zone_pid = $("#update_zone_pid").val();
            var dwxk_adsense_id = $("#update_dwxk_adsense_id").val();
            var is_default = $("input[name='edit_is_default']:checked").val();

            if (!zone_pid || !zone_name) {
                alert('推广位名称或推广位pid不能为空');
                return false;
            }

            var get_url = "<?php echo U('User/editPid');?>";
            $.get(get_url, {
                zone_pid: zone_pid,
                zone_name: zone_name,
                id: id,
                dwxk_adsense_id: dwxk_adsense_id,
                uid: uid,
                is_default: is_default
            }, function (res) {
                alert(res.msg);
                if (res.code == 0) {
                    window.location.href = window.location.href;
                }
            });
        }

        function do_delete_pid() {
            var id = $("#update_id").val();
            var uid = $("#update_uid").val();

            if (!confirm('是否删除该推广位')) {
                return false;
            }

            var get_url = "<?php echo U('User/deletePid');?>";
            $.get(get_url, {id: id, uid: uid}, function (res) {
                alert(res.msg);
                if (res.code == 0) {
                    window.location.href = window.location.href;
                }
            });
        }
    </script>


    <script>
	function edit() {
        jQuery('#modal_6').modal('show', {backdrop: 'static'});
    }

    function editPassword() {
    	var password = $("#password").val();
        var newpassword = $("#newpassword").val();
    	var repassword = $("#repassword").val();
    	if (!newpassword || !repassword) {
    		alert('密码或重复密码为空');
    		return false;
    	} else if ( newpassword != repassword ) {
    		alert('两次密码不一样，请确认密码！');
    		return false;
    	}
	 	var get_url = "<?php echo U('Auth/revisePassword');?>";
        $.get(get_url,{password:password, newpassword:newpassword, repassword:repassword},function(res){
            if(res.code == 1){
                alert(res.msg);
            	window.location.href = "<?php echo U('Index/index');?>";
            } else if (res.code == 0) {
                alert(res.msg);
            	window.location.href = "<?php echo U('Index/index');?>";
            }

        });
    }

    function read(id) {
        jQuery('#modal_7').modal('show', {backdrop: 'static'});
        var url = "<?php echo U('Merchant/read');?>";
        $.get(url, {id:id}, function (res) {
            jQuery('#modal_7 .modal-body').html(res.html);
        })
    }

    function isPush(id, title) {
        jQuery('#modal_8').modal('show', {backdrop: 'static'});
        jQuery('#modal_8 #goods_id').val(id);
        jQuery('#modal_8 #title').val(title);
    }

    function doSet() {
        var id = $("#goods_id").val();
        var type = $("#type").val();
        var title = $("#title").val();
        var is_push = $("input[name=is_push]:checked").val();

        var get_url = "<?php echo U('Items/setHandpick');?>";
        $.get(get_url,{id:id, type:type, title:title, is_push:is_push},function(res){
            if(res.status == 1){
                alert(res.info);
                window.location.href = "<?php echo U('Items/index');?>";
            } else if (res.status == 0) {
                alert(res.info);
                window.location.href = "<?php echo U('Items/index');?>";
            }

        });
    }

</script>

<div class="modal fade" id="modal_6">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">修改密码</h4>
            </div>
            <form method="get" action="<?php echo U('Auth/revisePassword');?>">
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="field-1" class="control-label">原始密码</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="请输入原始密码">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="field-1" class="control-label">新密码</label>
                            <input type="password" name="newpassword" class="form-control" id="newpassword" placeholder="请输入新密码">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="field-1" class="control-label">确认密码</label>
                            <input type="password" name="repassword" class="form-control" id="repassword" placeholder="请输入用户确认密码">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-info" onclick="editPassword()">确认</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_7">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">商家详情</h4>
            </div>
            <div class="modal-body">
                正在加载中...
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_8">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">设置精选</h4>
            </div>
            <form method="get" action="<?php echo U('Items/setHandpick');?>">
                <div class="modal-body">
                    <input type="hidden" name="id" id="goods_id" value=""/>
                    <input type="hidden" name="type" id="type" value="Y"/>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">推送标题</label>
                                <textarea name="title" rows="3" class="form-control" id="title">

                                </textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="control-label">是否推送</label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="is_push" value="Y" />是
                            </label>
                            <label>
                                <input type="radio" name="is_push" value="N" checked="checked"/>否
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-info" onclick="doSet()">确认</button>
                </div>
            </form>
        </div>
    </div>
</div>

	<!-- Bottom Scripts -->
	<script src="/Public/Assets/js/bootstrap.min.js"></script>
	<script src="/Public/Assets/js/TweenMax.min.js"></script>
	<script src="/Public/Assets/js/resizeable.js"></script>
	<script src="/Public/Assets/js/joinable.js"></script>
	<script src="/Public/Assets/js/xenon-api.js"></script>
	<script src="/Public/Assets/js/xenon-toggles.js"></script>
	<!-- JavaScripts initializations and stuff -->
	<script src="/Public/Assets/js/xenon-custom.js"></script>
    <div id="back-to-top" class="iton-top-index">
        <a href="#"></a>
    </div>
<script type="text/javascript">
    $(function () {
        $("#back-to-top").hide();
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
</script>
</body>

</html>