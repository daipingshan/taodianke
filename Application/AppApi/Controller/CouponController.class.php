<?php
/**
 * 优惠券相关功能
 *
 * User: dongguangqi
 * Date: 2017/12/25
 */

namespace AppApi\Controller;

use Common\Org\Http;

/**
 * Class CouponController
 *
 * @package AppApi\Controller
 */
class CouponController extends CommonController {

    /**
     * 查询券历史使用记录
     */
    public function index() {
        $cashier_id  = I('get.cashier_id', 0, 'intval');
        $start_day   = I('get.start_day', '', 'trim');
        $end_day     = I('get.end_day', '', 'trim');
        $page        = I('get.page', 1, 'int');

        if ('' == $start_day || '' == $end_day) {
            $this->outPut(null, -1, null, '参数异常');
        }

        if (0 == $cashier_id) {
            if (1 == $this->store_type) {
                $where = array('check_store_uid' => $this->uid);
            } else if (2 == $this->store_type) {
                $check_cashier_id = M('cashier')->where('cashier_uid=' . $this->uid)->getField('id');
                $where = array('check_cashier_id' => $check_cashier_id);
            } else {
                $this->outPut(null, -1, null, '您的账号无权查看');
            }
        } else {
            $where = array('check_cashier_id' => $cashier_id);
        }

        $start_time = strtotime($start_day);
        $end_time   = strtotime($end_day) + 86399;
        $where['used_time'] = array('between', array($start_time, $end_time));

        //获得时间段内各项总数
        $page--;
        $start_num = $page * $this->reqnum;
        $field = 'count(*) as total_num, sum(money) as total_coupon_money, sum(limit_money) as total_income';
        $total = M('coupon')->where($where)->field($field)->find();
        if (!isset($total['total_num'])) {
            $this->outPut(null, -1, null, '查询异常，请重试');
        }

        //获得当页具体数据
        $field = 'coupon_sn,money,limit_money,used_time';
        $coupons = M('coupon')->where($where)->field($field)->order('used_time asc')->limit($start_num, $this->reqnum + 1)->select();
        $this->setHasNext(false, $coupons);

        $total['list'] = $coupons;
        $this->outPut($total, 0);
    }

    /**
     * 查看券
     */
    public function detail() {
        $coupon_sn = I('get.coupon_sn', 0, 'intval');

        $field = 'status,coupon_sn,money,limit_money,end_time';
        $coupon = M('coupon')->where(array('coupon_sn' => $coupon_sn))->field($field)->find();

        if (empty($coupon)) {
            $this->outPut(null, -1, null, '无此优惠券信息，请检查重试！');
        }

        if ($coupon['status'] == 1 || $coupon['end_time'] < time()) {
            $this->outPut(null, -1, null, '此优惠券已使用或已过期！');
        }

        $this->outPut($coupon, 0);
    }

    /**
     * 验证使用券
     */
    public function useCoupon() {
        $coupon_sn   = I('get.coupon_sn', 0, 'intval');
        $cashier_uid = $this->uid;

        $cashier = M('cashier')->where(array('cashier_uid' => $cashier_uid, 'status' => 1))->find();

        if (!isset($cashier['cashier_uid'])) {
            $this->outPut(null, -1, null, '您没有收银权限');
        }

        $save_data = array(
            'status' => 1,
            'used_time' => time(),
            'check_cashier_id' => $cashier['id'],
            'check_store_uid' => $cashier['store_uid']
        );

        $where = array(
            'coupon_sn' => $coupon_sn,
            'status' => 0,
            'end_time' => array('gt', time())
        );
        $save_data_num = M('coupon')->where($where)->save($save_data);
        if (false === $save_data_num || 0 == $save_data_num) {
            $this->outPut(null, -1, null, '没有正确的验证使用券，请重试！');
        }

        /**
         * 给用户发微信消息提醒券已使用
         */
        $wx_open_id = M('coupon')->alias('c')->join('left join ytt_store_wxuser wu ON c.wxuser_id = wu.id')->where('coupon_sn=' . $coupon_sn)->getField('wu.openid');
        if (!empty($wx_open_id)) {
            $info = array(
                'first' => '您好，您的优惠券已于' . date('H:i') . '核销成功',
                'keyword1' => $coupon_sn,
                'keyword2' => '宅喵生活线下门店消费',
                'keyword3' => '宅喵生活',
                'remark' => '点击查看详情'
            );
            $this->_sendWxMsg($info, $wx_open_id);
        }

        $this->outPut(null, 0, null, ' 验券成功');
    }

    /**
     * @param $info
     * @param $open_id
     * @return mixed
     */
    protected function _sendWxMsg($info, $open_id) {
        $url    = "https://api.weixin.qq.com/cgi-bin/message/template/send";
        $sendData  = array(
            'touser'      => $open_id,
            'template_id' => C('STORE_WEIXIN_BASE.check_coupon_tmpl_id'),
            "url" => C('STORE_WEIXIN_BASE.public_number_url') . '/Coupon/index?status=use',
            'topcolor'    => '#7B68EE',
            'data'        => array(
                'first'    => array(
                    'value' => urlencode($info['first']),
                    'color' => '#000000',
                ),
                'keyword1' => array(
                    'value' => urlencode($info['keyword1']),
                    'color' => '#333333',
                ),
                'keyword2' => array(
                    'value' => urlencode($info['keyword2']),
                    'color' => '#333333',
                ),
                'keyword3' => array(
                    'value' => urlencode($info['keyword3']),
                    'color' => '#333333',
                ),
                'remark'   => array(
                    'value' => urlencode($info['remark']),
                    'color' => '#666666'
                ),
            )
        );

        $json_data = urldecode(json_encode($sendData));
        $token     = $this->_getWeChatAccessToken('store_weChat_access_token');
        $url       = $url . "?access_token={$token}";
        $obj       = new Http();
        $res       = json_decode($obj->post($url, $json_data));

        if ($res->errcode == 0) {
            return true;
        } else {
            return false;
        }
    }

}