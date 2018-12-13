<?php

namespace Proxy\Controller;

class IndexController extends CommonController{

    /**
     * 管理首页
     */
    public function index(){
        $start_time   = strtotime(date('Y-m-01'));
        $end_time     = time();
        $where        = array('user_id' => session('Auth_user'));
        $settle_where = array('pay_status' => 'settle', 'UNIX_TIMESTAMP(earning_time)' => array('between', array($start_time, $end_time)));
        $paid_where   = array('pay_status' => array('in', 'paid,settle'), 'UNIX_TIMESTAMP(create_time)' => array('between', array($start_time, $end_time)));
        $settle_money = M('order_commission')->where($where)->where($settle_where)->sum('fee');
        $paid_money   = M('order_commission')->where($where)->where($paid_where)->sum('fee');
        if(session('proxy_type') == 1){
            $pid = session('pid');
            list($one, $two, $_, $_) = explode('_', $pid);
            $num = M('user')->where(array('_string' => "pid like '{$one}_$two%' AND pid <> '{$pid}'"))->count('id');
        }else{
            $num = M('user')->where(array('ParentID' => session('Auth_user')))->count('id');
        }
        $total_money = M('order_withdraw')->where(array('user_id' => session('Auth_user')))->sum('amount');

        // 代理分享二维码
        $proxy = M('user')->where(array('id' => session('Auth_user')))->find();

        if (empty($proxy['pid'])) {
            $this->outPut(null, -1, null, ' 您不是代理，暂时不能分享二维码！');
        } else if (empty($proxy['wx_qrcode_url'])) {
            $img_url = $this->_getQrcodeUrl($proxy['pid'], $proxy['store_type']);
            $qrcode_url = getImgUrl($img_url);
            M('user')->where(array('id' => session('Auth_user')))->setField('wx_qrcode_url', $img_url);
        } else {
            $tmp = basename($proxy['wx_qrcode_url'], '.jpg');
            $tmp = explode('_', $tmp);
            if (C('WEIXIN_MP.share_bg_update_time') > $tmp[1]) {
                $img_url = $this->_getQrcodeUrl($proxy['pid'], $proxy['store_type']);
                $qrcode_url = getImgUrl($img_url);
                M('user')->where(array('id' => session('Auth_user')))->setField('wx_qrcode_url', $img_url);
            } else {
                $qrcode_url = $proxy['wx_qrcode_url'];
            }
        }

        $data        = array(
            'settle_money' => $settle_money ? $settle_money : 0,
            'paid_money'   => $paid_money ? $paid_money : 0,
            'num'          => $num ? $num : 0,
            'total_money'  => $total_money ? $total_money : 0,
            'qrcode'       => $qrcode_url,
        );
        $this->assign($data);
        $this->display();
    }
}
