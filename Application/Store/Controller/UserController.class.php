<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/1/18
 * Time: 14:00
 */
namespace Store\Controller;


use Common\Org\sendSms;

class UserController extends CommonController {

    /**
     * 会员中心
     */
    public function index() {
        $open_id = session('wx_user_openid');
        if (!$open_id) {
            $this->_WeChatLogin(4);
        }
        $info = M('store_wxuser')->where(array('openid' => $open_id))->field('username,mobile,proxy_uid')->find();
        if (empty($info['mobile'])) {
            $this->redirect('bindMobile');
        }
        $this->assign('uid', $info['proxy_uid']);
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 绑定手机号
     */
    public function bindMobile() {
        $open_id = session('wx_user_openid');
        if (!$open_id) {
            $this->_WeChatLogin(5);
        }
        $info = M('store_wxuser')->where(array('openid' => $open_id))->field('mobile')->find();
        if ($info['mobile']) {
            $this->redirect('index');
        }
        $this->display();
    }

    /**
     * 绑定手机号
     */
    public function doBindMobile() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $open_id = session('wx_user_openid');
        if (!$open_id) {
            $this->error('登录已失效，请刷新！');
        }
        $username = I('post.username', '', 'trim');
        $mobile   = I('post.mobile', '', 'trim');
        $code     = I('post.code', '', 'int');
        if (!$username) {
            $this->error('会员姓名不能为空！');
        }
        if (!$mobile) {
            $this->error('会员手机号码不能为空！');
        }
        if (!checkMobile($mobile)) {
            $this->error('会员手机号码格式不正确！');
        }
        $sms_code = S('tdk_' . $mobile);
        if (!$sms_code) {
            $this->error('验证码尚未获取或已失效！');
        }
        if ($sms_code != $code) {
            $this->error('验证码错误！');
        }
        $res = M('store_wxuser')->where(array('openid' => $open_id))->save(array('mobile' => $mobile, 'username' => $username));
        if ($res) {
            S('tdk_' . $mobile, null);
            $this->success('绑定成功！');
        } else {
            $this->error('绑定失败！');
        }
    }

    /**
     * 获取验证码
     */
    public function smsSend() {
        $mobile = I('post.mobile', '', 'trim');
        if (!checkMobile($mobile)) {
            $this->error('手机号码格式不正确！');
        }
        $code = rand(1000, 9999);
        $sms  = new sendSms();
        $res  = $sms->sendMsg($mobile, "您的短信验证码为:" . $code);
        //$res = sendSms($mobile, $code);
        if ($res) {
            S('tdk_' . $mobile, $code, 90);
            $this->success('发送成功');
        } else {
            $this->error($res['info']);
        }
    }
}