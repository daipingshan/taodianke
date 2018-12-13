<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17 0017
 * Time: 下午 5:25
 */

namespace AppAdmin\Controller;

/**
 * Class SettleController
 *
 * @package Common\Org
 */
class SettleController extends CommonController {

    /**
     * @var int
     */
    protected $limit = 10;

    /**
     * 结算列表
     */
    public function index() {
        $status   = I('get.status', '', 'trim');
        $proxy_id = I('get.proxy_id', 0, 'int');
        $time     = I('get.time', '', 'trim');
        $real_name = I('get.real_name', '', 'trim');
        $type     = I('get.type', 'select', 'trim');
        $where    = array();
        if ($status) {
            $where['status'] = $status;
        }
        if ($proxy_id) {
            $where['proxy_id'] = $proxy_id;
        }
        if ($time) {
            list($start_time, $end_time) = explode('-', $time);
            $where['add_time'] = array('between', array(date('Y-m-d', strtotime($start_time)), date('Y-m-d 23:59:59', strtotime($end_time))));
        }

        if (!empty($real_name)) {
            $where['real_name'] = array('like', '%' . $real_name . '%');
        }

        $proxy = $this->_getProxy();
        $model = M('order_withdraw');
        if ($type == "down") {
            $data = $model->field('proxy_id,bank_account,real_name,amount,status,add_time,settlement_time')->where($where)->order('id desc')->select();
            foreach ($data as &$val) {
                $val['proxy_name'] = $proxy[$val['proxy_id']];
                $val['status']     = $val['status'] == 'Y' ? '已打款' : '未打款';
            }
            $head = array(
                'proxy_name'      => '所属代理',
                'bank_account'    => '支付宝账号',
                'real_name'       => '真实姓名',
                'amount'          => '结算结额',
                'status'          => '结算状态',
                'add_time'        => '申请时间',
                'settlement_time' => '结算时间'
            );
            if ($time) {
                $file_name = '代理结算信息' . $time . '下载';
            } else {
                $file_name = '代理结算信息下载';
            }
            download_xls($data, $head, $file_name);
        } else {
            $count = $model->where($where)->count('id');
            $page  = $this->pages($count, $this->limit);
            $data  = $model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('id desc')->select();
            $this->assign(array('page' => $page->show(), 'data' => $data, 'proxy_list' => $proxy));
            $this->display();
        }
    }

    /**
     * 打款操作
     */
    public function doPayment() {
        $model = M('order_withdraw');
        $id    = I('get.id', 0, 'int');
        $p     = I('get.p', 0, 'int');
        $info  = $model->find($id);
        if ($id && $info) {
            $data['id']              = $id;
            $data['status']          = 'Y';
            $data['settlement_time'] = date("Y-m-d H:i:s");
            $res                     = $model->save($data);
            if ($res) {
                $pid = M('user')->getFieldById($info['user_id'], 'pid');
                if ($pid) {
                    $date  = date('m月d日', strtotime($info['add_time']));
                    $alert = "您{$date}申请的{$info['amount']}元提现已支付到账，请查看！";
                    $this->_sendJPush($alert, array('alias' => array($pid)), array('withdraw_id' => $id), 'all');
                }
                $this->success('打款成功', U('index', array('p' => $p)));
            } else {
                $this->error('打款失败', U('index', array('p' => $p)));
            }
        } else {
            $this->error('请求参数不合法', U('index', array('p' => $p)));
        }
    }
}