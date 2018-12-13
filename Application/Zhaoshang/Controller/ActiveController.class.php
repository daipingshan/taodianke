<?php
/**
 * Created by PhpStorm.
 * User: superegoliu
 * Date: 2016/2/29
 * Time: 16:21
 */
namespace Admin\Controller;
/**
 * 商品管理
 */
class ActiveController extends CommonController{

    /**
     * 获取活动列表
     */
    public function index() {
        $search = array(
            'query' => urldecode(I('get.query', '', 'trim')),
        );

        $where = array(
         'type'=>1
        );

        if (trim($search['query'])) {
            $where['title'] = array('like', "%{$search['query']}%");
        }

        $notice = M('activity');
        $count = $notice->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $notice->where($where)->limit($limit)->order('id desc')->select();
        foreach($list as &$v){
            $v['pic'] = getImagePath($v['pic'],'oss');
        }
        $data = array(
            'pages' => $page->show(),
            'list' => $list,
            'search' => $search,
        );
        $this->assign($list);
        $this->assign($data);
        $this->display();
    }
    public function add()
    {
        if($_POST){
            $area_data = I('post.active', array(), '');
            $area_data['time'] = time();
            $area_data['type']=1;
            $res = M('activity')->add($area_data);
            if (!$res) {
                $this->ajaxReturn(array('code' => 0, 'success' => '广告添加失败！'));
            }
            $this->ajaxReturn(array('code' => 0, 'success' => '广告添加成功！'));
            die();
        }else{

            $this->display();
        }
    }

    /**
     * 编辑商品
     */
    public function edit()
    {
        $id = I('get.gid', '', 'trim');
        if($_POST){
            $where['id']=$_POST['id'];
            $area_data = I('post.active', array(), '');
            $res = M('activity')->where($where)->save($area_data);
            if (!$res) {
                $this->redirect_message(U('Active/add'), array('error' => '广告修改失败！'));
            }
            $this->redirect_message(U('Active/index'), array('success' => '广告修改成功！'));
            die();
        }else{
            $data=M('activity')->where(array('id'=>$id))->select();
            $this->assign($data);
            $this->display();
        }
    }
    /**
     * 删除商品
     */
    public function delete() {
        $gid = I('get.gid', '', 'trim');
        if (!$gid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除商品的id不能为空！'));
        }
        $goods = M('activity');
        $where = array('id' => $gid);
        $goods_count = $goods->where($where)->count();
        if (!$goods_count || $goods_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '你删除的商品不存在！'));
        }

        $res = $goods->where($where)->delete();
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除商品失败！'));
        }

        $this->ajaxReturn(array('code' => 0, 'success' => '删除商品成功！'));
    }

    /**
     * 添加 更新  检测数据
     */
    public function check_add_edit_data() {
        $area_data = I('post.active', array(), '');
        $aid = I('post.id', '', 'trim');
        if (!isset($area_data['name']) || !trim($area_data['name'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '标题名称不能为空！'));
        }
        if (!isset($area_data['pic']) || trim($area_data['pic']) == '') {
            $this->ajaxReturn(array('code' => -1, 'error' => '图片不能为空！'));
        }

        $this->ajaxReturn(array('code' => 0));
    }
}