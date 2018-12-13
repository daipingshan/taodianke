<?php

namespace Admin\Controller;

/**
 * 商品管理
 */
class GoodsController extends CommonController {

    /**
     * @var bool 是否验证uid
     */
    public function index() {

        $where = array();

        $goods = M('goods');
        $count = $goods->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $goods->where($where)->limit($limit)->select();

        if ($list) {
            $cate_ids = array();
            foreach ($list as &$v) {
                $v['is_top_name'] = '正常';
                if (isset($v['is_top']) && $v['is_top'] == 1) {
                    $v['is_top_name'] = '店铺推荐';
                }
                $v['status_name'] = '正常';
                if (isset($v['status']) && $v['status'] == 1) {
                    $v['status_name'] = '特价';
                }
                if (isset($v['status']) && $v['status'] == 2) {
                    $v['status_name'] = '售罄';
                }
                if (isset($v['cate_fid']) && $v['cate_fid']) {
                    $cate_ids[$v['cate_fid']] = $v['cate_fid'];
                }
                if (isset($v['cid']) && $v['cid']) {
                    $cate_ids[$v['cid']] = $v['cid'];
                }
            }
            unset($v);
            $cate_info = array();
            if ($cate_ids) {
                $cate_info = M('goods_category')->where(array('id' => array('in', array_keys($cate_ids))))->getField('id,name', true);
            }
            foreach ($list as &$v) {
                $v['cate_name'] = ternary($cate_info[$v['cate_fid']], '') . '-' . ternary($cate_info[$v['cid']], '');
            }
            unset($v);
        }

        $data = array(
            'pages' => $page->show(),
            'list' => $list,
        );
        
        $this->assign($data);

        $this->display();
    }

    /**
     * 删除商品
     */
    public function delete() {
        $gid = I('get.gid', '', 'trim');
        if (!$gid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除商品的id不能为空！'));
        }

        $goods = M('goods');
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
     *  添加商品
     */
    public function add() {
        if (IS_POST) {
            $goods_data = I('post.goods', array(), '');
            $goods_data['create_time'] = time();
            $res = M('goods')->add($goods_data);
            if (!$res) {
                $this->redirect_message(U('Goods/add'), array('error' => '商品添加失败！'));
            }
            $this->redirect_message(U('Goods/index'), array('success' => '商品添加成功！'));
            die();
        }

        $merchent_list = M('merchant')->where(array('business' => 1))->field('id,username')->select();

        $f_cate_list = M('goods_category')->where(array('fid' => 0))->field('id,name')->select();

        $data = array(
            'merchent_list' => $merchent_list,
            'f_cate_list' => $f_cate_list,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 根据父id获取子分类
     */
    public function get_c_cate_by_fid() {
        $fid = I('post.fid', '', 'trim');
        if (!$fid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '父id为空！'));
        }
        $f_cate_list = M('goods_category')->where(array('fid' => $fid))->field('id,name')->select();
        $this->ajaxReturn(array('code' => 0, 'data' => $f_cate_list));
    }

    public function check_add_edit() {
        $goods_data = I('post.goods', array(), '');
        if (!isset($goods_data['title']) || !trim($goods_data['title'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '商品名称不能为空！'));
        }
        if (!isset($goods_data['pic']) || !trim($goods_data['pic'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '商品图片必须上传！'));
        }
        if (!isset($goods_data['guige']) || !trim($goods_data['guige'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '商品规格不能为空！'));
        }
        if (!isset($goods_data['cate_fid']) || !trim($goods_data['cate_fid'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '请选择商品分类！'));
        }
        if (!isset($goods_data['cid']) || !trim($goods_data['cid'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '请选择商品分类！'));
        }
        if (!isset($goods_data['mid']) || !trim($goods_data['mid'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '请选择商品所属商户！'));
        }
        if (!isset($goods_data['sell_price']) || trim($goods_data['sell_price']) == '') {
            $this->ajaxReturn(array('code' => -1, 'error' => '商品价格为空！'));
        }
        if (!isset($goods_data['origin_price']) || trim($goods_data['origin_price']) == '') {
            $this->ajaxReturn(array('code' => -1, 'error' => '商品原价格为空！'));
        }
        if (!isset($goods_data['purchase_price']) || trim($goods_data['purchase_price']) == '') {
            $this->ajaxReturn(array('code' => -1, 'error' => '商品进货价格为空！'));
        }
        if (!isset($goods_data['status']) || trim($goods_data['status']) == '') {
            $this->ajaxReturn(array('code' => -1, 'error' => '请选择商品的销售状态！'));
        }
        if (!isset($goods_data['is_top']) || trim($goods_data['is_top']) == '') {
            $this->ajaxReturn(array('code' => -1, 'error' => '请选择商品的状态！'));
        }
        if (!isset($goods_data['kucun']) || intval($goods_data['kucun']) <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '商品的库存必须大于0！'));
        }
        $this->ajaxReturn(array('code' => 0));
    }

    /**
     * 商品图片上传
     */
    public function uploadImg() {
        $type = I('get.type', 'team', 'strval');
        $data = $this->upload('img', $type, '', array(), true);
        if ($data) {
            $res = array(
                "state" => "SUCCESS",
                "url" => $data[0]['newpath'] . '/' . trim($data[0]['savename'], '/'),
                "title" => $data[0]['savename'],
                "error" => 0,
            );
        } else {
            $error = '上传失败';
            if (isset($this->error) && trim($this->error)) {
                $error = $this->error;
            }
            $res = array(
                "state" => $error,
                "error" => 1,
                "message" => $error,
            );
        }
        ob_clean();
        $this->ajaxReturn($res);
    }

    /**
     *  编辑商品
     */
    public function edit() {
        if (IS_POST) {
            $gid = I('post.gid', '', 'trim');
            $page = I('post.page', '', 'trim');

            $goods_data = I('post.goods', array(), '');
            if (!$gid) {
                $this->redirect_message(U('Goods/index'), array('error' => '编辑的商品id不能为空！'));
            }

            $res = M('goods')->where(array('id' => $gid))->save($goods_data);
             if ($res===false) {
                $this->redirect_message(U('Goods/edit', array('gid' => $gid)), array('error' => '商品编辑失败！'));
            }
            $this->redirect_message(U('Goods/index', array('p' => $page)), array('success' => '商品编辑成功！'));
            die();
        }

        $gid = I('get.gid', '', 'trim');
        if (!$gid) {
            $this->redirect_message(U('Goods/index'), array('error' => '编辑的商品id不能为空！'));
        }

        $goods_info = M('goods')->where(array('id' => $gid))->find();
        if (!$goods_info) {
            $this->redirect_message(U('Goods/index'), array('error' => '编辑的商品不存在！'));
        }

        $goods_info['image'] = '';
        if (isset($goods_info['pic']) && trim($goods_info['pic'])) {
            $goods_info['image'] = getImagePath($goods_info['pic'],'oss');
        }

        $merchent_list = M('merchant')->where(array('business' => 1))->field('id,username')->select();

        $f_cate_list = M('goods_category')->where(array('fid' => 0))->field('id,name')->select();

        $data = array(
            'merchent_list' => $merchent_list,
            'f_cate_list' => $f_cate_list,
            'data' => $goods_info,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 商品库管理
     */
    public function goods_set_list() {
        
        $search = array(
            'query'=>urldecode(I('get.query','','trim')),
            'cate_fid'=>I('get.cate_fid','','trim'),
            'cid'=>I('get.cid','','trim'),
        );
        
        $where = array();
        
        if(trim($search['query'])){
            $where['title'] = array('like',"%{$search['query']}%");
        }
        if(trim($search['cate_fid'])){
            $where['cate_fid'] = $search['cate_fid'];
        }
        if(trim($search['cid'])){
            $where['cid'] = $search['cid'];
        }

        $goods_set = M('goods_set');
        $count = $goods_set->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $goods_set->where($where)->limit($limit)->order('id desc ')->select();

        if ($list) {
            $cate_ids = array();
            foreach ($list as &$v) {
                if (isset($v['cate_fid']) && $v['cate_fid']) {
                    $cate_ids[$v['cate_fid']] = $v['cate_fid'];
                }
                if (isset($v['cid']) && $v['cid']) {
                    $cate_ids[$v['cid']] = $v['cid'];
                }
            }
            unset($v);
            $cate_info = array();
            if ($cate_ids) {
                $cate_info = M('goods_category')->where(array('id' => array('in', array_keys($cate_ids))))->getField('id,name', true);
            }
            foreach ($list as &$v) {
                $v['cate_name'] = ternary($cate_info[$v['cate_fid']], '') . '-' . ternary($cate_info[$v['cid']], '');

                $v['pic'] = getImagePath($v['pic'],'oss');
            }
            unset($v);
        }
        
        $f_cate_list = M('goods_category')->where(array('fid' => 0))->field('id,name')->select();

        $data = array(
            'pages' => $page->show(),
            'list' => $list,
            'f_cate_list' => $f_cate_list,
            'search' => $search,
        );
        $this->assign($data);

        $this->display();
    }

    /**
     *  添加商品库
     */
    public function goods_set_add() {
        if (IS_POST) {
            $goods_set_data = I('post.goods_set', array(), '');
            $goods_set_data['create_time'] = time();
            $res = M('goods_set')->add($goods_set_data);
            if (!$res) {
                $this->ajaxReturn(array('code' => -1, 'error' => '商品库添加失败！'));
            }
            $this->ajaxReturn(array('code' => 0, 'success' => '商品库添加成功！'));
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
     * 商品库信息删除
     */
    public function goods_set_delete() {
        $gid = I('get.gid', '', 'trim');
        if (!$gid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除商品库的id不能为空！'));
        }

        $goods_set = M('goods_set');
        $where = array('id' => $gid);
        $goods_count = $goods_set->where($where)->count();
        if (!$goods_count || $goods_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '你删除的商品库不存在！'));
        }

        $res = $goods_set->where($where)->delete();
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除商品库失败！'));
        }

        $this->ajaxReturn(array('code' => 0, 'success' => '删除商品库成功！'));
    }
    
    /**
     * 检测商品库数据合法
     */
    public function goods_set_check_add_edit(){
        $goods_set_data = I('post.goods_set', array(), '');
        if (!isset($goods_set_data['title']) || !trim($goods_set_data['title'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '图片名称不能为空！'));
        }
        if (!isset($goods_set_data['pic']) || !trim($goods_set_data['pic'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '商品库图片必须上传！'));
        }
        if (!isset($goods_set_data['guige']) || !trim($goods_set_data['guige'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '图片规格不能为空！'));
        }
        if (!isset($goods_set_data['cate_fid']) || !trim($goods_set_data['cate_fid'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '请选择图片分类！'));
        }
        if (!isset($goods_set_data['cid']) || !trim($goods_set_data['cid'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '请选择图片分类！'));
        }
        if (!isset($goods_set_data['origin_price']) || trim($goods_set_data['origin_price']) == '') {
            $this->ajaxReturn(array('code' => -1, 'error' => '价格不能为空！'));
        }
        
        $this->ajaxReturn(array('code' => 0));
    }
    
     /**
     *  编辑商品
     */
    public function goods_set_edit() {
        if (IS_POST) {
            $gid = I('post.gid', '', 'trim');
            $goods_set_data = I('post.goods_set', array(), '');
            if (!$gid) {
                $this->ajaxReturn(array('code' => -1, 'error' => '编辑的商品库id不能为空！'));
            }

            $res = M('goods_set')->where(array('id' => $gid))->save($goods_set_data);
            if ($res===false) {
                $this->ajaxReturn(array('code' => -1, 'error' => '商品库编辑失败！'));
            }
            $this->ajaxReturn(array('code' => 0, 'success' => '商品库编辑成功！'));
            die();
        }

        $gid = I('get.gid', '', 'trim');
        if (!$gid) {
            $this->redirect_message(U('Goods/goods_set_list'), array('error' => '编辑的商品库id不能为空！'));
        }

        $goods_info = M('goods_set')->where(array('id' => $gid))->find();
        if (!$goods_info) {
            $this->redirect_message(U('Goods/goods_set_list'), array('error' => '编辑的商品库不存在！'));
        }

        $goods_info['image'] = '';
        if (isset($goods_info['pic']) && trim($goods_info['pic'])) {
            $goods_info['image'] = getImagePath($goods_info['pic'],'oss');
        }

        $f_cate_list = M('goods_category')->where(array('fid' => 0))->field('id,name')->select();

        $data = array(
            'f_cate_list' => $f_cate_list,
            'data' => $goods_info,
        );
        $this->assign($data);
        $this->display();
    }

}
