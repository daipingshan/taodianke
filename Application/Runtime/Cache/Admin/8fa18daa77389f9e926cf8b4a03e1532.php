<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!-- saved from url=(0098)http://kolplatform.jinritemai.com/index/myarticle/previewarticle?id=4891607&sig=1512525770338.7427 -->
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta id="viewport" name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,minimal-ui">
    <!--页面title-->
    <title>特卖商品库</title>
    <!--预览页面PC -->
    <link href="http://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <style>@import url("//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc2/css/bootstrap-glyphicons.css");</style>
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/previewgallery.css?v=<?php echo time();?>">
    <link rel="stylesheet" href="/Public/Admin/css/top_items.css?v=<?php echo time();?>">
    <link rel="stylesheet" href="/Public/Admin/css/layer.css">
    <link rel="stylesheet" href="/Public/Admin/js/layui/css/layui.css" media="all">
    <script src="/Public/Admin/js/jquery.min.js"></script>
    <script src="/Public/Admin/js/swipe.js"></script>
    <script src="/Public/plugins/bower_components/layer/layer.js"></script>
    <script src="http://cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="/Public/Admin/js/layui/layui.js"></script>
    <style>
        #select-items {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 99999;
            background: #fff;
            width: 100%;
        }

        #content {
            margin: 100px 0;
            overflow: auto;
        }

        #page {
            position: fixed;
            bottom: 0;
            left: 0;
            z-index: 99999;
            background: #fff;
            width: 100%;
        }

        .col-lg-3 {
            width: 23%
        }

        #page div > span {
            display: inline-block;
            line-height: 70px;
        }
    </style>
</head>
<body>
<div id="select-items">
    <div class="row">
        <form action="" class="form-inline" style="margin: 10px 80px">
            <div class="row">
                <div class="form-group">
                    <label>商品品类</label>
                    <select class="form-control" name="category1_id">
                        <option value="">一级品类</option>
                        <option value="11">电脑硬件/显示器/电脑周边</option>
                        <option value="14">数码相机/单反相机/摄像机</option>
                        <option value="16">女装/女士精品</option>
                        <option value="20">电玩/配件/游戏/攻略</option>
                        <option value="21">居家日用</option>
                        <option value="23">古董/邮币/字画/收藏</option>
                        <option value="25">玩具/童车/益智/积木/模型</option>
                        <option value="26">汽车/用品/配件/改装</option>
                        <option value="27">家装主材</option>
                        <option value="28">ZIPPO/瑞士军刀/眼镜</option>
                        <option value="29">宠物/宠物食品及用品</option>
                        <option value="30">男装</option>
                        <option value="33">书籍/杂志/报纸</option>
                        <option value="34">音乐/影视/明星/音像</option>
                        <option value="35">奶粉/辅食/营养品/零食</option>
                        <option value="98">包装</option>
                        <option value="1101">笔记本电脑</option>
                        <option value="1201">MP3/MP4/iPod/录音笔</option>
                        <option value="1512">手机</option>
                        <option value="1625">女士内衣/男士内衣/家居服</option>
                        <option value="1801">美容护肤/美体/精油</option>
                        <option value="2813">成人用品/情趣用品</option>
                        <option value="50002766">零食/坚果/特产</option>
                        <option value="50002768">个人护理/保健/按摩器材</option>
                        <option value="50006842">箱包皮具/热销女包/男包</option>
                        <option value="50006843">女鞋</option>
                        <option value="50007216">鲜花速递/花卉仿真/绿植园艺</option>
                        <option value="50007218">办公设备/耗材/相关服务</option>
                        <option value="50008090">3C数码配件</option>
                        <option value="50008141">酒类</option>
                        <option value="50008163">床上用品</option>
                        <option value="50008164">住宅家具</option>
                        <option value="50008165">童装/婴儿装/亲子装</option>
                        <option value="50008907">手机号码/套餐/增值业务</option>
                        <option value="50010404">服饰配件/皮带/帽子/围巾</option>
                        <option value="50010728">运动/瑜伽/健身/球迷用品</option>
                        <option value="50010788">彩妆/香水/美妆工具</option>
                        <option value="50011397">珠宝/钻石/翡翠/黄金</option>
                        <option value="50011699">运动服/休闲服装</option>
                        <option value="50011740">流行男鞋</option>
                        <option value="50011972">影音电器</option>
                        <option value="50012029">运动鞋new</option>
                        <option value="50012082">厨房电器</option>
                        <option value="50012100">生活电器</option>
                        <option value="50012164">闪存卡/U盘/存储/移动硬盘</option>
                        <option value="50013864">饰品/流行首饰/时尚饰品新</option>
                        <option value="50013886">户外/登山/野营/旅行用品</option>
                        <option value="50014812">尿片/洗护/喂哺/推车床</option>
                        <option value="50016348">家庭/个人清洁工具</option>
                        <option value="50016349">厨房/烹饪用具</option>
                        <option value="50016422">粮油米面/南北干货/调味品</option>
                        <option value="50017300">乐器/吉他/钢琴/配件</option>
                        <option value="50018004">电子词典/电纸书/文化用品</option>
                        <option value="50018222">DIY电脑</option>
                        <option value="50018264">网络设备/网络相关</option>
                        <option value="50019780">平板电脑/MID</option>
                        <option value="50020275">传统滋补营养品</option>
                        <option value="50020332">基础建材</option>
                        <option value="50020485">五金/工具</option>
                        <option value="50020579">电子/电工</option>
                        <option value="50020808">家居饰品</option>
                        <option value="50020857">特色手工艺</option>
                        <option value="50022517">孕妇装/孕产妇用品/营养</option>
                        <option value="50022703">大家电</option>
                        <option value="50023282">美发护发/假发</option>
                        <option value="50023717">OTC药品/医疗器械/计生用品</option>
                        <option value="50023722">隐形眼镜/护理液</option>
                        <option value="50023724">其他</option>
                        <option value="50024099">电子元器件市场</option>
                        <option value="50025004">个性定制/设计服务/DIY</option>
                        <option value="50025111">本地化生活服务</option>
                        <option value="50025705">洗护清洁剂/卫生巾/纸/香薰</option>
                        <option value="50025707">度假线路/签证送关/旅游服务</option>
                        <option value="50026316">咖啡/麦片/冲饮</option>
                        <option value="50026800">保健食品/膳食营养补充食品</option>
                        <option value="50050359">水产肉类/新鲜蔬果/熟食</option>
                        <option value="50468001">手表</option>
                        <option value="50510002">运动包/户外包/配件</option>
                        <option value="122650005">童鞋/婴儿鞋/亲子鞋</option>
                        <option value="122684003">自行车/骑行装备/零配件</option>
                        <option value="122852001">居家布艺</option>
                        <option value="122928002">收纳整理</option>
                        <option value="122950001">节庆用品/礼品</option>
                        <option value="122952001">餐饮具</option>
                        <option value="124044001">品牌台机/品牌一体机/服务器</option>
                        <option value="124050001">全屋定制</option>
                        <option value="124242008">智能设备</option>
                        <option value="124354002">电动车/配件/交通工具</option>
                        <option value="124458005">茶</option>
                        <option value="124484008">模玩/动漫/周边/cos/桌游</option>
                        <option value="200000004">整车销售</option>
                        <option value="200000008">农用物资</option>
                        <option value="200000046">商业/办公家具</option>
                    </select>
                </div>
                <div class="form-group">
                    <select class="form-control" name="keyword_type">
                        <option value="word"
                        <?php if(($param['keyword_type'] == 'word') OR ($param['keyword_type'] == '')): ?>selected<?php endif; ?>
                        >商品名称</option>
                        <option value="platform_sku_id"
                        <?php if($param['keyword_type'] == 'platform_sku_id'): ?>selected<?php endif; ?>
                        >商品ID</option>
                    </select>
                    <input type="text" name="keyword_value" value="<?php echo ($param['keyword_value']); ?>" placeholder="商品名称搜索"
                           class="form-control">
                </div>
                <div class="form-group">
                    <select class="form-control" name="keyword_type_shop">
                        <option value="word"
                        <?php if(($param['keyword_type_shop'] == 'word') OR ($param['keyword_type_shop'] == '')): ?>selected<?php endif; ?>
                        >店铺名称</option>
                        <option value="shop_id"
                        <?php if($param['keyword_type_shop'] == 'shop_id'): ?>selected<?php endif; ?>
                        >店铺ID</option>
                    </select>
                    <input type="text" name="keyword_value_shop" value="<?php echo ($param['keyword_value_shop']); ?>"
                           placeholder="店铺名称搜索" class="form-control">
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sort btn-null" value="">综合排序</button>
                    <button type="button" class="btn btn-default btn-sort btn-hotrank" value="hotrank">
                        热度<span
                            class="glyphicon glyphicon-arrow-down"></span>
                    </button>
                    <button type="button" class="btn btn-default btn-sort btn-cos_ratio" value="cos_ratio">
                        佣金比例<span
                            class="glyphicon glyphicon-arrow-down"></span></button>
                    <button type="button" class="btn btn-default btn-sort btn-sku_price" value="sku_price">
                        价格<span
                            class="glyphicon glyphicon-arrow-down"></span>
                    </button>
                </div>
            </div>
            <div class="row" style="margin-top: 10px">
                <div class="form-group">
                    <label>佣金比例</label>
                    <input type="number" name="cos_ratio_down" class="form-control" value="<?php echo ($param['cos_ratio_down']); ?>">%
                    -
                    <input type="number" name="cos_ratio_up" class="form-control" value="<?php echo ($param['cos_ratio_up']); ?>">%
                </div>
                <div class="form-group">
                    <label>价格</label>
                    <input type="number" name="sku_price_down" class="form-control" value="<?php echo ($param['sku_price_down']); ?>">元
                    -
                    <input type="number" name="sku_price_up" class="form-control" value="<?php echo ($param['sku_price_up']); ?>">元
                </div>
                <input type="hidden" name="order" id="order" value="<?php echo ($param['order']); ?>"/>
                <input type="hidden" name="sort" id="sort" value="<?php echo ($param['sort']); ?>"/>
                <button type="submit" class="btn btn-primary">搜索</button>
            </div>
        </form>
    </div>
    <hr/>
</div>
<div id="content">
    <div class="row">
        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><div class="col-lg-12" style="margin-top: 20px">
                <?php if(is_array($row)): $i = 0; $__LIST__ = $row;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-lg-3 box">
                        <div class="img">
                            <img src="<?php echo ($vo["figure"]); ?>" style="margin: 0 10%;width: 80%">
                            <a href="<?php echo ($vo["sku_url"]); ?>" target="_blank">
                                <div class="intro">
                                    <?php echo ($vo["sku_title"]); ?>
                                </div>
                            </a>
                        </div>
                        <div class="content" style="margin: 0 10%">
                            <h6 style="margin: 10px 0"><?php echo ($vo['shop_name']); ?></h6>
                            <div class="col-lg-12 no-padding type-time">
                                <div class="col-lg-5 no-padding" style="font-size: 16px;color: #FF420F;">
                                    <b>￥<?php echo ($vo["sku_price"]); ?></b>
                                </div>
                                <div class="col-lg-7 no-padding time" style="font-size: 12px; !important;">
                                    热度：<b style="color: #FF420F"><?php echo ($vo['hotrank']); ?></b>
                                </div>
                            </div>
                            <div class="col-lg-12 no-padding type-time">
                                <div class="col-lg-5 no-padding" style="font-size: 12px;">
                                    佣金比率：<b style="color: #FF420F"><?php echo ($vo['cos_info']['cos_ratio']*100); ?>%</b>
                                </div>
                                <div class="col-lg-7 no-padding time" style="font-size: 12px; !important;">
                                    预估：<b style="color: #FF420F">￥<?php echo ($vo['cos_info']['cos_fee']); ?></b>
                                </div>
                            </div>
                            <div class="col-lg-12 add-btn" style="margin: 20px 0;position: relative">
                                <button class="btn btn-danger select-items" style="width: 100%"
                                        data-id="<?php echo ($vo["platform_sku_id"]); ?>">选择商品
                                </button>
                            </div>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <div id="page">
        <hr/>
        <div style="width: 80%;margin: 0 auto">
            <ul style="width: 100%;">
                <?php echo ($page); ?>
            </ul>
        </div>
    </div>
</div>
</body>
<script>
    var category_id = "<?php echo ($param['category1_id']); ?>";
    category_id = parseInt(category_id);
    if (category_id > 0) {
        $('select[name=category1_id] option').each(function () {
            if ($(this).val() == category_id) {
                $(this).attr('selected', true);
            }
        })
    }
    var sort = "<?php echo ($param['sort']); ?>";
    var order = "<?php echo ($param['order']); ?>";
    $('.btn-sort').removeClass('btn-primary');
    if (sort) {
        $('.btn-' + sort).addClass('btn-primary');
        if (order == 'asc') {
            $('.btn-' + sort + ' span').attr('class', 'glyphicon glyphicon-arrow-up');
        } else {
            $('.btn-' + sort + ' span').attr('class', 'glyphicon glyphicon-arrow-down');
        }
    } else {
        $('.btn-null').addClass('btn-primary');
    }

    $('.btn-sort').click(function () {
        if ($(this).find('span').hasClass('glyphicon-arrow-down')) {
            $('#order').val('asc');
        } else if ($(this).find('span').hasClass('glyphicon-arrow-up')) {
            $('#order').val('desc');
        }
        $('#sort').val($(this).val())
        $('form').submit();
    });

    $('button.select-items').click(function () {
        var type = "<?php echo ($type); ?>";
        var goods_id = $(this).data('id');
        var param = $('form').serialize();
        $.get("<?php echo U('saveParam');?>", param, function () {
            if (type == 'sale') {
                window.parent.location.href = "<?php echo U('Sale/itemsList');?>" + '?shop_goods_id=' + goods_id;
            } else {
                window.parent.location.href = "<?php echo U('TopLine/newItemsList');?>" + '?shop_goods_id=' + goods_id;
            }

        });
    });

</script>
</html>