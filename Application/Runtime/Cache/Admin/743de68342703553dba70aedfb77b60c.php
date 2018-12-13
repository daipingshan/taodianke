<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!-- saved from url=(0098)http://kolplatform.jinritemai.com/index/myarticle/previewarticle?id=4891607&sig=1512525770338.7427 -->
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta id="viewport" name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,minimal-ui">
    <!--页面title-->
    <title>预览文章</title>
    <!--预览页面PC -->
    <link href="http://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <style>@import url("//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc2/css/bootstrap-glyphicons.css");</style>
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/previewgallery.css?v=<?php echo time();?>">
    <link rel="stylesheet" href="/Public/Admin/css/layer.css">
    <link rel="stylesheet" href="/Public/Admin/js/layui/css/layui.css" media="all">
    <script src="/Public/Admin/js/jquery.min.js"></script>
    <script src="/Public/Admin/js/swipe.js"></script>
    <script src="/Public/plugins/bower_components/layer/layer.js"></script>
    <script src="http://cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="/Public/Admin/js/layui/layui.js"></script>
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

        #simulator {
            float: left;
            margin-left: 100px;
        }

        #cate {
            float: right;
            margin-right: 100px;
            margin-top: 50px;
        }
    </style>
</head>
<body>
<div>
    <div id="simulator">
        <div id="main">
            <div id="gallery" class="swipe" style="visibility: visible;">
                <div class="swipe-wrap">
                    <!--figcaption为图片描述，控制在200字以内-->
                    <!--img标签width和height属性须事先给出，由于懒加载的需要，必须把"src"改写为"alt-src" -->
                    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['type'] == 'img'): ?><div class="swiper-slide num-<?php echo ($key); ?>" data-id="<?php echo ($vo["shop_goods_id"]); ?>">
                                <figure>
                                    <?php $temp = json_encode($vo); ?>
                                    <a class="up-item" href="javascript:;" onclick="updateImgCache($(this))"
                                       data-val='<?php echo ($temp); ?>'>编辑图集</a>
                                    <a class="down-item" href="javascript:;" data-id="<?php echo ($vo['shop_goods_id']); ?>"
                                       onclick="delItemCache($(this))">一键删除</a>
                                    <div class="img-wrap">
                                        <img src="<?php echo ($vo["img"]); ?>">
                                    </div>
                                    <div class="bottom-bar">
                                        <figcaption>
                                            <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong> <?php echo ($vo["description"]); ?>
                                        </figcaption>
                                    </div>
                                </figure>
                            </div>
                            <?php else: ?>
                            <div class="swiper-slide num-<?php echo ($key); ?>" data-id="<?php echo ($vo["shop_goods_id"]); ?>">
                                <figure>
                                    <?php $temp = json_encode($vo); ?>
                                    <?php $temp_son = $vo['attached_imgs']; ?>
                                    <a class="up-item" href="javascript:;" onclick="updateItemCache($(this))"
                                       data-val='<?php echo ($temp); ?>' data-son-img="<?php echo ($temp_son[0]['img']); ?>"
                                       data-son-info="<?php echo ($temp_son[0]['description']); ?>">编辑商品</a>
                                    <a class="down-item" href="javascript:;" data-id="<?php echo ($vo['shop_goods_id']); ?>"
                                       onclick="delItemCache($(this))">一键删除</a>
                                    <div class="img-wrap">
                                        <img src="<?php echo ($vo["img"]); ?>">
                                        <div class="pswp__price_tag position_left"
                                             style="top:20%;left:60%;">
                                            <span>¥<?php echo ($vo["price"]); ?></span>
                                            <i class="dot-con">
                                                <i class="dot-animate"></i>
                                                <i class="dot"></i>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="bottom-bar">
                                        <h2>
                                            <a class="direct-link"
                                               href="<?php echo ($vo["real_url"]); ?>"
                                               target="_blank">直达链接</a>
                                        </h2>
                                        <figcaption>
                                            <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong> <?php echo ($vo["description"]); ?>--<?php echo ($vo["sec_cid"]); ?>
                                        </figcaption>
                                    </div>
                                </figure>
                            </div>
                            <div class="swiper-slide num-<?php echo ($key); ?>" data-id="<?php echo ($vo["shop_goods_id"]); ?>">
                                <figure>
                                    <?php $temp_son = $vo['attached_imgs']; ?>
                                    <div class="img-wrap">
                                        <img src="<?php echo ($temp_son[0]["img"]); ?>">
                                    </div>
                                    <div class="bottom-bar">
                                        <h2>
                                            <a class="direct-link"
                                               href="<?php echo ($vo["real_url"]); ?>"
                                               target="_blank">直达链接</a>
                                        </h2>
                                        <figcaption>
                                            <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong>
                                            <?php echo ($temp_son[0]["description"]); ?>--<?php echo ($vo["sec_cid"]); ?>
                                        </figcaption>
                                    </div>
                                </figure>
                            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </div>

            <div class="btn-container">
                <span class="prevBtn" onclick="mySwipe.prev();"></span>
                <span class="nextBtn" onclick="mySwipe.next();"></span>
            </div>
        </div>
    </div>
    <div id="cate">
        <?php if(is_array($cate_data)): $i = 0; $__LIST__ = $cate_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cate): $mod = ($i % 2 );++$i;?><h3><?php echo ($cate['name']); ?>-【<?php echo ($cate['num']); ?>】</h3><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>
<div class="modal fade" id="update-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">编辑商品</h4>
            </div>
            <form class="bs-example bs-example-form" role="form">
                <div class="modal-body">
                    <div class="input-group">
                        <span class="input-group-addon">商品标题</span>
                        <input type="text" class="form-control" name="name" id="update-name"
                               placeholder="请输入商品标题"/>
                    </div>
                    <br>
                    <div style="float:left;width: 48%">
                        <div class="input-group">
                            <span class="input-group-addon">主图封面</span>
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
                            <span class="input-group-addon">主图文案<br/><br/><button type="button"
                                                                                  class="btn btn-info btn-sm translate">
                                伪原创
                            </button></span>
                        <textarea name="info" class="form-control" id="update-info" rows="5"
                                  placeholder="请输入商品文案"></textarea>
                        </div>
                    </div>
                    <div style="float:right;width: 48%">
                        <div class="input-group">
                            <span class="input-group-addon">副图封面</span>
                            <div class="covers-img">
                                <img id="covers-son-img"/>
                            </div>
                            <div class="layui-upload-drag" id="upload-son">
                                <i class="layui-icon"></i>
                                <p>点击上传，或将文件拖拽到此处</p>
                            </div>
                        </div>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon">副图文案<br/><br/><button type="button"
                                                                                  class="btn btn-info btn-sm translate">
                                伪原创
                            </button></span>
                        <textarea name="info" class="form-control" id="update-son-info" rows="5"
                                  placeholder="请输入商品文案"></textarea>
                        </div>
                    </div>
                    <div style="clear: both"></div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon">商品排序</span>
                        <input type="number" class="form-control" name="sort" id="sort"
                               placeholder="请输入商品排序（数值越小越靠前1-10）"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="update_id" id="update-id"/>
                    <input type="hidden" name="update_img" id="update-img"/>
                    <input type="hidden" name="update_son_img" id="update-son-img"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" id="update">修改</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<div class="modal fade" id="update-img-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">编辑图集</h4>
            </div>
            <form class="bs-example bs-example-form" role="form">
                <div class="modal-body">
                    <div class="input-group">
                        <span class="input-group-addon">图集封面</span>
                        <div class="covers-img">
                            <img id="img" src="/Public/Admin/images/default.jpg"/>
                        </div>
                        <div class="layui-upload-drag" id="img-upload">
                            <i class="layui-icon"></i>
                            <p>点击上传，或将文件拖拽到此处</p>
                        </div>
                    </div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon">图集文案<br/><br/><button type="button"
                                                                              class="btn btn-info btn-sm translate">
                            伪原创
                        </button></span>
                            <textarea name="cookie" class="form-control" id="img-info" rows="5"
                                      placeholder="请输入商品文案"></textarea>
                    </div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon">图集排序</span>
                        <input type="number" class="form-control" name="sort" id="img-sort"
                               placeholder="请输入商品排序（数值越小越靠前1-10）"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="update_id" id="img-id"/>
                    <input type="hidden" name="update_img" id="img-update-img"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" id="img-update-btn">修改</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script type="text/javascript">
    var num = "<?php echo count($data);?>";

    $(function () {
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
                        $('#update-img').val(res.info.url);
                        layer.msg('上传成功！');
                    } else {
                        layer.msg(res.info);
                    }
                }
            });
            upload.render({
                elem: '#upload-son'
                , size: 5 * 1024 * 1024 //限制文件大小，单位 KB
                , url: upload_url
                , accept: 'file' //普通文件
                , exts: 'jpg|png|jpeg|gif' //只允许上传压缩文件
                , done: function (res) {
                    if (res.status == 1) {
                        $('#covers-son-img').attr('src', res.info.url);
                        $('#update-son-img').val(res.info.url);
                        layer.msg('上传成功！');
                    } else {
                        layer.msg(res.info);
                    }
                }
            });

            upload.render({
                elem: '#img-upload'
                , size: 5 * 1024 * 1024 //限制文件大小，单位 KB
                , url: upload_url
                , accept: 'file' //普通文件
                , exts: 'jpg|png|jpeg|gif' //只允许上传压缩文件
                , done: function (res) {
                    if (res.status == 1) {
                        $('#img').attr('src', res.info.url);
                        $('#img-update-img').val(res.info.url);
                        layer.msg('上传成功！');
                    } else {
                        layer.msg(res.info);
                    }
                }
            });
        });
        if ($('#gallery').find('.swipe-wrap').children().length) {
            window.mySwipe = $('#gallery').Swipe({
                startSlide: 0,
                // speed: 400,
                // auto: 10000,
                continuous: false,
                disableScroll: false,
                stopPropagation: false,
                callback: function (index, elem) {
                },
                transitionEnd: function (index, elem) {
                }
            }).data('Swipe');
        }

        $('#gallery').hover(function (e) {
            // e.preventDefault();

        }, function (e) {
            // e.preventDefault();

        });
    });
    function delItemCache(obj) {
        $.post("<?php echo U('delItemCache');?>", {id: obj.data('id')}, function (res) {
            layer.msg(res.info);
            if (res.status == 1) {
                num = num - 1;
                parent.$('#cart .cart-num').text(num);
                location.reload();
            }
        })
    }

    $('#update').click(function () {
        var _this = $(this);
        var id = $('#update-id').val();
        var name = $('#update-name').val();
        var img = $('#update-img').val();
        var son_img = $('#update-son-img').val();
        var info = $('#update-info').val();
        var son_info = $('#update-son-info').val();
        var sort = $('#sort').val();
        if (!name) {
            layer.msg('请输入商品标题！');
            return false;
        }
        if (!img) {
            layer.msg('主图信息有误，请上传后修改！');
            return false;
        }
        if (!son_img) {
            layer.msg('副图信息有误，请上传后修改！');
            return false;
        }
        if (!info) {
            layer.msg(' 请输入主图文案！');
            return false;
        }
        if (!son_info) {
            layer.msg(' 请输入副图文案！');
            return false;
        }
        _this.attr('disabled', true);
        $.post("<?php echo U('updateItemCache');?>", {
            id: id,
            name: name,
            img: img,
            son_img: son_img,
            info: info,
            son_info: son_info,
            sort: sort,
        }, function (res) {
            layer.msg(res.info);
            if (res.status == 1) {
                setTimeout(function () {
                    window.location.reload();
                }, 2000)
            } else {
                _this.removeAttr('disabled');
            }
        })
    })

    function updateItemCache(obj) {
        var item = obj.data('val');
        var son_img = obj.data('son-img');
        var son_info = obj.data('son-info');
        $('#update-name').val(item.name);
        $('#covers-img').attr('src', item.img);
        $('#covers-son-img').attr('src', son_img);
        $('#update-info').val(item.description);
        $('#update-son-info').val(son_info);
        $('#update-name').val(item.name);
        $('#sort').val(item.sort);
        $('#update-id').val(item.shop_goods_id);
        $('#update-son-img').val(son_img);
        $('#update-img').val(item.img);
        $('#update-modal').modal('show');
    }


    function updateImgCache(obj) {
        var img = obj.data('val');
        $('#img').attr('src', img.img);
        $('#img-info').val(img.description);
        $('#img-id').val(img.shop_goods_id);
        $('#img-sort').val(img.sort);
        $('#img-update-img').val(img.img);
        $('#update-img-modal').modal('show');
    }

    $('#img-update-btn').click(function () {
        var _this = $(this);
        var id = $('#img-id').val();
        var img = $('#img-update-img').val();
        var info = $('#img-info').val();
        var sort = $('#img-sort').val();
        if (!img) {
            layer.msg('图集信息有误，请上传后修改！');
            return false;
        }
        if (!info) {
            layer.msg(' 请输入图集文案！');
            return false;
        }
        _this.attr('disabled', true);
        $.post("<?php echo U('updateImgCache');?>", {
            id: id,
            img: img,
            info: info,
            sort: sort,
        }, function (res) {
            layer.msg(res.info);
            if (res.status == 1) {
                setTimeout(function () {
                    window.location.reload();
                }, 2000)
            } else {
                _this.removeAttr('disabled');
            }
        })
    })

    $('.translate').click(function () {
        var _this = $(this);
        var content = _this.parents('.input-group').find('textarea').val();
        if (!content) {
            layer.msg('文案内容不能为空！');
            return false;
        }
        $.post("<?php echo U('translate');?>", {content: content}, function (res) {
            layer.msg(res.info.msg)
            if (res.status == 1) {
                _this.parents('.input-group').find('textarea').val(res.info.content);
            }
        });
    })
</script>