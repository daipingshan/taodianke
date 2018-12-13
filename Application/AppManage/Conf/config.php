<?php
return array(
    //'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
        '__JS__'     => '/Public/Assets/js',
        '__CSS__'    => '/Public/Assets/css',
        '__CSSImg__' => '/Public/Assets/images'
    ),
    'DB_HOST'           => 'localhost',
    'DB_NAME'           => 'aitaoke',
    'DB_USER'           => 'root',
    'DB_PWD'            => 'root',
    'DB_PORT'           => '3306',
    'DB_PREFIX'         => 'ytt_',

    'url_model'           => 2,
    //默认错误跳转对应的模板文件
    'TMPL_ACTION_ERROR'   => THINK_PATH . 'Public/error.html',
    //默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => THINK_PATH . 'Public/success.html',

    //开启路由
    'URL_ROUTER_ON'       => true,
    //路由规则
    'URL_ROUTE_RULES'     => array(),

);