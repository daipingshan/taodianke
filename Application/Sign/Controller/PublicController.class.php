<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2015/10/9
 * Time: 17:44
 */

namespace Sign\Controller;

class PublicController extends CommonController {

    /**
     * 公共模块不需要检测用户是否登录
     * @var bool
     */
    protected $checkUser = false;

    /**
     * 构造方法
     */
    public function __construct() {
        parent::__construct();
        $user_id = session('user_id');
        if ($user_id && ACTION_NAME != 'logout') {
            redirect(U('Index/index'));
        }
    }

    /**
     * 默认访问页面
     */
    public function index() {
        $this->redirect('login');
    }

    /**
     * 管理员登录页面
     */
    public function login() {
        $this->display();
    }

    /**
     * 登录逻辑处理
     */
    public function doLogin() {
        if (IS_POST) {
            $username    = I('post.username', '', 'trim');
            $password    = I('post.password', '', 'trim');
            if (!$username || !$password) {
                session('error', '用户名或密码不能为空!');
                $this->redirect('login');
            }
            $userModel          = M('user');
            $where['username'] = $username;
            $info               = $userModel->field(array('id', 'username', 'password'))->where($where)->find();
            if (encryptPwd($password) == $info['password']) {
                session('user_id', $info['id']);
                session('user_name', $info['username']);
                // 操作日志
                redirect(U('Index/index'));
            } else {
                session('error', '用户名或密码错误!');
                $this->redirect('login');
            }
        } else {
            session('error', '非法请求!');
            $this->redirect('login');
        }
    }

    /**
     * 注册
     */
    public function register(){
        $this->display();
    }

    /**
     * 提交注冊
     */
    public function doRegister(){
       if (IS_POST) {
            $username    = I('post.username', '', 'trim');
            $code        = I('post.code', '', 'trim');
            $password    = I('post.password', '', 'trim');
            if (!$username || !$password || !$code) {
                session('error', '请求参数不完整!');
                $this->redirect('register');
            }
            if(!$this->checkVerify($code)){
                session('error', '验证码错误!');
                $this->redirect('register');
            }
            $userModel = M('user');
            $data      = array('username'=>$username,'password'=>encryptPwd($password));
            $res       = $userModel->add($data);
            if ($res) {
                session('error', '注册成功请登录！');
                redirect(U('Index/index'));
            } else {
                session('error', '注册失败!');
                $this->redirect('register');
            }
        } else {
            session('error', '非法请求!');
            $this->redirect('register');
        }
    }

    /**
     * 验证码
     */
    public function verify(){
        $config =    array(
            'fontSize'    =>    30,    // 验证码字体大小
            'length'      =>    4,     // 验证码位数
            'useNoise'    =>    true, // 关闭验证码杂点
        );
        $Verify =     new \Think\Verify($config);
        // 设置验证码字符为纯数字
        $Verify->codeSet = '0123456789'; 
        $Verify->entry();
    }

    /**
     * 检测输入的验证码是否正确，$code为用户输入的验证码字符串
     */
    public function checkVerify($code){
        $verify = new \Think\Verify();
        return $verify->check($code);
    }

    /**
     * 管理员退出登录
     */
    public function logout() {
        session(null);
        redirect(U('Public/login'));
    }
}