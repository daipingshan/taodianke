<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2017/1/4
 * Time: 0:59
 */

namespace AppApi\Controller;

/**
 * Class UserController
 *
 * @package AppApi\Controller
 */
class BalanceController extends CommonController {

    /**
     * 用户信息
     */
    public function index() {

        $order_model = M('order_commission');

        // 拼接时间戳
        $now            = time();
        $today_zero     = strtotime(date("Y-m-d")); //  当天凌晨
        $yesterday_zero = $today_zero - 86400;        //  昨天凌晨
        $yesterday      = date('Ymd', $today_zero - 86400); // 昨天
        $last_month     = date('Ymd', $now - 29 * 86400); //30天前

        //  成交预估收入
        $day_where      = array(
            'user_id'                     => $this->uid,
            'UNIX_TIMESTAMP(create_time)' => array('between', array($yesterday_zero, $now)),
        );
        $success_amount = $order_model->where($day_where)->field('fee, total_money, create_time, pay_status')->select();

        $today_success_amount     = 0; //今日成交预估收入
        $yesterday_success_amount = 0; //昨天成交预估收入
        $today_gmv                = 0; //今日交易额
        $yesterday_gmv            = 0; //昨日交易额
        foreach ($success_amount as $k => $v) {
            if ($v['pay_status'] == 'paid' || $v['pay_status'] == 'settle') {
                $order_create_time = strtotime($v['create_time']);
                if ($order_create_time >= $today_zero) {
                    $today_success_amount += $v['fee'];
                    $today_gmv += $v['total_money'];
                } else {
                    $yesterday_success_amount += $v['fee'];
                    $yesterday_gmv += $v['total_money'];
                }
            }
        }


        //未结算预估收入
        $no_settle_where      = array(
            'user_id'     => $this->uid,
            'pay_status'  => 'settle',
            'withdraw_id' => 0,
        );
        $no_settlement_amount = $order_model->where($no_settle_where)->sum('fee');

        //累计结算收入
        $settlement_amount = M('order_withdraw')->where(array('user_id' => $this->uid))->sum('amount');

        //排除掉昨天的主要考虑凌晨未二次计算好昨天数据的时间段
        $where     = array('uid' => $this->uid, 'day' => array('lt', $yesterday));
        $orders    = M('bi_order_commission')->where($where)->select();
        $month_gmv = $today_gmv + $yesterday_gmv; //30日交易额
        $total_gmv = $today_gmv + $yesterday_gmv; //总交易额
        foreach ($orders as $key => $order) {
            $total_gmv += $order['total_gmv'];

            if ($order['day'] >= $last_month) {
                $month_gmv += $order['total_gmv'];
            }
        }

        //账户余额
        $amount = $this->_amount();
        $today  = date('d');
        if ($today < C('balance_time')) {
            $wait_withdraw_amount = $this->_amount('select_amount');
        } else {
            $wait_withdraw_amount = $amount;
        }
        $paid_amount = $order_model->where(array('pay_status' => 'paid', 'user_id' => $this->uid))->sum('fee');
        $data        = array(
            // 兼容1.5.0 之前的版本  start
            'current_month_amount'       => 0,
            'pro_month_amount'           => 0,
            'current_month_pay_amount'   => 0,
            'current_day_amount'         => 0,
            'current_day_success_count'  => 0,
            'pro_day_amount'             => 0,
            'pro_day_success_count'      => 0,
            // end

            // 今日预估
            'current_day_success_amount' => $today_success_amount ? $today_success_amount : 0,
            // 昨日预估
            'pro_day_success_amount'     => $yesterday_success_amount ? $yesterday_success_amount : 0,
            // 未结算预估
            'no_settlement_amount'       => $no_settlement_amount ? $no_settlement_amount : 0,
            // 累计收入
            'settlement_amount'          => $settlement_amount ? $settlement_amount : 0,
            //  可结算
            'amount'                     => $amount ? $amount : 0,
            // 付款预估收入
            'paid_amount'                => $paid_amount ? $paid_amount : 0,
            // 提现日待提现金额
            'wait_withdraw_amount'       => $wait_withdraw_amount ? $wait_withdraw_amount : 0,

            'today_gmv'     => $today_gmv, //今日交易额
            'yesterday_gmv' => $yesterday_gmv, //昨日交易额
            'month_gmv'     => $month_gmv, //30天交易额
            'total_gmv'     => $total_gmv, //总交易额
        );

        $this->outPut($data, 0);
    }

    /**
     * 获取订单明细
     */
    public function getOrder() {
        $where         = array('o_c.user_id' => $this->uid);
        $pay_status    = I('get.payStatus', '', 'trim');
        $page          = I('get.page', 0, 'int');
        $withdraw_id   = I('get.withdraw_id', 0, 'int');
        $add_start_day = I('get.add_start_day', '', 'trim');
        $add_end_day   = I('get.add_end_day', '', 'trim');
        $last_id       = I('get.last_id', 0, 'int');
        if ($pay_status == '1') {
            $pay_status = 'paid';
        }
        if ($pay_status == '2') {
            $pay_status = 'fail';
        }
        if ($pay_status == '3') {
            $pay_status = 'settle';
        }
        if ($pay_status == '4') {
            $pay_status               = 'settle';
            $where['o_c.withdraw_id'] = 0;
        }
        if ($pay_status) {
            $where['o_c.pay_status'] = $pay_status;
        }
        if ($withdraw_id) {
            $where['o_c.withdraw_id'] = $withdraw_id;
        }
        if ($add_start_day && $add_end_day) {
            $where['UNIX_TIMESTAMP(o_c.create_time)'] = array('between', array(strtotime($add_start_day), strtotime($add_end_day) + 86399));
        }
        $join_order = "left join ytt_order o ON o.order_id = o_c.order_id and o_c.item_id = o.item_id and o_c.order_num = o.order_num";
        $join_item  = "left join ytt_items i ON i.num_iid = o_c.item_id";
        if ($page) {
            $page--;
            $start_num = $page * $this->reqnum;
            $data      = M('order_commission')->alias('o_c')->field('o_c.*,o.title,o.img,o.order_type as shop_type_view,o.shop_type,i.id as item_num_id')->join($join_order)->join($join_item)->where($where)->order('o_c.create_time desc')->limit($start_num, $this->reqnum + 1)->select();
        } else {
            if ($last_id) {
                $where['o_c.id'] = array('lt', $last_id);
            }
            $data = M('order_commission')->alias('o_c')->field('o_c.*,o.title,o.img,o.order_type as shop_type_view,o.shop_type,i.id as item_num_id')->join($join_order)->join($join_item)->where($where)->order('o_c.id desc')->limit($this->reqnum + 1)->select();
        }
        $this->setHasNext(false, $data);
        $order_status = array('fail' => '订单失效', 'settle' => '订单结算', 'success' => '订单成功', 'paid' => '订单付款', 'refund' => '订单退款');
        foreach ($data as &$val) {
            $commission_rate        = round($val['fee'] / $val['total_money'] * 100, 1);
            $val['total_fee']       = $val['total_money'];
            $val['discount_rate']   = $commission_rate . "%";
            $val['commission_rate'] = $commission_rate . "%";
            $val['paystatus']       = $order_status[$val['pay_status']];
            $val['pay_status']      = $order_status[$val['pay_status']];
            $val['earningtime']     = $val['earning_time'];
            if ($val['order_pid'] == $val['pid']) {
                $val['promoter'] = "self";
            } else {
                $val['promoter'] = "sub_proxy";
            }
            if ($val['item_num_id'] > 0) {
                $val['item_is_online'] = "Y";
            } else {
                $val['item_is_online'] = "N";
            }
            $val['num_iid'] = $val['item_id'];
            if ($val['shop_type'] == 'J') {
                $val['goods_type'] = "chuchujie";
            } else {
                $val['goods_type'] = "tdk";
            }
        }
        $this->outPut($data, 0);
    }

    /**
     * 获取更多统计信息
     */
    public function search() {
        $start_day = I('get.start_day', '', 'trim');
        $end_day   = I('get.end_day', '', 'trim');
        $zone_id   = I('get.zone_id', 0, 'intval');
        if (!$start_day || !$end_day) {
            $this->outPut(null, -1, null, '请求参数不完整，请重试！');
        }
        $data = array();

        //改为纯数字的日期
        $start_day_int = intval(str_replace('-', '', $start_day));
        $end_day_int   = intval(str_replace('-', '', $end_day));

        //给所有日期各项数据初始化值为0
        $temp_day = $start_day_int;
        while ($temp_day <= $end_day_int) {
            $temp_day        = strval($temp_day);
            $data[$temp_day] = array(
                'day'               => $temp_day[4] . $temp_day[5] . '.' . $temp_day[6] . $temp_day[7],
                'paid_num'          => 0,
                'paid_income'       => 0,
                'settlement_income' => 0,
                'total_gmv'         => 0
            );
            $temp_day        = intval(date("Ymd", strtotime("$temp_day +1 days")));
        }

        if (0 == $zone_id) {
            $where = array('uid' => $this->uid, 'day' => array('between', array($start_day_int, $end_day_int)));
        } else {
            $zones = $this->_getZone();
            $zone  = $zones[$zone_id];

            $where = array(
                'pid' => array('in', array($zone['pid'], $zone['dwxk_adsense_id'])),
                'day' => array('between', array($start_day_int, $end_day_int))
            );
        }
        $field  = 'day, sum(total_paid_num) as paid_num, sum(total_paid_fee) as paid_income, sum(total_settle_fee) as settlement_income, sum(total_gmv) as total_gmv';
        $orders = M('bi_order_commission')->where($where)->field($field)->index('day')->group('day')->select();
        foreach ($orders as $day => $order) {
            $day          = strval($day);
            $format_day   = $day[4] . $day[5] . '.' . $day[6] . $day[7];
            $order['day'] = $format_day;

            $order['paid_income']       = $order['paid_income'] == 0 ? 0 : $order['paid_income'];
            $order['settlement_income'] = $order['settlement_income'] == 0 ? 0 : $order['settlement_income'];
            $order['total_gmv']         = $order['total_gmv'] == 0 ? 0 : $order['total_gmv'];

            $data[$day] = $order;
        }

        $today_str = date('Ymd');
        if ($start_day_int <= $today_str && $end_day_int >= $today_str) {
            $data[$today_str] = $this->_getTodayOrderCommission($zone_id);
        }

        $data = array_values($data);
        $this->outPut($data, 0);
    }

    protected function _getTodayOrderCommission($zone_id = 0) {
        $today      = date('Ymd');
        $today_zero = strtotime($today);

        if (0 == $zone_id) {
            $paid_where = array(
                'user_id'                     => $this->uid,
                'pay_status'                  => array('in', array('paid', 'settle')),
                'UNIX_TIMESTAMP(create_time)' => array('egt', $today_zero)
            );

            $settle_where = array(
                'user_id'                      => $this->uid,
                'pay_status'                   => 'settle',
                'UNIX_TIMESTAMP(earning_time)' => array('egt', $today_zero)
            );
        } else {
            $zones = $this->_getZone();
            $zone  = $zones[$zone_id];

            $paid_where = array(
                'user_id'                     => $this->uid,
                'pay_status'                  => array('in', array('paid', 'settle')),
                'UNIX_TIMESTAMP(create_time)' => array('egt', $today_zero),
                'pid'                         => array('in', array($zone['pid'], $zone['dwxk_adsense_id']))
            );

            $settle_where = array(
                'user_id'                      => $this->uid,
                'pay_status'                   => 'settle',
                'UNIX_TIMESTAMP(earning_time)' => array('egt', $today_zero),
                'pid'                          => array('in', array($zone['pid'], $zone['dwxk_adsense_id']))
            );
        }

        $paid_data   = M('order_commission')->field('id,fee,total_money')->where($paid_where)->select();
        $paid_num    = 0; //订单数
        $paid_income = 0; //今日预估收入
        $total_gmv   = 0; //总交易额
        foreach ($paid_data as $key => $order) {
            $paid_num++;
            $paid_income += $order['fee'];
            $total_gmv += $order['total_money'];
        }

        $settlement_income = M('order_commission')->where($settle_where)->sum('fee');

        return array(
            'day'               => $today[4] . $today[5] . '.' . $today[6] . $today[7],
            'paid_num'          => $paid_num,
            'paid_income'       => $paid_income,
            'settlement_income' => floatval($settlement_income),
            'total_gmv'         => $total_gmv
        );
    }
}