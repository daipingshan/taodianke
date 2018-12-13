<?php

namespace Admin\Controller;
/**
 *用户管理
 */
class UserController extends CommonController {

    /**
     * @var bool 是否验证uid
     */

    public function index() {

        $query = urldecode(I('get.query', '', 'trim'));

        $where = array();
        if ($query) {
            $where['_string'] = "`name` like '%{$query}%' or `mobile` like '%{$query}%' or telphone like '%{$query}%' or nickname like '%{$query}%'";
        }

        $user  = M('user');
        $count = $user->where($where)->count();
        $page  = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list  = $user->where($where)->limit($limit)->select();

        $data = array(
            'query' => $query,
            'pages' => $page->show(),
            'list'  => $list,
        );
        $this->assign($data);
        $this->display();
    }

    public function shenqing() {
        $where         = array();
        $user          = M('feedback');
        $where['type'] = '申请开店';
        $count         = $user->where($where)->count();
        $page          = $this->pages($count, $this->reqnum);
        $limit         = $page->firstRow . ',' . $page->listRows;
        $list          = $user->where($where)->limit($limit)->order('create_time desc,id desc ')->select();

        $data = array(
            'pages' => $page->show(),
            'list'  => $list,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 查看用户详情
     */
    public function info_view() {
        $uid = I('get.uid', '', 'trim');
        if (!$uid) {
            $this->redirect_message(U('User/index'), array('error' => '用户id不能为空！'));
        }

        $user_info = M('user')->where(array('id' => $uid))->find();
        $this->assign($user_info);
        $this->display();
    }

    /**
     * 查看优惠券
     */
    public function coupon() {

        $where  = array();
        $search = array(
            'code'    => I('get.code', '', 'trim'),
            'consume' => I('get.consume', '', 'trim')
        );
        if (trim($search['code'])) {
            $where['code'] = $search['code'];
        }
        if (trim($search['consume'])) {
            $where['consume'] = $search['consume'];
        }

        $card  = M('card');
        $count = $card->where($where)->count();
        $page  = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list  = $card->where($where)->limit($limit)->select();
        $data  = array(
            'pages' => $page->show(),
            'list'  => $list,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 开启环信 账号密码
     */
    public function open_ease_mob_username() {
        $uid = I('get.uid', '', 'trim');
        if (!$uid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '开启的用户名id不能为空！'));
        }

        $user_info = M('user')->where(array('id' => $uid))->field(array('id,ease_mob_id,ease_mob_password'))->find();
        if (!$user_info) {
            $this->ajaxReturn(array('code' => -1, 'error' => '开启的用户不存在！'));
        }

        if (isset($user_info['ease_mob_id']) && trim($user_info['ease_mob_id'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '已经开启环信账号，不要重复开启！'));
        }

        if (isset($user_info['ease_mob_password']) && trim($user_info['ease_mob_password'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '已经开启环信账号，不要重复开启！'));
        }

        $ease_mob = new \Common\Org\Easemob();
        $res      = $ease_mob->register_user($uid);
        if (isset($res['error']) && trim($res['error'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '开启失败！' . $res['error']));
        }

        $data = array(
            'ease_mob_id'       => isset($res['username']) ? $res['username'] : '',
            'ease_mob_password' => isset($res['password']) ? $res['password'] : '',
        );
        $res  = M('user')->where(array('id' => $uid))->save($data);
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '开启失败！'));
        }

        $this->ajaxReturn(array('code' => 0, 'success' => '开启成功！'));
    }

    /**
     * 用户列表
     */
    public function userList() {
        $username = I('get.username', '', 'trim');
        $where    = array();
        if ($username) {
            $where['name|zzname'] = array('like', "%{$username}%");
        }
        $count            = M('tmuser')->where($where)->count();
        $page             = $this->pages($count, $this->reqnum);
        $limit            = $page->firstRow . ',' . $page->listRows;
        $data             = M('tmuser')->where($where)->limit($limit)->order('id desc')->select();
        $sale_account     = M('sale_account')->where(array('id' => array('neq', 1)))->select();
        $top_line_account = M('top_line_account')->where(array('id' => array('neq', 1)))->select();
        $assign           = array(
            'pages'            => $page->show(),
            'data'             => $data,
            'sale_account'     => json_encode($sale_account),
            'top_line_account' => json_encode($top_line_account)
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 添加账号
     */
    public function addAccount() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $username  = I('post.username', '', 'trim');
        $password  = I('post.password', '', 'trim');
        $real_name = I('post.real_name', '', 'trim');
        $pid       = I('post.pid', '', 'trim');
        $group_id  = I('post.group_id', 0, 'int');
        if (!$username) {
            $this->error('账号名称不能为空！');
        }
        if (!$real_name) {
            $this->error('真实姓名不能为空！');
        }
        if (!$password) {
            $this->error('密码不能为空！');
        }
        $reg_exp = "/^m{2}_\d{7,9}_\d{7,9}_\d{7,9}$/";
        if (!preg_match($reg_exp, $pid)) {
            $this->error('PID格式不符合要求！');
        }
        if (!$group_id) {
            $this->error('请给账号授权！');
        }
        $data = array('name' => $username, 'password' => encryptPwd($password), 'zzname' => $real_name, 'pid' => $pid, 'group_id' => $group_id);
        $res  = M('tmuser')->add($data);
        if ($res) {
            S('sale_user_data', null);
            $this->success('添加成功');
        } else {
            $this->error('添加失败！');
        }
    }

    /**
     * 修改账号
     */
    public function updateAccount() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('post.id', 0, 'int');
        if (!$id) {
            $this->error('账号不存在！');
        }
        $username  = I('post.username', '', 'trim');
        $password  = I('post.password', '', 'trim');
        $real_name = I('post.real_name', '', 'trim');
        $pid       = I('post.pid', '', 'trim');
        $group_id  = I('post.group_id', 0, 'int');
        if (!$username) {
            $this->error('账号名称不能为空！');
        }
        if (!$real_name) {
            $this->error('真实姓名不能为空！');
        }
        $reg_exp = "/^m{2}_\d{7,9}_\d{7,9}_\d{7,9}$/";
        if (!preg_match($reg_exp, $pid)) {
            $this->error('PID格式不符合要求！');
        }
        if (!$group_id) {
            $this->error('请给账号授权！');
        }
        $data = array('name' => $username, 'zzname' => $real_name, 'pid' => $pid, 'id' => $id, 'group_id' => $group_id);
        if ($password) {
            $data['password'] = encryptPwd($password);
        }
        $res = M('tmuser')->save($data);
        if ($res !== false) {
            S('sale_user_data', null);
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
        }
    }

    /**
     * 删除账号
     */
    public function deleteAccount() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('post.id', 0, 'int');
        if (!$id) {
            $this->error('账号不存在！');
        }
        $res = M('tmuser')->delete($id);
        if ($res) {
            S('sale_user_data', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 特卖达人授权
     */
    public function userSaleAuth() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id      = I('post.id', 0, 'int');
        $type    = I('post.type', 'sale', 'trim');
        $auth_id = I('post.auth_id', '', 'trim');
        if (!$id) {
            $this->error('账号不存在！');
        }
        if (!$auth_id) {
            $this->error('请选择授权账号！');
        }
        if ($type == 'sale') {
            $save_data = array('id' => $id, 'sale_account_ids' => implode(',', $auth_id));
        } else {
            $save_data = array('id' => $id, 'top_line_account_ids' => implode(',', $auth_id));
        }
        $res = M('tmuser')->save($save_data);
        if ($res) {
            $this->success('授权成功');
        } else {
            $this->error('授权失败！');
        }
    }

    /**
     * 特卖达人授权
     */
    public function userTopLineAuth() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id      = I('post.id', 0, 'int');
        $auth_id = I('post.auth_id', '', 'trim');
        if (!$id) {
            $this->error('账号不存在！');
        }
        if (!$auth_id) {
            $this->error('请选择授权账号！');
        }
        $res = M('tmuser')->save(array('id' => $id, 'top_line_account_ids' => implode(',', $auth_id)));
        if ($res) {
            $this->success('授权成功');
        } else {
            $this->error('授权失败！');
        }
    }
}
