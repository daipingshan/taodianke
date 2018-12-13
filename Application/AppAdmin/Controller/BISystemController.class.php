<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/1/15
 * Time: 11:28
 */

namespace AppAdmin\Controller;

/**
 * BI系统
 * Class BISystemController
 *
 * @package AppAdmin\Controller
 */
class BISystemController extends CommonController {

    /**
     * 公司概况
     */
    public function index() {
        $start_time   = date('Y-m-d') . " 00:00:00";
        $end_time     = date('Y-m-d') . " 23:59:59";
        $today_paid   = M('order')->where(array('pay_status' => 'paid', 'create_time' => array('between', array($start_time, $end_time))))->sum('fee');
        $today_settle = M('order')->where(array('pay_status' => 'settle', 'earning_time' => array('between', array($start_time, $end_time))))->sum('fee');
        $yes_paid     = M('order')->where(array('pay_status' => 'paid', 'create_time' => array('between', array(date("Y-m-d", strtotime("-1 days")) . " 00:00:00", date("Y-m-d", strtotime("-1 days")) . " 23:59:59"))))->sum('fee');
        $yes_settle   = M('order')->where(array('pay_status' => 'settle', 'earning_time' => array('between', array(date("Y-m-d", strtotime("-1 days")) . " 00:00:00", date("Y-m-d", strtotime("-1 days")) . " 23:59:59"))))->sum('fee');
        $item_list    = M('items')->field('shop_type,count(id) as num')->where(array('pass' => 1))->index('shop_type')->group('shop_type')->select();
        $data         = array(
            'today_paid'   => $today_paid,
            'today_settle' => $today_settle,
            'yes_paid'     => $yes_paid,
            'yes_settle'   => $yes_settle,
            'item_list'    => $item_list,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 订单统计
     */
    public function order() {
        $time       = I('get.time', '', 'trim');
        $start_time = date("m/d/Y", strtotime("-1 months"));
        $end_time   = date("m/d/Y");
        $where      = array('pay_status' => array('in', array('paid', 'settle')));
        if ($time) {
            list($start_time, $end_time) = explode('-', $time);
        } else {
            $time = $start_time . " - " . $end_time;
        }
        $start_time  = date("Y-m-d", strtotime($start_time));
        $end_time    = date("Y-m-d", strtotime($end_time));
        $where_time  = array('create_time' => array('between', array($start_time . " 00:00:00", $end_time . " 23:59:59")));
        $order_list  = M('order')->field("id,fee,pay_status,total_money,item_id,left(create_time,10) as day,pid")->where($where)->where($where_time)->order('day desc')->select();
        $map_time    = array('earning_time' => array('between', array($start_time . " 00:00:00", $end_time . " 23:59:59")));
        $order_money = M('order')->field("left(earning_time,10) as day,sum(fee) as money")->where(array('pay_status' => 'settle'))->where($map_time)->group('day')->order('day desc')->select();
        $data        = $total_data = $pid_num = $item_num = array();
        $num         = $total_money = $total_fee = $all_fee = 0;
        foreach ($order_list as $order) {
            $num++;
            $pid_num[$order['pid']]      = $order['pid'];
            $item_num[$order['item_id']] = $order['item_id'];
            $total_money                 = $total_money + $order['total_money'];
            $all_fee                     = $all_fee + $order['fee'];
            if (isset($data[$order['day']])) {
                $pid_day_num[$order['pid']]      = $order['pid'];
                $item_day_num[$order['item_id']] = $order['item_id'];
                $data[$order['day']]['num']      = $data[$order['day']]['num'] + 1;
                $data[$order['day']]['money']    = $data[$order['day']]['money'] + $order['total_money'];
                $data[$order['day']]['pid_num']  = count($pid_day_num);
                $data[$order['day']]['item_num'] = count($item_day_num);
                $data[$order['day']]['all_fee']  = $data[$order['day']]['all_fee'] + $order['fee'];
            } else {
                $pid_day_num                     = $item_day_num = array();
                $pid_day_num[$order['pid']]      = $order['pid'];
                $item_day_num[$order['item_id']] = $order['item_id'];
                $data[$order['day']]             = array(
                    'day'      => $order['day'],
                    'num'      => 1,
                    'money'    => $order['total_money'],
                    'pid_num'  => 1,
                    'item_num' => 1,
                    'fee'      => 0,
                    'all_fee'  => $order['fee'],
                );
            }
        }
        foreach ($order_money as $val) {
            $total_fee                = $total_fee + $val['money'];
            $data[$val['day']]['fee'] = $val['money'];
        }
        $total_data = array(
            'num'      => $num,
            'money'    => $total_money,
            'fee'      => $total_fee,
            'all_fee'  => $all_fee,
            'pid_num'  => count($pid_num),
            'item_num' => count($item_num),
        );
        $this->assign(array('data' => $data, 'total_data' => $total_data, 'time' => $time));
        $this->display();
    }
}