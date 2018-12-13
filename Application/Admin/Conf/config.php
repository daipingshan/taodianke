<?php

return array(
    'TMPL_PARSE_STRING' => array(
        '__JS_PATH__'      => '/Public/Admin/js',
        '__CSS_PATH__'     => '/Public/Admin/css',
        '__IMAGE_PATH__'   => '/Public/Admin/images',
        '__PLUGINS_PATH__' => '/Public/plugins',
    ),
    'USER_AUTH_GATEWAY' => 'Public/index',
    'SAVE_USER_KEY'     => 'AdminInfo',
    'CSS_VER'           => 1,
    'JS_VER'            => 1,
    'DB_TYPE'           => 'mysql',
    /**'DB_HOST'           => 'rds4s805d2zw55r9o5sj.mysql.rds.aliyuncs.com',
     * 'DB_NAME'           => 'datetaoke',
     * 'DB_USER'           => 'kuaidian_2016',
     * 'DB_PWD'            => 'kuaidian_20162016',
     * 'DB_PORT'           => '3306',**/
    'DB_HOST'           => 'rm-bp14074suyvwpe33f.mysql.rds.aliyuncs.com',
    'DB_NAME'           => 'temai',
    'DB_USER'           => 'taodianke',
    'DB_PWD'            => 'taodianke@163_com',
    'DB_PORT'           => '3306',
    'DB_PREFIX'         => 'ytt_',
    /*自定义配置*/
    'LOAD_EXT_CONFIG'   => 'auth,token',
);
