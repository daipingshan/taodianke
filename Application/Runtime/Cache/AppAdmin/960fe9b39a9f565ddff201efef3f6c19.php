<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="Xenon Boostrap Admin Panel" />
	<meta name="author" content="" />
	
	<title>商品管理</title>

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
                <h1 class="title">商品列表</h1>
                <p class="description">显示所有商品订单列表</p>
            </div>
            <div class="breadcrumb-env">
                <ol class="breadcrumb bc-1">
                    <li><a href="/AppAdmin/"><i class="fa-home"></i>主页</a></li>
                    <li class="active"> 商品管理</li>
                    <li class="active"><strong>商品列表</strong></li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="panel panel-default col-sm-12">
                <div class="panel-heading">
                    <h3 class="panel-title">搜索商品</h3>
                    <div class="panel-options">
                        <a href="#" data-toggle="panel">
                            <span class="collapse-icon">–</span>
                            <span class="expand-icon">+</span>
                        </a>
                        <a href="#" data-toggle="remove">
                            ×
                        </a>
                    </div>
                </div>
                <form method="get">
                    <div class="panel-body">
                        <div class="row col-margin">
                            <div class="col-xs-2">
                                <input type="text" name="title" value="<?php echo I('get.title');?>" class="form-control"
                                       placeholder="商品名称"/>
                            </div>
                            <div class="col-xs-2">
                                <input type="text" name="num_iid" value="<?php echo I('get.num_iid');?>" class="form-control"
                                       placeholder="商品编号"/>
                            </div>
                            <div class="col-sm-2">
                                <select name="jing" class="form-control">
                                    <option value="0" <?php if(I('get.jing') == 0): ?>selected<?php endif; ?>>选择是否精选</option>
                                    <option value="1" <?php if(I('get.jing') == 1): ?>selected<?php endif; ?>>精选商品</option>
                                    <option value="2" <?php if(I('get.jing') == 2): ?>selected<?php endif; ?>>非精选商品</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select name="cate_id" class="form-control">
                                    <option value="0">选择分类</option>
                                    <?php if(is_array($cate)): foreach($cate as $k=>$v): ?><option value="<?php echo ($k); ?>"
                                        <?php if(I('get.cate_id') == $k): ?>selected<?php endif; ?>
                                        ><?php echo ($v); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>

                            <div class="col-sm-2">
                                <select name="shop_type" class="form-control">
                                    <option value="0">选择商品来源</option>
                                    <?php if(is_array($shop_type)): foreach($shop_type as $k=>$v): ?><option value="<?php echo ($k); ?>"
                                        <?php if(I('get.shop_type') == $k): ?>selected<?php endif; ?>
                                        ><?php echo ($v); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>

                            <div class="col-xs-1">
                                <input type="submit" class="btn btn-secondary btn-single" value="搜索"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-12">
            <div class="alert alert-success" style="display: none">
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <strong></strong>
            </div>
        </div>
        <div class="col-md-12">
            <div class="alert alert-danger" style="display: none;">
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <strong></strong>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">商品列表 - <?php echo ($count); ?> 件商品</h3>
                        <div class="panel-options"><a href="#"> <i class="linecons-cog"></i> </a>
                            <a href="#" data-toggle="panel"> <span class="collapse-icon">&ndash;</span>
                                <span class="expand-icon">+</span> </a> <a href="#" data-toggle="reload">
                                <i class="fa-rotate-right"></i> </a> <a href="#" data-toggle="remove"> &times; </a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table cellspacing="0" class="table table-small-font table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th width="10%">商品图片</th>
                                    <th width="6%">商品分类</th>
                                    <th width="30%">商品名称 / 领券地址 / 月销量 / 来源</th>
                                    <th width="15%">价格</th>
                                    <th width="15%">时间</th>
                                    <th width="10%">计划 / 排序</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr>
                                        <td><img src="<?php echo ($vo["pic_url"]); ?>" width="80" hidden="80" class="goodslist"/></td>
                                        <td><?php echo ($cate[$vo['cate_id']]); ?></td>
                                        <td>
                                            <?php if($vo["shop_type"] == 'J'): ?><a href="<?php echo ($vo['item_url']); ?>"
                                               target="_blank" style="color: #00A0E9;"><?php echo ($vo["title"]); ?></a>
                                            <?php else: ?>
                                                <a href="https://detail.tmall.com/item.htm?id=<?php echo ($vo['num_iid']); ?>"
                                               target="_blank" style="color: #00A0E9;"><?php echo ($vo["title"]); ?></a><?php endif; ?>

                                            <br/>
                                            <a href="<?php echo ($vo['click_url']); ?>" target="_blank"
                                               style="color: firebrick">领券地址</a><br>
                                            月销量：<?php echo ($vo["volume"]); ?><br>
                                            商品来源：
                                            <?php switch($vo["shop_type"]): case "B": ?>天猫<?php break;?>
                                                <?php case "C": ?>淘宝<?php break;?>
                                                <?php case "D": ?>京东<?php break;?>
                                                <?php case "J": ?>楚楚街<?php break;?>
                                                <?php default: ?>
                                                未知<?php endswitch;?>
                                        </td>

                                        <td style="color:red;">
                                            券后价：￥<?php echo ($vo['coupon_price']); ?><br/>
                                            优惠券金额：￥<?php echo ($vo['quan']); ?><br/>
                                            佣金：￥<?php echo ($vo['commission']); ?>
                                        </td>
                                        <td>
                                            券开始时间：<?php echo ($vo["starttime"]); ?><br>
                                            券结束时间：<?php echo ($vo["endtime"]); ?><br>
                                            <?php if($vo['handpick_time'] > 0): ?>精选时间：<?php echo (date("Y-m-d H:i:s",$vo['handpick_time'])); ?><br/><br/><?php endif; ?>
                                        </td>
                                        <td>
                                            <?php switch($vo["uname"]): case "wfa": ?>定向<?php break;?>
                                                <?php case "tongyong": ?>通用<?php break;?>
                                                <?php case "queqiao": ?>鹊桥<?php break;?>
                                                <?php default: ?>
                                                未知<?php endswitch;?>
                                            <br/><br/><?php echo ($vo["ordid"]); ?>
                                        </td>
                                        <td>
                                            <?php if($vo['handpick_time'] == 0): ?><a href="javascript:;" onclick="isPush(<?php echo ($vo['id']); ?>, '<?php echo ($vo['title']); ?>');" >
                                                    <button class="btn btn-secondary"><font><font>设置精选</font></font>
                                                    </button>
                                                </a>
                                                <?php else: ?>
                                                <a href="javascript:;" onclick="isPush(<?php echo ($vo['id']); ?>, '<?php echo ($vo['title']); ?>');">
                                                    <button class="btn btn-warning"><font><font>更新精选</font></font>
                                                    </button>
                                                </a>
                                                <a href="<?php echo U('Items/setHandpick',array('id'=>$vo['id'],'type'=>'N'));?>">
                                                    <button class="btn " style="background-color: #00A0E9;"><font style="color:white"><font>取消精选</font></font>
                                                    </button>

                                                </a><?php endif; ?>
                                            <?php switch($vo["shop_type"]): case "B": ?><button class="btn" data-clipboard-text="taodianke://Item/detail?id=<?php echo ($vo['num_iid']); ?>&type=tdk&V=2">复制URL</button><?php break;?>
                                                <?php case "C": ?><button class="btn" data-clipboard-text="taodianke://Item/detail?id=<?php echo ($vo['num_iid']); ?>&type=tdk&V=2">复制URL</button><?php break;?>
                                                <?php case "D": ?><button class="btn" data-clipboard-text="taodianke://Item/detail?id=<?php echo ($vo['num_iid']); ?>&type=jd&V=2">复制URL</button><?php break;?>
                                                <?php case "J": ?><button class="btn" data-clipboard-text="taodianke://Item/detail?id=<?php echo ($vo['num_iid']); ?>&type=chuchujie&V=2">复制URL</button><?php break;?>
                                                <?php default: ?>
                                                未知<?php endswitch;?>
                                            <a href="javascript:;" onclick="delItems(<?php echo ($vo['num_iid']); ?>);">
                                                <button class="btn" style="background-color:#fbc404;margin-left: 0;"><font style="color:white"><font>删除商品</font></font>
                                                </button>
                                            </a>


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
    <script src="/Public/Assets/js/clipboard.min.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        var btns = document.querySelectorAll('button');
        var clipboard = new Clipboard(btns);

        clipboard.on('success', function(e) {
            $('.alert-success strong').text('恭喜您，复制成功！');
            $('.alert-success').show();
            setTimeout(function(){
                $('.alert-success').hide();
            },3000);
        });

        clipboard.on('error', function(e) {
            $('.alert-danger strong').text('复制失败！');
            $('.alert-danger').show();
            setTimeout(function(){
                $('.alert-danger').hide();
            },3000);
        });

    </script>
    <script type="text/javascript">
        function delItems(num_iid){
            $res = confirm('确定删除该商品');
            if ($res) {
                var post_url = "<?php echo U('Items/delItems');?>";
                var index = "<?php echo U('Items/index');?>";
                $.post(post_url, {'num_iid':num_iid}, function(res){
                    if(res.status == 1){
                        $('.alert-success strong').text(res.info);
                        $('.alert-success').show();
                        setTimeout(function(){
                            $('.alert-success').hide();
                        },3000);
                        window.location.href = index;
                    }else{
                        $('.alert-danger strong').text(res.info);
                        $('.alert-danger').show();
                        setTimeout(function(){
                            $('.alert-danger').hide();
                        },3000);

                    }
                })
                return true;
            }else{
                return false;
            }
        }
    </script>