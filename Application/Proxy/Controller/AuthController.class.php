<?php

namespace Proxy\Controller;

use Think\Controller;

class AuthController extends Controller{

    public function index(){

        redirect(U('Public/login'));
    }

    public function login(){
        if(IS_POST){
            $phone    = I('post.phone', '', 'trim');
            $password = I('post.password', '', 'trim');
            $code     = I('post.code', '', 'trim');
            if(!$code){
                $data['status']  = false;
                $data['content'] = '验证码不得为空！';
                $this->ajaxReturn($data);
            }
            if(!$this->check_verify($code)){
                $data['status']  = false;
                $data['content'] = '验证码错误！';
                $this->ajaxReturn($data);
            }
            if(!$phone || strlen($phone) != 11 || !$password){
                $data['status']  = false;
                $data['content'] = '手机号或密码不正确，请稍后重试！';
                $this->ajaxReturn($data);
            }

            $user = M('user');

            $where = array(
                'mobile' => $phone,
                'password' => md5($password),
            );
            $auth_user = $user->where($where)->find();
            if(empty($auth_user)){
                $data['status']  = false;
                $data['content'] = '您的账号不存在，请联系管理员！';
                $this->ajaxReturn($data);
            }else{
                if ($auth_user['stauts'] == '0') {
                    $data['status']  = false;
                    $data['content'] = '您的账号已经禁用，请联系管理员！';
                    $this->ajaxReturn($data);
                }
                $_SESSION['Auth_user']  = $auth_user['id'];
                $_SESSION['pid']        = $auth_user['pid'];
                $_SESSION['proxy_type'] = $auth_user['proxy_type'];
                $data['status']         = true;
                $data['content']        = "success";
                $this->ajaxReturn($data);

            }
            $data['status']  = false;
            $data['content'] = "ERROR : Auth error 57！";
            $this->ajaxReturn($data);

        }

    }

    public function logout(){
        session_destroy();
        redirect(U('/Proxy/login/'));
    }

    private function check_verify($code, $id = ''){
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }

    /**
     *  修改用户的密码
     *
     * @param uid        string
     * @param password   string
     * @return bool
     */
    public function revisePassword(){
        $param = array(
            'password'    => I('get.password', '', 'trim'),
            'newpassword' => I('get.newpassword', '', 'trim'),
            'repassword'  => I('get.repassword', '', 'trim'),
        );
        $uid   = session('Auth_user');
        $user  = M('user')->where(array('id' => $uid))->find();
        if ($user['password'] != md5($param['password'])) {
            $this->ajaxReturn(array('code' => '0', 'msg' => '您的原始密码不正确！'));
        }
        if ($param['newpassword'] != $param['repassword']) {
            $this->ajaxReturn(array('code' => '0', 'msg' => '两次密码不一样，请确认密码！'));
        }

        $user_up = M('user')->where(array('id' => $uid))->setField('password', md5($param['newpassword']));

        if ($user_up) {
            $this->ajaxReturn(array('code' => '1', 'msg' => '密码修改成功！'));
        } else {
            $this->ajaxReturn(array('code' => '0', 'msg' => '密码修改失败！'));
        }
    }


}