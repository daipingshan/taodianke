<?php
return array(
    'AUTH_LIST' => array(
        1 => array(
            'name' => '超级管理员',
            'data' => array(
                array('menu_name' => '特卖达人', 'url' => 'Index/index'),
                array('menu_name' => '文章管理', 'son_menu' => array(
                    array('menu_name' => '头条文章', 'url' => 'TopLine/topNewsList'),
                    array('menu_name' => '文章列表', 'url' => 'News/index'),
                    array('menu_name' => '登记文章', 'url' => 'News/add'),
                    array('menu_name' => '商品查看', 'url' => 'Order/GetNews'),
                )),
                array('menu_name' => '订单管理', 'son_menu' => array(
                    array('menu_name' => '所有订单', 'url' => 'Order/index'),
                    array('menu_name' => '放心购订单', 'url' => 'Order/Fxg'),
                    array('menu_name' => '认领订单', 'url' => 'Order/Renlin'),
                    array('menu_name' => '放心购商品库', 'url' => 'Order/product'),
                    array('menu_name' => '订单统计', 'url' => 'OrderCount/order'),
                    array('menu_name' => '文章统计', 'url' => 'OrderCount/article'),
                )),
                array('menu_name' => '商品管理', 'son_menu' => array(
                    array('menu_name' => '商品列表', 'url' => 'News/searsch'),
                )),
                array('menu_name' => '用户管理', 'son_menu' => array(
                    array('menu_name' => '用户列表', 'url' => 'User/userList'),
                )),
                array('menu_name' => '今日头条', 'son_menu' => array(
                    array('menu_name' => '账号管理', 'url' => 'TopLine/index'),
                    array('menu_name' => '选品管理', 'url' => 'TopLine/newItemsList'),
                    array('menu_name' => '更多选品管理', 'url' => 'TopLine/itemsMoreList'),
                    array('menu_name' => '链接选品', 'url' => 'TopLine/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'TopLine/newsList'),
                    array('menu_name' => '微头条', 'url' => 'TopLine/daTaoKeItemList'),
                )),
                array('menu_name' => '特卖达人', 'son_menu' => array(
                    array('menu_name' => '账号管理', 'url' => 'Sale/index'),
                    array('menu_name' => '高阅读量文章', 'url' => 'Sale/highReadNewsList'),
                    array('menu_name' => '高阅读量商品', 'url' => 'Sale/highReadItemsList'),
                    array('menu_name' => '特卖新品库', 'url' => 'Sale/newItems'),
                    array('menu_name' => '非商品图集', 'url' => 'Sale/imgList'),
                    array('menu_name' => '选品管理', 'url' => 'Sale/itemsList'),
                    array('menu_name' => '链接选品', 'url' => 'Sale/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'Sale/newsList'),
                )),
                array('menu_name' => '特卖专区', 'url' => 'SaleArticle/index'),
            )
        ),
        2 => array(
            'name' => '文案组长',
            'data' => array(
                array('menu_name' => '特卖达人', 'url' => 'Index/index'),
                array('menu_name' => '文章管理', 'son_menu' => array(
                    array('menu_name' => '头条文章', 'url' => 'TopLine/topNewsList'),
                    array('menu_name' => '新头条文章', 'url' => 'SaleArticle/index'),
                    array('menu_name' => '文章列表', 'url' => 'News/index'),
                    array('menu_name' => '登记文章', 'url' => 'News/add'),
                    array('menu_name' => '商品查看', 'url' => 'Order/GetNews'),
                )),
                array('menu_name' => '订单管理', 'son_menu' => array(
                    array('menu_name' => '所有订单', 'url' => 'Order/index'),
                    array('menu_name' => '放心购订单', 'url' => 'Order/Fxg'),
                    array('menu_name' => '认领订单', 'url' => 'Order/Renlin'),
                    array('menu_name' => '订单统计', 'url' => 'OrderCount/order'),
                    array('menu_name' => '放心购商品库', 'url' => 'Order/product'),
                )),
                array('menu_name' => '特卖达人', 'son_menu' => array(
                    array('menu_name' => '特卖新品库', 'url' => 'Sale/newItems'),
                    array('menu_name' => '非商品图集', 'url' => 'Sale/imgList'),
                    array('menu_name' => '选品管理', 'url' => 'Sale/itemsList'),
                    array('menu_name' => '链接选品', 'url' => 'Sale/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'Sale/newsList'),
                ))
            )
        ),
        3 => array(
            'name' => '文案合作',
            'data' => array(
                array('menu_name' => '特卖达人', 'url' => 'Index/index'),
                array('menu_name' => '特卖达人', 'son_menu' => array(
                    array('menu_name' => '新头条文章', 'url' => 'SaleArticle/index'),
                    array('menu_name' => '特卖新品库', 'url' => 'Sale/newItems'),
                    array('menu_name' => '非商品图集', 'url' => 'Sale/imgList'),
                    array('menu_name' => '选品管理', 'url' => 'Sale/itemsList'),
                    array('menu_name' => '链接选品', 'url' => 'Sale/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'Sale/newsList'),
                ))
            )
        ),
        4 => array(
            'name' => '头条发文',
            'data' => array(
                array('menu_name' => '特卖达人', 'url' => 'Index/index'),
                array('menu_name' => '今日头条', 'son_menu' => array(
                    array('menu_name' => '选品管理', 'url' => 'TopLine/newItemsList'),
                    array('menu_name' => '链接选品', 'url' => 'TopLine/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'TopLine/newsList'),
                ))
            )
        ),
        5 => array(
            'name' => '文案合作2',
            'data' => array(
                array('menu_name' => '特卖达人', 'url' => 'Index/index'),
                /*array('menu_name' => '特卖达人', 'son_menu' => array(
                    array('menu_name' => '新头条文章', 'url' => 'SaleArticle/index'),
                    array('menu_name' => '特卖新品库', 'url' => 'Sale/newItems'),
                    array('menu_name' => '非商品图集', 'url' => 'Sale/imgList'),
                    array('menu_name' => '选品管理', 'url' => 'Sale/itemsList'),
                    array('menu_name' => '链接选品', 'url' => 'Sale/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'Sale/newsList'),
                ))*/
            )
        ),
        8 => array(
            'name' => '文案合作1',
            'data' => array(
                array('menu_name' => '特卖达人', 'url' => 'Index/index'),
                array('menu_name' => '文章管理', 'son_menu' => array(
                    array('menu_name' => '文章列表', 'url' => 'News/index'),
                    array('menu_name' => '登记文章', 'url' => 'News/add'),
                )),
                array('menu_name' => '订单管理', 'son_menu' => array(
                    array('menu_name' => '放心购订单', 'url' => 'Order/Fxg'),
                    array('menu_name' => '认领订单', 'url' => 'Order/Renlin'),
                    array('menu_name' => '放心购商品库', 'url' => 'Order/product'),
                    array('menu_name' => '订单统计', 'url' => 'OrderCount/order'),
                )),
            )
        ),
    )
);