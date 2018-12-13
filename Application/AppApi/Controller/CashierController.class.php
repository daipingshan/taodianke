<?php
/**
 * 收银员相关功能
 *
 * User: dongguangqi
 * Date: 2017/12/25
 */

namespace AppApi\Controller;

/**
 * Class CashierController
 *
 * @package AppApi\Controller
 */
class CashierController extends CommonController {

    /**
     * 查询所有收银员
     */
    public function index() {
        $page = I('get.page', 1, 'intval');

        $page--;
        $start_num = $page * $this->reqnum;

        $where = array('store_uid' => $this->uid);
        $order = 'status desc,open_time desc';
        $field = 'id as cashier_id,real_name,cashier_mobile,status,open_time';
        $cashiers = M('cashier')->where($where)->order($order)->field($field)->limit($start_num, $this->reqnum + 1)->select();
        $this->setHasNext(false, $cashiers);
        $this->outPut($cashiers, 0);
    }

    /**
     * 新增收银员
     */
    public function add() {
        $real_name      = I('real_name', '', 'trim');
        $cashier_mobile = I('cashier_mobile', '', 'trim');

        if ('' == $real_name || '' == $cashier_mobile) {
            $this->outPut(null, -1, null, '参数异常！');
        }

        $store_user = M('user')->where(array('id' => $this->uid))->field('store_type')->find();
        if (!isset($store_user['store_type']) || 1 != $store_user['store_type']) {
            $this->outPut(null, -1, null, '您还不是店主，无此权限新增收银！');
        }

        //查询收银员是否有注册账号
        $field = 'coupon_sn,money,limit_money,end_time';
        $user = M('user')->where(array('mobile' => $cashier_mobile))->field('id,store_type')->find();
        if (empty($user)) {
            $this->outPut(null, -1, null, '该手机号暂未注册，请核实后再试！');
        } else if ($this->uid != $user['id'] && $user['store_type'] == 1) {
            $this->outPut(null, -1, null, '请勿设置其他店的店主为收银！');
        }

        //查询是否已成为收银，已存在的更新时间，不存在的新增收银。并更改账号店员类型为收银员
        $cashiers = M('cashier')->where(array('cashier_uid' => $user['id']))->select();
        $cashier = array();
        foreach ($cashiers as $key => $value) {
            if ($value['status'] == 1 && $value['store_uid'] != $this->uid) {
                $this->outPut(null, -1, null, '该账号已是其他店的收银，请勿重复添加！');
            }

            if ($value['store_uid'] == $this->uid) {
                $cashier = $value;
            }
        }

        $model   = M();
        $model->startTrans();
        $now = time();
        if (empty($cashier)) {
            $data = array(
                'store_uid' => $this->uid,
                'cashier_uid' => $user['id'],
                'real_name' => $real_name,
                'cashier_mobile' => $cashier_mobile,
                'status' => 1,
                'add_time' => $now,
                'open_time' => $now
            );
            M('cashier')->add($data);
        } else {
            M('cashier')->where('id=' . $cashier['id'])->save(array('real_name' => $real_name, 'status' => 1, 'open_time' => $now));
        }
        M('user')->where(array('id' => $user['id'], 'store_type' => 0))->save(array('store_type' => 2));

        if ($model->commit()) {
            $this->outPut(null, 0);
        } else {
            $model->rollback();
            $this->outPut(null, -1, null, '添加失败，请重试！');
        }
    }

    /**
     * 更新收银员状态
     */
    public function edit() {
        $cashier_id = I('cashier_id', 0, 'intval');
        $status     = I('status', 0, 'intval');
        $status     = 0 == $status ? 0 : 1;

        $cashier = M('cashier')->where('id=' . $cashier_id)->find();
        if (empty($cashier)) {
            $this->outPut(null, -1, null, '没有该收银员，请重试！');
        }

        if ($status == 1) {
            $cashier_temp = M('cashier')->where(array('cashier_uid' => $cashier['cashier_uid'], 'status' => 1))->find();
            if (isset($cashier_temp['store_uid']) && $this->uid != $cashier_temp['store_uid']) {
                $this->outPut(null, -1, null, '该收银员已在其他店入职，请核实后再更新！');
            }
        }

        $model   = M();
        $model->startTrans();

        if (0 == $status) {
            M('cashier')->where('id=' . $cashier_id)->save(array('status' => $status));
            M('user')->where(array('id' => $cashier['cashier_uid'], 'store_type' => 2))->save(array('store_type' => 0));
        } else {
            M('cashier')->where('id=' . $cashier_id)->save(array('status' => $status, 'open_time' => time()));
            M('user')->where(array('id' => $cashier['cashier_uid'], 'store_type' => 0))->save(array('store_type' => 2));
        }

        if ($model->commit()) {
            $this->outPut(null, 0);
        } else {
            $model->rollback();
            $this->outPut(null, -1, null, '更改失败，请重试！');
        }
    }
}