<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2016/12/29
 * Time: 22:05
 */

namespace Sign\Controller;


class IndexController extends CommonController {

    /**
     * 首頁
     */
    public function index() {
        $this->assign('id',$this->userId);
        $this->display();
    }

    /**
     * 报名系统数据保存
     */
    public function sign() {
        $data = I('post.', '', 'trim');
        if (!$data ) {
            session('error','请求参数不合法！');
            $this->redirect('index');
        }
        $res = M('sign')->add($data);
        if ( $res ) {
            $this->redirect('detail', array('id' => $res));
        } else {
            session('error','报名失败！');
            $this->redirect('index');
        }
    }

    /**
     * 报名详情
     */
    public function detail() {
        $id   = I('get.id', 0, 'int');
        $info = M('sign')->find($id);
        if ( !$id || !$info) {
            session('error','请求参数不合法！');
            $this->redirect('index');
        }
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 去支付
     */
    public function pay() {
        $id   = I('get.id', 0, 'int');
        $info = M('sign')->find($id);
        if ( !$id ) {
            $this->redirect('index');
        }
        $data     = array(
            'notify_url'   => 'http://t.liutingfeng.com' . U('Sign/Index/callBack', array('id' => $info['id'])),
            'base_url'     => 'http://t.liutingfeng.com' . U('Sign/Index/pay', array('id' => $info['id'])),
            'out_trade_no' => $info['id'],
            'subject'      => '【报名系统】_' . $info['name'],
            'total_fee'    => 10,
        );
        $Pay      = new \Sign\Org\Pay();
        $pay_data = $Pay->doPay($data);
        $assign   = array('success_url' => U('Pay/wait', array('id' => $info['id'])), 'data' => $pay_data);
        $this->assign($assign);
        $this->display();
    }

    /**
     * 支付回调
     */
    public function callBack(){
        $id = I('get.id', '', 'int');
        $info         = M('sign')->find($id);
        if ( !$id || !$info || $info['pay_status'] == 'pay') {
            ob_clean();
            exit;
        }
        $Pay = new \Sign\Org\Pay();
        $res = $Pay->notifyCallBack();
        if ( $res['status'] == true ) {
            $this->_paySuccess($id,$res);
            ob_clean();
            exit;
        } else {
            ob_clean();
            exit;
        }
    }

    /**
     * @param $id
     * @param $res
     */
    protected function _paySuccess($id,$res){
        $status  = M('sign')->getFieldById($id,'pay_status');
        if ( !$id || $status == 'pay') {
            return false;;
        }
        $Model = M();
        $Model->startTrans();
        $res = M('sign')->save(array('id'=>$id,'pay_status'=>'pay'));
        $pay_data = array(
            'sign_id'=>$id,
            'total_fee'=>$res['data']['total_fee'],
            'trade_no'=>$res['data']['trade_no'],
            'add_time'=>time(),
        );
        $pay_res = M('pay_log')->add($pay_data);
        if($res && $pay_res){
            $Model->commit();
            return true;
        }else{
            $Model->rollback();
            return false;
        }
    }

    /**
     * 等待页面
     */
    public function wait() {
        $id = I('get.id',0,'int');
        $status = M('sign')->getFieldById($id,'pay_status');
        if(!$id || !$status){
            session('error','请求参数不合法！');
            $this->redirect('index');
        }
        if($status == 'pay'){
            $this->redirect('success',array('id'=>$id));
        }
        $this->assign('id',$id);
        $this->display();
    }

    /**
     * 失败页面
     */
    public function error() {
        $info = I('get.error', '支付失败！','trim');
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 检测
     */
    public function check() {
        $id = I('get.id',0,'int');
        $status = M('sign')->getFieldById($id,'pay_status');
        if(!$id || !$status){
            $this->ajaxReturn(array('code'=>-1,'info'=>'参数错误！'));
        }
        if($status == 'unpay'){
            $this->ajaxReturn(array('code'=>-1,'info'=>'未支付！'));
        }else{
            $this->ajaxReturn(array('code'=>1));
        }
    }

    /**
     * 支付成功
     */
    public function success(){
        $id   = I('get.id', 0, 'int');
        $info = M('sign')->find($id);
        if ( !$id ) {
            session('error','请求参数不合法！');
            $this->redirect('index');
        }
        $this->assign('info', $info);
        $this->display();
    }


}