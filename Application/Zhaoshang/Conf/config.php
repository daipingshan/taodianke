<?php

return array(
    'TMPL_PARSE_STRING' => array(
        '__JS_PATH__' => '/Public/zhaoshang/js',
        '__CSS_PATH__' => '/Public/zhaoshang/css',
        '__IMAGE_PATH__' => '/Public/zhaoshang/images',
        '__PLUGINS_PATH__' => '/Public/plugins',
    ),
    'USER_AUTH_GATEWAY' => 'Public/index',
    'SAVE_USER_KEY' => 'AdminInfo',
    'CSS_VER' => 1,
    'JS_VER' => 1,
    'DB_TYPE'           => 'mysql',
    /**'DB_HOST'           => 'rds4s805d2zw55r9o5sj.mysql.rds.aliyuncs.com',
    'DB_NAME'           => 'zhaoshang',
    'DB_USER'           => 'zhaoshang',
    'DB_PWD'            => 'zhaoshang@123',
    'DB_PORT'           => '3306',**/
    'DB_HOST'           => 'rm-bp14074suyvwpe33f.mysql.rds.aliyuncs.com',
    'DB_NAME'           => 'temai',
    'DB_USER'           => 'taodianke',
    'DB_PWD'            => 'taodianke@163_com',
    'DB_PORT'           => '3306',
    'DB_PREFIX'         => '',
    /**
     * 权限认证配置
     */
    'AUTH_CONFIG' => array(
        'OPEN_AUTH_RULE_REGISTER' => true,
        // 无权限  无登录 验证的uri
        'NO_AUTH_NO_LOGIN_URI' => array(
            'admin/team/uploadimg' => '团单图片上传', // 插件兼容性处理
        ),
        'SUPER_ADMIN_ID' => array(
            '300623' => '刘廷锋',
        ),
        // 招商权限组配置
        'CB_AUTH_GROUP_ID' => array(
            // 招商总监权限组id
            'MANAGER' => 8,
            // 招商普通职员权限组id
            'EMPLOYEE' => 7,
        ),
        'COMMON_AUTH_LIST' => array(
            'admin/index/index' => '首页',
            'admin/index/daysign' => '签到显示',
            'admin/index/ajaxdaysign' => '签到显示',
            'admin/index/ajaxtotal' => '异步获取',
            'admin/encyclopedias/qingtuanencyclopedias' => '青团百科',
            'admin/encyclopedias/encyclopediasdetail' => '百科详情',
        ),
        'AUTH_ON' => true, //认证开关
        'AUTH_TYPE' => 2, // 认证方式，1为时时认证；2为登录认证。
        'AUTH_GROUP' => 'auth_group', //用户组数据表名
        'AUTH_GROUP_ACCESS' => 'auth_group_access', //用户组明细表
        'AUTH_RULE' => 'auth_rule', //权限规则表
        'AUTH_USER' => 'user'//用户信息表
    )
);
