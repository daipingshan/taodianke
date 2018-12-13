<?php

namespace Fxapi\Controller;

class UserController extends CommonController {

	// 修改密码-------------------------------------------------------
	public function resetPwd() {
		$oldpass = I('post.oldpass', '', 'trim');
        $newpass = I('post.newpass', '', 'trim');
        $re_newpass = I('post.re_newpass', '', 'trim');
        $model = D('User');
        $userRes = $model->where(['id'=>$this->uid])->field('id,password')->find();
        if (strcmp($userRes['password'], encryptPwd(trim($oldpass))) !== 0) {
        	$this->outPut(null, -1, null, '原密码错误');
        }
        if ($newpass !== $re_newpass) {
        	$this->outPut(null, -1, null, '两次密码输入不一致');
        }
        if (false === $model->where(['id'=>$this->uid])->setField('password',encryptPwd($newpass))) {
        	$this->outPut(null, -1, null, '操作失败');
        }
        $this->outPut(null, 0, null, '密码已修改');
	}

    // 获取用户信息-------------------------------------------------------
    public function getUser() {
        $model = D('User');
        $map = ['id'=>$this->uid];
        $filed = 'username,realname,phone,address,level,ratio,pid,status,login_time,mid,pay_bank_no,pay_bank_name';
        $user = $model->field($filed)->where($map)->find();
        if (!$user) {
            $this->outPut(null, -1, null, '用户不存在');
        } else {
            $this->outPut(['list'=>[$user]], 0, null);
        }
    }

    // 注册下线账号-------------------------------------------------------
    public function regist() {
        $username = I('post.username', '', 'trim');
        $password = I('post.password', '', 'trim');
        $realname = I('post.realname', '', 'trim');
        $phone    = I('post.phone', '', 'trim');
        // $address  = I('post.address', '', 'trim');
        $model = D('User');
        $error = [];
        $user = $model->where(['username'=>$username])->find();
        if ($user) {
            $error[] = '用户名已被占用！';
        } else {
            if (strlen($username) < 6) {
                $error[] = (count($error)+1).'.用户名太短！';
            } else if (strlen($username) > 16) {
                $error[] = (count($error)+1).'.用户名太长！';
            }
            if (strlen($password) < 6) {
                $error[] = (count($error)+1).'.密码太短！';
            } else if (strlen($password) > 16) {
                $error[] = (count($error)+1).'.密码太长！';
            }
            if (!preg_match('/^[\x80-\xff]{4,16}$/',$realname)) {
                $error[] = (count($error)+1).'.请输入2~8个汉字姓名！';
            }
            if (!checkMobile($phone)) {
                $error[] = (count($error)+1).'.手机号码错误！';
            }
        }
        $data = [
            'username' => $username,
            'password' => encryptPwd($password),
            'realname' => $realname,
            'phone'    => $phone,
            'level'    => 2,
            'pid'      => $this->uid
        ];
        if (!empty($error)) {
            $this->outPut(null, -1, null, implode('|', $error));
        } else {
            $uid = $model->add($data);
            if (!$uid) {
                $error[] = '注册失败！';
            }
        }
        $this->outPut(['uid'=>$uid], 0, null);
    }

    public function update($uid = 0) {
        if ($uid == 0) {
            $uid = $this->uid;
        }
        $realname = I('post.realname', '', 'trim');
        $phone    = I('post.phone', '', 'trim');
        $address  = I('post.address', '', 'trim');
        $pay_bank_no    = I('post.pay_bank_no', '', 'trim');
        $pay_bank_name  = I('post.pay_bank_name', '', 'trim');
        $model = D('User');
        $error = [];
        if (!preg_match('/^[\x80-\xff]{4,16}$/',$realname)) {
            $error[] = (count($error)+1).'.请输入2~8个汉字姓名！';
        }
        if (!checkMobile($phone)) {
            $error[] = (count($error)+1).'.手机号码错误！';
        }
        $data = [
            'realname' => $realname,
            'phone'    => $phone,
            'address'  => $address,
            'pay_bank_no' => $pay_bank_no,
            'pay_bank_name' => $pay_bank_name
        ];
        if (!empty($error)) {
            $this->outPut(null, -1, null, implode('|', $error));
        } else {
            $res = $model->where(['id'=>$uid])->save($data);
            if (!$res) {
                $this->outPut(null, -1, null,'更新失败');
            } else {
                $this->outPut(null, 0, null,'更新成功');
            }
        }
    }

    // 下线列表---------------------------------------------------------
    public function users() {
        $phone = I('param.phone','','trim');
        $kword = I('param.kword','','trim');
        $map = [
            'pid' => $this->uid
        ];
        if ($phone) {
            $map['phone'] = $phone;
        }
        if ($kword) {
            $map['username|realname'] = ['LIKE','%'.$kword.'%'];
        }
        $model = D('User');
        $filed = 'username,realname,phone,address,level,ratio,pid,status,login_time,mid,pay_bank_no,pay_bank_name';
        $count = $model->field($filed)->where($map)->count();
        $offset = I('param.offset',0,'intval');
        $limit  = I('param.limit',$count,'intval');
        $list = $model->where($map)->limit($offset,$limit)->select();
        $data = [
            'list'   => $list,
            'offset' => $offset,
            'limit'  => $limit,
            'count'  => $count
        ];
        $this->outPut($data, 0, null);
    }

}
