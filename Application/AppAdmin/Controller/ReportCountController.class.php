<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/10 0010
 * Time: 下午 2:53
 */

namespace AppAdmin\Controller;

/**
 * 报表统计
 * Class ReportCountController
 *
 * @package AppAdmin\Controller
 */
class ReportCountController extends CommonController {

    /**
     * 基本信息
     */
    public function index() {
        $admin_id       = session('auth_user');
        $department_id  = session('department_id');
        $position_level = session('position_level');
        $where          = array();
        if ($position_level == 'middle') {
            $where['department_id'] = $department_id;
        } elseif ($position_level == 'basic') {
            $where['admin_id'] = $admin_id;
        }
        $merchant_num      = M('merchant')->where($where)->count('id');
        $online_num        = M('deal')->where($where)->where(array('status' => 'ing'))->count('id');
        $start_day         = date('Y-m-d', strtotime('-2 months'));
        $end_day           = date('Y-m-d');
        $count_res         = M('deal')->field("FROM_UNIXTIME(online_time,'%Y-%m-%d') as day,count(id) as day_online_num")->where($where)->where(array('status' => array('neq', 'reject'), 'online_time' => array('BETWEEN', array(strtotime($start_day), strtotime($end_day)))))->index('day')->group('day')->select();
        $sum_res           = M('deal')->field("FROM_UNIXTIME(settle_time,'%Y-%m-%d') as day,sum(real_settle_money) as day_money,sum(order_total_money) as day_pay_money")->where($where)->where(array('status' => array('in', array('apply_settle', 'pending_paid', 'confirmed_paid')), 'online_time' => array('BETWEEN', array(strtotime($start_day), strtotime($end_day)))))->index('day')->group('day')->select();
        $pay_sum_money     = M('deal')->where($where)->sum('order_total_money');
        $service_fee_money = M('deal')->where($where)->sum('real_settle_money');
        $current_day       = $start_day;
        $data              = $day = $goods_num = $total_money = $pay_money = array();
        while (strtotime($current_day) <= strtotime($end_day)) {
            $data[]      = $end_day;
            $end_day = date('Y-m-d', strtotime("$end_day -1 days"));
        }
        foreach ($data as $val) {
            $day[]         = $val;
            $goods_num[]   = isset($count_res[$val]['day_online_num']) ? $count_res[$val]['day_online_num'] : 0;
            $total_money[] = isset($sum_res[$val]['day_money']) ? $sum_res[$val]['day_money'] : 0;
            $pay_money[]   = isset($sum_res[$val]['day_pay_money']) ? $sum_res[$val]['day_pay_money'] : 0;
        }
        $assign = array(
            'online_num'        => $online_num,
            'merchant_num'      => $merchant_num,
            'day'               => json_encode($day),
            'goods_num'         => json_encode($goods_num),
            'total_money'       => json_encode($total_money),
            'pay_money'         => json_encode($pay_money),
            'pay_sum_money'     => $pay_sum_money ? $pay_sum_money : 0,
            'service_fee_money' => $service_fee_money ? $service_fee_money : 0,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 业绩统计
     */
    public function myCount() {
        $search_department_id = I('get.department_id', 0, 'int');
        $time                 = I('get.time', '', 'trim');
        $search_admin_id      = I('get.admin_id', 0, 'int');
        $type                 = I('get.type', 'select', 'trim');
        $admin_id             = session('auth_user');
        $department_id        = session('department_id');
        $position_level       = session('position_level');

        $where                = array('s.status' => 'confirmed_paid', 's.merchant_payment_day' => array('egt', strtotime(date('Y-m-01'))));
        $admin_user           = array();
        $department           = $this->_getDepartment();
        if ($position_level == 'middle') {
            $admin_user               = M('admin')->where(array('department_id' => $department_id))->getField('id,full_name as name');
            $where['s.department_id'] = $department_id;
        } elseif ($position_level == 'basic') {
            $where['s.admin_id'] = $admin_id;
        }
        if ($search_department_id) {
            $where['s.department_id'] = $search_department_id;
        }
        if ($search_admin_id) {
            $where['s.admin_id'] = $search_admin_id;
        }

        if(empty($time)){
            $time = date('m/01/Y').'-'.date('m/d/Y');
        } else {
            list($start_time, $end_time) = explode('-', $time);
            $where['s.merchant_payment_day'] = array('between', array(strtotime($start_time), strtotime("$end_time +1 days") - 1));
        }
        $data = M('deal_settle')
            ->alias('s')
            ->field('d.department_name,sum(s.paid_total_money) as paid_total_money, group_concat(deal_ids) as deal_ids_str, a.full_name as name, s.admin_id')
            ->join('left join ytt_department d ON d.id = s.department_id')
            ->join('left join ytt_admin a ON a.id = s.admin_id')
            ->where($where)
            ->group('admin_id')
            ->order('paid_total_money desc')
            ->select();

        $all_total_money = 0; //所有人支付金额
        $all_total_deal_num = 0; //所有人的上单总数
        foreach ($data as $key => $deal_settle) {
            $all_total_money += $deal_settle['paid_total_money'];
            $data[$key]['deal_ids_num'] = count(explode(',', $deal_settle['deal_ids_str'])); //单个人的上单数
            $all_total_deal_num += $data[$key]['deal_ids_num'];
        }

        if ($type == "down") {
            $head = array(
                'department_name'  => '所属部门',
                'name'             => '客户经理',
                'paid_total_money' => '业绩总额',
                'deal_ids_num'     => '上单总数',
            );
            if ($time) {
                $file_name = '业绩报表' . $time . '下载';
            } else {
                $file_name = '业绩报表' . date('Y-m-01') . "----" . date('Y-m-d') . '下载';
            }
            download_xls($data, $head, $file_name);
        } else {
            $this->assign(
                array('data' => $data, 'department' => $department, 'admin_user' => $admin_user,
                      'all_total_money' => $all_total_money, 'all_total_deal_num' => $all_total_deal_num, 'time' => $time)
            );
            $this->display();
        }
    }

}