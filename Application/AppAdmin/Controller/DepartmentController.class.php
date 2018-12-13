<?php

namespace AppAdmin\Controller;

class DepartmentController extends CommonController {

    /*
     *  部门管理首页
     * */
    public function index() {
        $department = M('department');
        $list       = $department->select();
        $data       = array();
        foreach ($list as $k => $v) {
            $data[$k]['id']   = $v['id'];
            $data[$k]['name'] = $v['department_name'];
            $department_name  = $department->where(array('id' => $v['parent_id']))->getField('department_name');
            if ($v['parent_id'] == '0' || !$department_name) {
                $data[$k]['superior_department_name'] = '暂无上级';
            } else {
                $data[$k]['superior_department_name'] = $department_name;
            }
        }
        $this->assign('list', $data);
        $this->display();
    }

    /*
     *  添加部门的基本信息
     *
     * */
    public function add() {

        $department      = M('department');
        $department_info = $department->where(array('parent_id' => '0'))->select();
        if (IS_POST) {
            $param = array(
                'parent_id'            => I('post.id', '', 'int'),
                'department_name'      => I('post.department_name', '', 'trim'),
                'dataoke_account_info' => I('post.dataoke_account_info', '', 'trim'),
                'cookie'               => I('post.cookie', '', 'trim'),
                'remark'               => I('post.remark', '', 'trim'),
            );
            if (!$param['department_name'] || !$param['cookie'] || !$param['dataoke_account_info']) {
                $this->error('部门名称或cookie未填写，请重新添加部门', U('Department/index'));
                exit();
            }
            $res = $department->add($param);
            if ($res) {
                S('tdk_department', null);
                S('tdk_department_cookie', null);
                $this->success('添加成功', U('Department/index'));
                exit();
            } else {
                $this->error('添加失败', U('Department/index'));
                exit();
            }
        }
        $this->assign('list', $department_info);
        $this->display();
    }

    /*
     *  编辑部门的基本信息
     *
     * */
    public function edit() {

        $id         = I('get.id', '', 'trim');
        $department = M('department');
        $list       = $department->where(array('id' => $id))->find();

        $info = $department->where(array('parent_id' => '0'))->field('id, department_name')->select();

        if (IS_POST) {
            $tmp = array(
                'id'                   => I('post.id', '', 'int'),
                'parent_id'            => I('post.parent_id', '', 'int'),
                'dataoke_account_info' => I('post.dataoke_account_info', '', 'trim'),
                'department_name'      => I('post.department_name', '', 'trim'),
                'cookie'               => I('post.cookie', '', 'trim'),
                'remark'               => I('post.remark', '', 'trim'),
            );
            if ($tmp['parent_id'] == $tmp['id']) {
                $this->error('编辑失败,上级部门不能是当前部门', U('Department/index'));
                exit();
            } elseif (!$tmp['department_name'] || !$tmp['cookie'] || !$tmp['dataoke_account_info']) {
                $this->error('部门名称或cookie未填写，请重新修改部门', U('Department/index'));
                exit();
            }

            $res = $department->where(array('id' => $tmp['id']))->save($tmp);
            if ($res !== false) {
                S('tdk_department', null);
                S('tdk_department_cookie', null);
                $this->success('编辑成功', U('Department/index'));
                exit();
            } else {
                $this->error('编辑失败', U('Department/index'));
                exit();
            }
        }

        $this->assign('list', $list);
        $this->assign('info', $info);
        $this->display();
    }

    /*
     *  删除部门的基本信息
     *
     * */
    public function del() {

        $id                      = I('post.id', '', '');
        $count_department_number = M('admin')->where(array('department_id' => $id))->count('id');
        if ($count_department_number > 0) {
            $this->error('删除失败,该部门中还有成员未清退！');
        }
        $res = M('department')->where(array('id' => $id))->delete();
        if ($res) {
            S('tdk_department', null);
            S('tdk_department_cookie', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

}