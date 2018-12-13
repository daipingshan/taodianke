<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/19 0019
 * Time: 下午 6:01
 */

namespace Proxy\Controller;

/**
 * 代理结算
 * Class SettleController
 *
 * @package Proxy\Controller
 */
class SettleController extends CommonController{

    /**
     * @var int
     */
    protected $limit = 10;

    /**
     * 结算列表
     */
    public function index(){
        $map['pid'] = session('pid');
        $proxy_id   = M('proxy')->where($map)->getField('id');
        $status     = I('get.status', '', 'trim');
        $where      = array('proxy_id' => $proxy_id);
        if($status){
            $where['status'] = $status;
        }
        $model = M('order_withdraw');
        $count = $model->where($where)->count('id');
        $page  = $this->pages($count, $this->limit);
        $data  = $model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('id desc')->select();
        $this->assign(array('page' => $page->show(), 'data' => $data));
        $this->display();
    }

    /**
     * 打款操作
     */
    public function doPayment(){
        $model      = M('order_withdraw');
        $id         = I('get.id', 0, 'int');
        $map['pid'] = session('pid');
        $proxy_id   = M('proxy')->where($map)->getField('id');
        $info       = $model->find($id);
        if($id && $info && $proxy_id == $info['proxy_id']){
            $data['id']              = $id;
            $data['status']          = 'Y';
            $data['settlement_time'] = date("Y-m-d H:i:s");
            $res                     = $model->save($data);
            if($res){
                $pid = M('user')->getFieldById($info['user_id'], 'pid');
                if($pid){
                    $date  = date('m月d日', strtotime($info['add_time']));
                    $alert = "您{$date}申请的{$info['amount']}元提现已支付到账，请查看！";
                    $this->_sendJPush($alert, array('alias' => array($pid)), array('withdraw_id' => $id), 'all');
                }
                $this->success('打款成功', U('index'));
            }else{
                $this->error('打款失败', U('index'));
            }
        }else{
            $this->error('请求参数不合法', U('index'));
        }
    }
}