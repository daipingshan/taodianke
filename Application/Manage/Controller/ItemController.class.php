<?php

namespace Manage\Controller;

use Manage\Controller\CommonController;

/**
 * 用户控制器
 * Class UserController
 * @package Manage\Controller
 */
class ItemController extends CommonController {

    // 物品管理
    public function index() {
        $formget = [
            'buyer_phone' => I('get.buyer_phone','','trim'),
            'level_1_id'  => I('get.level_1_id','','trim'),
            'checkout'    => I('get.checkout','','trim'),
            'kword'       => I('get.kword','','trim')
        ];
        $map = [
            'id' => ['GT', 0]
        ];
        if ($formget['buyer_phone']) {
            $map['buyer_phone'] = $formget['buyer_phone'];
        }
        if ($formget['level_1_id']) {
            $map['level_1_id'] = $formget['level_1_id'];
        }
        if ($formget['checkout']) {
            $map['checkout'] = $formget['checkout'];
        }
        if (strlen($kword) > 0) {
            $map['name'] = ['LIKE','%'.$formget['kword'].'%'];
        }
        $model = M('Item');
        $count = $model->where($map)->count();
        $pages = new \Think\Page($count,$this->reqnum);
        $list = $model->where($map)->order('id desc')->limit($pages->firstRow . ',' . $pages->listRows)->select();
        $user_model = M('user');
        foreach($list as $i => $v) {
            $level_1 = $user_model->where(['id'=>$v['level_1_id']])->getField('realname');
            if ($v['level_2_id']) {
                ' / '.$level_2 = $user_model->where(['id'=>$v['level_2_id']])->getField('realname');
            }
            $list[$i]['users'] = $level_1.$level_2;
        }
        // echo $model->getLastSql();
        $this->assign('list', $list);
        $this->assign('pages', $pages->show());
        $this->assign('formget', $formget);
        $this->assign('users', D('User')->where(['level'=>1])->select());
        $this->display();
    }

    public function insert() {
        $post = I('post.');
        $sign = $post['sign'];
        unset($post['sign']);
        if (!$sign == md5(implode('', $post))) {
            $this->error('非法提交！');
            exit;
        }
        
        $_POST['password'] = encryptPwd(trim($post['password']));

        $model = D('User');
        if ($model->create()) {
            if ($model->add()) {
                $this->success('添加成功');
                exit;
            } else {
                $this->errmsg = $model->getDbError();
            }
        } else {
            $this->errmsg = $model->getError();
        }
        $this->error($this->errmsg);
    }

    public function update() {
        $post = I('post.');
        $sign = $post['sign'];
        unset($post['sign']);
        if (!$sign == md5(implode('', $post))) {
            $this->error('非法提交！');
            exit;
        }

        $password = trim($post['password']);
        if (strlen($password) == 0) {
            unset($_POST['password']);
        } else {
            $_POST['password'] = encryptPwd($password);
        }

        $model = D('User');
        $pk = $model->getPk();
        if ($_POST[$pk]) {
            if ($model->create()) {
                $result = $model->where("$pk='" . I('post.' . $pk) . "'")->save();
                if ($result !== false) {
                    $this->success('修改数据成功！');
                    exit;
                } else {
                    $this->errmsg = $model->getDbError();
                }
            } else {
                $this->errmsg = $model->getError();
            }
        } else {
            $this->errmsg = '修改数据不存在！';
        }
        $this->error($this->errmsg);
    }

    public function addCheck() {
        $error = [];
        if (IS_POST) {
            // 验证账号
            $username = I('post.username','','trim');
            if (!preg_match('/^[a-zA-Z0-9_]{6,16}$/', $username)) {
                $error['username'] = '名称不合法';
            } else {
               $user = D('User')->where(['username'=>$username])->find();
                if ($user) {
                    $error['username'] = '名称已被占用';
                } else {
                    $check[] = $username;
                }
            }

            // 验证密码
            $password = I('post.password','','trim');
            if (strlen($password) < 6) {
                $error['password'] = '密码长度不够长';
            } else if (strlen($password) > 16) {
                $error['password'] = '密码长度不够短';
            } else {
                $check[] = $password;
            }

            // 验证姓名
            $realname = I('post.realname','','trim');
            if (!preg_match('/^[\x80-\xff]{4,16}$/',$realname)) {
                $error['realname'] = '姓名不合法，非中文或者长度不合适';
            } else {
                $check[] = $realname;
            }

            // 手机号
            $phone = I('post.phone','','trim');
            if (!checkMobile($phone)) {
                $error['phone'] = '错误的手机号';
            } else {
                $check[] = $phone;
            }

            // 提成比例
            $ratio = I('post.ratio','','floatval');
            if ($ratio < 0 || $ratio > 1.0) {
                $error['ratio'] = '提成比例错误';
            } else {
                $check[] = $ratio;
            }
        }
        $data['code']  = count($error) ?: 0;
        $data['error'] = $error;
        if ($data['code'] == 0) {
            $data['sign'] = md5(implode('', I('post.')));
        }
        $this->ajaxReturn($data);
    }

    public function editCheck() {
        $error = [];
        if (IS_POST) {
            // 验证账号
            $username = I('post.username','','trim');
            if (!preg_match('/^[a-zA-Z0-9_]{6,16}$/', $username)) {
                $error['username'] = '名称不合法';
            } else {
               $check[] = $username;
            }

            // 验证密码
            $password = I('post.password','','trim');
            if (strlen($password) != 0) {
                if (strlen($password) < 6) {
                    $error['password'] = '密码长度不够长';
                } else if (strlen($password) > 16) {
                    $error['password'] = '密码长度不够短';
                } else {
                    $check[] = $password;
                }
            }

            // 验证姓名
            $realname = I('post.realname','','trim');
            if (!preg_match('/^[\x80-\xff]{4,16}$/',$realname)) {
                $error['realname'] = '姓名不合法，非中文或者长度不合适';
            } else {
                $check[] = $realname;
            }

            // 手机号
            $phone = I('post.phone','','trim');
            if (!checkMobile($phone)) {
                $error['phone'] = '错误的手机号';
            } else {
                $check[] = $phone;
            }

            // 提成比例
            $ratio = I('post.ratio','','floatval');
            if ($ratio < 0 || $ratio > 1.0) {
                $error['ratio'] = '提成比例错误';
            } else {
                $check[] = $ratio;
            }
        }
        $data['code']  = count($error) ?: 0;
        $data['error'] = $error;
        if ($data['code'] == 0) {
            $data['sign'] = md5(implode('', I('post.')));
        }
        $this->ajaxReturn($data);
    }

    public function status($id, $val) {
        $model = D('User');
        $user = $model->where(['id'=>$id])->find();
        if (!$user) {
            $data = ['error'=>1,'mesg'=>'用户不存在！'];
        } else {
            if ($user['status'] != $val && in_array($user['status'], ['e','d'])) {
                $data = ['error'=>1,'mesg'=>'无效参数！'];
            } else {
                if ($val == 'e') {
                    $status = 'd';
                }
                if ($val == 'd') {
                    $status = 'e';
                }
                if (!$model->where(['id'=>$id])->setField('status',$status)) {
                    $data = ['error'=>1,'mesg'=>'操作失败！'];
                } else {
                    $data = ['error'=>0,'mesg'=>'操作成功！'];
                }
            }
        }
        $this->ajaxReturn($data);
    }

}
