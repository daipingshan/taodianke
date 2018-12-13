<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/18 0018
 * Time: 上午 9:17
 */

namespace AppAdmin\Controller;

/**
 * 订单管理
 * Class OrderController
 *
 * @package AppAdmin\Controller
 */
class OrderController extends CommonController {

    /**
     * 订单管理列表
     */
    public function index() {
        $title         = I('get.title', '', 'trim');
        $id            = I('get.id', '', 'trim');
        $item_id       = I('get.item_id', '', 'trim');
        $settle_status = I('get.settle_status', '', 'trim');
        $shop_type     = I('get.shop_type', '', 'trim');
        $withdraw_id   = I('get.withdraw_id', 0, 'int');
        $map           = array();
        if ($title) {
            $map['o.title'] = array('LIKE', '%' . $title . '%');
        }
        if ($id) {
            $map['c.order_id'] = $id;
        }
        if ($item_id) {
            $map['c.item_id'] = $item_id;
        }
        if ($settle_status) {
            if ($settle_status == 'Y') {
                $map['c.withdraw_id'] = array('gt', 0);
            } else {
                $map['c.withdraw_id'] = 0;
            }
        }
        if ($withdraw_id) {
            $map['c.withdraw_id'] = $withdraw_id;
        }
        if ($shop_type) {
            $map['o.shop_type'] = $shop_type;
        }
        $model     = M('order_commission');
        $count     = $model->alias('c')->join('left join ytt_order o ON o.order_id = c.order_id and o.order_num = c.order_num')->where($map)->count('c.id');
        $amount    = $model->alias('c')->join('left join ytt_order o ON o.order_id = c.order_id and o.order_num = c.order_num')->where($map)->sum('c.fee');
        $page      = $this->pages($count, $this->limit);
        $list      = $model->alias('c')->join('left join ytt_order o ON o.order_id = c.order_id and o.order_num = c.order_num')->join('left join ytt_user u on u.id = c.user_id')->field('c.*,o.title,o.number,o.order_type,u.username')->where($map)->order('c.id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $shop_type = array('C' => '淘宝', 'J' => '楚楚街');
        $this->assign(array('list' => $list, 'page' => $page->show(), 'count' => $count, 'amount' => $amount, 'shop_type' => $shop_type));
        $this->display();
    }

    /**
     * 订单管理列表
     */
    public function orderList() {
        $title   = I('get.title', '', 'trim');
        $id      = I('get.id', 0, 'int');
        $item_id = I('get.item_id', '', 'trim');
        $map     = array();
        if ($title) {
            $map['title'] = array('LIKE', '%' . $title . '%');
        }
        if ($id) {
            $map['order_id'] = $id;
        }
        if ($item_id) {
            $map['itemid'] = $item_id;
        }
        $model  = M('order_data');
        $count  = $model->where($map)->count('id');
        $page   = $this->pages($count, $this->limit);
        $list   = $model->alias('o')->where($map)->join('left join ytt_user u on u.pid = o.pid')->field('o.*,u.username')->limit($page->firstRow . ',' . $page->listRows)->order('id desc')->select();
        $amount = $model->where($map)->sum('fee');
        $this->assign(array('list' => $list, 'page' => $page->show(), 'count' => $count, 'amount' => $amount));
        $this->display();
    }

}