<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!-- saved from url=(0098)http://kolplatform.jinritemai.com/index/myarticle/previewarticle?id=4891607&sig=1512525770338.7427 -->
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta id="viewport" name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,minimal-ui">
    <!--页面title-->
    <title>预览文章</title>
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/previewgallery.css?v=<?php echo time();?>">
    <link rel="stylesheet" href="/Public/Admin/css/layer.css">
    <link href="http://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <style>@import url("//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc2/css/bootstrap-glyphicons.css");</style>
    <link rel="stylesheet" href="/Public/Admin/css/top_items.css?v=<?php echo time();?>">
    <script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
    <script src="/Public/Admin/js/swipe.js"></script>
    <script src="/Public/plugins/bower_components/layer/layer.js"></script>
    <script src="/Public/Admin/js/laydate/laydate.js"></script>
    <script src="http://cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <!--预览页面PC -->
</head>
<body>
<div id="simulator">
    <div id="main">
        <div id="gallery" class="swipe" style="visibility: visible;">
            <div class="swipe-wrap">
                <!--figcaption为图片描述，控制在200字以内-->
                <!--img标签width和height属性须事先给出，由于懒加载的需要，必须把"src"改写为"alt-src" -->
                <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="swiper-slide">
                        <figure>
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
<div id="form">
    <div id="page-wrapper">
        <div class="row" style="margin: 50px 20px 20px 0px;">
            <h2>保存文章</h2>
        </div>
        <form role="form" id='check-form'>
            <div class="form-group">
                <label>发布账号</label>
                <select class="form-control" name='tt_user_id' id="account_id">
                    <option value='0'>请选择发布账号</option>
                    <?php if(is_array($account)): $i = 0; $__LIST__ = $account;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><option value='<?php echo ($row["id"]); ?>'><?php echo ($row["username"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label>文章标题</label>
                <input class="form-control" name='title' id="title" placeholder="请输入文章标题（5-30个字符）"/>
            </div>
            <div class="form-group">
                <label>文章封面</label>
                <div class="cols-lg-12">
                    <label class="radio-inline">
                        <input type="radio" name="img_type" value="3" checked> 三图
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="img_type" value="1"> 单图
                    </label>
                </div>
                <div class="row">
                    <div class="img-covers" style="margin-left: 15px;">
                        <img src="/Public/Admin/images/add.jpg" class="add-logo"/>
                    </div>
                    <div class="img-covers more-img">
                        <img src="/Public/Admin/images/add.jpg" class="add-logo"/>
                    </div>
                    <div class="img-covers more-img">
                        <img src="/Public/Admin/images/add.jpg" class="add-logo"/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>定时发表</label>
                <input type="text" readonly name="send_time" id="send_time" class="form-control" value=""
                       placeholder="请选择定时发表时间"/>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="button" style="margin: 20px auto;width: 20%" id="save">保存</button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="img-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">正文图片</h4>
            </div>
            <div class="modal-body">
                <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><div class="modal-img">
                        <img src="<?php echo ($row["img"]); ?>" data-id="<?php echo ($row["id"]); ?>" style="width: 100%">
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
                <div style="clear: both"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script type="text/javascript">
    var num = "<?php echo count($data);?>";
    var img_index = 0;
    var img_id = [];
    $(function () {
        var begin_time = "<?php echo date('Y-m-d H:i:s');?>";
        var end_time = "<?php echo date('Y-m-d  H:i:s',strtotime('+1 days'));?>";
        //执行一个laydate实例
        laydate.render({
            elem: '#send_time' //指定元素
            , type: 'datetime'
            , min: begin_time
            , max: end_time
            , ready: function () {
                ins22.hint('只能选择2-24小时范围内的时间');
            }
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

        $('#save').click(function () {
            var _this = $(this);
            var title = $('#title').val();
            var account_id = $('#account_id option:selected').val();
            var img_type = $('input[name=img_type]:checked').val();
            var send_time = $('#send_time').val() ? $('#send_time').val() : 0;
            var img = [];
            if (account_id == 0) {
                layer.msg('请选择文章发布账号！');
                return false;
            }
            if (!title) {
                layer.msg('请输入文章标题！');
                return false;
            }
            var img_len = img_id.length;
            if (img_len == 0) {
                layer.msg('请选择封面图！');
                return false;
            }
            if (img_type == 3) {
                if (img_len != 3) {
                    var n = 3 - img_len;
                    layer.msg('您的封面图已选择' + img_len + '张，还差' + n + '张');
                    return false;
                }
                img = img_id;
            } else {
                img[0] = img_id[0];
            }
            _this.attr('disabled', true);
            var url = "<?php echo U('saveNews');?>";
            _this.text('正在保存中...');
            $.post(url, {account_id: account_id, title: title, img: img, send_time: send_time}, function (res) {
                layer.msg(res.info);
                if (res.status == 1) {
                    setTimeout(function () {
                        window.parent.open("<?php echo U('newsList');?>");
                        window.parent.location.reload();
                    }, 3000)
                } else {
                    _this.text('保存');
                    _this.removeAttr('disabled');
                }
            })

        })

        $('input[name=img_type]').click(function () {
            var val = $(this).val();
            if (val == 3) {
                if ($('.more-img').hasClass('hide')) {
                    $('.more-img').removeClass('hide')
                }
            } else {
                if (!$('.more-img').hasClass('hide')) {
                    $('.more-img').addClass('hide')
                }
            }
        })

        $('.img-covers').click(function () {
            img_index = $(this).index();
            $('#img-modal').modal('show');
        });

        $('.modal-img').click(function () {
            var id = $(this).find('img').data('id');
            var img = $(this).find('img').attr('src');
            img_id[img_index] = id;
            $('.img-covers:eq(' + img_index + ')').html("<img src='" + img + "'>");
            $('#img-modal').modal('hide');
        })
    });
</script>