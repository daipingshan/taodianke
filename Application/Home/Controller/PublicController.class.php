<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/21 0021
 * Time: 上午 8:56
 */

namespace Home\Controller;

/**
 * Class LoginController
 * @package Home\Controller\
 */
class PublicController extends CommonController {

    /**
     * 构造函数
     */
    public function __construct() {
        parent:: __construct();
        if (session('uid') && ACTION_NAME != 'logout') {
            $this->redirect('Index/index');
        }
    }

    /**
     * 用户登陆
     */
    public function login() {
        $img_data = $this->_getAdImg(4);
        $img      = $img_data[0]['img'];
        //记录用户来源
        if (isset($_GET['referer'])) {
            $jumpUrl = urldecode($_GET['referer']);
        } else if (isset($_SERVER['HTTP_REFERER'])) {
            $jumpUrl = $_SERVER['HTTP_REFERER'];
        } else {
            $jumpUrl = U('Index/index');
        }
        session('jumpUrl', $jumpUrl);
        $this->assign('img', $img);
        $this->display();
    }

    /**
     * 登陆操作
     */
    public function doLogin() {
        if (!IS_AJAX || !IS_POST) {
            $this->error('非法请求！');
        }
        $mobile   = I('post.mobile', '', 'trim');
        $password = I('post.password', '', 'trim');
        $where    = array('mobile' => $mobile);
        $userRes  = M('user')->where($where)->field('id,mobile,password,pid,bank_account,real_name,status,dwxk_adsense_id')->find();
        if (!$userRes) {
            $this->error('账户不存在，请重新输入！');
        } else if (0 == $userRes['status']) {
            $this->error('您的账号已被禁用！');
        } else if (!isset($userRes['password']) || strcmp($userRes['password'], md5(trim($password))) !== 0) {
            $this->error('账号或密码错误，请重新输入！');
        }
        if (!$userRes['bank_account'] || !$userRes['real_name']) {
            $this->error('您的提现信息不全！请联系管理员补充！');
        }
        if (!$userRes['pid']) {
            $this->error('账号未授权！');
        }
        session('uid', $userRes['id']);
        session('pid', $userRes['pid']);
        session('wei_xin_pid', $userRes['dwxk_adsense_id']);
        session('user', $userRes);
        $this->success('登陆成功', session('jumpUrl'));
    }

    /**
     * 用户注册
     */
    public function register() {
        $img_data = $this->_getAdImg(5);
        $img      = $img_data[0]['img'];
        $this->assign('img', $img);
        $this->display();
    }

    /**
     * 注册操作
     */
    public function doRegister() {

    }

    /**
     * 发送验证码
     */
    public function sendSms() {
        if (!IS_AJAX || !IS_POST) {
            $this->error('非法请求！');
        }
        $key    = I('post.key', '', 'trim');
        $mobile = I('post.mobile', '', 'trim');
        if (!$mobile) {
            $this->error('请输入手机号码！');
        }
        if (!checkMobile($mobile)) {
            $this->error('手机号码格式不正确！');
        }
        $id = M('user')->where(array('mobile' => $mobile))->getField('id');
        if ('1' == $key) {
            if ($id) {
                $this->error('你的号码已经注册，请重新输入！');
            }
        } else {
            if (!$id) {
                $this->error('你的号码尚未注册，请先注册！');
            }
        }
        $code   = rand(1000, 9999);
        $res    = array(
            'mobile'      => $mobile,
            'create_time' => time(),
            'code'        => $code,
            'action'      => 'register',
        );
        $sms_id = M('sms')->where(array('mobile' => $mobile))->find();
        if ($sms_id) {
            $res['num'] = $sms_id['num'] + 1;
            M('sms')->where(array('mobile' => $mobile))->save($res);
        } else {
            $res['num'] = '0';
            M('sms')->add($res);
        }
        $sms  = new \Common\Org\sendSms();
        $info = "您的短信验证码为:{$code}";
        $data = $sms->sendMsg($mobile, $info);
        if ($data) {
            $this->success('发送成功');
        } else {
            $this->error('发送失败,请稍后重试！');
        }
    }

    /**
     * 退出登陆
     */
    public function logout() {
        session(null);
        $this->redirect('login');
    }

}