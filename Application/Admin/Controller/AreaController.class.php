<?php

namespace Admin\Controller;

/**
 * 区域管理
 */
class AreaController extends CommonController {

    /**
     * @var bool 是否验证uid
     */
    public function index() {
        $fid = I('get.fid', '0', 'intval');
        $search = array(
            'query' => urldecode(I('get.query', '', 'trim')),
            'all_search' => I('get.all_search', '0', 'trim'),
        );

        $where = array(
            'fid' => $fid
        );

        if (trim($search['query'])) {
            $where['name'] = array('like', "%{$search['query']}%");
        }
        
        if(trim($search['all_search']) == '1'){
            unset($where['fid']);
        }

        $category = M('category');
        $count = $category->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $category->where($where)->limit($limit)->order('sort desc,id desc ')->select();
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
                $cate_f_info = $category->where(array('id' => array('in', array_keys($cate_fids))))->getField('id,name', true);
            }

            foreach ($list as &$v) {
                $v['f_name'] = '--';
                if (isset($v['fid']) && trim($v['fid'])) {
                    $v['f_name'] = ternary($cate_f_info[$v['fid']], '--');
                }
            }
            unset($v);
        }

        // 获取目录数据
        $catalog_data = array();
        if(trim($search['all_search']) != '1'){
             $catalog_data = $this->_get_catalog_data($fid);
        }
       
        $data = array(
            'pages' => $page->show(),
            'list' => $list,
            'search' => $search,
            'fid' => $fid,
            'catalog_data' => $catalog_data,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     *  获取目录数据
     * @param type $fid
     */
    private function _get_catalog_data($fid = 0) {
        if (!$fid) {
            return array(array('id' => 0, 'name' => '根区域'));
        }

        $category = M('category');
        $_fid = $fid;
        $n = 0;
        $data = array();
        while (true) {
            $res = $category->where(array('id' => $_fid))->field('id,name,fid')->find();
            if (isset($res['id']) && trim($res['id'])) {
                $data[] = array('id' => $res['id'], 'name' => $res['name']);
            }
            if (!isset($res['fid']) || !trim($res['fid'])) {
                $data[] = array('id' => 0, 'name' => '根区域');
                break;
            }
            $_fid = $res['fid'];
            $n++;
            if ($n > 8) {
                break;
            }
        }

        return array_reverse($data);
    }

    /**
     * 区域添加
     */
    public function add() {
        if (IS_POST) {
            $area_data = I('post.area', array(), '');

            $res = M('category')->add($area_data);
            if (!$res) {
                $this->ajaxReturn(array('code' => -1, 'error' => '区域添加失败！'));
            }
            $this->ajaxReturn(array('code' => 0, 'success' => '区域添加成功！'));
            die();
        }

        $fid = I('get.fid', 0, 'intval');
        $f_name = '根区域';
        if ($fid) {
            $f_category_res = M('category')->where(array('id' => $fid))->field('id,name')->find();
            if (isset($f_category_res['name']) && trim($f_category_res['name'])) {
                $f_name = $f_category_res['name'];
            } else {
                $fid = 0;
            }
        }

        $data = array(
            'fid' => $fid,
            'f_name' => $f_name,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 添加 更新  检测数据
     */
    public function check_add_edit_data() {
        $area_data = I('post.area', array(), '');
        $aid = I('post.aid', '', 'trim');
        if (!isset($area_data['name']) || !trim($area_data['name'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '区域名称不能为空！'));
        }
        $where = array(
            'name' => trim($area_data['name']),
        );
        if ($aid) {
            $where['id'] = array('neq', $aid);
        }
        $fid = 0;
        if (isset($area_data['fid']) && trim($area_data['fid'])) {
            $fid = $area_data['fid'];
        }
        $where['fid'] = $fid;

        $area_count = M('category')->where($where)->count();
        if ($area_count && $area_count > 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '该区域名称同目录下已被占用！'));
        }
        $this->ajaxReturn(array('code' => 0));
    }

    /**
     * 编辑 区域
     */
    public function edit() {
        if (IS_POST) {
            $area_data = I('post.area', array(), '');
            $aid = I('post.aid', '', 'trim');

            if (!$aid) {
                $this->ajaxReturn(array('code' => -1, 'error' => '编辑的区域id不能为空！'));
            }

            $res = M('category')->where(array('id' => $aid))->save($area_data);
            if ($res === false) {
                $this->ajaxReturn(array('code' => -1, 'error' => '区域编辑失败！'));
            }
            $this->ajaxReturn(array('code' => 0, 'success' => '区域编辑成功！'));
            die();
        }

        $aid = I('get.aid', 0, 'intval');
        
        $category = M('category');
        $area_info = array();
        if ($aid) {
            $area_info = $category->where(array('id' => $aid))->find();
            $area_info['f_name'] = '根区域';
            if (isset($area_info['fid']) && trim($area_info['fid'])) {
                $f_category_res = M('category')->where(array('id' => $area_info['fid']))->field('id,name')->find();
                if (isset($f_category_res['name']) && trim($f_category_res['name'])) {
                    $area_info['f_name'] = $f_category_res['name'];
                } else {
                    $area_info['fid'] = 0;
                }
            }
        }


        $data = array(
            'data' => $area_info,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 删除区域
     */
    public function delete() {
        $aid = I('get.aid', 0, 'intval');
        if (!$aid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除的区域id不能为空！'));
        }

        $category = M('category');

        // 判断该区域是否存在
        $where = array(
            'id' => $aid,
        );
        $area_count = $category->where($where)->count();
        if (!$area_count || $area_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除的区域不存在！'));
        }

        // 判断该区域下 是否存在子区域
        $where = array(
            'fid' => $aid,
        );
        $f_area_count = $category->where($where)->count();
        if ($f_area_count && $f_area_count > 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '该区域下存在子区域，无法直接删除，建议先删除该区域下的子区域！'));
        }

        // 判断该区域下是否有商户
        $where = array(
            '_string' => "province_id={$aid} or city_id={$aid} or area_id={$aid} or cid={$aid}",
        );
        $m_count = M('merchant')->where($where)->count();
        if ($m_count && $m_count > 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '该区域下存在商户，无法直接删除，建议先将该区域下的商户修改到其他区域下，再尝试删除！'));
        }

        // 执行删除
        $where = array(
            'id' => $aid,
        );
        $res = $category->where($where)->delete();
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '区域删除失败！'));
        }

        $this->ajaxReturn(array('code' => 0, 'success' => '删除区域成功！'));
    }

}
