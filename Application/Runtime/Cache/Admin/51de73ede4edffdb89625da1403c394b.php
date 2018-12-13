<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>文章详情</title>
    <style type="text/css">
        a:link, a:visited {
            text-decoration: none; /*超链接无下划线*/
        }

        a:hover {
            text-decoration: underline; /*鼠标放上去有下划线*/
        }
    </style>
</head>
<body>
<h2>图集页面地址:<a href="<?php echo ($location_url); ?>" target="_blank"><?php echo ($title); ?></a></h2>
<hr>
<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><h3>商品标题：<?php echo ($row["title"]); ?></h3>
    <p>商品描述：<a href="<?php echo ($row["url"]); ?>"><?php echo ($row["desc"]); ?></a></p>
    <p>商品链接：<a href="<?php echo ($row["goods_url"]); ?>"><?php echo ($row["goods_url"]); ?></a></p>
    <p>商品价格：<span style="color: red;margin-right: 200px"><?php echo ($row["price"]); ?></span> 商品平台：<span style="color: green;"><?php echo ($row["shop_type_name"]); ?></span>
    </p>
    <div>
        <img src="<?php echo ($row["img"]); ?>" style="width: 200px"/>
    </div>
    <hr><?php endforeach; endif; else: echo "" ;endif; ?>
</body>
</html>