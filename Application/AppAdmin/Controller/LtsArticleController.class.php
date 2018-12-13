<?php

namespace AppAdmin\Controller;

use Admin\Controller\PublicController;

class LtsArticleController extends CommonController {

    public function __construct() {
        parent:: __construct();
        $this->article = M('article');
        $this->cate    = M('article_cate');
        $this->msg     = M('msg');
    }

    /**
     * 文章管理首页
     *
     * @param $cate
     * @param $title
     * @return array
     */
    public function index() {
        $act_cate = I('get.cate', null, 'int');
        $title    = I('get.title', null, 'string');
        $map      = array();
        if (IS_GET && $act_cate != null) {
            $map['cate_id'] = $act_cate;
        }
        if (IS_GET && $title != null) {
            $map['title'] = array('like', "%$title%");
        }
        $cate = $this->cate->select();
        // 分页
        $count = $this->article->where($map)->field('id')->count('id');
        $page  = $this->pages($count, 10);

        $list = $this->article->where($map)->order('add_time DESC')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($list as &$val) {
            $val['url'] = C('api_domain_url') . '/Share/article/id/' . $val['id'];
        }
        $this->assign('list', $list);// 赋值数据集
        $this->count = $count;
        $this->assign('page', $page->show());
        $this->assign('cate', $cate);
        $this->display();
    }

    public function get_cate($cate = 0) {
        $where['pid'] = $cate;
        $rel          = $this->cate->where($where)->order('ordid')->select();
        foreach ($rel as $k => $v) {
            $catelist[$k] = $v;
            if ($v['pid'] != '') {
                $catelist[$k]['child_cate'] = $this->get_cate($v['id']);
            }
        }
        return $catelist;
    }

    //递归输出 栏目
    public function display_cate($cate, $line = false) {
        foreach ($cate as $v) {
            echo '<option value="' . $v['id'] . '">';
            if ($line == true) {
                echo '-';
            }
            echo $v['name'] . '</option>';
            if ($v['child_cate'] != null) {
                $this->display_cate($v['child_cate'], true);
            }
        }
    }

    /**
     * 文章编辑
     */
    public function edit() {
        $cate       = $this->get_cate();
        $this->cate = $cate;
        $map['id']  = I('get.id', 0, 'int');
        $art        = $this->article->where($map)->find();

        $art['info'] = html_entity_decode($art['info']);

        if (IS_POST) {
            $data['title']     = I('post.title', null, 'string');
            $data['cate_id']   = I('post.cate', null, 'int');
            $data['tags']      = I('post.tags', null, 'string');
            $data['author']    = I('post.author', null, 'string');
            $data['hits']      = I('post.hits', null, 'int');
            $data['ordid']     = I('post.ordid', null, 'int');
            $data['status']    = I('post.status', null, 'int');
            $data['info']      = I('post.info', null, 'htmlspecialchars');
            $data['seo_desc']  = I('post.seo_desc', null, 'htmlspecialchars');
            $data['last_time'] = time();
            if ($this->article->where($map)->save($data)) {
                if ($data['cate_id'] == 1) {
                    S('tdk_notice', null);
                }

                $this->success('添加成功', U('LtsArticle/index'));
                exit();
            } else {
                $this->error('修改失败');
            }
        }
        $this->art = $art;
        $this->display();
    }

    /**
     * 文章删除
     */
    public function del() {
        $id    = I('post.id', '', 'trim');
        $where = array('id' => $id);
        $res   = $this->article->where($where)->delete();
        if ($res) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '该广告删除成功'));
        } else {
            $this->ajaxReturn(array('code' => -1, 'msg' => '该广告删除失败'));
        }
    }

    /**
     * 文章添加
     */
    public function add() {
        $cate = $this->get_cate();
        //$this->display_cate($cate);
        $this->cate = $cate;
        if (IS_POST) {
            $data['title']    = I('post.title', null, 'string');
            $data['cate_id']  = I('post.cate', null, 'int');
            $data['tags']     = I('post.tags', null, 'string');
            $data['author']   = I('post.author', null, 'string');
            $data['hits']     = I('post.hits', null, 'int');
            $data['ordid']    = I('post.ordid', null, 'int');
            $data['status']   = I('post.status', null, 'int');
            $data['info']     = I('post.info', null, 'htmlspecialchars');
            $data['seo_desc'] = I('post.seo_desc', null, 'htmlspecialchars');
            $data['add_time'] = time();
            if ($data['title'] != null && $data['cate_id'] != null) {
                if ($this->article->add($data)) {
                    if ($data['cate_id'] == 1) {
                        S('tdk_notice', null);
                    }

                    $this->success('添加成功', U('LtsArticle/index'));
                    exit();
                }
            }
        }
        $this->display();
    }

    //投诉与建议
    public function msg() {
        $msg_list = M('msg')->alias('m')->join('left join ytt_user u ON m.fuid=u.id')->order('m.status asc,m.add_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->field('m.*,u.mobile')->select();

        $count = $this->msg->count();
        $Page  = new \Think\Page($count, 50);
        $show  = $Page->show();

        $this->count = $count;
        $this->assign('page', $show);
        $this->assign('list', $msg_list);
        $this->display();
    }

    /**
     * 查看投诉与建议
     */
    public function doMsgView() {
        $id = I('get.id', null, 'int');
        if (IS_GET && $id != null) {
            $msg_res = M('msg')->where(array('id' => $id))->save(array('status' => 1));
            if ($msg_res == 1) {
                $this->success('已经查看', U('LtsArticle/msg'));
            }
        } else {
            $this->error('未正确获取ID', U('LtsArticle/msg'));
        }
    }


    /**
     * 比优助手文章管理首页
     *
     * @param $cate
     * @param $title
     * @return array
     */
    public function biyouArticle() {
        $act_cate = I('get.cate', '', 'int');
        $title    = I('get.title', '', 'string');

        $map = array();
        if (!empty($act_cate)) {
            $map['cate_id'] = $act_cate;
        }
        if (!empty($title)) {
            $map['title'] = array('like', "%$title%");
        }

        // 分页
        $count = M('bu_article')->where($map)->count('id');
        $page  = $this->pages($count, 10);

        $list = M('bu_article')->where($map)->order('add_time DESC')->limit($page->firstRow . ',' . $page->listRows)->select();

        foreach ($list as &$val) {
            $val['url'] = C('bu_api_domain_url') . '/Share/article/id/' . $val['id'];
        }

        $cate = M('bu_article_cate')->index('id')->select();

        $this->assign('list', $list);
        $this->assign('page', $page->show());
        $this->assign('cate', $cate);
        $this->assign('count', $count);
        $this->assign('url', $url);
        $this->display();
    }

    /**
     * 比优助手文章添加
     */
    public function biyouAdd() {

        if (IS_POST) {
            $data['title']    = I('post.title', '', 'string');
            $data['cate_id']  = I('post.cate_id', '', 'int');
            $data['author']   = I('post.author', '', 'string');
            $data['sort']     = I('post.sort', '', 'int');
            $data['status']   = I('post.status', '', 'int');
            $data['info']     = I('post.info', '', '');
            $data['add_time'] = time();
            $bu_res           = M('bu_article')->add($data);
            if ($bu_res) {
                if ($data['cate_id'] == 1) {
                    S('bu_notice', null);
                }
                $this->success('添加成功', U('LtsArticle/biyouArticle'));
                exit;
            }
        }
        //  获取文章分类
        $cate = M('bu_article_cate')->select();
        $this->assign('cate', $cate);
        $this->display();
    }

    /**
     * 比优助手文章编辑
     */
    public function biyouEdit() {
        $id = I('get.id', 0, 'int');

        if (IS_POST) {
            $data['title']   = I('post.title', null, 'string');
            $data['cate_id'] = I('post.cate', null, 'int');
            $data['author']  = I('post.author', null, 'string');
            $data['sort']    = I('post.sort', null, 'int');
            $data['status']  = I('post.status', null, 'int');
            $data['info']    = I('post.info', null, '');

            $bu_res = M('bu_article')->where(array('id' => $id))->save($data);
            if ($bu_res) {
                if ($data['cate_id'] == 1) {
                    S('bu_notice', null);
                }
                $this->success('添加成功', U('LtsArticle/biyouArticle'));
                exit();
            } else {
                $this->error('修改失败');
            }
        }

        $art         = M('bu_article')->where(array('id' => $id))->find();
        $art['info'] = html_entity_decode($art['info']);
        $cate        = M('bu_article_cate')->select();

        $this->assign('cate', $cate);
        $this->assign('art', $art);
        $this->display();
    }

    /**
     * 比优助手文章删除
     */
    public function biyouDel() {
        $id  = I('post.id', '', 'trim');
        $res = M('bu_article')->where(array('id' => $id))->delete();
        if ($res) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '该广告删除成功'));
        } else {
            $this->ajaxReturn(array('code' => -1, 'msg' => '该广告删除失败'));
        }
    }

    /**
     * 比优助手投诉建议查看
     */
    public function biyouMsg() {
        $msg_list = M('bu_msg')->alias('m')->join('left join ytt_bu_user u ON m.user_id=u.id')->order('m.status asc,m.add_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->field('m.*,u.mobile')->select();

        $count       = M('bu_msg') > count();
        $Page        = new \Think\Page($count, 50);
        $show        = $Page->show();
        $this->count = $count;
        $this->assign('page', $show);
        $this->assign('list', $msg_list);
        $this->display();
    }

    /**
     * 查看比优助手投诉建议
     */
    public function doBiyouMsg() {
        $id = I('get.id', null, 'int');
        if (IS_GET && $id != null) {
            $bu_msg = M('bu_msg')->where(array('id' => $id))->save(array('status' => 1));
            if ($bu_msg == 1) {
                $this->success('已经查看', U('LtsArticle/biyouMsg'));
            }
        } else {
            $this->error('未正确获取ID', U('LtsArticle/biyouMsg'));
        }
    }

}