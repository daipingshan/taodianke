<?php

namespace AppAdmin\Controller;

class MerchantController extends CommonController {

    /**
     *   构造函数
     */
    public function __construct() {
        parent:: __construct();
        // 当前用户id
        $this->uid = session('auth_user');
        // 当前用户所在的部门id
        $this->department_id = session('department_id');
        // 当前用户的岗位级别
        $this->position_level = session('position_level');
    }

    /*
     *  商家管理首页
     * */
    public function index() {
        $merchant_name = I('get.merchant_name', '', 'trim');
        $mobile        = I('get.mobile', '', 'trim');
        $department_id = I('get.department_id', 0, 'intval');
        $admin_id      = I('get.admin_id', 0, 'intval');

        $where = array();
        $name  = '';
        if ($this->position_level == 'basic') {
            $where['admin_id'] = $this->uid;
        } else if ($this->position_level == 'middle') {
            $where['department_id'] = $this->department_id;
        }
        if (!$where['department_id'] && $department_id) {
            $where['department_id'] = $department_id;
        }
        if (!$where['admin_id'] && $admin_id) {
            $name              = M('admin')->where(array('id' => $admin_id))->getField('full_name');
            $where['admin_id'] = $admin_id;
        }

        if ($mobile != '') {
            $where['mobile|qq|wangwang|weixin'] = $mobile;;
        }
        if ($merchant_name != '') {
            $where['merchant_name'] = array('LIKE', '%' . $merchant_name . '%');
        }

        // 分页
        $count = M('merchant')->where($where)->field('id')->count('id');
        $page  = $this->pages($count, 10);
        // 数据展示
        $list = M('merchant')->where($where)
            ->field('id, merchant_name, contact, mobile, weixin, qq, wangwang, shop_name, shop_url, admin_id,department_id, remark')
            ->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();

        $user = $user_department = $user_ids = $department_admin = $_data = array();
        foreach ($list as $k => $v) {
            $user_ids[$v['admin_id']]             = $v['admin_id'];
            $user_department[$v['department_id']] = $v['department_id'];
        }

        if ($user_ids) {
            $user             = M('admin')->where(array('id' => array('in', array_keys($user_ids))))->field('id, full_name')->index('id')->select();
            $department_admin = M('department')->where(array('id' => array('in', array_keys($user_department))))->field('id, department_name')->index('id')->select();
        }

        $admin = array(
            'user'       => $user,
            'department' => $department_admin,
        );

        //  部门名称以及部门id
        $all_department = $this->_getDepartment();
        //  当前部门下所有的员工
        $all_user = M('admin')->where(array('department_id' => $this->department_id, 'status' => '1'))->field('id,full_name')->select();

        //  所有的部门，当前部门下的所有基层员工，当前用户的等级
        $department_admin = array(
            'department'     => $all_department,
            'user'           => $all_user,
            'position_level' => $this->position_level,
        );

        $this->assign('list', $list);
        $this->assign('name', $name);
        $this->assign('page', $page->show());
        $this->assign('department', $department_admin);
        $this->assign('admin', $admin);
        $this->display();
    }

    /*
     *  添加商家的基本信息
     *
     * */
    public function add() {
        if (IS_POST) {
            $tmp           = array(
                'merchant_name' => I('post.merchant_name', '', 'trim'),
                'merchant_type' => I('post.merchant_type', '', 'trim'),
                'shop_url'      => I('post.shop_url', '', 'trim'),
                'address'       => I('post.address', '', 'trim'),
                'contact'       => I('post.contact', '', 'trim'),
                'mobile'        => I('post.mobile', '', 'trim'),
                'shop_name'     => I('post.shop_name', '', 'trim'),
                'weixin'        => I('post.weixin', '', 'trim'),
                'qq'            => I('post.qq', '', 'trim'),
                'wangwang'      => I('post.wangwang', '', 'trim'),
                'remark'        => I('post.remark', '', 'trim'),
                'add_time'      => time(),
            );
            $admin_id      = I('post.admin_id', '', 'trim');
            $department_id = I('post.department_id', '', 'trim');
            if (!$admin_id) {
                $tmp['admin_id'] = session('auth_user');
            } else {
                $tmp['admin_id'] = $admin_id;
            }
            if (!$department_id) {
                $tmp['department_id'] = session('department_id');
            } else {
                $tmp['department_id'] = $department_id;
            }
            if (empty($tmp['merchant_name']) && empty($tmp['shop_name']) && empty($tmp['contact'])) {
                $this->error('商家资料不能为空，请重新添加！', U('Merchant/index'));
            }

            if ($tmp['merchant_type'] == '1' && !$tmp['shop_url']) {
                $this->error('线上商家，请填写商铺网址！', U('Merchant/index'));
            } else if ($tmp['merchant_type'] == '2' && !$tmp['address']) {
                $this->error('线下商家，请填写商铺地址！', U('Merchant/index'));
            }

            if (empty($tmp['mobile']) && empty($tmp['weixin']) && empty($tmp['qq']) && empty($tmp['wangwang'])) {
                $this->error('商家联系方式不能为空，请重新添加！', U('Merchant/index'));
            }

            $res = M('merchant')->add($tmp);
            if ($res) {
                $this->success('添加成功', U('Merchant/index'));
                exit;
            } else {
                $this->error('添加失败', U('Merchant/index'));
                exit;
            }
        }
        $department = $user = $data = array();
        if ($this->position_level == 'manager') {
            $department = $this->_getDepartment();
        } else if ($this->position_level == 'middle') {
            $user = M('admin')->where(array('department_id' => $this->department_id, 'status' => '1'))->field('id, full_name')->select();
        }

        $merchant_type = M('merchant')->order('id desc')->getField('merchant_type');

        $data = array(
            'position_level' => $this->position_level,
            'department'     => $department,
            'user'           => $user,
            'merchant_type'      => $merchant_type
        );
        $this->assign('data', $data);
        $this->display();
    }

    /*
     *  编辑商家的基本信息
     *
     * */
    public function edit() {
        $id = I('get.id', '', 'trim');

        $department = $user = $data = array();
        $list       = M('merchant')->where(array('id' => $id))->find();
        if (IS_POST) {
            $tmp = array(
                'id'            => I('post.id', '', 'int'),
                'merchant_name' => I('post.merchant_name', '', 'trim'),
                'merchant_type' => I('post.merchant_type', '', 'trim'),
                'shop_url'      => I('post.shop_url', '', 'trim'),
                'address'       => I('post.address', '', 'trim'),
                'contact'       => I('post.contact', '', 'trim'),
                'mobile'        => I('post.mobile', '', 'trim'),
                'shop_name'     => I('post.shop_name', '', 'trim'),
                'weixin'        => I('post.weixin', '', 'trim'),
                'qq'            => I('post.qq', '', 'trim'),
                'wangwang'      => I('post.wangwang', '', 'trim'),
                'remark'        => I('post.remark', '', 'trim'),
            );

            $admin_id      = I('post.admin_id', '', 'intval');
            $department_id = I('post.department_id', '', 'intval');

            if ($admin_id) {
                $tmp['admin_id'] = $admin_id;
            }
            if ($department_id) {
                $tmp['department_id'] = $department_id;
            }

            if (empty($tmp['merchant_name']) && empty($tmp['shop_name']) && empty($tmp['contact'])) {
                $this->error('商家资料不能为空，请重新添加！', U('Merchant/index'));
            }

            if ($tmp['merchant_type'] == '1' && !$tmp['shop_url']) {
                $this->error('线上商家，请填写商铺网址！', U('Merchant/index'));
            } else if ($tmp['merchant_type'] == '2' && !$tmp['address']) {
                $this->error('线下商家，请填写商铺地址！', U('Merchant/index'));
            }

            if (empty($tmp['mobile']) && empty($tmp['weixin']) && empty($tmp['qq']) && empty($tmp['wangwang'])) {
                $this->error('商家联系方式不能为空，请重新添加！', U('Merchant/index'));
                exit;
            }

            $res = M('merchant')->where(array('id' => $tmp['id']))->save($tmp);

            if ($res) {
                $this->success('编辑成功', U('Merchant/index'));
                exit;
            } else {
                $this->error('编辑失败', U('Merchant/index'));
                exit;
            }
        }
        if ($this->position_level == 'manager') {
            $department = $this->_getDepartment();

        } else if ($this->position_level == 'middle') {
            $user = M('admin')
                ->where(array('department_id' => $this->department_id, 'status' => '1', 'position_level' => 'basic'))->field('id, full_name')->select();
        }

        $data = array(
            'id'             => $id,
            'position_level' => $this->position_level,
            'department'     => $department,
            'user'           => $user,
        );
        $name = M('admin')->where(array('id' => $list['admin_id'], 'status' => '1'))->getField('full_name');
        $this->assign('data', $data);
        $this->assign('name', $name);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     *  获取部门内部的员工列表
     *
     */
    public function getDepartmenPersonnel() {
        $department_id = I('get.id', '', '');

        $personnel = M('admin')->field('id, full_name')->where(array('department_id' => $department_id, 'status' => '1'))->select();

        if ($personnel) {
            $this->ajaxReturn(array('code' => 0, 'data' => $personnel));
        } else {
            $this->ajaxReturn(array('code' => -1, 'data' => ''));
        }
    }

    /*
     *  删除商家的基本信息
     *
     * */
    public function del() {
        if ($this->position_level != 'basic') {
            $this->error('您没有删除商家的权限，请联系管理员！');
        }
        $id  = I('post.id', '', 'trim');
        $res = M('merchant')->where(array('id' => $id))->delete();
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     *  获取商家的详情
     *
     * @param id int
     * @return array
     */
    public function read() {
        $id = I('get.id', '', 'intval');

        $where                 = array(
            'id' => $id,
        );
        $merchant              = M('merchant')->where($where)->find();
        $merchant['user_name'] = M('admin')->where(array('id' => $merchant['admin_id']))->getField('full_name');

        $merchant['department_name'] = M('department')->where(array('id' => $merchant['department_id']))->getField('department_name');

        if ($merchant) {
            $this->assign(array('list' => $merchant));
            $html = $this->fetch();
            $this->ajaxReturn(array('html' => $html));
        } else {
            $this->ajaxReturn(array('html' => ''));

        }
    }

    /**
     * 交接用户
     */
    public function changMerchant() {
        $admin_id = I('get.admin_id', '', 'trim');
        $merchant_ids = I('get.merchant_ids', '', 'trim');
        //  移交商家
        $where = array('id' => array('in', $merchant_ids));
        $merchant_res = M('merchant')->where($where)->setField(array('admin_id' => $admin_id));
        //  移交商家进行中的商品
        $deal_where = array('status' => 'ing', 'merchant_id' => array('in', $merchant_ids));
        $deal_res = M('deal')->where($deal_where)->setField(array('admin_id' => $admin_id));

        if ($merchant_res && $deal_res) {
            $url = U('Merchant/index', array('admin_id' => $admin_id));
        } else {
            $url = U('Merchant/index');
        }
        redirect($url);
    }

}