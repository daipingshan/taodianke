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
<link rel="stylesheet" href="/Public/Admin/js/layui/css/layui.css" media="all">
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
<style>
    .covers-img {
        float: left;
    }

    .layui-upload-drag {
        float: right;
    }

    .covers-img img {
        margin-left: 20px;
        width: 135px;
    }
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
        <div class="col-lg-8">
            <h1 class="page-header">非商品图集</h1>
        </div>
        <div class="col-lg-4" style="margin-top: 45px">
            <button class="btn btn-danger" id="add-img-open">添加图集</button>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form>
                <div class="col-lg-2">
                    <input type="text" class="form-control" name='keyword' value="<?php echo I('get.keyword');?>"
                           placeholder="关键字查询">
                </div>
                <div class="col-lg-3">
                    <select name="cate_id" class="form-control">
                        <option value="">请选择分类</option>
                        <?php if(is_array($img_cate)): foreach($img_cate as $k=>$row): ?><option value="<?php echo ($k); ?>"
                            <?php if(I('get.cate_id') == $k): ?>selected<?php endif; ?>
                            ><?php echo ($row); ?></option><?php endforeach; endif; ?>
                    </select>
                </div>
                <?php if(!empty($user_data)): ?><div class="col-lg-3">
                        <select name="user_id" class="form-control">
                            <option value="">请选择用户</option>
                            <?php if(is_array($user_data)): foreach($user_data as $key=>$row): ?><option value="<?php echo ($row["id"]); ?>"
                                <?php if(I('get.user_id') == $row['id']): ?>selected<?php endif; ?>
                                ><?php echo ($row["name"]); ?></option><?php endforeach; endif; ?>
                        </select>
                    </div><?php endif; ?>
                <div>
                    <button type="submit" class="btn btn-danger">查询</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><div class="col-lg-12" style="margin-top: 20px">
                <?php if(is_array($row)): $i = 0; $__LIST__ = $row;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-lg-3 box">
                        <div class="img" style="position: relative">
                            <img class="lazy" data-original="<?php echo ($vo["img"]); ?>">
                            <div class="intro">
                                <?php echo ($vo["description"]); ?>
                            </div>
                            <?php if(!empty($user_data)): ?><button class="btn btn-danger btn-small" data-id="<?php echo ($vo["id"]); ?>" onclick="delImg($(this))"
                                        style="top:20px;right: 20px;position: absolute">删除
                                </button><?php endif; ?>
                        </div>
                        <div class="content">
                            <div class="col-lg-12 add-btn" style="margin: 20px 0;position: relative">
                                <?php if(($vo['is_add']) == "1"): ?><button class="btn btn-success" style="width: 100%">已添加至选品库</button>
                                    <?php else: ?>
                                    <div class="col-lg-6">
                                        <button class="btn btn-danger addItemCache" style="width: 100%;font-size: 12px"
                                                data-id="<?php echo ($vo["id"]); ?>" data-val='<?php echo ($vo["post_data"]); ?>' data-type="0">一键添加
                                        </button>
                                    </div>
                                    <div class="col-lg-6">
                                        <button class="btn btn-danger addItemCache" style="width: 100%;font-size: 12px"
                                                data-id="<?php echo ($vo["id"]); ?>" data-val='<?php echo ($vo["post_data"]); ?>' data-type="1">伪原创
                                        </button>
                                    </div><?php endif; ?>
                            </div>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <div class="row" style="margin: 20px 0 50px 0">
        <ul class="pagination" style="width: 100%">
            <?php echo ($page); ?>
        </ul>
    </div>
    <div class="bottom-position">
        <div class="col-lg-3">
            <div class="cart" id="cart" onclick="openCart()">
                <a href="javascript:;" class="cart-icon"></a>
                <a href="javascript:;" class="cart-num"><?php echo ($cart_count); ?></a>
            </div>
        </div>
        <div class="col-lg-3">
            <button class="btn btn-success" style="margin: 25px 30%;width: 40%" onclick="saveCart()">一键保存</button>
        </div>
        <div class="col-lg-3">
            <button class="btn btn-danger" style="margin: 25px 30%;width: 40%" onclick="openCart()">一键预览</button>
        </div>
        <div class="col-lg-3">
            <div id="back-to-top" class="iton-top-index" style="margin: 25px auto">
                <a href="#"></a>
            </div>
        </div>
    </div>
</div>
<div id="add-img" style="display: none">
    <form class="bs-example bs-example-form" role="form">
        <div class="modal-body">
            <div class="input-group">
                <span class="input-group-addon">图集分类</span>
                <select name="cate_id" id="cate_id" class="form-control">
                    <option value="">请选择分类</option>
                    <?php if(is_array($img_cate)): foreach($img_cate as $k=>$row): ?><option value="<?php echo ($k); ?>"><?php echo ($row); ?></option><?php endforeach; endif; ?>
                </select>
            </div>
            <br>
            <div class="input-group">
                <span class="input-group-addon">图集封面</span>
                <div class="covers-img">
                    <img id="covers-img"/>
                </div>
                <div class="layui-upload-drag" id="upload">
                    <i class="layui-icon"></i>
                    <p>点击上传，或将文件拖拽到此处</p>
                </div>
            </div>
            <br>
            <div class="input-group">
                <span class="input-group-addon">图集文案</span>
                        <textarea name="cookie" class="form-control" id="info" rows="5"
                                  placeholder="请输入商品文案"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="update_img" id="img"/>
            <button type="button" class="btn btn-primary" id="add">添加</button>
        </div>
    </form>
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
<script src="/Public/Admin/js/layui/layui.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var is_add = true
        $("img.lazy").lazyload({effect: "fadeIn"});
        $('#add-img-open').click(function () {
            layer.open({
                title: '添加图集',
                type: 1,
                area: ['600px', '500px'],
                fix: true,
                maxmin: true,
                content: $('#add-img'),
            })
        });
        var upload_url = "<?php echo U('uploadSaleImg');?>";
        layui.use('upload', function () {
            var $ = layui.jquery
                    , layer = layui.layer
                    , upload = layui.upload;
            //拖拽上传
            upload.render({
                elem: '#upload'
                , size: 5 * 1024 * 1024 //限制文件大小，单位 KB
                , url: upload_url
                , accept: 'file' //普通文件
                , exts: 'jpg|png|jpeg|gif' //只允许上传压缩文件
                , done: function (res) {
                    if (res.status == 1) {
                        $('#covers-img').attr('src', res.info.url);
                        $('#img').val(res.info.url);
                        layer.msg('上传成功！');
                    } else {
                        layer.msg(res.info);
                    }
                }
            });
        });
        $('#add').click(function () {
            var _this = $(this);
            var cate_id = $('#cate_id option:selected').val();
            var img = $('#img').val();
            var info = $('#info').val();
            if (!cate_id) {
                layer.msg('请选择图集分类！');
                return false;
            }
            if (!img) {
                layer.msg('图集封面有误，请上传后添加！');
                return false;
            }
            if (!info) {
                layer.msg('请输入图集文案！');
                return false;
            }
            _this.attr('disabled', true);
            var url = "<?php echo U('addImg');?>";
            $.post(url, {cate_id: cate_id, img: img, info: info}, function (res) {
                layer.msg(res.info);
                if (res.status == 1) {
                    setTimeout(function () {
                        location.reload();
                    }, 2000)
                }
            })
        });

        $('.addItemCache').click(function () {
            if (is_add === false) {
                layer.msg('正在请求中...，操作太频繁，请稍后再试！');
                return false;
            }
            var item_num = parseInt($('#cart .cart-num').text());
            if (item_num >= 10) {
                layer.msg('最多只能添加10件商品！');
                return false;
            }
            var _this = $(this);
            var text = _this.text();
            var item_id = $(this).data('id');
            var post_data = $(this).data('val');
            var type = $(this).data('type');
            var url = "<?php echo U('addImgCache');?>";
            is_add = false;
            $.post(url, {id: item_id, post_data: post_data, type: type}, function (res) {
                if (res.status == 1) {
                    var img = _this.parents('.box').find('.img img');
                    flyCart(img);
                    $('#cart .cart-num').text(item_num + 1);
                    _this.removeClass('addItemCache btn-danger').addClass('btn-success').text('添加成功');
                    _this.unbind();
                } else {
                    layer.msg(res.info);
                    _this.text(res.info);
                    setTimeout(function () {
                        _this.text(text);
                    }, 3000)
                }
                is_add = true;
            });
        });

        function flyCart(imgtodrag) {
            var cart = $('#cart');
            if (imgtodrag) {
                var imgclone = imgtodrag.clone()
                        .offset({
                            top: imgtodrag.offset().top,
                            left: imgtodrag.offset().left
                        })
                        .css({
                            'opacity': '0.5',
                            'position': 'absolute',
                            'height': '150px',
                            'width': '150px',
                            'z-index': '100'
                        })
                        .appendTo($('body'))
                        .animate({
                            'top': cart.offset().top + 10,
                            'left': cart.offset().left + 10,
                            'width': 75,
                            'height': 75
                        }, 1000, 'easeInOutExpo');

                imgclone.animate({
                    'width': 0,
                    'height': 0
                }, function () {
                    $(this).detach()
                });
            }
        }
    });

    function openCart() {
        var num = parseInt($('#cart .cart-num').text());
        if (num == 0) {
            layer.msg('请先添加商品后再预览！');
            return false;
        }
        layer.open({
            title: '预览文章',
            type: 2,
            area: ['800px', '670px'],
            fix: true,
            maxmin: true,
            content: "<?php echo U('cartList');?>",
        })
    }


    function saveCart() {
        var num = parseInt($('#cart .cart-num').text());
        if (num == 0) {
            layer.msg('请先添加商品后再保存！');
            return false;
        }
        layer.open({
            title: '保存文章',
            type: 2,
            area: ['1000px', '670px'],
            fix: true,
            maxmin: true,
            content: "<?php echo U('saveCart');?>",
        });
    }

    function delImg(obj) {
        var id = obj.data('id');
        $.get("<?php echo U('delImg');?>", {id: id}, function (res) {
            layer.msg(res.info);
            if (res.status == 1) {
                setTimeout(function () {
                    location.reload();
                }, 2000)
            }
        })
    }


    $(function () {
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