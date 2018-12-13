<?php

namespace Fxapi\Controller;

class ItemController extends CommonController {

    public function index() {
        $stime         = I('param.stime','','trim');
        $etime         = I('param.etime','','trim');
        $buyer_phone   = I('param.buyer_phone','','trim');
        $rebate_status = I('param.rebate_status','','trim');
        $checkout      = I('param.checkout','','trim');
        $level_2_id    = I('param.level_2_id','','trim');
        $kword         = I('param.kword','','trim');
        $map = [
            'level_1_id' => $this->uid
        ];
        if ($stime && $etime) {
            $map['buy_time'] = ['BETWEEN',[strtotime($stime),strtotime($etime)]];
        }
        if ($buyer_phone) {
            $map['buyer_phone'] = $buyer_phone;
        }
        if ($rebate_status) {
            $map['rebate_status'] = $rebate_status;
        }
        if ($checkout) {
            $map['checkout'] = $checkout;
        }
        if ($level_2_id) {
            $map['level_2_id'] = $level_2_id;
        }
        if ($kword) {
            $map['name'] = ['LIKE','%'.$kword.'%'];
        }
        $model = M('item');
        $count = $model->where($map)->count();
        $offset = I('param.offset',0,'intval');
        $limit  = I('param.limit',$count,'intval');
        $list = $model->where($map)->order('id desc')->limit($offset,$limit)->select();
        foreach ($list as $i => $row) {
            $list[$i]['buy_time'] = date('Y-m-d H:i', $row['buy_time']);
        }
        $data = [
            'list'   => $list,
            'offset' => $offset,
            'limit'  => $limit,
            'count'  => $count
        ];
        $this->outPut($data, 0, null);
    }

    public function find() {
        $id = I('post.id');
        $item = M('item')->where(['id'=>$id])->find();
        $item['buy_time'] = date('Y-m-d H:i', $item['buy_time']);
        if (!$item) {
            $this->outPut(null, -1, null, '信息不存在');
        } else {
            $this->outPut(['list'=>[$item]], 0, null);
        }
    }

    public function add() {
        $name     = I('post.name','','trim');
        $buy_time = I('post.buy_time','','trim');
        $buyer_phone = I('post.buyer_phone','','trim');
        $charge      = I('post.charge',0,'trim');
        $balance     = I('post.balance',0,'trim');
        $price       = I('post.price','','trim');
        $korder_id   = I('post.korder_id','','trim');
        $express_id   = I('post.express_id','','trim');
        $rebate_money  = I('post.rebate_money',0,'trim');
        $rebate_status = I('post.rebate_status',0,'trim');
        $checkout      = I('post.checkout','未结算','trim');
        $level_2_id    = I('post.level_2_id','','trim');
        // 验证
        $error = [];
        if ($name == '') {
            $error[] = '物品名称不能为空';
        }
        if ($buy_time == '') {
            $buy_time = time();
            // $error[] = '购买时间不能为空';
        }
        if (!checkMobile($buyer_phone)) {
            $error[] = '购买者手机号错误';
        }
        if ($price <= 0) {
            $error[] = '物品金额错误';
        }

        if (!empty($error)) {
            $this->outPut(null, -1, null, implode('|', $error));
        }

        // 准备数据
        $data = [
            'name'     => $name,
            'buy_time' => $buy_time,
            'buyer_phone' => $buyer_phone,
            'charge'   => $charge,
            'balance'  => $balance,
            'price'    => $price,
            'korder_id' => $korder_id,
            'express_id'=> $express_id,
            'rebate_money' =>$rebate_money,
            'level_1_id' => $this->uid
        ];
        if ($rebate_status) {
            $data['rebate_status'] = $rebate_status;
        }
        if ($checkout) {
            $data['checkout'] = $checkout;
        }

        if ($level_2_id) {
            $data['level_2_id'] = $level_2_id;
        }

        $iid = M('item')->add($data);

        if (!$iid) {
            $this->outPut(null, -1, null, '添加失败！');
        } else {
            $this->outPut(null, 0, null);
        }

    }

    public function edit() {
        $model = M('item');
        $id = I('post.id');
        $item = $model->where(['id'=>$id])->find();
        if (!$item) {
            $this->outPut(null, -1, null, '信息不存在');
        } else if ($item['checked'] >= 3) {
            $this->outPut(null, -1, null, '结算中，禁止修改');
        } else {
            $name     = I('post.name','','trim');
            $buy_time = I('post.buy_time','','trim');
            $buyer_phone = I('post.buyer_phone','','trim');
            $charge      = I('post.charge',0,'trim');
            $balance     = I('post.balance',0,'trim');
            $price       = I('post.price','','trim');
            $korder_id   = I('post.korder_id','','trim');
            $express_id   = I('post.express_id','','trim');
            $rebate_money  = I('post.rebate_money',0,'trim');
            $rebate_status = I('post.rebate_status',0,'trim');

            // 验证
            $error = [];
            if ($name == '') {
                $error[] = '物品名称不能为空';
            }
            if ($buy_time == '') {
                $buy_time = time();
            }
            if (!checkMobile($buyer_phone)) {
                $error[] = '购买者手机号错误';
            }
            if ($price <= 0) {
                $error[] = '物品金额错误';
            }
            if ($korder_id == '') {
                $error[] = '快店订单号不能为空';
            }

            if (!empty($error)) {
                $this->outPut(null, -1, null, implode('|', $error));
            }
            // 准备数据
            $data = [
                'name'     => $name,
                'buy_time' => $buy_time,
                'buyer_phone' => $buyer_phone,
                'charge'   => $charge,
                'balance'  => $balance,
                'price'    => $price,
                'korder_id' => $korder_id,
                'express_id'=> $express_id,
                'rebate_money' =>$rebate_money,
                'level_1_id' => $this->uid
            ];
            if ($rebate_status) {
                $data['rebate_status'] = $rebate_status;
            }
            if (false === $model->where(['id'=>$id])->save($data)) {
                $this->outPut(null, -1, null, '修改失败！');
            } else {
                $this->outPut(null, 0, null);
            }

        }

    }

    public function del() {
        $id = I('get.id');
        M('item')->where(['id'=>$id])->delete();
        $this->outPut(null, 0, null);
    }


    public function express() {
        $text = I('param.text',0,'strval');
        if (!$text) {
            $this->outPut(null, -1, null, '请提供单号');
        }
        $typeurl = 'http://www.kuaidi100.com/autonumber/autoComNum?text='.$text;
        $typinfo = json_decode(file_get_contents($typeurl),true);
        if (empty($typinfo['auto'])) {
            $this->outPut(null, -1, null, '无效单号');
        }
        $type = $typinfo['auto'][0]['comCode'];
        $infourl = 'http://www.kuaidi100.com/query?type='.$type.'&postid='.$text;
        $data = json_decode(file_get_contents($infourl),true);
        if (empty($data['data'])) {
            $this->outPut(null, -1, null, '暂无信息');
        } else {
            $html = '<table>';
            foreach ($data['data'] as $i => $info) {
                $html .= "<tr><td>{$info['time']}</td><td>{$info['context']}</td></tr>";
            }
            $html .= '</table>';
        }
        $this->outPut(['html'=>$html], 0, null);
    }

}
