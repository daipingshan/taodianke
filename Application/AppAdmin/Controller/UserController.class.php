<?php

namespace AppAdmin\Controller;

class UserController extends CommonController {

    /**
     * 用户管理首页
     *
     * @return array
     */
    public function index() {
        $data = array(
            'real_name'       => I('get.real_name', '', 'trim'),
            'reg_time'        => I('get.zhuce_time', '', 'trim'),
            'tel'             => I('get.tel', '', 'trim'),
            'pid'             => I('get.pid', '', 'trim'),
            'proxy_type'      => I('get.proxy_type', '', 'trim'),
            'bank_account' => I('get.bank_account', '', 'trim'),
            'parent_id'       => I('get.parent_id', 0, 'intval'),
        );

        $map = array();
        if ($data['real_name']) {
            $map['real_name'] = array(
                'like',
                '%' . $data['real_name'] . '%'
            );
        }
        if ($data['reg_time']) {
            $start_tmp       = explode('/', $data['reg_time']);
            $zhuce_time      = mktime(0, 0, 0, $start_tmp[0], $start_tmp[1], $start_tmp[2]);
            $map['reg_time'] = array(
                'egt',
                $zhuce_time
            );
        }
        if ($data['tel']) {
            $map['mobile'] = $data['tel'];
        }
        if ($data['pid']) {
            $map['pid'] = $data['pid'];
        }
        if ($data['bank_account']) {
            $map['bank_account'] = $data['bank_account'];
        }
        if ($data['proxy_type']) {
            $map['proxy_type'] = $data['proxy_type'];
        }

        if ($data['parent_id'] > 0) {
            $map['ParentID'] = $data['parent_id'];
        }

        $count = M('user')->where($map)->count('id');
        $page  = $this->pages($count, $this->limit);

        $list = M('user')->where($map)->order('id DESC')->limit($page->firstRow . ',' . $page->listRows)->select();

        $user = $userids = array();
        foreach ($list as $k => $v) {
            $userids[$v['parentid']] = $v['parentid'];
            //  获取推广位信息
            $zone[$v['id']] = M('zone')->where(array('uid' => $v['id']))->select();
        }

        if ($userids) {
            $user = M('user')->where(array(
                'id' => array(
                    'in',
                    array_keys($userids)
                )
            ))->field('id, real_name')->index('id')->select();
        }

        $this->assign('list', $list);
        $this->assign('zone', $zone);
        $this->assign('user', $user);
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 修改用户信息
     */
    public function edit() {
        $user_id = I('get.id', 0, 'intval');
        $user    = M('user');
        if ($user_id != 0 && $user_id != '') {
            $userinfo = $user->where('id=' . $user_id)->find();
            $this->assign('userinfo', $userinfo);
        }

        if (IS_POST && $user_id != 0) {
            $data['email']               = I('post.email', null, 'email');
            $data['real_name']           = I('post.real_name', null, 'string');
            $data['qq']                  = I('post.qq', null, 'int');
            $data['bank_account']        = I('post.bank_account', null, 'string');
            $data['ParentID']            = I('post.ParentID', 0, 'number_int');
            $data['proxy_type']          = I('post.proxy_type', 0, 'number_int');
            $data['join_agent_time']     = I('post.join_agent_time', '', 'trim') ? strtotime(I('post.join_agent_time', '', 'trim')) : 0;
            $data['is_wechat_marketing'] = I('post.is_wechat_marketing', 'N', 'trim');
            $data['status']              = I('post.status', 1, 'number_int');
            $pwd                         = trim(I('post.password', null, 'string'));
            if ($pwd != '') {
                $data['password'] = md5($pwd);
            }
            if ($user->where('id = ' . $user_id)->save($data)) {
                $this->success('修改成功', U('/AppAdmin/User/index'));
                exit();
            } else {
                $this->error('未做修改。', U('/AppAdmin/User/index'));
            }
        }
        $this->assign('id', $user_id);
        $this->display();
    }

    /**
     * 用户管理首页
     *
     * @return array
     */
    public function biYou() {
        $data = array(
            'pid'      => I('get.pid', '', 'trim'),
            'mobile'   => I('get.mobile', '', 'trim'),
            'username' => I('get.username', '', 'trim'),
            'reg_time' => I('get.reg_time', '', 'trim'),
        );

        $map = array();
        if ($data['username']) {
            $map['username'] = array(
                'like',
                '%' . $data['username'] . '%'
            );
        }
        if ($data['reg_time']) {
            $start_tmp       = explode('/', $data['reg_time']);
            $zhuce_time      = mktime(0, 0, 0, $start_tmp[0], $start_tmp[1], $start_tmp[2]);
            $map['reg_time'] = array(
                'egt',
                $zhuce_time
            );
        }
        if ($data['mobile']) {
            $map['mobile'] = $data['mobile'];
        }
        if ($data['pid']) {
            $map['pid'] = $data['pid'];
        }

        $count = M('bu_user')->where($map)->count('id');
        $page  = $this->pages($count, $this->limit);

        $list = M('bu_user')->where($map)->order('id DESC')->limit($page->firstRow . ',' . $page->listRows)->select();

        $this->assign('list', $list);
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 修改用户信息
     */
    public function biyouUserEdit() {
        $user_id = I('get.id', 0, 'intval');
        $user    = M('bu_user');
        if ($user_id != 0 && $user_id != '') {
            $userinfo = $user->where(array('id' => $user_id))->find();
            $this->assign('userinfo', $userinfo);
        }

        if (IS_POST && $user_id != 0) {
            $pwd = trim(I('post.password', null, 'string'));
            if ($pwd != '' || $pwd != null) {
                $data['password'] = md5(md5($pwd));
            }
            if ($user->where(array('id' => $user_id))->save($data)) {
                $this->success('修改成功', U('User/biYou'));
                exit();
            } else {
                $this->error('未做修改。', U('User/biYou'));
            }
        }
        $this->assign('id', $user_id);
        $this->display();
    }

    /**
     * 获取用户推广位
     */
    public function buZone() {
        $user_id = I('get.id', 0, 'intval');
        $user    = M('bu_zone')->where(array('user_id' => $user_id))->select();
        if ($user) {
            $this->ajaxReturn(array(
                'code' => 0,
                'data' => $user,
                'msg'  => '推广位获取成功'
            ));
        } else {
            $this->ajaxReturn(array(
                'code' => -1,
                'data' => '',
                'msg'  => '推广位获取失败'
            ));
        }
    }

    /**
     * 生成用户的二维码
     *
     * @param $id
     * @return array
     */
    public function getWechatQrcode() {
        $user_id = I('get.id', 0, 'intval');
        $user    = M('user')->where(array('id' => $user_id))->find();
        if (empty($user['pid'])) {
            $this->ajaxReturn(array(
                'code' => -1,
                'msg'  => '该用户还没有绑定pid'
            ));
        } else if (empty($user['wx_qrcode_url'])) {
            $img_url    = $this->_getQrcodeUrl($user['pid'], $user['store_type']);
            $qrcode_url = getImgUrl($img_url);
            M('user')->where(array('id' => $user_id))->setField('wx_qrcode_url', $img_url);
        } else {
            $tmp = basename($user['wx_qrcode_url'], '.jpg');
            $tmp = explode('_', $tmp);
            if (C('WEIXIN_MP.share_bg_update_time') > $tmp[1]) {
                $img_url    = $this->_getQrcodeUrl($user['pid'], $user['store_type']);
                $qrcode_url = getImgUrl($img_url);
                M('user')->where(array('id' => $user_id))->setField('wx_qrcode_url', $img_url);
            } else {
                $qrcode_url = getImgUrl($user['wx_qrcode_url']);
            }
        }

        if ($qrcode_url) {
            $this->ajaxReturn(array(
                'code' => 0,
                'data' => $qrcode_url,
                'msg'  => '二维码生成成功'
            ));
        } else {
            $this->ajaxReturn(array(
                'code' => -1,
                'data' => '',
                'msg'  => '二维码生成失败'
            ));
        }
    }

    /**
     * 获取报名用户的基本信息
     */
    public function getConsumer() {
        $count = M('join_consumer')->count('id');
        $page  = $this->pages($count, $this->limit);

        $consumer = M('join_consumer')->order('id DESC')->limit($page->firstRow . ',' . $page->listRows)->select();

        $this->assign('page', $page->show());
        $this->assign('consumer', $consumer);
        $this->display();
    }

    /**
     * 添加推广位pid
     */
    public function addMorePid() {
        $uid             = I('get.uid', '', 'int');
        $zone_pid        = I('get.zone_pid', '', 'trim');
        $dwxk_adsense_id = I('get.dwxk_adsense_id', 0, 'int');
        $zone_name       = I('get.zone_name', '', 'trim');
        $is_default      = I('get.is_default', 0, 'int');

        // 检测信息的有效性
        $res = $this->checkPid($zone_name, $zone_pid, $dwxk_adsense_id);
        if ($res['code'] == -1) {
            $this->ajaxReturn($res);
        }

        // 检测系统中是否含有用户填写的pid或大微信客id
        $where['pid'] = $zone_pid;
        if ($dwxk_adsense_id > 0) {
            $where['dwxk_adsense_id'] = $dwxk_adsense_id;
            $where['_logic']          = 'OR';
        }
        $pid_num = M('zone')->where($where)->count('id');
        if ($pid_num > 0) {
            $this->ajaxReturn(array(
                'code' => -1,
                'msg'  => '您填写的pid或大微信客推广位已存在，请核对后再添加'
            ));
        }

        $count = M('zone')->where(array('uid' => $uid))->count();
        if ($count == 0) {
            $is_default = 1;
        }
        $add_res = 0;
        $model   = M();
        $model->startTrans();
        try {
            if ($is_default == 1) {
                $user_save_data = array('pid' => $zone_pid, 'dwxk_adsense_id' => $dwxk_adsense_id, 'wx_qrcode_url' => '');
                if ($count == 0) { //首次添加推广位默认设置成为代理的日期为当天
                    $user_save_data['join_agent_time'] = time();
                } else {
                    M('wxuser')->where('proxy_uid=' . $uid)->save(array('proxy_pid' => $zone_pid));
                    M('zone')->where(array('uid' => $uid))->save(array('is_default' => 0));
                }
                M('user')->where(array('id' => $uid))->save($user_save_data);

                $token = M('user')->where(array('id' => $uid))->getField('token');
                S('tdk_' . $token, null);
            }

            $param = array(
                'uid'             => $uid,
                'zone_name'       => $zone_name,
                'pid'             => $zone_pid,
                'dwxk_adsense_id' => $dwxk_adsense_id,
                'is_default'      => $is_default
            );
            M('zone')->add($param);

            if ($model->commit()) {
                S('tdk_zone_' . $uid, null);
                $this->ajaxReturn(array(
                    'code' => 0,
                    'msg'  => '推广位添加成功！'
                ));
            } else {
                $model->rollback();
                $this->ajaxReturn(array(
                    'code' => -1,
                    'msg'  => '推广位添加失败，请稍后重试'
                ));
            }
        } catch (\Exception $e) {
            $model->rollback();
            $this->ajaxReturn(array(
                'code' => -1,
                'msg'  => '推广位添加失败，请稍后重试'
            ));
        }
    }

    /**
     * 编辑推广位
     */
    public function editPid() {
        $id              = I('get.id', '', 'int');
        $uid             = I('get.uid', '', 'int');
        $zone_pid        = I('get.zone_pid', '', 'trim');
        $zone_name       = I('get.zone_name', '', 'trim');
        $dwxk_adsense_id = I('get.dwxk_adsense_id', '', 'trim');
        $is_default      = I('get.is_default', 0, 'int');

        // 检测信息的有效性
        $res = $this->checkPid($zone_name, $zone_pid, $dwxk_adsense_id);
        if ($res['code'] == -1) {
            $this->ajaxReturn($res);
        }

        // 检测系统中是否含有用户填写的pid或大微信客id
        $where['pid'] = $zone_pid;
        if ($dwxk_adsense_id > 0) {
            $where['dwxk_adsense_id'] = $dwxk_adsense_id;
            $where['_logic']          = 'OR';
        }
        $zone = M('zone')->where($where)->find();
        if (isset($zone['id']) && $zone['id'] != $id) {
            $this->ajaxReturn(array(
                'code' => -1,
                'msg'  => '您填写的pid或大微信客推广位已在其他账号名下，请核对后再修改'
            ));
        }

        $model = M();
        $model->startTrans();
        try {
            if ($is_default == 1) {
                M('zone')->where(array('uid' => $uid))->save(array('is_default' => 0));

                $user_save_data = array('pid' => $zone_pid, 'dwxk_adsense_id' => $dwxk_adsense_id);
                if ($zone['pid'] != $zone_pid) {
                    $user_save_data['wx_qrcode_url'] = ''; //新设置的默认推广位重新生成推广海报

                    M('wxuser')->where('proxy_uid=' . $uid)->save(array('proxy_pid' => $zone_pid));
                    M('store_wxuser')->where('proxy_uid=' . $uid)->save(array('proxy_pid' => $zone_pid));
                }
                M('user')->where(array('id' => $uid))->save($user_save_data);

                $token = M('user')->where(array('id' => $uid))->getField('token');
                S('tdk_' . $token, null);
            }

            $param = array(
                'id'              => $id,
                'zone_name'       => $zone_name,
                'pid'             => $zone_pid,
                'dwxk_adsense_id' => $dwxk_adsense_id,
                'is_default'      => $is_default
            );
            M('zone')->save($param);

            if ($model->commit()) {
                S('tdk_zone_' . $uid, null);
                $this->ajaxReturn(array(
                    'code' => 0,
                    'msg'  => '推广位编辑成功！'
                ));
            } else {
                $this->ajaxReturn(array(
                    'code' => -1,
                    'msg'  => '推广位编辑失败，请稍后重试'
                ));
            }
        } catch (\Exception $e) {
            $model->rollback();
            $this->ajaxReturn(array(
                'code' => -1,
                'msg'  => '推广位编辑失败，请稍后重试'
            ));
        }
    }

    /**
     * 删除推广位
     */
    public function deletePid() {
        $id  = I('get.id', '', 'int');
        $uid = I('get.uid', '', 'int');

        $zones = M('zone')->where(array('uid' => $uid))->index('id')->select();
        if (empty($zones) || !isset($zones[$id])) {
            $this->ajaxReturn(array(
                'code' => -1,
                'msg'  => '推广位不存在'
            ));
        } else if (count($zones) > 1 && 1 == $zones[$id]['is_default']) {
            $this->ajaxReturn(array(
                'code' => -1,
                'msg'  => '还有更多推广位，请勿删除默认推广位'
            ));
        }

        if (1 == $zones[$id]['is_default']) {
            $model = M();
            $model->startTrans();
            try {
                M('zone')->where(array('id' => $id))->delete();
                M('user')->where(array('id' => $uid))->save(array('pid' => '', 'dwxk_adsense_id' => 0));

                if ($model->commit()) {
                    S('tdk_zone_' . $uid, null);
                    $this->ajaxReturn(array(
                        'code' => 0,
                        'msg'  => '推广位删除成功！'
                    ));
                } else {
                    $this->ajaxReturn(array(
                        'code' => -1,
                        'msg'  => '推广位删除失败，请稍后重试'
                    ));
                }
            } catch (\Exception $e) {
                $model->rollback();
                $this->ajaxReturn(array(
                    'code' => -1,
                    'msg'  => '推广位删除失败，请稍后重试'
                ));
            }
        } else {
            $res = M('zone')->where(array('id' => $id))->delete();
            if (true == $res) {
                $this->ajaxReturn(array(
                    'code' => 0,
                    'msg'  => '推广位删除成功！'
                ));
                S('tdk_zone_' . $uid, null);
            } else {
                $this->ajaxReturn(array(
                    'code' => -1,
                    'msg'  => '推广位删除失败，请稍后重试'
                ));
            }
        }
    }

    /**
     * 检测用户pid或大微信客推广位的合法性
     */
    public function checkPid($zone_name, $zone_pid, $dwxk_adsense_id) {
        $data = array(
            'code' => -1,
            'msg'  => ''
        );

        if (empty($zone_pid) || empty($zone_name)) {
            $data['msg'] = 'pid或推广位名称不能为空，请检查后重新填写';
            return $data;
        }

        //  检测用户填写的pid是否合法
        $pid_res = preg_match("/^m{2}_\d{7,9}_\d{7,9}_\d{7,9}$/", $zone_pid);
        if ($pid_res != 1) {
            $data['msg'] = '您填写的pid不合法，请核对后重新填写';
            return $data;
        }

        //  检查大微信客推广位的合法性
        if ($dwxk_adsense_id > 0) {
            $dwxk_check_res = preg_match("/^\d{5}$/", $dwxk_adsense_id);
            if (!empty($dwxk_adsense_id) && ($dwxk_check_res != 1)) {
                $data['msg'] = '您填写的大微信客推广位不合法，请核对后重新填写';
                return $data;
            }
        }

        $data['code'] = 0;
        return $data;
    }

    /**
     * 设置店主身份
     */
    public function setStore() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('post.id', 0, 'int');
        $info = M('user')->find($id);
        if (!$id || !$info) {
            $this->error('用户信息不存在无法设置店主身份！');
        }
        $realname    = I('post.realname', '', 'trim');
        $user_remark = I('post.user_remark', '', 'trim');
        $address     = I('post.address', '', 'trim');
        if (!$realname) {
            $this->error('真实姓名不能为空！');
        }
        if (!$user_remark) {
            $this->error('店铺名称不能为空！');
        }
        if (!$address) {
            $this->error('店铺地址不能为空！');
        }

        //查询是否已成为收银，已存在的更新时间，不存在的新增收银。并更改账号店员类型为收银员
        $cashier = M('cashier')->where(array('store_uid' => $id, 'cashier_uid' => $id))->find();
        $model   = M();
        $model->startTrans();
        $now = time();
        if (empty($cashier)) {
            $data = array(
                'store_uid' => $id,
                'cashier_uid' => $id,
                'real_name' => $realname,
                'cashier_mobile' => $info['mobile'],
                'status' => 1,
                'add_time' => $now,
                'open_time' => $now
            );
            M('cashier')->add($data);
        } else {
            M('cashier')->where('id=' . $cashier['id'])->save(array('real_name' => $realname, 'status' => 1, 'open_time' => $now));
        }
        $save_data = array('id' => $id, 'real_name' => $realname, 'user_remark' => $user_remark, 'address' => $address, 'store_type' => 1, 'wx_qrcode_url' => '');
        M('user')->save($save_data);
        if ($model->commit()) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败！');
        }
    }

    /**
     * 取消店主身份
     */
    public function delStore() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('post.id', 0, 'int');
        $info = M('user')->find($id);
        if (!$id || !$info) {
            $this->error('用户信息不存在无法取消店主身份！');
        }
        if ($info['store_type'] != 1) {
            $this->error('用户身份本身不是店主身份，无法取消店主身份！');
        }

        $model   = M();
        $model->startTrans();

        M('cashier')->where(array('store_uid' => $id, 'cashier_uid' => $id))->save(array('status' => 0));
        M('user')->where(array('id' => $id))->save(array('store_type' => 0, 'wx_qrcode_url' => ''));

        if ($model->commit()) {
            $this->success('取消成功');
        } else {
            $this->error('取消失败！');
        }
    }

}