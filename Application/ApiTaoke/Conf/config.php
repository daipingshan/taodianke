<?php
return array(
    //'配置项'=>'配置值'
    'verifyKey'  => 'youngtxxx',        //验证使用密钥   注意：一点确定不能随意修改，否则客户端无法使用
    'dataKey'    => 'youngtdata',       //加密数据使用密钥   注意：一点确定不能随意修改，否则客户端无法使用
    'tokenKey'   => 'youngtyyy',        //token验证使用密钥  注意：一点确定不能随意修改，否则客户端无法使用
    'signCheck'  => false,               //是否验证签名
    'tokenCheck' => false,              //是否验证token
    'PID'         => 'mm_121610813_',    

    // 数据试库
    'DB_TYPE'           => 'mysql',
    /**'DB_HOST'           => 'rds4s805d2zw55r9o5sj.mysql.rds.aliyuncs.com',
    'DB_NAME'           => 'datetaoke',
    'DB_USER'           => 'kuaidian_2016',
    'DB_PWD'            => 'kuaidian_20162016',
    'DB_PORT'           => '3306',**/
    'DB_HOST'           => 'rm-bp14074suyvwpe33f.mysql.rds.aliyuncs.com',
    'DB_NAME'           => 'temai',
    'DB_USER'           => 'taodianke',
    'DB_PWD'            => 'taodianke@163_com',
    'DB_PORT'           => '3306',
    'DB_PREFIX'         => 'ytt_',

    // 支付方式配置
    'TEN_PAY'=>false,
    'ALI_PAY'=>false,
    'WX_PAY' =>true,
    'UMS_PAY'=>false,
    'E_PAY'  =>false,
    'UNION_PAY'=>true,
    'LIANLIAN_PAY'=>true,
    'WE_PAY'=>true,

    // 获取手机验证码
    'IS_CLIENT_SEND'=>false,
    'IS_CLIENT_SEND_NEW'=>true,
    'CAHCETIME'=>3600,//缓存保存数据
    'PIDMM'=>'mm_121610813_22448587_79916379',//mmid值mm_30670348_16376926_61364492
    // app启动界面配置
    'APP_LOAD_IMAGE'=>array(
        'url'=>'load/default2016.png',
        // 'url'=>'load/load_double11.png',
        // 'url'=>'load/load_double12.png',
        // 'url'=>'load/load_double_dan.jpg',
        // 'url'=>'load/load_2016spring.png',
        //` 'url'=>'load/load201638.jpg',
        //'url'=>'load/load2016312.jpg',
        //'url'=>'load/load2016315.jpg',
        //'url'=>'load/load20160401.jpg',
         'url'=>'load/loadtaqing.jpg',
        'is_use'=>'Y',
    ),
    //模板样式路径配置
    'TMPL_PARSE_STRING' => array(
        '__JS_PATH__'      => '/Public/Api/js',
        '__CSS_PATH__'     => '/Public/Api/css',
        '__IMAGE_PATH__'   => '/Public/Api/images',
        '__PLUGINS_PATH__' => '/Public/plugins',
    ),

    //客户端更新配置
    'AppUpdateIos'=>array(
        'ver'=>'1',
        'is_force'=>'N',
        'description'=>"1.性能优化\n 2.界面美化\n 3.新增更多支付方式\n" ,
        'url'=>"https://itunes.apple.com/cn/app/qing-tuan-wang/id921133719?mt=8"
    ),

    'AppUpdateAndroid'=>array(
        'ver'=>'4.0.9',
        'is_force'=>'N',
        'description'=>"1、支持双11特惠活动、大量吃喝玩乐半价商品。\n",
        'url'=>"http://ytfile.oss-cn-hangzhou.aliyuncs.com/youngt.apk"
    ),
);