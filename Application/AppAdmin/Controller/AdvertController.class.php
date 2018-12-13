<?php

namespace AppAdmin\Controller;

class AdvertController extends CommonController {

    /**
     * app轮播图广告位管理
     */
    public function index() {
        $ad_name   = I('get.ad_name', '', 'trim');
        $board_id  = I('get.board_id', '', 'trim');
        $is_online = I('get.is_online', 0, 'int');

        $where = array();
        if ($ad_name != '') {
            $where['name'] = array('LIKE', '%' . $ad_name . '%');
        }
        if ($board_id != '') {
            $where['board_id'] = $board_id;
        }
        if ($is_online == 1) {
            $now                 = time();
            $where['start_time'] = array('elt', $now);
            $where['end_time']   = array('egt', $now);
            $where['status']     = 1;
        }
        // 分页
        $count = M('ad')->where($where)->field('id')->count('id');
        $page  = $this->pages($count, 10);

        // 数据展示
        $list = M('ad')->where($where)
            ->field('id, board_id, name, content, start_time, end_time, add_time, ordid, status')
            ->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();

        $adboard = $ad_cate = $_data = array();
        foreach ($list as $k => $v) {
            $ad_cate[$v['board_id']] = $v['board_id'];
        }

        if ($ad_cate) {
            $adboard = M('adboard')->where(array('id' => array('in', array_keys($ad_cate))))->field('id, name')->index('id')->select();
        }

        // 所有广告分类
        $all_board = M('adboard')->field('id, name')->select();
        $data      = array();
        foreach ($all_board as $val) {
            $data[$val['id']] = $val['name'];
        }

        $this->assign('list', $list);
        $this->assign('adboard', $adboard);
        $this->assign('all_board', $data);
        $this->assign('ad_name', $ad_name);
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 添加广告信息
     */
    public function add() {
        if (IS_POST) {
            $tmp = array(
                'board_id'   => I('post.board_id', 15, 'intval'),
                'name'       => I('post.name', '', 'trim'),
                'content'    => I('post.content', '', 'trim'),
                'url'        => I('post.url', '', 'trim'),
                'start_time' => I('post.start_time', '', 'trim'),
                'end_time'   => I('post.end_time', '', 'trim'),
                'ordid'      => I('post.ordid', 255, 'intval'),
                'status'     => I('post.status', 0, 'intval'),
                'add_time'   => time(),
                'type'       => 'image',
            );

            if (empty($tmp['content']) || empty($tmp['name'])) {
                $this->error('图片或广告名称不能为空');
            }

            $start_tmp         = explode('/', $tmp['start_time']);
            $end_tmp           = explode('/', $tmp['end_time']);
            $tmp['start_time'] = mktime(0, 0, 0, $start_tmp[0], $start_tmp[1], $start_tmp[2]);
            $tmp['end_time']   = mktime(23, 59, 59, $end_tmp[0], $end_tmp[1], $end_tmp[2]);

            $res = M('ad')->add($tmp);
            if ($res) {
                S('tdk_ad_img', null);
                $this->success('添加成功', U('Advert/index'));
                exit;
            } else {
                $this->error('添加失败');
            }
        }
        // 所有广告分类
        $all_board = M('adboard')->field('id, name')->select();
        $data      = array();
        foreach ($all_board as $val) {
            $data[$val['id']] = $val['name'];
        }
        $this->assign('all_board', $data);
        $this->display();
    }

    /**
     * 编辑广告信息
     */
    public function edit() {
        $id = I('get.id', '', 'trim');
        if (IS_POST) {
            $tmp = array(
                'id'         => I('post.id', '', 'int'),
                'board_id'   => I('post.board_id', '', 'trim'),
                'name'       => I('post.name', '', 'trim'),
                'content'    => I('post.content', '', 'trim'),
                'url'        => I('post.url', '', 'trim'),
                'start_time' => I('post.start_time', '', 'trim'),
                'end_time'   => I('post.end_time', '', 'trim'),
                'ordid'      => I('post.ordid', '', 'trim'),
                'status'     => I('post.status', '', 'trim'),
            );

            $start_tmp         = explode('/', $tmp['start_time']);
            $tmp['start_time'] = mktime(0, 0, 0, $start_tmp[0], $start_tmp[1], $start_tmp[2]);
            $end_tmp           = explode('/', $tmp['end_time']);
            $tmp['end_time']   = mktime(23, 59, 59, $end_tmp[0], $end_tmp[1], $end_tmp[2]);
            $res               = M('ad')->where(array('id' => $tmp['id']))->save($tmp);
            if ($res !== false) {
                S('tdk_ad_img', null);
                $this->success('编辑成功', U('Advert/index'));
                exit;
            } else {
                $this->error('编辑失败');
            }
        }
        // 广告信息
        $list = M('ad')->where(array('id' => $id))->find();
        // 所有广告分类
        $all_board = M('adboard')->field('id, name')->select();
        $data      = array();
        foreach ($all_board as $val) {
            $data[$val['id']] = $val['name'];
        }
        $this->assign('all_board', $data);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 删除广告
     */
    public function del() {
        $id    = I('post.id', '', 'trim');
        $where = array('id' => $id);
        $res   = M('ad')->where($where)->delete();
        if ($res) {
            S('tdk_ad_img', null);
            $this->ajaxReturn(array('code' => 0, 'msg' => '该广告删除成功'));
        } else {
            $this->ajaxReturn(array('code' => -1, 'msg' => '该广告删除失败'));
        }
    }

    /**
     * app轮播图广告位管理
     */
    public function biyouAdvert() {
        $ad_name = I('get.ad_name', '', 'trim');
        $type_id = I('get.type_id', '', 'trim');

        $where = array();
        if ($ad_name != '') {
            $where['title'] = array('LIKE', '%' . $ad_name . '%');
        }
        if ($type_id != '') {
            $where['type_id'] = $type_id;
        }
        // 分页
        $count = M('bu_ad')->where($where)->field('id')->count('id');
        $page  = $this->pages($count, 10);

        // 数据展示
        $list = M('bu_ad')->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();

        $adboard = $ad_cate = $_data = array();
        foreach ($list as $k => $v) {
            $ad_cate[$v['type_id']] = $v['type_id'];
        }

        if ($ad_cate) {
            $adboard = M('bu_ad_type')->where(array('id' => array('in', array_keys($ad_cate))))->field('id, name')->index('id')->select();
        }

        // 所有广告分类
        $all_board = M('bu_ad_type')->field('id, name')->select();
        $data      = array();
        foreach ($all_board as $val) {
            $data[$val['id']] = $val['name'];
        }

        $this->assign('list', $list);
        $this->assign('adboard', $adboard);
        $this->assign('all_board', $data);
        $this->assign('ad_name', $ad_name);
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 添加广告信息
     */
    public function biyouAdd() {
        if (IS_POST) {
            $tmp = array(
                'type_id'    => I('post.type_id', 15, 'intval'),
                'img'        => I('post.content', '', 'trim'),
                'url'        => I('post.url', '', 'trim'),
                'title'      => I('post.title', '', 'trim'),
                'begin_time' => I('post.begin_time', '', 'trim'),
                'end_time'   => I('post.end_time', '', 'trim'),
                'sort'       => I('post.sort', 255, 'intval'),
                'status'     => I('post.status', 0, 'intval'),
            );

            if (empty($tmp['title']) || empty($tmp['img'])) {
                $this->error('图片或广告名称不能为空');
            }

            $start_tmp         = explode('/', $tmp['begin_time']);
            $end_tmp           = explode('/', $tmp['end_time']);
            $tmp['begin_time'] = mktime(0, 0, 0, $start_tmp[0], $start_tmp[1], $start_tmp[2]);
            $tmp['end_time']   = mktime(0, 0, 0, $end_tmp[0], $end_tmp[1], $end_tmp[2]);

            $res = M('bu_ad')->add($tmp);
            if ($res) {
                S('bu_ad_img', null);
                $this->success('添加成功', U('Advert/biyouAdvert'));
                exit;
            } else {
                $this->error('添加失败');
            }
        }
        // 所有广告分类
        $all_board = M('bu_ad_type')->field('id, name')->select();
        $data      = array();
        foreach ($all_board as $val) {
            $data[$val['id']] = $val['name'];
        }
        $this->assign('all_board', $data);
        $this->display();
    }

    /**
     * 编辑广告信息
     */
    public function biyouEdit() {
        $id = I('get.id', '', 'trim');
        if (IS_POST) {
            $tmp = array(
                'id'         => I('post.id', '', 'int'),
                'type_id'    => I('post.type_id', 15, 'intval'),
                'img'        => I('post.content', '', 'trim'),
                'url'        => I('post.url', '', 'trim'),
                'title'      => I('post.title', '', 'trim'),
                'begin_time' => I('post.begin_time', '', 'trim'),
                'end_time'   => I('post.end_time', '', 'trim'),
                'sort'       => I('post.sort', 255, 'intval'),
                'status'     => I('post.status', 0, 'intval'),
            );

            $start_tmp         = explode('/', $tmp['begin_time']);
            $tmp['begin_time'] = mktime(0, 0, 0, $start_tmp[0], $start_tmp[1], $start_tmp[2]);
            $end_tmp           = explode('/', $tmp['end_time']);
            $tmp['end_time']   = mktime(0, 0, 0, $end_tmp[0], $end_tmp[1], $end_tmp[2]);

            $res = M('bu_ad')->where(array('id' => $tmp['id']))->save($tmp);
            if ($res !== false) {
                S('bu_ad_img', null);
                $this->success('编辑成功', U('Advert/biyouAdvert'));
                exit;
            } else {
                $this->error('编辑失败');
            }
        }
        // 广告信息
        $list = M('bu_ad')->where(array('id' => $id))->find();
        // 所有广告分类
        $all_board = M('bu_ad_type')->field('id, name')->select();
        $data      = array();
        foreach ($all_board as $val) {
            $data[$val['id']] = $val['name'];
        }
        $this->assign('all_board', $data);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 删除广告
     */
    public function biyouDel() {
        $id    = I('post.id', '', 'trim');
        $where = array('id' => $id);
        $res   = M('bu_ad')->where($where)->delete();
        if ($res) {
            S('bu_ad_img', null);
            $this->ajaxReturn(array('code' => 0, 'msg' => '该广告删除成功'));
        } else {
            $this->ajaxReturn(array('code' => -1, 'msg' => '该广告删除失败'));
        }
    }


}