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
class UserController extends CommonController {

    /**
     * 用户信息
     */
    public function index() {
        $info = M('user')->field('id,username,pid,bank_name,bank_account,real_name,proxy_type,is_wechat_marketing,store_type')->find($this->uid);
        if (!$info['bank_name'] || !$info['bank_account'] || !$info['real_name']) {
            $info['is_bind_bank'] = 0;
        } else {
            $info['is_bind_bank'] = 1;
        }
        $info['bank_account'] = mb_substr($info['bank_account'], 0, 3) . "****" . mb_substr($info['bank_account'], -4);
        $info['image']        = getHeadImg($this->uid);
        $info['amount']       = $this->_amount();
        $info['zones']        = array_values($this->_getZone());
        $this->outPut($info, 0);
    }

    /**
     * 获取用户收藏
     */
    public function like() {
        $page = I('get.page', 1, 'trim');
        $page--;
        $start_num = $page * $this->reqnum;
        $ids_list  = M('items_like')->field('id,num_iid')->where(array('uid' => $this->uid))->index('num_iid')->order('id desc')->limit($start_num, $this->reqnum + 1)->select();
        if (!$ids_list) {
            $this->outPut(array(), 0);
        }

        if ($this->openSearchStatus === true) {
            $query = "num_iid:'" . implode("' OR num_iid:'", array_keys($ids_list)) . "'";
            $sort  = array(array('key' => 'id', 'val' => 0));
            $data  = $this->_getOpenSearchList($query, $sort, null, 0, $this->reqnum + 1);
        } else {
            $where['num_iid'] = array('in', array_keys($ids_list));
            $data             = $this->_getItemsList($where, 'id desc', 0, $this->reqnum + 1);
        }
        $this->setHasNext(false, $ids_list, $this->reqnum);
        $this->outPut($data, 0);
    }

    /**
     * 修改个人信息
     */
    public function updateInfo() {
        $username          = I('param.username', null, 'trim');
        $user              = M('user');
        $where['username'] = $username;
        if ($username == '' || $username == null) {
            $this->outPut(null, -1, null, '昵称不得为空！');
        }
        if (!$rel = $user->where($where)->find()) {
            if ($user->where('id=' . $this->uid)->save($where)) {
                $this->outPut('true', 0);
            }
        } else {
            $this->outPut(null, -1, null, '昵称已存在！');
        }
    }

    /**
     * 加入推广
     */
    public function addLike() {
        $num_iid = I('get.num_iid', '', 'trim');
        if (!$num_iid) {
            $this->outPut(null, -1, null, '缺少商品编号无法加入推广！');
        }
        $count = M('items_like')->where(array('uid' => $this->uid, 'num_iid' => $num_iid))->count('id');
        if ($count) {
            $this->outPut(null, -1, null, '该商品已加入推广，不能重复加入！');
        }
        $data = array('num_iid' => $num_iid, 'uid' => $this->uid);
        $res  = M('items_like')->add($data);
        if ($res) {
            $this->outPut($res, 0);
        } else {
            $this->outPut(null, -1, null, '加入失败！');
        }
    }

    /**
     * 取消推广
     */
    public function delLike() {
        $id = I('get.num_iid', '', 'trim');
        if (!$id) {
            $this->outPut(null, -1, null, '缺少推广编号无法取消推广！');
        }
        $where = array('uid' => $this->uid, 'num_iid' => $id);
        $res   = M('items_like')->where($where)->delete();
        if ($res) {
            $this->outPut($res, 0);
        } else {
            $this->outPut(null, -1, null, '取消失败！');
        }
    }

    /**
     * 绑定银行卡
     */
    public function addBank() {
        $bank_account = I('post.bank_account', '', 'trim');
        $real_name    = I('post.real_name', '', 'trim');
        if (!$bank_account) {
            $this->outPut(null, -1, null, '缺少银行账号！');
        }
        if (!$real_name) {
            $this->outPut(null, -1, null, '缺少真实姓名！');
        }
        $data = array('id' => $this->uid, 'bank_account' => $bank_account, 'real_name' => $real_name);
        $res  = M('user')->save($data);
        if ($res) {
            $this->outPut(null, 0);
        } else {
            $this->outPut(null, -1, null, '绑定失败！');
        }
    }

    /**
     * 申请提现
     */
    public function cash() {
        $id   = $this->uid;
        $pid  = $this->_getUserPid();
        $info = M('user')->field('bank_account,real_name,proxy_type')->find($id);
        if ($info['proxy_type'] == 1) {
            $this->outPut(null, -1, null, '该账号为企业级代理，不能提现！');
        }
        //为了区分代理信息
        $proxy_id = $this->_getProxyId($pid);
        if ($proxy_id == 0) {
            $this->outPut(null, -1, null, '账号异常，请联系管理员！');
        }
        if (!$info['bank_account'] || !$info['real_name']) {
            $this->outPut(null, -1, null, '请先绑定支付宝信息申请提现！');
        }
        $amount = $this->_amount();
        if (!$amount) {
            $this->outPut(null, -1, null, '对不起，您的提现金额不足！');
        }
        $user_data = array('add_time' => date('Y-m-d H:i:s'), 'amount' => $amount, 'user_id' => $id, 'pid' => $pid, 'proxy_id' => $proxy_id);
        $user_data = array_merge($info, $user_data);
        $model     = M();
        $model->startTrans();
        $res        = M('order_withdraw')->add($user_data);
        $today      = date('d');
        $first_time = strtotime(date("Y-m-01", strtotime('-1 month ')));
        $pro_time   = strtotime(date('Y-m-01'));
        $where      = array('user_id' => $this->uid, 'pay_status' => 'settle', 'withdraw_id' => 0);
        if ($today < C('balance_time')) {
            $where['_string'] = "UNIX_TIMESTAMP(earning_time) < {$first_time} ";
        } else {
            $where['_string'] = "UNIX_TIMESTAMP(earning_time) < {$pro_time} ";
        }
        $data['withdraw_id'] = $res;
        $order_res           = M('order_commission')->where($where)->save($data);
        if ($res && $order_res) {
            $model->commit();
            $this->outPut(null, 0, null, '提现申请成功');
        } else {
            $model->rollback();
            $this->outPut(null, -1, null, '提现申请失败！');
        }
    }

    /**
     * 提现列表
     */
    public function cashList() {
        $page  = I('get.page', 1, 'trim');
        $where = array('user_id' => $this->uid);
        $page--;
        $start_num = $page * $this->reqnum;
        $data      = M('order_withdraw')->where($where)->order('id desc')->limit($start_num, $this->reqnum + 1)->select();
        foreach ($data as &$val) {
            $val['bank_account'] = substr($val['bank_account'], 0, 3) . "****" . substr($val['bank_account'], -4);
            $val['cash_time']    = $val['settlement_time'];
        }
        $this->setHasNext(false, $data, $this->reqnum);
        $this->outPut($data, 0);
    }

    /**
     * @param $pid
     *
     * @return int
     */
    protected function _getProxyId($pid) {
        if (!$pid) {
            return 0;
        }
        $pid_data = explode('_', $pid);
        $where    = array('pid' => array('like', "{$pid_data[0]}_{$pid_data[1]}%"));
        $proxy_id = M('proxy')->where($where)->getField('id');
        if ($proxy_id) {
            return intval($proxy_id);
        } else {
            return 0;
        }
    }

    /**
     * 修改密码
     */
    public function updatePassword() {
        $old_password = I('post.old_password', '', 'trim');
        $new_password = I('post.new_password', '', 'trim');
        $password     = M('user')->where(array('id' => $this->uid))->getField('password');
        if (!$old_password) {
            $this->outPut(null, -1, null, '原始密码不能为空！');
        }
        if (!$new_password) {
            $this->outPut(null, -1, null, '新密码不能为空！');
        }
        if ($old_password != $password) {
            $this->outPut(null, -1, null, '原始密码错误！');
        }
        $res = M('user')->where(array('id' => $this->uid))->save(array('password' => $new_password));
        if ($res !== false) {
            $this->outPut(null, 0, null, '修改成功');
        } else {
            $this->outPut(null, -1, null, '修改失败！');
        }
    }

}