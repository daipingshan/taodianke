<?php
$config = array(
    //模板样式路径配置
    'TMPL_PARSE_STRING'   => array(
        '__JS__'           => '/Public/Home/js',
        '__CSS__'          => '/Public/Home/css',
        '__IMAGE__'        => '/Public/Home/images',
        '__IMG__'          => '/Public/Home/img',
        '__PLUGINS_PATH__' => '/Public/plugins',
    ),
    'TMPL_ACTION_SUCCESS' => '../Application/Home/View/Common/error.html', // 定义公共错误模板
    'TMPL_ACTION_ERROR'   => '../Application/Home/View/Common/error.html', // 定义公共错误模板
    'V'                   => 2,
);
return $config;