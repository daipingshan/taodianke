<?php

namespace AppAdmin\Controller;

use Think\Controller;

class AuthController extends Controller {
    public function index() {

        redirect(U('Public/login'));
    }

    public function login() {
        if (IS_POST) {
            $username = I('post.username', '', 'trim');
            $password = I('post.password', '', 'trim');
            $code     = I('post.code', '', 'trim');
            if (!$code) {
                $data['status']  = false;
                $data['content'] = '验证码不得为空！';
                $this->ajaxReturn($data);
            }
            if (!$this->check_verify($code)) {
                $data['status']  = false;
                $data['content'] = '验证码错误！';
                $this->ajaxReturn($data);
            }
            if (!$username || !$password) {
                $data['status']  = false;
                $data['content'] = '用户名或密码不能为空！';
                $this->ajaxReturn($data);
            }

            $admin = M('admin');

            $where     = array(
                'username' => $username,
                'password' => md5($password),
            );
            $auth_user = $admin->where($where)->find();

            if (empty($auth_user)) {
                $data['status']  = false;
                $data['content'] = '您的账号或密码不正确，请稍后重试！';
                $this->ajaxReturn($data);
            } else {
                if ($auth_user['status'] == '0') {
                    $data['status']  = false;
                    $data['content'] = '您的账号已禁用！';
                    $this->ajaxReturn($data);
                }
                $admin->where('id='.$auth_user['id'])->save(array( 'last_time' => time(), 'last_ip' => get_client_ip() ));
                $_SESSION['auth_user']      = $auth_user['id'];
                $_SESSION['role_id']        = $auth_user['role_id'];
                $_SESSION['department_id']  = $auth_user['department_id'];
                $_SESSION['position_level'] = $auth_user['position_level'];
                $_SESSION['username']       = $auth_user['username'];
                if ($auth_user['role_id'] == 1) {
                    //超级管理员
                    $nav_list = $this->navClass(526);
                } else {
                    //查询权限
                    $_SESSION['role_ids'] = $this->_getUserRoleIds($auth_user['role_id']);
                    //缓存目录
                    $nav_list = $this->navClass(526, $_SESSION['role_ids']);
                }
                //目录缓存
                S('nav_list'.$auth_user['id'], $nav_list);

                $data['status']  = true;
                $data['content'] = "success";
                $this->ajaxReturn($data);
            }
            $data['status']  = false;
            $data['content'] = "错误的查寻！";
            $this->ajaxReturn($data);
        }
    }

    private function _getUserRoleIds($id) {
        $map['role_id'] = $id;
        if (!$result = M('admin_auth')->where($map)->getField('menu_id', true)) {
            $this->error('登录时查询用户权限出错。', '/Appadmin/Login/');
            exit();
        } else {
            $role_ids = implode(',', $result);
            return $role_ids;
        }

    }

    public function logout() {
        session_destroy();
        redirect(U('/AppAdmin/login/'));
    }

    //验证码
    private function check_verify($code, $id = '') {
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }

    //多维数组展示分类
    public function navClass($pid = 0, $role_ids = null) {
        $menu           = M('menu');
        $map['pid']     = $pid;
        $map['display'] = 1;
        if ($role_ids != null) {
            $map['id'] = array( 'in', $role_ids );
        }
        $list = $menu->where($map)->order('ordid')->select();
        foreach ($list as $k => $v) {
            if ($c_list = $this->navClass($v[id], $role_ids)) {
                $list[$k]['child_nav'] = $c_list;
            }
        }
        return $list;
    }

    /**
     *  修改用户的密码
     *
     * @param uid        string
     * @param password   string
     * @return bool
     */
    public function revisePassword() {
        $param = array(
            'password'    => I('get.password', '', 'trim'),
            'newpassword' => I('get.newpassword', '', 'trim'),
            'repassword'  => I('get.repassword', '', 'trim'),
        );
        $uid   = session('auth_user');
        $user  = M('admin')->where(array( 'id' => $uid ))->find();
        if ($user['password'] != md5($param['password'])) {
            $this->ajaxReturn(array( 'code' => '0', 'msg' => '您的原始密码不正确！' ));
        }
        if ($param['newpassword'] != $param['repassword']) {
            $this->ajaxReturn(array( 'code' => '0', 'msg' => '两次密码不一样，请确认密码！' ));
        }
        if ($param['password'] == $param['newpassword']) {
            $this->ajaxReturn(array( 'code' => '0', 'msg' => '原始密码跟新密码不能一样，请换个新密码！' ));
        }

        $user_up = M('admin')->where(array( 'id' => $uid ))->setField('password', md5($param['newpassword']));

        if ($user_up !== false) {
            $this->ajaxReturn(array( 'code' => '1', 'msg' => '密码修改成功！' ));
        } else {
            $this->ajaxReturn(array( 'code' => '0', 'msg' => '密码修改失败！' ));
        }
    }

}