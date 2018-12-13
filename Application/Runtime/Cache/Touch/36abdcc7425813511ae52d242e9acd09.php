<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="keywords" content="淘店客-我的优惠券">
    <meta name="description" content="淘店客-我的优惠券">
    <link href="https://pic.taodianke.com//static/Touch/css/index.css?v=2" rel="stylesheet" type="text/css"/>
    <link href="https://pic.taodianke.com//static/Touch/css/reset.css" rel="stylesheet" type="text/css"/>
    <title>淘店客-我的优惠券</title>
    <script src="https://pic.taodianke.com//static/Touch/js/rem.js"></script>
</head>
<body>
<!--tab-->
<div class="tab">
    <ul class="cl tab_con">
        <?php if($status == 'not_use'): ?><li class="active">未使用</li>
            <?php else: ?>
            <li><a href="<?php echo U('index');?>?status=not_use">未使用</a></li><?php endif; ?>
        <?php if($status == 'use'): ?><li class="active">已使用</li>
            <?php else: ?>
            <li><a href="<?php echo U('index');?>?status=use">已使用</a></li><?php endif; ?>
        <?php if($status == 'expires'): ?><li class="active">已过期</li>
            <?php else: ?>
            <li><a href="<?php echo U('index');?>?status=expires">已过期</a></li><?php endif; ?>
    </ul>
</div>
<div class="perch_div"></div>
<!--优惠券开始-->
<div class="content">
    <ul class="content_ul cl" id="data">
        <?php if($status == 'not_use'): if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><li class="con_list not-use">
                    <div class="discount">
                        <div class="money fl">
                            <h2><b><?php echo ($row["money"]); ?></b>元</h2>
                            <span>满<?php echo ($row["limit_money"]); ?>元可用</span>
                        </div>
                        <div class="title_text fl">
                            <h3>宅喵生活优惠券</h3>
                            <p><?php echo substr($row['coupon_sn'],0,4);?>&nbsp;&nbsp;<?php echo substr($row['coupon_sn'],-4);?></p>
                            <span>有效期至<?php echo (date("Y年m月d日",$row["end_time"])); ?></span>
                        </div>
                        <div class="code fl">
                            <a href="javascript:;" data-src="<?php echo ($row["img"]); ?>" data-money="<?php echo ($row["money"]); ?>"
                               data-limit-money="<?php echo ($row["limit_money"]); ?>" data-time='<?php echo (date("Y年m月d日",$row["end_time"])); ?>'>
                                <img src="https://pic.taodianke.com//static/Touch/images/code.png"/>
                            </a>
                            <span>立即使用</span>
                        </div>
                    </div>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php elseif($status == 'use'): ?>
            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><li class="con_list con_list_use">
                    <div class="discount">
                        <div class="money fl">
                            <h2><b><?php echo ($row["money"]); ?></b>元</h2>
                            <span>满<?php echo ($row["limit_money"]); ?>元可用</span>
                        </div>
                        <div class="title_text fl">
                            <h3>宅喵生活优惠券</h3>
                            <p><?php echo substr($row['coupon_sn'],0,4);?>&nbsp;&nbsp;<?php echo substr($row['coupon_sn'],-4);?></p>
                            <span>有效期至<?php echo (date("Y年m月d日",$row["end_time"])); ?></span>
                        </div>
                        <div class="code fl">
                            <a href="javascript:;">
                                <img src="https://pic.taodianke.com//static/Touch/images/use.png"/>
                            </a>
                            <img class="flag-use" src="https://pic.taodianke.com//static/Touch/images/flag-use.png"/>
                            <span>立即使用</span>
                        </div>
                    </div>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><li class="con_list con_list_use">
                    <div class="discount">
                        <div class="money fl">
                            <h2><b><?php echo ($row["money"]); ?></b>元</h2>
                            <span>满<?php echo ($row["limit_money"]); ?>元可用</span>
                        </div>
                        <div class="title_text fl">
                            <h3>宅喵生活优惠券</h3>
                            <p><?php echo substr($row['coupon_sn'],0,4);?>&nbsp;&nbsp;<?php echo substr($row['coupon_sn'],-4);?></p>
                            <span>有效期至<?php echo (date("Y年m月d日",$row["end_time"])); ?></span>
                        </div>
                        <div class="code fl">
                            <a href="javascript:;">
                                <img src="https://pic.taodianke.com//static/Touch/images/use.png"/>
                            </a>
                            <img class="flag-use" src="https://pic.taodianke.com//static/Touch/images/time.png"/>
                            <span>立即使用</span>
                        </div>
                    </div>
                </li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
    </ul>
    <p class="load_more">
        <?php if(($is_next) == "1"): ?><a href="javascript:;" onclick="getMore($(this));">加载更多</a>
            <?php else: ?>
            <a href="javascript:;">已加载完成，没有更多数据了</a><?php endif; ?>
    </p>
</div>
<div class="maxing"></div>
<div class="window_open">
    <div class="code_tit">
    		<span class="show_m fl">
    			<b></b>元
    		</span>
        <div class="fl">
            <h3 class="show_if">满<span></span>元可用</h3>
            <p style="text-align: center;line-height: 0.5rem;font-size: 0.2rem" class="time"></p>
        </div>

    </div>
    <p class="img_code">
        <img src="https://pic.taodianke.com//static/Touch/images/code.png"/>
    </p>
    <p class="code_num"></p>
    <div class="close">
        <a href="javascript:;" class="close"></a>
    </div>
</div>
<script src="https://pic.taodianke.com//static/Touch/js/jquery-1.11.0.min.js" type="text/javascript"></script>
<script>
    var page = 2;
    var status = "<?php echo ($status); ?>";
    $(function () {
        //点击立即使用
        $(document).on('click', '.not-use .code a', function () {
            var _img = $(this).attr('data-src');
            var _money = $(this).attr('data-money');
            var _cond = $(this).attr('data-limit-money');
            var _codeNum = $(this).parent().prev('.title_text').find('p').html();
            var _time = $(this).attr('data-time');
            openWindow(_img, _money, _codeNum, _cond, _time);
        });
        $('.close').click(function () {
            closeWindow();
        })
        //打开弹窗
        function openWindow(img, money, code, cond, time) {
            $('.maxing,.window_open').addClass('active');
            $('.img_code>img').attr('src', img)
            $('.show_m>b').html(money);
            $('.code_num').html(code);
            $('.show_if>span').html(cond)
            $('p.time').html('有效期至' + time);
        }

        //关闭弹窗
        function closeWindow() {
            $('.maxing,.window_open').removeClass('active');
        }
    })
    /**
     * 加载更多数据
     */
    function getMore(obj) {
        var url = "<?php echo U('getMoreCoupon');?>";
        $.get(url, {status: status, page: page}, function (res) {
            if (res.status == 1) {
                var data = res.info.data;
                var html = "";
                if (data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        if (status == 'not_use') {
                            html += '<li class="con_list">';
                        } else {
                            html += '<li class="con_list con_list_use">';
                        }
                        html += '<div class="discount">';
                        html += '<div class="money fl">';
                        html += '<h2><b>' + data[i].money + '</b>元</h2>';
                        html += ' <span>满' + data[i].limit_money + '元可用</span>';
                        html += '</div>';
                        html += '<div class="title_text fl">';
                        html += '<h3>宅喵生活优惠券</h3>';
                        html += '<p>' + data[i].coupon_sn.substring(0, 4) + '&nbsp;&nbsp;' + data[i].coupon_sn.substring(4, 8) + '</p>';
                        html += '<span>有效期至' + data[i]['end_time'] + ' </span>';
                        html += '<div>';
                        html += ' <div class="code fl">';
                        if (status == 'not_use') {
                            html += '<a href="javascript:;" data-time="' + data[i].end_time + '" data-src="' + data[i]['img'] + '" data-money="' + data[i].money + '" data-limit-money="' + data[i].limit_money + '"><img src="https://pic.taodianke.com//static/Touch/images/code.png"/></a>';
                        } else {
                            html += '<a href="javascript:;"><img src="https://pic.taodianke.com//static/Touch/images/use.png"/></a>';
                            if (status == 'use') {
                                html += ' <img class="flag-use" src="https://pic.taodianke.com//static/Touch/images/flag-use.png"/>';
                            } else {
                                html += ' <img class="flag-use" src="https://pic.taodianke.com//static/Touch/images/time.png"/>';
                            }
                        }
                        html += '<span>立即使用</span>';
                        html += '</div>';
                        html += '</div>';
                        html += '</li>';
                    }
                    $('#data').append(html);
                }
                if (res.info.is_next == 0) {
                    obj.attr('onclick', 'javascript:;').text('已加载完成，没有更多数据了');
                }
                page++;
            } else {
                alert(res.info);
            }
        });
    }


</script>
</body>
</html>