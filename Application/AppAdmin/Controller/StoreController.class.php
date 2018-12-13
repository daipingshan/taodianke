<?php
namespace AppAdmin\Controller;

use Common\Org\Http;

/**
 * Class StoreController
 *
 * @package AppAdmin\Controller
 */
class StoreController extends CommonController {
    /**
     * 门店管理
     */
    public function index() {
        $where = array('store_type' => 1);
        $count = M('user')->where($where)->count('id');
        $page  = $this->pages($count, $this->limit);
        $data  = M('user')->field('id,real_name,user_remark,address')->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($data as &$val) {
            $money        = M('coupon')->where(array('check_store_uid' => $val['id'], 'status' => 1))->sum('money');
            $val['money'] = $money ? $money : 0;

        }
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 编辑店主身份
     */
    public function setStore() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('post.id', 0, 'int');
        $info = M('user')->find($id);
        if (!$id || !$info) {
            $this->error('用户信息不存在无法设置店主身份！');
        }
        $realname    = I('post.realname', '', 'trim');
        $user_remark = I('post.user_remark', '', 'trim');
        $address     = I('post.address', '', 'trim');
        if (!$realname) {
            $this->error('真实姓名不能为空！');
        }
        if (!$user_remark) {
            $this->error('店铺名称不能为空！');
        }
        if (!$address) {
            $this->error('店铺地址不能为空！');
        }
        $save_data = array('id' => $id, 'real_name' => $realname, 'user_remark' => $user_remark, 'address' => $address);
        $res       = M('user')->save($save_data);
        if ($res !== false) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败！');
        }
    }

    /**
     * 收银员列表
     */
    public function cashier() {
        $store_id = I('get.store_id', 0, 'int');
        $info     = M('user')->find($store_id);
        if (!$store_id || !$info || $info['store_type'] != 1) {
            $this->error('店主信息有误，不能查看收银员');
        }
        $count = M('cashier')->where(array('store_uid' => $store_id))->count('id');
        $page  = $this->pages($count, $this->limit);
        $data  = M('cashier')->where(array('store_uid' => $store_id))->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 设置收银员状态
     */
    public function setCashierStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('cashier')->find($id);
        if (!$id || !$info) {
            $this->error('收银员信息不存在！');
        }
        $status = $info['status'] == 1 ? 0 : 1;
        if ($status == 1) {
            $count = M('cashier')->where(array('cashier_uid' => $info['cashier_uid'], 'status' => 1))->count('id');
            if ($count > 0) {
                $this->error('该收银员已在其他店入职，请核实后再更新！');
            }
        }
        $model = M();
        $model->startTrans();
        try {
            if (0 == $status) {
                M('cashier')->where(array('id' => $id))->save(array('status' => $status));
                M('user')->where(array('id' => $info['cashier_uid'], 'store_type' => 2))->save(array('store_type' => 0));
            } else {
                M('cashier')->where(array('id' => $id))->save(array('status' => $status, 'open_time' => time()));
                M('user')->where(array('id' => $info['cashier_uid'], 'store_type' => 0))->save(array('store_type' => 2));
            }
            $model->commit();
            $this->success('设置成功');
        } catch (\Exception $e) {
            $model->rollback();
            $this->error('设置失败');
        }
    }

    /**
     * 会员管理
     */
    public function user() {
        $uid      = I('get.uid', 0, 'int');
        $nickname = I('get.nickname', '', 'trim');
        $time     = I('get.time', '', 'trim');
        $type     = I('get.type', 'select', 'trim');
        $where    = array();
        if ($uid) {
            $where['wx.proxy_uid'] = $uid;
        }
        if ($nickname) {
            $where['wx.nickname'] = $nickname;
        }
        if ($time) {
            list($start_time, $end_time) = explode('-', $time);
            $where['wx.add_time'] = array('between', array(strtotime($start_time), strtotime("$end_time +1 days") - 1));
        }
        $field = "wx.id,wx.headimgurl,wx.nickname,wx.add_time,u.real_name as proxy_name,wx.username,wx.mobile";
        if ($type == "down") {
            $data = M('store_wxuser')->alias('wx')->field($field)->where($where)->join('left join ytt_user u ON u.id = wx.proxy_uid')->order('wx.id desc')->select();
            foreach ($data as &$val) {
                $val['add_time'] = date('Y-m-d H:i:s', $val['add_time']);
            }
            $head = array(
                'username'   => '用户姓名',
                'mobile'     => '手机号码',
                'nickname'   => '用户昵称',
                'proxy_name' => '所属代理',
                'add_time'   => '关注时间',
            );
            if ($time) {
                $file_name = '微信会员' . $time . '下载';
            } else {
                $file_name = '微信会员下载';
            }
            download_xls($data, $head, $file_name);
        } else {
            $count = M('store_wxuser')->alias('wx')->where($where)->count('id');
            $page  = $this->pages($count, $this->limit);
            $data  = M('store_wxuser')->alias('wx')->field($field)->where($where)->join('left join ytt_user u ON u.id = wx.proxy_uid')->limit($page->firstRow . ',' . $page->listRows)->order('wx.id desc')->select();
            foreach ($data as &$val) {
                $val['add_time'] = date('Y-m-d H:i:s', $val['add_time']);
            }
            $store_data = M('user')->field('id,user_remark as name')->where(array('store_type' => 1))->select();
            $this->assign('data', $data);
            $this->assign('store_data', $store_data);
            $this->assign('page', $page->show());
            $this->assign('time', $time);
            $this->display();
        }
    }

    /**
     * 抵扣中心
     */
    public function coupon() {
        $nickname         = I('get.nickname', '', 'trim');
        $coupon_sn        = I('get.coupon_sn', 0, 'int');
        $min              = I('get.min', '', 'trim');
        $max              = I('get.max', '', 'trim');
        $time             = I('get.time', '', 'trim');
        $check_store_uid  = I('get.check_store_uid', 0, 'int');
        $check_cashier_id = I('get.check_cashier_id', 0, 'int');
        $sort             = I('get.sort', '', 'trim');
        $type             = I('get.type', 'select', 'trim');
        $where            = array('c.status' => 1);
        if ($nickname) {
            $where['wx.nickname'] = $nickname;
        }
        if ($time) {
            list($start_time, $end_time) = explode('-', $time);
            $where['c.used_time'] = array('between', array(strtotime($start_time), strtotime("$end_time +1 days") - 1));
        }
        if ($coupon_sn) {
            $where['c.coupon_sn'] = $coupon_sn;
        }
        if ($min && $max) {
            $where['c.money'] = array('between', array($min, $max));
        }
        if ($check_store_uid) {
            $where['c.check_store_uid'] = $check_store_uid;
            if ($check_cashier_id) {
                $where['c.check_cashier_id'] = $check_cashier_id;
            }
        }
        if ($sort == 'money') {
            $order = "c.money desc";
        } else if ($sort == 'add_time') {
            $order = "wx.add_time desc";
        } else {
            $order = "c.used_time desc";
        }
        $field = "wx.headimgurl,wx.nickname,wx.add_time,u.real_name as proxy_name,c.used_time,c.money,c.limit_money";
        if ($type == "down") {
            $data = M('coupon')
                ->alias('c')
                ->field($field)
                ->where($where)
                ->join('left join ytt_wxuser wx ON wx.id = c.wxuser_id')
                ->join('left join ytt_user u ON u.id = wx.proxy_uid')
                ->order($order)
                ->select();
            foreach ($data as &$val) {
                $val['add_time']  = date('Y-m-d H:i:s', $val['add_time']);
                $val['used_time'] = date('Y-m-d H:i:s', $val['used_time']);
            }
            $head = array(
                'nickname'    => '用户昵称',
                'proxy_name'  => '所属代理',
                'money'       => '抵扣金额',
                'limit_money' => '实际消费金额>=',
                'used_time'   => '使用时间',
                'add_time'    => '首次关注时间',
            );
            if ($time) {
                $file_name = '微信会员' . $time . '下载';
            } else {
                $file_name = '微信会员下载';
            }
            download_xls($data, $head, $file_name);
        } else {
            $total_money       = M('coupon')
                ->alias('c')
                ->where($where)
                ->join('left join ytt_wxuser wx ON wx.id = c.wxuser_id')
                ->join('left join ytt_user u ON u.id = wx.proxy_uid')
                ->sum('c.money');
            $total_limit_money = M('coupon')
                ->alias('c')
                ->where($where)
                ->join('left join ytt_wxuser wx ON wx.id = c.wxuser_id')
                ->join('left join ytt_user u ON u.id = wx.proxy_uid')
                ->sum('c.limit_money');
            $count             = M('coupon')
                ->alias('c')
                ->where($where)
                ->join('left join ytt_wxuser wx ON wx.id = c.wxuser_id')
                ->join('left join ytt_user u ON u.id = wx.proxy_uid')
                ->count('c.id');
            $page              = $this->pages($count, $this->limit);
            $data              = M('coupon')
                ->alias('c')
                ->field($field)
                ->where($where)
                ->join('left join ytt_wxuser wx ON wx.id = c.wxuser_id')
                ->join('left join ytt_user u ON u.id = wx.proxy_uid')
                ->limit($page->firstRow . ',' . $page->listRows)
                ->order($order)
                ->select();
            foreach ($data as &$val) {
                $val['add_time']  = date('Y-m-d H:i:s', $val['add_time']);
                $val['used_time'] = date('Y-m-d H:i:s', $val['used_time']);
            }
            $store_data = M('user')->field('id,user_remark as name')->where(array('store_type' => 1))->select();
            $this->assign('total_money', $total_money ? $total_money : 0);
            $this->assign('total_limit_money', $total_limit_money ? $total_limit_money : 0);
            $this->assign('data', $data);
            $this->assign('store_data', $store_data);
            $this->assign('page', $page->show());
            $this->assign('time', $time);
            $this->display();
        }
    }

    /**
     * 获取收银员
     */
    public function getCashier() {
        $store_id = I('get.store_id', 0, 'int');
        $data     = M('cashier')->field('id,real_name')->where(array('store_uid' => $store_id, 'status' => 1))->select();
        $this->success(array('data' => $data ? $data : array()));
    }

    /**
     * 微信公众号配置相关
     */
    public function publicNumberConfig() {
        $content = M('config')->getFieldById(1, 'content');
        $content = unserialize($content);
        $this->assign('content', $content);
        $this->display();
    }
}