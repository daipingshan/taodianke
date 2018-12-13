<?php

namespace Admin\Controller;

/**
 * 代理商管理
 */
class AgentController extends CommonController {

    public function index() {

       $query = urldecode(I('get.query','','trim'));

        $where = array();
        if($query){
            $where['_string'] = "`username` like '%{$query}%' or `mobile` like '%{$query}%'";
        }

        $agent = M('agent');
        $count = $agent->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $agent->where($where)->limit($limit)->order('id desc')->select();

        if ($list) {
            foreach ($list as $k => $v) {
                $area = array();
                $category = M('category');
                if ($v['province_id']) {
                    $map = array('id'=>$v['province_id']);
                    $name = $category->where($map)->getField('name');
                    $name != '' ? $area['province'] = $name : null;
                }
                if (isset($area['province']) && trim($area['province']) !='' && $v['city_id']) {
                    $map = array('id'=>$v['city_id']);
                    $name = $category->where($map)->getField('name');
                    $name != '' ? $area['city'] = $name : null;
                }
                if (isset($area['city']) && trim($area['city']) !='' && $v['area_id']) {
                    $map = array('id'=>$v['area_id']);
                    $name = $category->where($map)->getField('name');
                    $name != '' ? $area['area'] = $name : null;
                }
                $v['areatext'] = implode('/', $area);
                $list[$k] = $v;
            }
        }

        $data = array(
            'query'=>$query,
            'pages' => $page->show(),
            'list' => $list,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 添加代理商
     */
    public function add() {
        if (IS_POST) {
            $agent_data = I('post.agent', array(), '');
            $this->handle_add_edit_data($agent_data);
            $res = M('agent')->add($agent_data);
            if (!$res) {
                $this->redirect_message(U('agent/add'), array('error' => '代理商添加失败！'));
            }
            $this->redirect_message(U('agent/index'), array('success' => '代理商添加成功！'));
            die();
        }
        $f_cate_list = M('category')->where(array('fid' => 0))->field('id,name')->select();

        $data = array(
            'f_cate_list' => $f_cate_list,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 锁定代理商
     */
    public function lock() {
        $aid = I('get.aid', '', 'trim');
        if (!$aid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '代理商不存在！'));
        }

        $agent = M('agent');
        $where = array('id' => $aid);
        $agent_count = $agent->where($where)->count();
        if (!$agent_count || $agent_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '代理商不存在！'));
        }
        $data=array(
            'status'=>1
        );
        $res = $agent->where($where)->save($data);
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '锁定代理商失败！'));
        }

        $this->ajaxReturn(array('code' => 0, 'success' => '锁定代理商成功！'));
    }

    /**
     * 解锁代理商
     */
    public function unlock() {
        $aid = I('get.aid', '', 'trim');
        if (!$aid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '代理商不存在！'));
        }

        $agent = M('agent');
        $where = array('id' => $aid);
        $agent_count = $agent->where($where)->count();
        if (!$agent_count || $agent_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '代理商不存在！'));
        }
        $data=array(
            'status'=>0
        );
        $res = $agent->where($where)->save($data);
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '解锁代理商失败！'));
        }

        $this->ajaxReturn(array('code' => 0, 'success' => '解锁代理商成功！'));
    }

    /**
     * 根据父id获取子分类
     */
    public function get_c_cate_by_fid() {
        $fid = I('post.fid', '', 'trim');
        if (!$fid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '父id为空！'));
        }
        $f_cate_list = M('category')->where(array('fid' => $fid))->field('id,name')->select();
        $this->ajaxReturn(array('code' => 0, 'data' => $f_cate_list));
    }

    /**
     * 编辑代理商
     */
    public function edit() {
        if (IS_POST) {
            $agent_data = I('post.agent', array(), '');
            $agent_id = I('post.aid', array(), '');
            $this->handle_add_edit_data($agent_data);
            $res = M('agent')->where(array('id' => $agent_id))->save($agent_data);
            if ($res === false) {
                $this->redirect_message(U('agent/edit', array('aid' => $agent_id)), array('error' => '代理商编辑失败！'));
            }
            $this->redirect_message(U('agent/index'), array('success' => '代理商编辑成功！'));
            die();
        }

        $agent_id = I('get.aid', '', '');

        $agent_info = M('agent')->where(array('id' => $agent_id))->find();

        if (!$agent_info) {
            $this->redirect_message(U('agent/index'), array('error' => '代理商不存在！'));
        }

        $f_cate_list = M('category')->where(array('fid' => 0))->field('id,name')->select();

        $data = array(
            'f_cate_list' => $f_cate_list,
            'data' => $agent_info
        );
        $this->assign($data);
        $this->display();
    }

    /**
     *  添加 编辑  数据监测
     */
    public function check_add_edit_data() {
        $agent_data = I('post.agent', array(), '');
        $agent_id = I('post.aid', array(), '');

        if (!isset($agent_data['username']) || !trim($agent_data['username'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '登录名不能为空！'));
        }

        if (!$agent_id && (!isset($agent_data['password']) || !trim($agent_data['password']))) {
            $this->ajaxReturn(array('code' => -1, 'error' => '登录密码不能为空！'));
        }

        if (!isset($agent_data['realname']) || !trim($agent_data['realname'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '姓名不能为空！'));
        }

        if (!isset($agent_data['mobile']) || !trim($agent_data['mobile'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '手机号码不能为空！'));
        }

        if (!checkMobile($agent_data['mobile'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '手机号码格式错误！'));
        }

        // if (!isset($agent_data['address']) || !trim($agent_data['address'])) {
        //     $this->ajaxReturn(array('code' => -1, 'error' => '地址不能为空！'));
        // }

        if (!isset($agent_data['bank_name']) || !trim($agent_data['bank_name'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '开户行不能为空！'));
        }
        if (!isset($agent_data['bank_user']) || !trim($agent_data['bank_user'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '开户名不能为空！'));
        }
        if (!isset($agent_data['bank_no']) || !trim($agent_data['bank_no'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '银行账户不能为空！'));
        }

        $agent = M('agent');

        // 登录用户名唯一
        $where = array(
            'username' => trim($agent_data['username']),
        );
        if ($agent_id) {
            $where['id'] = array('neq', $agent_id);
        }
        $agent_count = $agent->where($where)->count();
        if ($agent_count && $agent_count > 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '该代登录名已经被占用！'));
        }

        // 手机号码唯一
        $where = array(
            'mobile' => trim($agent_data['mobile']),
        );
        if ($agent_id) {
            $where['id'] = array('neq', $agent_id);
        }
        $agent_count = $agent->where($where)->count();
        if ($agent_count && $agent_count > 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '该手机号已经存在！'));
        }

        $this->ajaxReturn(array('code' => 0));
    }

    /**
     *  添加编辑数据处理
     * @param type $data
     */
    private function handle_add_edit_data(&$data) {
        if (isset($data['password'])) {
            if (trim($data['password'])) {
                $data['passwd'] = encryptPwd($data['password']);
            }
            unset($data['password']);
        }
    }

    /**
     * 代理商删除
     */
    public function delete() {
        $aid = I('get.aid', '', 'trim');
        if (!$aid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除代理商的id不能为空！'));
        }

        $agent = M('agent');
        $where = array('id' => $aid);
        $agent_count = $agent->where($where)->count();
        if (!$agent_count || $agent_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '你删除的代理商不存在！'));
        }

        $where = array('id' => $aid);
        $res = $agent->where($where)->delete();
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除代理商失败！'));
        }

        $this->ajaxReturn(array('code' => 0, 'success' => '删除代理商成功！'));
    }

}
