<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31 0031
 * Time: 上午 9:49
 */

namespace AppApi\Controller;

/**
 * Class ProxyController
 *
 * @package AppApi\Controller
 */
class ProxyController extends CommonController {

    /**
     * 获取子代理
     */
    public function subProxy() {
        $page  = I('get.page', 1, 'int');
        $where = array('u.ParentID' => $this->uid);
        $page--;
        $start_num = $page * $this->reqnum;
        $data      = M('user')
            ->alias('u')
            ->join('left join ytt_user_proxy_ratio p ON p.uid = '.$this->uid.' and p.cid = u.id')
            ->where($where)
            ->field('u.id as proxy_user_id,u.pid as proxy_pid,u.mobile,u.real_name,p.dip as ratio')
            ->order('u.id desc')
            ->limit($start_num, $this->reqnum + 1)
            ->select();
        $this->setHasNext(false, $data, $this->reqnum);
        foreach ($data as &$val) {
            $val['ratio']  = isset($val['ratio']) ? intval($val['ratio']) : 100;
            $val['mobile'] = substr($val['mobile'], 0, 3)."****".substr($val['mobile'], 7, 4);
        }
        unset($val);
        $this->outPut($data, 0);
    }

    /**
     * 获取子代理订单
     */
    public function subProxyOrderCommission() {
        $page      = I('get.page', 1, 'trim');
        $proxy_pid = I('get.proxy_pid', '', 'trim');
        $where     = array('o_c.user_id' => $this->uid, 'o_c.from_pid' => $proxy_pid, 'o_c.pay_status' => 'settle');
        $page--;
        $start_num    = $page * $this->reqnum;
        $order_status = array('fail' => '订单失效', 'settle' => '订单结算', 'success' => '订单成功', 'paid' => '订单付款', 'refund' => '订单退款');
        $join_order   = "left join ytt_order o ON o.order_id = o_c.order_id and o_c.item_id = o.item_id and o_c.order_num = o.order_num";
        $join_item    = "left join ytt_items i ON i.num_iid = o_c.item_id";
        $data         = M('order_commission')->alias('o_c')->field('o_c.*,o.title,o.img,o.order_type as shop_type_view,o.shop_type,i.id as item_num_id')->join($join_order)->join($join_item)->where($where)->order('o_c.create_time desc')->limit($start_num, $this->reqnum + 1)->select();
        $this->setHasNext(false, $data, $this->reqnum);
        foreach ($data as &$val) {
            $val['discount_rate']   = $val['commission_rate']."%";
            $val['commission_rate'] = $val['commission_rate']."%";
            $val['pay_status']      = $order_status[$val['pay_status']];
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
        unset($val);
        $this->outPut($data, 0);
    }

    /**
     * 设置代理分成比例
     */
    public function subProxyRatio() {
        $proxy_user_id = I('post.proxy_user_id', 0, 'int');
        $user_id       = $this->uid;
        $ratio         = I('post.ratio', 0, 'int');
        if (!$proxy_user_id) {
            $this->outPut(null, -1, null, ' 请求参数不合法！');
        }
        if ($ratio <= 0 || $ratio > 100) {
            $this->outPut(null, -1, null, '分成比例必须在1-100之间！');
        }
        $id = M('user_proxy_ratio')->where(array('uid' => $user_id, 'cid' => $proxy_user_id))->getField('id');
        if (!$id) {
            $res = M('user_proxy_ratio')->add(array('uid' => $user_id, 'cid' => $proxy_user_id, 'dip' => $ratio));
        } else {
            $res = M('user_proxy_ratio')->save(array('id' => $id, 'dip' => $ratio));
        }
        if ($res !== false) {
            $this->outPut(null, 0, null, ' 设置成功');
        } else {
            $this->outPut(null, -1, null, ' 设置失败！');
        }
    }

    /**
     * 代理获取公众号分享二维码
     *
     * @param $uid
     * @return array
     */
    public function getWxQrcode() {
        $proxy = M('user')->where(array('id' => $this->uid))->find();
        if (empty($proxy['pid'])) {
            $this->outPut(null, -1, null, ' 您不是代理，暂时不能分享二维码！');
        } else if (empty($proxy['wx_qrcode_url'])) {
            $img_url    = $this->_getQrcodeUrl($proxy['pid'], $proxy['store_type']);
            $qrcode_url = getImgUrl($img_url);
            M('user')->where(array('id' => $this->uid))->setField('wx_qrcode_url', $img_url);
        } else {
            $tmp = basename($proxy['wx_qrcode_url'], '.jpg');
            $tmp = explode('_', $tmp);
            if (C('WEIXIN_MP.share_bg_update_time') > $tmp[1]) {
                $img_url    = $this->_getQrcodeUrl($proxy['pid'], $proxy['store_type']);
                $qrcode_url = getImgUrl($img_url);
                M('user')->where(array('id' => $this->uid))->setField('wx_qrcode_url', $img_url);
            } else {
                $qrcode_url = getImgUrl($proxy['wx_qrcode_url']);
            }
        }
        $data = array(
            'qrcode_url' => $qrcode_url,
        );
        $this->outPut($data, 0);
    }

    /**
     * 查看历史推广记录
     */
    public function customerList() {
        $page      = I('get.page', 1, 'intval');
        $start_day = I('get.start_day', '', 'trim');
        $end_day   = I('get.end_day', '', 'trim');

        $page--;
        $start_num    = $page * $this->reqnum;
        $where = array(
            'proxy_uid' => $this->uid,
            'add_time'  => array('between', array(strtotime($start_day), strtotime($end_day) + 86399)),
            'status'    => 'Y'
        );
        $total_num = M('wxuser')->where($where)->count();
        $data      = M('wxuser')->field('headimgurl,nickname,add_time')->where($where)->order('add_time desc')->limit($start_num, $this->reqnum + 1)->select();
        $this->setHasNext(false, $data, $this->reqnum);

        foreach ($data as $key => $user) {
            if (empty($user['nickname'])) {
                $data[$key]['nickname'] = '未知';
            }

            if (empty($user['headimgurl'])) {
                $data[$key]['headimgurl'] = 'http://';
            }
        }

        $this->outPut(array('total_num' => $total_num, 'list' => $data), 0);
    }

}