<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2016/12/29
 * Time: 22:05
 */

namespace AppApi\Controller;

class IndexController extends CommonController {

    /**
     * 首頁
     */
    public function index() {
        $page = I('get.page', 1, 'int');
        $page--;
        $start_num = $page * $this->reqnum;
        if ($this->openSearchStatus === true) {
            $sort  = array(array('key' => 'ordid', 'val' => 1), array('key' => 'id', 'val' => 0));
            $query = '';
            $data  = $this->_getOpenSearchList($query, $sort, null, $start_num, $this->reqnum + 1);
        } else {
            $order = 'ordid asc,id desc';
            $where = array('pass' => 1);
            $data  = $this->_getItemsList($where, $order, $start_num, $this->reqnum + 1);
        }
        $this->setHasNext(false, $data, $this->reqnum);
        $this->outPut($data, 0);
    }

    /**
     * 获取首页分类信息
     */
    public function cate() {
        $cate = $this->_getItemCate();
        $cate = array_values($cate);
        $this->outPut($cate, 0);
    }

    /**
     * 获取轮播图
     */
    public function getAdvert() {
        $data = $this->_getAdImg();
        $this->outPut($data, 0);
    }

    /**
     * 获取app首页5大功能模块
     */
    public function getAppModule() {
        $client      = I('get.client', '', 'trim');
        $client_data = array('android', 'ios');
        if (!in_array($client, $client_data)) {
            $this->outPut(null, -1, null, '平台参数不合法');
        }
        $cache_data = S('tdk_app_module');
        if ($cache_data && $cache_data[$client]) {
            $data = $cache_data[$client];
        } else {
            $where = array('status' => 1, 'client' => array('in', array($client, 'all')));
            $data  = M('app_home_module')->field('module_name,module_icon,type')->where($where)->order('sort asc')->limit(5)->select();
            foreach ($data as &$val) {
                $val['module_icon'] = getImgUrl($val['module_icon']);
            }
            unset($val);
            $cache_data[$client] = $data;
            S('tdk_app_module', $cache_data);
        }

        //针对不是代理的用户，去除高佣模块
        if (empty($this->pid)) {
            foreach ($data as $key => $value) {
                if ('high_commission' == $value['type']) {
                    unset($data[$key]);
                    break;
                }
            }
            $data = array_values($data);
        }

        $this->outPut($data, 0);
    }

}