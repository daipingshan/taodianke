<?php
namespace AppAdmin\Controller;

class AdminController extends CommonController {

    /*
     *  部门管理首页
     * */
    public function index(){
        $list =  M('admin')->select();
        $this->assign('list',$list);
        $this->display();
    }

    /*
     *  添加部门的基本信息
     *
     * */
    public function add() {
        $admin = M('admin');
        $admin_info = $admin->where(array('parent_id'=>'0'))->select();
        if (IS_POST){
            $map = array(
                'full_name'=>I('post.full_name', '','trim'),
                'sex'=>I('post.sex', '','trim'),
                'mobile'=>I('post.mobile', '','trim'),
                'weixin'=>I('post.weixin', '','trim'),
                'qq'=>I('post.qq', '','trim'),
                'entry_time'=>I('post.entry_time', '','trim'),
                'owner_department'=>I('post.owner_department', '','trim'),
                'position_owner'=>I('post.position_owner', '','trim'),
                'on_in'=>I('post.on_in', '','trim'),
                'remark'=>I('post.remark', '','trim'),
                'create_time'=>time(),
            );
            $res = $admin->add($map);
            if($res){
                $this->success('添加成功','/AppAdmin/Admin/index');
                exit();
            }else{
                $this->error('添加失败','/AppAdmin/Admin/index');
                exit();
            }
        }
        $this->assign('list',$admin_info);
        $this->display();
    }

    /*
     *  编辑部门的基本信息
     *
     * */
    public function edit() {
        $id = I('get.id', '','trim');
        $admin = M('admin');
        $list = $admin->where(array('id'=>$id))->find();


        if (IS_POST) {
            $map = array(
                'id'=>I('post.id', '','int'),
                'full_name'=>I('post.full_name', '','trim'),
                'sex'=>I('post.sex', '','trim'),
                'mobile'=>I('post.mobile', '','trim'),
                'weixin'=>I('post.weixin', '','trim'),
                'qq'=>I('post.qq', '','trim'),
                'entry_time'=>I('post.entry_time', '','trim'),
                'owner_department'=>I('post.owner_department', '','trim'),
                'position_owner'=>I('post.position_owner', '','trim'),
                'on_in'=>I('post.on_in', '','trim'),
                'remark'=>I('post.remark', '','trim'),
                'create_time'=>time(),
            );
            $res = $admin->where(array('id'=>$map['id']))->save($map);
            if($res){
                $this->success('编辑成功','/AppAdmin/Admin/index');
                exit();
            }else{
                $this->error('编辑失败','/AppAdmin/Admin/index');
                exit();
            }
        }

        $this->assign('id',$id);
        $this->assign('list',$list);
        $this->display();
    }

    /*
     *  删除部门的基本信息
     *
     * */
    public function del() {
        $id = I('get.id', '','trim');
        $res = M('admin')->where(array('id'=>$id))->delete();
        if ($res) {
            $this->success('删除成功', '/AppAdmin/Admin/index');
            exit();
        } else {
            $this->error('删除失败', '/AppAdmin/Admin/index');
            exit();
        }
    }

}