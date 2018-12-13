<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/3/30
 * Time: 15:27
 */

namespace Admin\Controller;

/**
 * 业绩统计
 * Class OrderCountController
 *
 * @package Admin\Controller
 */
class OrderCountController extends CommonController {

    /**
     * 订单统计
     */
    public function order() {
        $time    = I('get.time', 1, 'int');
        $times   = I('get.times', '', 'trim');
        $user_id = I('get.user_id', 0, 'int');
        $title   = I('get.title', '', 'trim');
        if ($this->user['id'] != 0) {
            $user_id = $this->user['id'];
        }
        $user = M('tmuser')->where(array('group_id' => 2))->getField('id,name');
        if ($time == 2) {
            $end_time   = strtotime(date('Y-m-d')) - 1;
            $start_time = strtotime(date('Y-m-d', strtotime('-1 days')));
        } else if ($time == 3) {
            $end_time   = strtotime(date('Y-m-d', strtotime('-1 days'))) - 1;
            $start_time = strtotime(date('Y-m-d', strtotime('-2 days')));
        } else {
            $end_time   = time();
            $start_time = strtotime(date('Y-m-d'));
        }
        if ($times) {
            list($start_time, $end_time) = explode(' - ', $times);
            if ($start_time && $end_time) {
                $start_time = strtotime($start_time);
                $end_time   = strtotime($end_time) + 86399;
            }
        }
        $where = array('UNIX_TIMESTAMP(pay_time)' => array('between', array($start_time, $end_time)), 'order_status' => array('in', array('订单完成', '订单付款')));
        if ($user_id) {
            $where['user_id'] = $user_id;
        }
        if ($title) {
            $where['article_title'] = array('like', "%{$title}%");
        }
        $field = 'left(pay_time,10) as time ,count(id) as news_num,name,sum(income) as income,order_source,article_title,id';
        $data  = M('fxg')->field($field)->where($where)->group('time,article_title')->order('time desc,income desc,news_num desc')->select();
        $this->assign('user', $user);
        $this->assign('data', $data);
        $this->assign('time', date('Y-m-d', $start_time) . ' - ' . date('Y-m-d', $end_time));
        $this->display();
    }

    /**
     * 文章统计
     */
    public function article() {
        $time = I('get.time', '', 'trim,urldecode');
        if ($time) {
            list($start_time, $end_time) = explode(' - ', $time);
            if ($start_time && $end_time) {
                $start_time = strtotime($start_time);
                $end_time   = strtotime($end_time);
            }
        } else {
            $end_time   = time();
            $start_time = strtotime(date('Y-m-01'));
            $time       = date('Y-m-d', $start_time) . ' - ' . date('Y-m-d', $end_time);
        }
        $where     = array('time' => array('between', array($start_time, $end_time)));
        $fxg_where = array('order_status' => array('in', array('订单完成', '订单付款')));
        $field     = 'count(id) as news_num,name,user_id';
        $data      = M('tmnews')->field($field)->where($where)->group('user_id')->index('user_id')->select();
        $user_data = $news_num = $order_num = $total_fee = array();
        foreach ($data as &$val) {
            $news_ids             = M('tmnews')->where($where)->where(array('user_id' => $val['user_id']))->getField('id', true);
            $fxg_where['news_id'] = array('in', array_values($news_ids));
            $fxg                  = M('fxg')->where($fxg_where)->field('count(id) as num,sum(income) as fee')->select();
            $val['order_num']     = $fxg[0]['num'] ? $fxg[0]['num'] : 0;
            $val['order_fee']     = $fxg[0]['fee'] ? $fxg[0]['fee'] : 0;
            $user_data[]          = $val['name'];
            $news_num[]           = $val['news_num'] ? $val['news_num'] : 0;
            $order_num[]          = $fxg[0]['num'] ? $fxg[0]['num'] : 0;
            $total_fee[]          = $fxg[0]['fee'] ? $fxg[0]['fee'] : 0;
        }
        $this->assign(array('data' => $data, 'time' => $time, 'user_data' => json_encode($user_data), 'news_num' => json_encode($news_num), 'order_num' => json_encode($order_num), 'total_fee' => json_encode($total_fee)));
        $this->assign('data', $data);
        $this->assign('time', $time);
        $this->display();
    }

    /**
     * 文章订单统计
     */
    public function articleOrder() {
        $user_id = I('get.user_id', 0, 'int');
        $time    = I('get.time', '', 'trim,urldecode');
        if ($time) {
            list($start_time, $end_time) = explode(' - ', $time);
            if ($start_time && $end_time) {
                $start_time = strtotime($start_time);
                $end_time   = strtotime($end_time);
            }
        } else {
            $end_time   = time();
            $start_time = strtotime(date('Y-m-d', strtotime('-30 days')));
            $time       = date('Y-m-d', $start_time) . ' - ' . date('Y-m-d', $end_time);
        }
        $where = array('UNIX_TIMESTAMP(pay_time)' => array('between', array($start_time, $end_time)), 'order_status' => array('in', array('订单完成', '订单付款')));
        if ($user_id) {
            $where['user_id'] = $user_id;
        }
        $field = 'count(id) as news_num,name,sum(income) as income,order_source,article_title,id';
        $data  = M('fxg')->field($field)->where($where)->group('article_title')->order('income desc,news_num desc')->select();
        $this->assign('data', $data);
        $this->assign('time', $time);
        $this->display();
    }
    /***
     *
     * 第三方订单统计
     */
   public function orderDsf(){
       $time    = I('get.time', 1, 'int');
       $times   = I('get.times', '', 'trim');
       $user_id = I('get.user_id', 0, 'trim');
       $title   = I('get.title', '', 'trim');
       if ($this->user['id'] != 0) {
           $user_id = $this->user['id'];
       }
       $user =array(
           '52时尚'=>'52时尚',
           '叮当运动派'=>'叮当运动派',
           '白浅上神'=>'白浅上神',
           '果不其然'=>'果不其然'
       );
       if ($time == 2) {
           $end_time   = strtotime(date('Y-m-d')) - 1;
           $start_time = strtotime(date('Y-m-d', strtotime('-1 days')));
       } else if ($time == 3) {
           $end_time   = strtotime(date('Y-m-d', strtotime('-1 days'))) - 1;
           $start_time = strtotime(date('Y-m-d', strtotime('-2 days')));
       } else {
           $end_time   = time();
           $start_time = strtotime(date('Y-m-d'));
       }
       if ($times) {
           list($start_time, $end_time) = explode(' - ', $times);
           if ($start_time && $end_time) {
               $start_time = strtotime($start_time);
               $end_time   = strtotime($end_time) + 86399;
           }
       }
       $where = array('UNIX_TIMESTAMP(pay_time)' => array('between', array($start_time, $end_time)), 'order_status' => array('in', array('订单完成', '订单付款')));
       if ($user_id) {
           $where['name'] = $user_id;
       }
       if ($title) {
           $where['article_title'] = array('like', "%{$title}%");
       }
       $field = 'left(pay_time,10) as time ,count(id) as news_num,name,sum(income) as income,order_source,article_title,id';
       $data  = M('gg_fxg')->field($field)->where($where)->group('time,article_title')->order('time desc,income desc,news_num desc')->select();
       $this->assign('user', $user);
       $this->assign('data', $data);
       $this->assign('time', date('Y-m-d', $start_time) . ' - ' . date('Y-m-d', $end_time));
       $this->display();
   }

}