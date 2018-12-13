<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/21 0021
 * Time: 上午 9:31
 */

namespace Home\Controller;

/**
 * Class IndexController
 * @package Home\Controller
 */
class IndexController extends CommonController {

    /**
     * 首页
     */
    public function index() {
        $cate   = $this->_getItemCate();
        $ad_img = $this->_getAdImg(1);
        if ($this->openSearchStatus === true) {
            $sort  = array(array('key' => 'ordid', 'val' => 1), array('key' => 'id', 'val' => 0));
            $query = '';
            $count = $this->_getOpenSearchCount($query, null, 'www');
            $page  = $this->pages($count, $this->limit);
            $data  = $this->_getOpenSearchList($query, $sort, null, $page->firstRow, $page->listRows, 'www');
        } else {
            $order = 'ordid asc,id desc';
            $where = array('pass' => 1);
            $count = M('items')->where($where)->count('id');
            $page  = $this->pages($count, $this->limit);
            $data  = $this->_getItemsList($where, $order, $page->firstRow, $page->listRows, 'www');
        }
        $assign = array('cate' => $cate, 'ad_img' => $ad_img, 'data' => $data, 'page' => $page->show(), 'act' => 'index');
        $this->assign($assign);
        $this->display();
    }

}