<?php if (!defined('THINK_PATH')) exit();?>s
<!DOCTYPE html>
<html lang="cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="Xenon Boostrap Admin Panel" />
	<meta name="author" content="" />
	
	<title>系统配置</title>

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
                <h1 class="title">系统配置</h1>
                <p class="description">配置信息</p>
            </div>
            <div class="breadcrumb-env">
                <ol class="breadcrumb bc-1">
                    <li><a href="/AppAdmin/"><i class="fa-home"></i>主页</a></li>
                    <li class="active"> 站点设置</li>
                    <li class="active"><strong>系统配置</strong></li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success" style="display: none" id="success">
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <strong></strong>
                </div>
            </div>
            <div class="col-md-12">
                <div class="alert alert-danger" style="display: none;" id="fail">
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <strong></strong>
                </div>
            </div>
            <div class="col-md-12">
                <ul class="nav nav-tabs nav-tabs-justified">
                    <li class="active">
                        <a href="#home-3" data-toggle="tab">
                            <span class="visible-xs"><i class="fa-home"></i></span>
                            <span class="hidden-xs">基础配置</span>
                        </a>
                    </li>
                    <!--  <li>
                          <a href="#profile-3" data-toggle="tab">
                              <span class="visible-xs"><i class="fa-user"></i></span>
                              <span class="hidden-xs">APP启动配置</span>
                          </a>
                      </li>-->
                    <li>
                        <a href="#messages-3" data-toggle="tab">
                            <span class="visible-xs"><i class="fa-envelope-o"></i></span>
                            <span class="hidden-xs">APP更新配置</span>
                        </a>
                    </li>
                    <li>
                        <a href="#jiguang-3" data-toggle="tab">
                            <span class="visible-xs"><i class="fa-envelope-o"></i></span>
                            <span class="hidden-xs">APP极光推送配置</span>
                        </a>
                    </li>
                    <li>
                        <a href="#settings-3" data-toggle="tab">
                            <span class="visible-xs"><i class="fa-cog"></i></span>
                            <span class="hidden-xs">轻淘客配置</span>
                        </a>
                    </li>
                    <li>
                        <a href="#wechat-3" data-toggle="tab">
                            <span class="visible-xs"><i class="fa-cog"></i></span>
                            <span class="hidden-xs">大微信客配置</span>
                        </a>
                    </li>
                    <li>
                        <a href="#mili-3" data-toggle="tab">
                            <span class="visible-xs"><i class="fa-cog"></i></span>
                            <span class="hidden-xs">迷离团队高佣配置</span>
                        </a>
                    </li>
                    <li>
                        <a href="#search-3" data-toggle="tab">
                            <span class="visible-xs"><i class="fa-cog"></i></span>
                            <span class="hidden-xs">搜索词频过滤</span>
                        </a>
                    </li>
                    <li>
                        <a href="#inbox-3" data-toggle="tab">
                            <span class="visible-xs"><i class="fa-bell-o"></i></span>
                            <span class="hidden-xs">开发中</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="home-3">
                        <div>
                            <form class="save">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="web_url">站点域名</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control check" id="web_url" name="web_url"
                                               value="<?php echo ($content['web_url']); ?>" placeholder="请输入站点域名！">
                                    </div>
                                    <div style="clear: both"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="manage_domain_url">管理后台域名</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control check" id="manage_domain_url"
                                               name="manage_domain_url" value="<?php echo ($content['manage_domain_url']); ?>"
                                               placeholder="请输入管理后台域名！">
                                    </div>
                                    <div style="clear: both"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="api_domain_url">接口域名</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control check" id="api_domain_url"
                                               name="api_domain_url" value="<?php echo ($content['api_domain_url']); ?>"
                                               placeholder="请输入接口域名！">
                                    </div>
                                    <div style="clear: both"></div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="wechat_mp_domain_url">公众号域名</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control check" id="wechat_mp_domain_url"
                                               name="wechat_mp_domain_url" value="<?php echo ($content['wechat_mp_domain_url']); ?>"
                                               placeholder="请输入公众号域名！">
                                    </div>
                                    <div style="clear: both"></div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="with_time">提现时间</label>
                                    <div class="col-sm-8">
                                        <input type="number" class="form-control check" id="with_time"
                                               name="balance_time" value="<?php echo ($content['balance_time']); ?>"
                                               placeholder="请输入每月提现日期！">
                                    </div>
                                    <div style="clear: both"></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-2 "></div>
                                    <div class="col-sm-10">
                                        <input type="submit" class="btn btn-secondary  btn-single" value="保存">
                                    </div>
                                    <div style="clear: both"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane" id="profile-3">
                        <form class="save">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="app-img">图片地址</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="app-img" name="AppLoadImg[url]"
                                           value="<?php echo ($content['AppLoadImg']['url']); ?>" placeholder="请输入图片地址！">
                                </div>
                                <div style="clear: both"></div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2 "></div>
                                <div class="col-sm-10">
                                    <input type="submit" class="btn btn-secondary  btn-single" value="保存">
                                </div>
                                <div style="clear: both"></div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="messages-3">
                        <div class="col-md-6">
                            <!-- Default panel -->
                            <form class="save">
                                <div class="panel panel-default panel-border">
                                    <div class="panel-heading">
                                        Android版本升级配置
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">是否强制更新</label>
                                            <div class="col-sm-8">
                                                <label>
                                                    <input type="radio" name="AppUpdateAndroid[is_force]"
                                                    <?php if(($content['AppUpdateAndroid']['is_force']) == "Y"): ?>checked="checked"<?php endif; ?>
                                                    value="Y" />是
                                                </label>
                                                <label>
                                                    <input type="radio" name="AppUpdateAndroid[is_force]"
                                                    <?php if(($content['AppUpdateAndroid']['is_force']) == "N"): ?>checked="checked"<?php endif; ?>
                                                    value="N" >否
                                                </label>
                                            </div>
                                            <div style="clear: both"></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="an-version">版本号</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control check" id="an-version"
                                                       name="AppUpdateAndroid[version]"
                                                       value="<?php echo ($content['AppUpdateAndroid']['version']); ?>"
                                                       placeholder="请输入版本号！">
                                            </div>
                                            <div style="clear: both"></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="an-url">下载地址</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control check" id="an-url"
                                                       name="AppUpdateAndroid[url]"
                                                       value="<?php echo ($content['AppUpdateAndroid']['url']); ?>"
                                                       placeholder="请输入下载地址！">
                                            </div>
                                            <div style="clear: both"></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="an-content">更新内容</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control check" placeholder="请输入更新内容！"
                                                          name="AppUpdateAndroid[description]" rows="5" id="an-content"><?php echo ($content['AppUpdateAndroid']['description']); ?></textarea>
                                            </div>
                                            <div style="clear: both"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-5"></div>
                                        <div class="col-sm-7">
                                            <input type="submit" class="btn btn-secondary  btn-single" value="保存">
                                        </div>
                                        <div style="clear: both"></div>
                                    </div>
                                    <div style="clear: both"></div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <!-- Bordered panel -->
                            <form class="save">
                                <div class="panel panel-default panel-border">
                                    <div class="panel-heading">
                                        Ios版本升级配置
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">是否强制更新</label>
                                            <div class="col-sm-8">
                                                <label>
                                                    <input type="radio" name="AppUpdateIos[is_force]"
                                                    <?php if(($content['AppUpdateIos']['is_force']) == "Y"): ?>checked="checked"<?php endif; ?>
                                                    value="Y" />是
                                                </label>
                                                <label>
                                                    <input type="radio" name="AppUpdateIos[is_force]"
                                                    <?php if(($content['AppUpdateIos']['is_force']) == "N"): ?>checked="checked"<?php endif; ?>
                                                    value="N" >否
                                                </label>
                                            </div>
                                            <div style="clear: both"></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="ios-version">版本号</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control check" id="ios-version"
                                                       name="AppUpdateIos[version]"
                                                       value="<?php echo ($content['AppUpdateIos']['version']); ?>"
                                                       placeholder="请输入版本号！">
                                            </div>
                                            <div style="clear: both"></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="ios-url">下载地址</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control check" id="ios-url"
                                                       name="AppUpdateIos[url]"
                                                       value="<?php echo ($content['AppUpdateIos']['url']); ?>" placeholder="请输入下载地址！">
                                            </div>
                                            <div style="clear: both"></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="ios-content">更新内容</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control check" placeholder="请输入更新内容！"
                                                          name="AppUpdateIos[description]" rows="5" id="ios-content"><?php echo ($content['AppUpdateIos']['description']); ?></textarea>
                                            </div>
                                            <div style="clear: both"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-5"></div>
                                        <div class="col-sm-7">
                                            <input type="submit" class="btn btn-secondary  btn-single" value="保存">
                                        </div>
                                        <div style="clear: both"></div>
                                    </div>
                                    <div style="clear: both"></div>
                                </div>
                            </form>
                        </div>
                        <div style="clear: both"></div>
                        </form>
                    </div>
                    <div class="tab-pane" id="jiguang-3">
                        <form class="save">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="app-key">AppKey</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control check" id="app-key" name="JPUSH[app_key]"
                                           value="<?php echo ($content['JPUSH']['app_key']); ?>" placeholder="请输入极光推送appKey！">
                                </div>
                                <div style="clear: both"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="secret">Secret</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control check" id="secret" name="JPUSH[app_secret]"
                                           value="<?php echo ($content['JPUSH']['app_secret']); ?>" placeholder="请输入极光推送Secret！">
                                </div>
                                <div style="clear: both"></div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2 "></div>
                                <div class="col-sm-10">
                                    <input type="submit" class="btn btn-secondary  btn-single" value="保存">
                                </div>
                                <div style="clear: both"></div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="settings-3">
                        <form class="save">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">是否启用</label>
                                <div class="col-sm-10">
                                    <div class="form-block">
                                        <label>
                                            <input type="radio" name="QTK[is_use]"
                                            <?php if(($content['QTK']['is_use']) == "Y"): ?>checked="checked"<?php endif; ?>
                                            value="Y" />是
                                        </label>
                                        <label>
                                            <input type="radio" name="QTK[is_use]"
                                            <?php if(($content['QTK']['is_use']) == "N"): ?>checked="checked"<?php endif; ?>
                                            value="N" >否
                                        </label>
                                    </div>
                                </div>
                                <div style="clear: both"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="qtk-url">请求地址</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control check" id="qtk-url" name="QTK[request_url]"
                                           value="<?php echo ($content['QTK']['request_url']); ?>" placeholder="请输入请求地址！">
                                </div>
                                <div style="clear: both"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="qtk-re-url">刷新地址</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control check" id="qtk-re-url"
                                           name="QTK[refresh_url]" value="<?php echo ($content['QTK']['refresh_url']); ?>"
                                           placeholder="请输入刷新地址！">
                                </div>
                                <div style="clear: both"></div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2 "></div>
                                <div class="col-sm-10">
                                    <input type="submit" class="btn btn-secondary  btn-single" value="保存">
                                </div>
                                <div style="clear: both"></div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="wechat-3">
                        <form class="save">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">大微信客cookie</label>
                                <div class="col-sm-10">
                                    <div class="form-block">
                                        <textarea class="form-control check" placeholder="请输入请求cookie！"
                                                  name="DWXK[cookie]" rows="10"><?php echo ($content['DWXK']['cookie']); ?></textarea>
                                    </div>
                                </div>
                                <div style="clear: both"></div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2 "></div>
                                <div class="col-sm-10">
                                    <input type="submit" class="btn btn-secondary  btn-single" value="保存">
                                </div>
                                <div style="clear: both"></div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="mili-3">
                        <form class="save">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">是否启用</label>
                                <div class="col-sm-10">
                                    <div class="form-block">
                                        <label>
                                            <input type="radio" name="MILI[is_use]"
                                            <?php if(($content['MILI']['is_use']) == "Y"): ?>checked="checked"<?php endif; ?>
                                            value="Y" />是
                                        </label>
                                        <label>
                                            <input type="radio" name="MILI[is_use]"
                                            <?php if(($content['MILI']['is_use']) == "N"): ?>checked="checked"<?php endif; ?>
                                            value="N" >否
                                        </label>
                                    </div>
                                </div>
                                <div style="clear: both"></div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2 "></div>
                                <div class="col-sm-10">
                                    <input type="submit" class="btn btn-secondary  btn-single" value="保存">
                                </div>
                                <div style="clear: both"></div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="search-3">
                        <form class="save">
                            <div class="form-group">
                                <label class="col-sm-12 control-label"
                                       for="app-img">用户id<span style="color: red">（多个用户id使用英文逗号隔开）</span></label>
                                <div class="col-sm-10">
                                    <textarea class="form-control check" placeholder="请输入用户id！"
                                              name="SearchUserIds" rows="3"><?php echo ($content['SearchUserIds']); ?></textarea>
                                </div>
                            </div>
                            <div style="clear: both"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-2 "></div>
                        <div class="col-sm-10">
                            <input type="submit" class="btn btn-secondary  btn-single" value="保存">
                        </div>
                        <div style="clear: both"></div>
                    </div>
                    </form>
                </div>
                <div class="tab-pane" id="inbox-3">
                    <p>正在开发中......</p>
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
<script src="/Public/Assets/js/ajaxfileupload.js"></script>
<script>
    $(function () {
        $('input[type=submit]').click(function () {
            var status = true;
            var post_url = "<?php echo U('Config/edit');?>";
            var val = "";

            var _this = $(this);
            $(this).parents('form.save').find('.check').each(function () {
                val = $(this).val();
                val = trim(val);
                if (!val) {
                    $('.alert-danger strong').text($(this).attr('placeholder'));
                    $('.alert-danger').show();
                    setTimeout(function () {
                        $('.alert-danger').hide();
                    }, 3000)
                    status = false;
                    return false;
                }
            });
            if (status) {
                $.post(post_url, _this.parents('form').serialize(), function (res) {
                    if (res.status == 1) {
                        $('.alert-success strong').text(res.info);
                        $('.alert-success').show();
                        setTimeout(function () {
                            $('.alert-success').hide();
                        }, 3000)
                    } else {
                        $('.alert-danger strong').text(res.info);
                        $('.alert-danger').show();
                        setTimeout(function () {
                            $('.alert-danger').hide();
                        }, 3000)
                    }
                })
            }
            return false;
        });
    })

    //异步上传图片
    function ajaxFileUpload(name, trueInput, img, now) {
        //判断是否有选择上传文件
        var imgPath = $('#' + name).val();
        if (!imgPath) {
            setInputClass('order_img_url', 1, "请选择上传图片！");
            return false;
        }
        $.ajaxFileUpload({
            url: "<?php echo U('Common/uploadImgLocal');?>",
            secureuri: false, //是否需要安全协议，一般设置为false
            fileElementId: name, //文件上传域的ID
            dataType: 'json', //返回值类型 一般设置为json
            success: function (data) {
                if (data.code == 1) {
                    InfoMsg(data.code, data.msg);
                    return false;
                } else {
                    $('#' + img).attr({
                        'href': data.url,
                        'class': 'upIMG' + name
                    }).find('img').attr({'src': data.url, 'class': 'col-sm-12 upIMG' + name});
                    $('#' + trueInput).val(data.url);
                    InfoMsg(data.code, data.msg);
                }
            },
            error: function (e) {
                setInputClass('order_img_url', 1, '通讯失败');
            }
        });
    }

    //设置表单提醒与错误
    function InfoMsg(code, msg) {
        if (code == 1) {
            $('#fail').find('strong').text(msg);
            $('#fail').show();
            setTimeout(function () {
                $('#fail').hide();
            }, 3000)
        } else {
            $('#success').find('strong').text(msg);
            $('#success').show();
            setTimeout(function () {
                $('#success').hide();
            }, 3000)
        }

    }

    function trim(str) {
        var result;
        result = str.replace(/(^\s+)|(\s+$)/g, "");
        result = result.replace(/\s/g, "");
        return result;
    }
</script>
</div></html>