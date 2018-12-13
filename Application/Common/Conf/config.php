<?php

$config = array(

    'SHOW_PAGE_TRACE' => false,              // 显示页面Trace信息
    'LOAD_EXT_CONFIG' => 'setting',

    'PWD_ENCRYPT_STR' => 'youngtxxx',

    'URL_CASE_INSENSITIVE' => false,

    'tokenKey'  => 'youngtyyy',
    'PID'       => 'mm_121610813_22448587_79916379',
    /* URL设置 */
    'URL_MODEL' => 2,                  //URL模式

    'COOKIE_PREFIX'   => 'tp_',
    'COOKIE_EXPIRE'   => 86400 * 7,
    'COOKIE_PATH'     => '/',
    'COOKIE_DOMAIN'   => $_SERVER['HTTP_HOST'],
    //'配置项'=>'配置值'
    'SESSION_OPTIONS' => array(
        'domain' => $_SERVER['HTTP_HOST'],
    ),

    /*图片地址前缀*/
    'IMG_PREFIX'      => 'https://pic.taodianke.com/',
    /* 令牌验证 */
    'TOKEN_ON'        => false,
    'TOKEN_NAME'      => '__hash__',
    'TOKEN_TYPE'      => 'md5',
    'TOKEN_RESET'     => false,

    'uploadPath'            => './Uploads/',
    'dirPath'               => './Uploads/tdk/qrcode/',
    /*日志配置*/
    'LOG'                   => true,
    /*自定义配置*/
    'LOAD_EXT_CONFIG'       => 'db,cache',
    /* 子域名配置 */
    'APP_SUB_DOMAIN_DEPLOY' => 1,             // 开启子域名配置
    'APP_SUB_DOMAIN_RULES'  => array(
        'api'   => 'AppApi',
        'admin' => 'AppAdmin',
        'm'     => 'Touch',
        'proxy' => 'proxy',
    ),
);
return $config;

