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
    </style>
</head>
<body>
<div id="simulator">
    <div id="main">
        <div id="gallery" class="swipe" style="visibility: visible;">
            <div class="swipe-wrap">
                <!--figcaption为图片描述，控制在200字以内-->
                <!--img标签width和height属性须事先给出，由于懒加载的需要，必须把"src"改写为"alt-src" -->
                <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="swiper-slide" data-id="<?php echo ($vo["id"]); ?>" data-sort="<?php echo ($key+1); ?>">
                        <figure>
                            <!-- <a class="up-item" href="javascript:;"
                                onclick="upItemCache(<?php echo ($vo['id']); ?>,$(this))">上移</a>
                             <a class="down-item" href="javascript:;"
                                onclick="downItemCache(<?php echo ($vo['id']); ?>,$(this))">下移</a>-->
                            <?php $temp = json_encode($vo); ?>
                            <a class="up-item" href="javascript:;" onclick="updateItemCache($(this))"
                               data-val='<?php echo ($temp); ?>'>编辑商品</a>
                            <a class="down-item" href="javascript:;" data-id="<?php echo ($vo["id"]); ?>"
                               onclick="delItemCache($(this))">一键删除</a>
                            <div class="img-wrap">
                                <img src="<?php echo ($vo["img"]); ?>">
                                <div class="pswp__price_tag position_left" style="top:20%;left:60%;">
                                    <span>¥<?php echo ($vo["price"]); ?></span>
                                    <i class="dot-con">
                                        <i class="dot-animate"></i>
                                        <i class="dot"></i>
                                    </i>
                                </div>
                            </div>
                            <div class="bottom-bar">
                                <h2>
                                    <?php echo ($vo["title"]); ?>
                                    <a class="direct-link"
                                       href="<?php echo ($vo["tmall_url"]); ?>"
                                       target="_blank">直达链接</a>
                                </h2>
                                <figcaption>
                                    <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong> <?php echo ($vo["describe_info"]); ?>
                                </figcaption>
                            </div>
                        </figure>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>

        <div class="btn-container">
            <span class="prevBtn" onclick="mySwipe.prev();"></span>
            <span class="nextBtn" onclick="mySwipe.next();"></span>
        </div>
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
                        <span class="input-group-addon">商品封面</span>
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
                        <span class="input-group-addon">商品文案<br/><br/><button type="button"
                                                                              class="btn btn-info btn-sm translate">
                            伪原创
                        </button></span>
                        <textarea name="cookie" class="form-control" id="update_info" rows="5"
                                  placeholder="请输入商品文案"></textarea>
                    </div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon">商品排序</span>
                        <input type="number" class="form-control" name="sort" id="sort"
                               placeholder="请输入商品排序（数值越小越靠前1-10）"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="update_id" id="update_id"/>
                    <input type="hidden" name="update_img" id="update_img"/>
                    <input type="hidden" name="update_img_key" id="update_img_key"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" id="update">修改</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script type="text/javascript">
    var num = "<?php echo count($data);?>";

    $(function () {
        var upload_url = "<?php echo U('uploadTopImg');?>";
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
                        $('#update_img').val(res.info.url);
                        $('#update_img_key').val(res.info.img_key);
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
                obj.parents('.swiper-slide').remove();
            }
        })
    }

    $('#update').click(function () {
        var _this = $(this);
        var id = $('#update_id').val();
        var content = $('#update_info').val();
        var img_url = $('#update_img').val();
        var img_key = $('#update_img_key').val();
        var sort = $('#sort').val();
        if (!img_url || !img_key) {
            layer.msg('图片信息有误，请上传后修改！');
            return false;
        }
        if (!content) {
            layer.msg(' 请输入商品文案！');
            return false;
        }
        _this.attr('disabled', true);
        $.post("<?php echo U('updateItemCache');?>", {
            id: id,
            content: content,
            img_url: img_url,
            img_key: img_key,
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
    /*    function upItemCache(id, obj) {
     var index = obj.parents('.swiper-slide').index();
     var sort = obj.parents('.swiper-slide').data('sort');
     if (index == 0) {
     layer.msg('已经是第一位了，不能上移了');
     return false;
     }
     obj.attr('disabled', true);
     var other_id = obj.parents('#gallery').find('.swiper-slide').eq(index - 1).data('id');
     $.get("<?php echo U('moveItemCache');?>", {id: id, other_id: other_id, type: '-'}, function (res) {
     layer.msg(res.info);
     if (res.status == 1) {
     obj.parents('.swiper-slide').attr('data-srot', sort - 1);
     obj.parents('.swiper-slide').find('strong').text(sort - 1 + '/' + num)
     obj.parents('#gallery').find('.swiper-slide').eq(index - 1).find('strong').text(sort + '/' + num)
     obj.parents('#gallery').find('.swiper-slide').eq(index - 1).prev().before(clone);
     obj.parents('.swiper-slide').remove();
     }
     obj.removeAttr('disabled');
     });
     }
     function downItemCache(id, obj) {
     var index = obj.parents('.swiper-slide').index();
     var sort = obj.parents('.swiper-slide').data('sort');
     if (index == num - 1) {
     layer.msg('已经是最后一位了，不能下移了');
     return false;
     }
     obj.attr('disabled', true);
     var other_id = obj.parents('#gallery').find('.swiper-slide').eq(index + 1).data('id');
     $.get("<?php echo U('moveItemCache');?>", {id: id, other_id: other_id, type: '+'}, function (res) {
     layer.msg(res.info);
     if (res.status == 1) {
     obj.parents('.swiper-slide').attr('data-srot', sort + 1);
     obj.parents('.swiper-slide').find('strong').text(sort + 1 + '/' + num);
     obj.parents('#gallery').find('.swiper-slide').eq(index + 1).find('strong').text(sort + '/' + num)
     var clone = obj.parents('.swiper-slide').clone(true);
     obj.parents('#gallery').find('.swiper-slide').eq(index).next().after(clone);
     obj.parents('.swiper-slide').remove();
     }
     obj.removeAttr('disabled');
     });
     }*/

    function updateItemCache(obj) {
        var item = obj.data('val');
        $('#covers-img').attr('src', item.img);
        $('#update_info').val(item.describe_info);
        $('#update_id').val(item.id);
        $('#update_img').val(item.img);
        $('#update_img_key').val(item.json_data.uri);
        $('#sort').val(item.sort);
        $('#update-modal').modal('show');
    }

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