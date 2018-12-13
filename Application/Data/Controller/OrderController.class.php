<?php
/**
 * 对订单数据做二次加工
 * Author: dongguangqi
 */

namespace Data\Controller;

/**
 *
 *
 * @package Data\Controller
 */
class OrderController extends CommonController {

    public function __construct() {
        parent::__construct();
        set_time_limit(600);
    }

    /**
     * 订单佣金二次加工，按Pid统计出每天的数据
     * 含：每日付款总数、预估收入、结算收入、交易额等
     */
    public function commission($day_zero = 0) {
        $order_model = M('order_commission');

        if ($day_zero > 0) {
            $yesterday = date("Ymd", $day_zero);
            $yesterday_zero = $day_zero; //  昨天0点
        } else {
            $yesterday = date("Ymd", time() - 86400);
            $yesterday_zero = strtotime($yesterday); //  昨天0点
        }

        $paid_where      = array(
            'UNIX_TIMESTAMP(create_time)' => array('between', array($yesterday_zero, $yesterday_zero + 86399)),
            'pay_status' => array('neq', 'fail')
        );
        $field = 'user_id as uid, count(order_id) as total_paid_num, sum(fee) as total_paid_fee, sum(total_money) as total_gmv, pid';
        $commission_data = $order_model->where($paid_where)->field($field)->index('pid')->group('pid')->select();

        foreach ($commission_data as $pid => $order) {
            $commission_data[$pid]['day'] = $yesterday;
            $commission_data[$pid]['uid'] = intval($order['uid']);
            $commission_data[$pid]['total_settle_fee'] = 0;

            //缓存总交易额数据 暂不使用
            //$total_gmv = floatval(S("tdk_total_gmv_" . $order['user_id']));
            //S("tdk_total_gmv_" . $order['user_id'], $total_gmv + $order['total_gmv']);
        }

        $settle_where      = array(
            'UNIX_TIMESTAMP(earning_time)' => array('between', array($yesterday_zero, $yesterday_zero + 86399)),
        );
        $field = 'user_id, sum(fee) as total_settle_fee, pid';
        $settle_data = $order_model->where($settle_where)->field($field)->index('pid')->group('pid')->select();
        foreach ($settle_data as $pid => $order) {
            if (isset($commission_data[$pid])) {
                $commission_data[$pid]['total_settle_fee'] = $order['total_settle_fee'];
            } else {
                $commission_data[$pid] = array(
                    'uid' => intval($order['user_id']),
                    'total_paid_num' => 0,
                    'total_paid_fee' => 0,
                    'total_gmv' => 0,
                    'pid' => $order['pid'],
                    'day' => $yesterday,
                    'total_settle_fee' => $order['total_settle_fee'],
                );
            }
        }

        if (M('bi_order_commission')->addAll(array_values($commission_data))) {
            echo "success";
        } else {
            echo "fail";
        }
    }

    /**
     * 初始化订单佣金二次加工数据
     */
    /*public function initCommission() {
        $now = time();
        $day_zero = 1498838400;
        for ($day_zero = 1498838400; $day_zero < ($now - 86400); $day_zero += 86400) {
            echo date('Y-m-d', $day_zero);
            $this->commission($day_zero);
            echo "\n";
        }
    }*/
}