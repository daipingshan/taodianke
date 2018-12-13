<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2015/10/9
 * Time: 17:44
 */

namespace SignAdmin\Controller;

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
        $user_id = session('admin_id');
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
            $userModel          = M('admin');
            $where['username'] = $username;
            $info               = $userModel->field(array('id', 'username', 'password'))->where($where)->find();
            if (encryptPwd($password) == $info['password']) {
                session('admin_id', $info['id']);
                session('admin_name', $info['username']);
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
     * 管理员退出登录
     */
    public function logout() {
        session(null);
        redirect(U('Public/login'));
    }
}