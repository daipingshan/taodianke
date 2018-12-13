<?php

namespace Admin\Controller;

/**
 * 类别管理
 */
class CategoryController extends CommonController {

    /**
     * @var bool 是否验证uid
     */
    public function index() {

        $search = array(
            'query' => urldecode(I('get.query', '', 'trim')),
        );

        $where = array();

        if (trim($search['query'])) {
            $where['name'] = array('like', "%{$search['query']}%");
        }

        $goods_category = M('goods_category');
        $count = $goods_category->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $goods_category->where($where)->limit($limit)->order('sort desc,id desc ')->select();
        if ($list) {
            $cate_fids = array();
            foreach ($list as &$v) {
                if (isset($v['fid']) && trim($v['fid'])) {
                    $cate_fids[$v['fid']] = $v['fid'];
                }
            }
            unset($v);
            $cate_f_info = array();
            if ($cate_fids) {
                $cate_f_info = $goods_category->where(array('id' => array('in', array_keys($cate_fids))))->getField('id,name', true);
            }

            foreach ($list as &$v) {
                $v['f_name'] = '--';
                if (isset($v['fid']) && trim($v['fid'])) {
                    $v['f_name'] = ternary($cate_f_info[$v['fid']], '--');
                }
            }
            unset($v);
        }
        $data = array(
            'pages' => $page->show(),
            'list' => $list,
            'search' => $search,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 分类添加
     */
    public function add() {
         if (IS_POST) {
            $goods_category_data = I('post.goods_category', array(), '');
   
            $res = M('goods_category')->add($goods_category_data);
            if (!$res) {
                $this->ajaxReturn(array('code' => -1, 'error' => '分类添加失败！'));
            }
            $this->ajaxReturn(array('code' => 0, 'success' => '分类添加成功！'));
            die();
        }

        $f_cate_list = M('goods_category')->where(array('fid' => 0))->field('id,name')->select();

        $data = array(
            'f_cate_list' => $f_cate_list,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 分类编辑
     */
    public function edit() {
        if (IS_POST) {
            $cid = I('post.cid', '', 'trim');
            $goods_category_data = I('post.goods_category', array(), '');
            if (!$cid) {
                $this->ajaxReturn(array('code' => -1, 'error' => '编辑的分类id不能为空！'));
            }

            $res = M('goods_category')->where(array('id' => $cid))->save($goods_category_data);
            if ($res===false) {
                $this->ajaxReturn(array('code' => -1, 'error' => '分类编辑失败！'));
            }
            $this->ajaxReturn(array('code' => 0, 'success' => '分类编辑成功！'));
            die();
        }

        $cid = I('get.cid', '', 'trim');
        if (!$cid) {
            $this->redirect_message(U('Category/index'), array('error' => '编辑的分类id不能为空！'));
        }

        $goods_info = M('goods_category')->where(array('id' => $cid))->find();
        if (!$goods_info) {
            $this->redirect_message(U('Category/index'), array('error' => '编辑的分类不存在！'));
        }

       
        $f_cate_list = M('goods_category')->where(array('fid' => 0))->field('id,name')->select();

        $data = array(
            'f_cate_list' => $f_cate_list,
            'data' => $goods_info,
        );
        $this->assign($data);
        $this->display();
    }
    
    /**
     * 添加编辑时 数据检查
     */
    public function check_add_edit_data(){
        $goods_category = I('post.goods_category', array(), '');
        $goods_category_id = I('post.cid', '', 'trim');
        if (!isset($goods_category['name']) || !trim($goods_category['name'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '类型名称不能为空！'));
        }
        if(!isset($goods_category['sort']) ){
            $this->ajaxReturn(array('code' => -1, 'error' => '排序必须为数字！'));
        }
        
        $where = array('name'=>trim($goods_category['name']));
        if($goods_category_id){
            $where['id'] = array('neq',$goods_category_id);
        }
        $goods_category_count = M('goods_category')->where($where)->count();
        if($goods_category_count && $goods_category_count>0){
            $this->ajaxReturn(array('code' => -1, 'error' => '该类型已经存在！'));
        }
        
        if($goods_category_id){
            $where = array('fid'=>$goods_category_id);
            $c_goods_category_count = M('goods_category')->where($where)->count();
            if($c_goods_category_count && $c_goods_category_count>0){
                if(isset($goods_category['fid']) && trim($goods_category['fid'])){
                    $this->ajaxReturn(array('code' => -1, 'error' => '该类型下有子分类，不能将其变为其他类型的子分类！'));
                }
            }
        }
        $this->ajaxReturn(array('code' => 0));
    }

    /**
     * 分类删除
     */
    public function delete() {
        $cid = I('get.cid', '', 'trim');
        if (!$cid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除分类的id不能为空！'));
        }

        $goods_category = M('goods_category');
        $where = array('id' => $cid);
        $goods_category_count = $goods_category->where($where)->count();
        if (!$goods_category_count || $goods_category_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '你删除的分类不存在！'));
        }
        
        $where = array('fid'=>$cid);
        $c_goods_category_count = $goods_category->where($where)->count();
        if ($c_goods_category_count && $c_goods_category_count > 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '你删除的分类存在子分类，无法直接删除，请先删除其子分类，再尝试删除！'));
        }
        $where = array('id' => $cid);
        $res = $goods_category->where($where)->delete();
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除分类失败！'));
        }

        $this->ajaxReturn(array('code' => 0, 'success' => '删除分类成功！'));
    }

}
