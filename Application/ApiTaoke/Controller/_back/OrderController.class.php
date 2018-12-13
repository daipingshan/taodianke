<?php

namespace Fxapi\Controller;

class OrderController extends CommonController {

    public function index() {
        $stime    = I('param.stime','','trim');
        $etime    = I('param.etime','','trim');
        $order_id = I('param.order_id','','trim');
        $order_status = I('param.order_status','','trim');
        $map = [
            'ad_position_name' => $this->uid
        ];
        if ($stime && $etime) {
            $map['pay_time'] = ['BETWEEN',[strtotime($stime),strtotime($etime)]];
        }
        if ($order_id) {
            $map['order_id'] = $order_id;
        }
        if ($order_status) {
            $map['order_status'] = ['LIKE',$order_status];
        }
        $model = M('order');
        $count = $model->where($map)->count();
        $offset = I('param.offset',0,'intval');
        $limit  = I('param.limit',$count,'intval');
        $list = $model->where($map)->order('create_time desc')->limit($offset,$limit)->select();
        foreach ($list as $i => $row) {
            $list[$i]['pay_time'] = date('Y-m-d H:i', $row['pay_time']);
        }
        $data = [
            'list'   => $list,
            'offset' => $offset,
            'limit'  => $limit,
            'count'  => $count
        ];
        $this->outPut($data, 0, null);
    }

    public function claim() {
        $order_id = I('param.order_id',0,'strval');
        $map['order_id'] = $order_id;
        $model = M('order');
        $order = $model->where($map)->find();
        if ($order) {
            $res = $model->where($map)->setField('ad_position_name',$this->uid);
            if ($res) {
                $this->outPut(null, 0, null,'认领成功');
            } else {
                $this->outPut(null, -1, null,'认领失败');
            }
        } else {
            $this->outPut(null, -1, null,'订单不存在');
        }
    }

    public function suborders() {
        $uid   = I('param.uid','','trim');
        $stime = I('param.stime','','trim');
        $etime = I('param.etime','','trim');
        $ispay = I('param.ispay','','trim');

        $suid = M('user')->where(['pid'=>$this->uid])->getField('id',true);
        if ($uid) {
            if (!in_array($uid, $suid)) {
                $this->outPut(null, -1, null, '非法请求');
            } else {
                $suid = [$uid];
            }
        } else {
            $suid[] = strval($this->uid);
        }
        $map = [
            'ad_position_name' => ['IN', $suid],
            'order_status' => ['LIKE', '订单结算']
        ];
        if ($ispay === '0') {
            $map['pay_id'] = 0;
        }
        if ($ispay === '1') {
            $map['pay_id'] = ['GT',0];
        }
        if ($stime && $etime) {
            $map['jiesuan_time'] = [['egt',str_replace('/', '-', $stime)],['lt',str_replace('/', '-', $etime)]];
        }
        $model = M('order');
        $count = $model->where($map)->count();
        $offset = I('param.offset',0,'intval');
        $limit  = I('param.limit',$count,'intval');
        $list = $model->where($map)->order('jiesuan_time desc')->limit($offset,$limit)->select();
        // echo $model->getLastSql();exit;
        foreach ($list as $i => $row) {
            $list[$i]['pay_time'] = date('Y/m/d H:i:s', $row['pay_time']);
        }
        $data = [
            'list'   => $list,
            'offset' => $offset,
            'limit'  => $limit,
            'count'  => $count
        ];
        $this->outPut($data, 0, null);
    }

    public function payorders() {
        $uids = M('user')->where(['pid'=>$this->uid])->getField('id',true);
        $uids[] = $this->uid;
        $map = [
            'ad_position_name' => ['IN',$uids],
            'pay_id'       => 0,
            'order_status' => ['LIKE', '订单结算']
        ];
        $model = M('order');
        $list = $model->where($map)->select();
        $money = 0;
        foreach ($list as $i => $order) {
            $money += $order['offer_money'];
        }
        $data = [
            'money' => $money,
            'list'  => $list
        ];
        $this->outPut($data, 0, null);
    }

    public function askpay() {
        $uids = M('user')->where(['pid'=>$this->uid])->getField('id',true);
        $uids[] = $this->uid;
        $map = array(
            'ad_position_name' => ['IN',$uids],
            'pay_id'       => 0,
            'order_status' => ['LIKE', '订单结算']
        );
        $model = M('order');
        $list = $model->where($map)->getField('id,order_id,offer_money',true);
        $ids = array_keys($list);
        $money = 0;
        foreach ($list as $id => $order) {
            $money += $order['offer_money'];
        }
        if ($money > 0) {
            $user = M('user')->where(['id'=>$this->uid])->find();
            if ($user['pay_bank_no'] == '' || $user['pay_bank_name'] == '' || $user['realname'] == '') {
                $this->outPut(null, -1, null,'请完善账户信息');
            }
            $data = array(
                'money' => $money,
                'ask_time' => date('Y/m/d H:i:s',time()),
                'pay_bank_no'   => $user['pay_bank_no'],
                'pay_bank_name' => $user['pay_bank_name'],
                'pay_bank_user' => $user['realname']
            );
            $pay_id = M('payed')->add($data);
            if ($pay_id) {
                $model->where(['id'=>['IN',$ids]])->setField('pay_id',$pay_id);
                $this->outPut(null, 0, null,'申请已提交');
            } else {
                $this->outPut(null, -1, null,'申请失败');
            }
        } else {
            $this->outPut(null, -1, null,'无可结算数据');
        }
        
    }

}
