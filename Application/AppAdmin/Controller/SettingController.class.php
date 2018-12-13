<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2017/8/29 0029
 * Time: 上午 8:52
 */

namespace AppAdmin\Controller;

/**
 * Class SettingController
 *
 * @package AppAdmin\Controller
 */
class SettingController extends CommonController {

    /**
     * 淘店客后台顶级id
     *
     * @var int
     */
    private $menu_parents_id = 526;

    /**
     * 获取菜单
     */
    protected function _getAuthMenu() {
        $data = S('tdk_auth_menu');
        if (!$data) {
            $data = M('menu')->where(array('pid' => $this->menu_parents_id))->order('ordid asc,id asc')->select();
            foreach ($data as &$val) {
                $val['son_data'] = M('menu')->where(array('pid' => $val['id']))->order('ordid asc,id asc')->select();
            }
            S('tdk_auth_menu', $data);
        }
        return $data;
    }

    /**
     * 获取部门
     *
     * @param array $data
     * @param        $parent_id
     * @param string $delimiter
     * @return array
     */
    protected function _getDepartment(&$data = array(), $parent_id = 0, $delimiter = '') {
        $list = M('department')->where(array('parent_id' => $parent_id))->getField('id,department_name');
        if ($list) {
            foreach ($list as $key => $val) {
                $data[$key] = $delimiter . $val;
                $this->_getDepartment($data, $key, '----');
            }
        }
        return $data;
    }

    /**
     * 用户列表
     */
    public function index() {
        $model   = M('user');
        $keyword = I('get.keyword', '', 'trim');
        $where   = array();
        if ($keyword) {
            $where['mobile|username|email'] = array('like', "%$keyword%");
        }
        $count = $model->where($where)->count();
        $Page  = $this->pages($count, $this->limit);
        $data  = $model->where($where)->order('id DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('data', $data);
        $this->assign('page', $Page->show());
        $this->display();
    }

    /**
     * 角色列表
     */
    public function positionList() {
        $model = M('admin_role');
        $count = $model->count();
        $Page  = $this->pages($count, $this->limit);
        $data  = $model->order('id DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('data', $data);
        $this->assign('page', $Page->show());
        $this->display();
    }

    /**
     * 添加角色
     */
    public function positionAdd() {
        if (IS_POST) {
            $data['name']   = I('post.name', '', 'trim');
            $data['remark'] = I('post.remark', '', 'trim');
            $data['ordid']  = I('post.ordid', 0, 'int');
            $data['status'] = I('post.status', 0, 'int');
            $checkbox       = I('post.purview', '', 'trim');
            if (!$data['name']) {
                $this->error('角色名为必填选项！');
            }
            if (!$checkbox) {
                $this->error('请勾选相应权限！');
            }
            $model = M();
            $model->startTrans();
            $position     = M('admin_role');
            $admin_auth   = M('admin_auth');
            $position_res = $position->add($data);
            $set_data     = array();
            foreach ($checkbox as $v) {
                $set_data[] = array('role_id' => $position_res, 'menu_id' => $v);
            }
            $auth_res = $admin_auth->addAll($set_data);
            if ($position_res && $auth_res) {
                $model->commit();
                $this->success('添加成功。', U('Setting/positionList'));
            } else {
                $model->rollback();
                $this->error('添加失败，请联系管理员！');
            }
        } else {
            $nav_list = $this->_getAuthMenu();
            $this->assign('nav_list', $nav_list);
            $this->display();
        }

    }

    /**
     * 修改角色
     */
    public function positionEdit() {
        if (IS_POST) {
            $data['name']   = I('post.name', '', 'trim');
            $data['remark'] = I('post.remark', '', 'trim');
            $data['ordid']  = I('post.ordid', 0, 'int');
            $data['status'] = I('post.status', 0, 'int');
            $data['id']     = I('post.id', 0, 'int');
            $checkbox       = I('post.purview', '', 'trim');
            if (!$data['id']) {
                $this->error('请求参数不合法！');
            }
            if (!$data['name']) {
                $this->error('角色名为必填选项！');
            }
            if (!$checkbox) {
                $this->error('请勾选相应权限！');
            }
            $model = M();
            $model->startTrans();
            $position     = M('admin_role');
            $admin_auth   = M('admin_auth');
            $position_res = $position->save($data);
            $admin_auth->where(array('role_id' => $data['id']))->delete();
            $set_data = array();
            foreach ($checkbox as $v) {
                $set_data[] = array('role_id' => $data['id'], 'menu_id' => $v);
            }
            $auth_res = $admin_auth->addAll($set_data);
            if ($position_res !== false && $auth_res) {
                $model->commit();
                $this->success('修改成功', U('Setting/positionList'));
            } else {
                $model->rollback();
                $this->error('修改失败，请联系管理员！');
            }
        } else {
            $position        = M('admin_role');
            $admin_auth      = M('admin_auth');
            $id              = I('get.id', 0, 'int');
            $role            = $position->where(array('id' => $id))->find();
            $admin_auth_data = $admin_auth->where(array('role_id' => $id))->select();
            $nav_list        = $this->_getAuthMenu();
            $this->assign('role', $role);
            $this->assign('nav_list', $nav_list);
            $this->assign('admin_auth', $admin_auth_data);
            $this->display();
        }

    }

    /**
     * 删除角色
     */
    public function positionDel() {
        $position = M('admin_role');
        $id       = I('get.id', 0, 'int');
        $info     = $position->find($id);
        if (!$id || !$info) {
            $this->error('角色信息不存在，无法删除！');
        }
        $count = M('admin')->where(array('role_id' => $id))->count('id');
        if ($count) {
            $this->error('该角色下存在相应管理员，不能删除！');
        }
        $model = M();
        $model->startTrans();
        $position_res = M('admin_role')->delete($id);
        $auth_res     = M('admin_auth')->where(array('role_id' => $id))->delete();
        if ($position_res && $auth_res) {
            $model->commit();
            $this->success('删除成功');
        } else {
            $model->rollback();
            $this->error('删除失败，请联系管理员！');
        }
    }

    /**
     * 管理员列表
     */
    public function adminList() {
        $model         = M('admin');
        $username      = I('get.username', '', 'trim');
        $role_id       = I('get.role_id', 0, 'int');
        $department_id = I('get.department_id', 0, 'int');
        $where         = array();
        if ($username) {
            $where['a.username|a.full_name'] = array('like', "%{$username}%");
        }
        if ($role_id) {
            $where['a.role_id'] = $role_id;
        }
        if ($department_id) {
            $where['a.department_id'] = $department_id;
        }
        $count = $model->alias('a')->where($where)->count('id');
        $Page  = $this->pages($count, $this->limit);
        $data  = $model->alias('a')->where($where)
            ->join('left join ytt_admin_role r ON r.id = a.role_id')
            ->join('left join ytt_department d ON d.id = a.department_id')
            ->field('a.*,r.name,d.department_name')
            ->order('a.id desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
        foreach ($data as $key => $val) {
            switch ($val['position_level']) {
                case 'basic':
                    $data[$key]['position_level'] = '基层员工';
                    break;
                case 'middle':
                    $data[$key]['position_level'] = '部门主管';
                    break;
                case 'manager':
                    $data[$key]['position_level'] = '部门经理';
                    break;
                default:
                    $data[$key]['position_level'] = '基层员工';
                    break;
            }
        }
        $department = $this->_getDepartment();
        $role_list  = M('admin_role')->getField('id,name');
        $this->assign('department_list', $department);
        $this->assign('role_list', $role_list);
        $this->assign('data', $data);
        $this->assign('page', $Page->show());
        $this->display();
    }

    /**
     * 添加管理员
     */
    public function adminAdd() {
        if (IS_POST) {
            $data['username']       = I('post.username', '', 'trim');
            $data['full_name']      = I('post.full_name', '', 'trim');
            $data['role_id']        = I('post.role_id', 0, 'int');
            $data['password']       = I('post.password', '', 'trim');
            $data['department_id']  = I('post.department_id', 0, 'int');
            $data['position_level'] = I('post.position_level', '', 'trim');
            $data['email']          = I('post.email', '', 'trim');
            $data['status']         = I('post.status', '', 'int');
            $data['open_id']        = I('post.open_id', '', 'trim');
            if (!$data['username'] || !$data['password'] || !$data['full_name']) {
                $this->error('用户名，姓名，密码为必填选项！');
            }
            $count = M('admin')->where(array('username' => $data['username']))->count('id');
            if ($count > 0) {
                $this->error('管理员账号已存在！');
            }
            $data['password'] = md5($data['password']);
            $res              = M('admin')->add($data);
            if ($res) {
                $this->success('添加成功', U('Setting/adminList'));
            } else {
                $this->error('添加失败，请联系管理员！');
            }
        } else {
            $department = $this->_getDepartment();
            $role_list  = M('admin_role')->getField('id,name');
            $this->assign('department_list', $department);
            $this->assign('role_list', $role_list);
            $this->display();
        }

    }

    /**
     * 修改管理员信息
     */
    public function adminEdit() {
        if (IS_POST) {
            $data['id']             = I('post.id', 0, 'int');
            $data['username']       = I('post.username', '', 'trim');
            $data['full_name']      = I('post.full_name', '', 'trim');
            $data['role_id']        = I('post.role_id', 0, 'int');
            $password               = I('post.password', '', 'trim');
            $data['department_id']  = I('post.department_id', 0, 'int');
            $data['position_level'] = I('post.position_level', '', 'trim');
            $data['email']          = I('post.email', '', 'trim');
            $data['status']         = I('post.status', '', 'int');
            $data['open_id']        = I('post.open_id', '', 'trim');
            if (!$data['id']) {
                $this->error('请求参数不合法！');
            }
            if (!$data['username'] || !$data['full_name']) {
                $this->error('用户名，姓名为必填选项！');
            }
            $count = M('admin')->where(array('username' => $data['username'], 'id' => array('neq', $data['id'])))->count('id');
            if ($count > 0) {
                $this->error('管理员账号已存在！');
            }
            if ('' != $password) {
                $data['password'] = md5($password);
            }
            $admin_info = M('admin')->find($data['id']);
            $model      = M();
            $model->startTrans();
            try {
                if ($admin_info['department_id'] != $data['department_id']) {
                    $department = array(3 => 'xiao_liu', 4 => 'sheng_tian', 5 => 'ye_lang');
                    M('merchant')->where(array('admin_id' => $data['id']))->save(array('department_id' => $data['department_id']));
                    M('deal')->where(array('admin_id' => $data['id']))->save(array('department_id' => $data['department_id'], 'type' => $department[$data['department_id']]));
                    M('deal_settle')->where(array('admin_id' => $data['id']))->save(array('department_id' => $data['department_id']));
                }
                M('admin')->save($data);
                $model->commit();
                $this->success('修改成功', U('Setting/adminList'));
            } catch (\Exception $e) {
                $model->rollback();
                $this->error('修改失败，请联系管理员！');
            }
        } else {
            $id         = I('get.id', 0, 'int');
            $admin_info = M('admin')->find($id);
            if (!$id || !$admin_info) {
                $this->error('管理员信息不存在！');
            }
            $role_list  = M('admin_role')->getField('id,name');
            $department = $this->_getDepartment();
            $this->assign('department_list', $department);
            $this->assign('role_list', $role_list);
            $this->assign('admin', $admin_info);
            $this->display();
        }
    }

    /**
     * 删除管理员
     */
    public function adminDel() {
        $this->error('禁用该功能，建议使用禁用账号的功能！', '/AppAdmin/Admin/index');
        $id    = I('post.id', 0, 'int');
        $count = M('merchant')->where(array('admin_id' => $id))->count('id');
        if ($count > 0) {
            $this->error('用户删除失败,该员工账号下还有商家没有下线或移交。');
        }
        $admin = M('admin')->delete($id);
        if ($admin) {
            $this->success('用户删除成功。');
        } else {
            $this->error('用户删除失败。');
        }
    }

    /**
     * 添加菜单
     */
    public function navAdd() {
        if (IS_POST) {
            $data['name']           = I('post.name', '', 'trim');
            $data['pid']            = I('post.pid', 0, 'int');
            $data['module_name']    = I('post.module_name', '', 'trim');
            $data['action_name']    = I('post.action_name', '', 'trim');
            $data['data']           = I('post.data', '', 'trim');
            $data['remark']         = I('post.remark', '', 'trim');
            $data['display']        = I('post.display', 0, 'int');
            $data['ordid']          = I('post.ordid', 0, 'int');
            $data['log']            = I('post.log', 0, 'int');
            $data['ico']            = I('post.ico', '', 'trim');
            $data['taodianke_menu'] = 1;
            if (!$data['name']) {
                $this->error('栏目名为必填选项！');
            }
            $res = M('menu')->add($data);
            if ($res) {
                S('tdk_auth_menu', null);
                $this->success('添加成功。', U('Setting/navList'));
            } else {
                $this->error('添加失败，请联系管理员！');
            }
        } else {
            $pid = I('get.pid', 0, 'int');
            $this->assign('pid', $pid);
            $this->assign('class_list', $this->_getAuthMenu());
            $this->display();
        }
    }

    /**
     * 修改菜单
     */
    public function navEdit() {
        if (IS_POST) {
            $data['id']             = I('post.id', 0, 'int');
            $data['name']           = I('post.name', '', 'trim');
            $data['pid']            = I('post.pid', 0, 'int');
            $data['module_name']    = I('post.module_name', '', 'trim');
            $data['action_name']    = I('post.action_name', '', 'trim');
            $data['data']           = I('post.data', '', 'trim');
            $data['remark']         = I('post.remark', '', 'trim');
            $data['display']        = I('post.display', 0, 'int');
            $data['ordid']          = I('post.ordid', 0, 'int');
            $data['log']            = I('post.log', 0, 'int');
            $data['ico']            = I('post.ico', '', 'trim');
            $data['taodianke_menu'] = 1;
            if (!$data['name']) {
                $this->error('栏目名为必填选项！');
            }
            $res = M('menu')->save($data);
            if ($res !== false) {
                S('tdk_auth_menu', null);
                $this->success('修改成功。', U('Setting/navList'));
            } else {
                $this->error('修改失败，请联系管理员！');
            }
        } else {
            $id   = I('get.id', 0, 'int');
            $info = M('menu')->find($id);
            if (!$id || !$info) {
                $this->error('栏目信息不存在！');
            }
            $this->assign('menu', $info);
            $this->assign('class_list', $this->_getAuthMenu());
            $this->display();
        }
    }

    /**
     * 菜单列表
     */
    public function navList() {
        $nav_list = $this->_getAuthMenu();
        $this->assign('nav_list', $nav_list);
        $this->display();
    }

    /**
     * 删除菜单
     */
    public function navDel() {
        $id   = I('get.id', 0, 'int');
        $info = M('menu')->find($id);
        if (!$id || !$info) {
            $this->error('栏目信息不存在！');
        }
        $res = M('menu')->delete($id);
        if ($res) {
            S('tdk_auth_menu', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    public function logList() {
        $model      = M('admin_log');
        $where      = array();
        $user_name  = I('get.user_name', '', 'trim');
        $controller = I('get.controller', '', 'trim');
        $action     = I('get.action', '', 'trim');
        if ($user_name) {
            $where['user_name'] = $user_name;
        }
        if ($controller) {
            $where['controller'] = $controller;
        }
        if ($action) {
            $where['action'] = $action;
        }
        $count = $model->where($where)->count();
        $Page  = $this->pages($count, $this->limit);
        $data  = $model->where($where)->order('id DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('data', $data);
        $this->assign('page', $Page->show());
        $this->display();
    }

    /**
     * app首页 5 大功能模块
     */
    public function appModule() {
        $model = M('app_home_module');
        $data  = $model->order('sort asc')->select();
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 添加
     */
    public function addAppModule() {
        if (IS_POST) {
            $data['module_name'] = I('post.module_name', '', 'trim');
            $data['module_icon'] = I('post.module_icon', '', 'trim');
            $data['type']        = I('post.type', '', 'trim');
            $data['client']      = I('post.client', 'all', 'trim');
            $data['sort']        = I('post.sort', 255, 'int');
            $data['status']      = I('post.status', 1, 'int');
            if (!$data['module_name']) {
                $this->error('模块名称不能为空！');
            }
            if (!$data['module_icon']) {
                $this->error('模块图标必须上传！');
            }
            if (!$data['type']) {
                $this->error('请求接口不能为空！');
            }
            $res = M('app_home_module')->add($data);
            if ($res) {
                S('tdk_app_module', null);
                $this->success('添加成功。', U('Setting/appModule'));
            } else {
                $this->error('添加失败，请联系管理员！');
            }
        } else {
            $this->display();
        }
    }

    /**
     * 修改
     */
    public function updateAppModule() {
        if (IS_POST) {
            $data['id']          = I('post.id', 0, 'int');
            $data['module_name'] = I('post.module_name', '', 'trim');
            $data['module_icon'] = I('post.module_icon', '', 'trim');
            $data['type']        = I('post.type', '', 'trim');
            $data['client']      = I('post.client', 'all', 'trim');
            $data['sort']        = I('post.sort', 255, 'int');
            $data['status']      = I('post.status', 1, 'int');
            if (!$data['id']) {
                $this->error('请求参数不合法！');
            }
            if (!$data['module_name']) {
                $this->error('模块名称不能为空！');
            }
            if (!$data['module_icon']) {
                $this->error('模块图标必须上传！');
            }
            if (!$data['type']) {
                $this->error('请求接口不能为空！');
            }
            $res = M('app_home_module')->save($data);
            if ($res !== false) {
                S('tdk_app_module', null);
                $this->success('修改成功。', U('Setting/appModule'));
            } else {
                $this->error('修改失败，请联系管理员！');
            }
        } else {
            $id   = I('get.id', 0, 'int');
            $info = M('app_home_module')->find($id);
            if (!$id || !$info) {
                $this->error('模块信息不存在！');
            }
            $this->assign('info', $info);
            $this->display();
        }
    }

}