<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/3 0003
 * Time: 下午 3:29
 */

namespace AppAdmin\Controller;

/**
 * 系统设置
 * Class ConfigController
 *
 * @package AppAdmin\Controller
 */
class ConfigController extends CommonController {

    /**
     * 读取系统配置信息
     */
    public function index() {
        $content = M('config')->getFieldById(1, 'content');
        $content = unserialize($content);
        $this->assign('content', $content);
        $this->display();
    }

    /**
     * 编辑系统设置
     */
    public function edit() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $content = M('config')->getFieldById(1, 'content');
        $content = unserialize($content);
        $data    = I('post.', '', 'trim');
        if ($content) {
            $data = array_merge($content, $data);
        }
        $post_content = serialize($data);
        $res          = M('config')->where(array('id' => 1))->save(array('content' => $post_content));
        if ($res !== false) {
            S('tdk_config', null);
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

    /**
    * 读取系统配置信息
    */
    public function biyouConfig() {
        $content = M('config')->getFieldById(2, 'content');
        $content = unserialize($content);
        $this->assign('content', $content);
        $this->display();
    }

    /**
    * 编辑系统设置
    */
    public function biyouConfigEdit() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $content = M('config')->getFieldById(2, 'content');
        $content = unserialize($content);
        $data    = I('post.', '', 'trim');
        if ($content) {
            $data = array_merge($content, $data);
        }
        $post_content = serialize($data);
        $res          = M('config')->where(array('id' => 2))->save(array('content' => $post_content));
        if ($res !== false) {
            S('bu_config', null);
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }


    /**
     * 代理信息设置首页
     */
    public function agentInfo() {
        $agent = M('proxy')->field('id, name, pid, update_cookie_time, update_token_time, update_qtkcookie_time, remark')->select();
        $count = M('proxy')->count('id');
        $page  = $this->pages($count, 10);
        $this->assign('page', $page->show());
        $this->assign('list', $agent);
        $this->display();
    }

    /**
     * 添加新代理
     */
    public function agentAdd() {
        if (IS_POST) {
            $tmp = array(
                'name'                  => I('post.name', '', 'trim'),
                'pid'                   => I('post.pid', '', 'trim'),
                'cookie'                => I('post.cookie', '', 'trim'),
                'update_cookie_time'    => time(),
                'taobao_access_toekn'   => I('post.taobao_access_toekn', '', 'trim'),
                'update_token_time'     => time(),
                'qtk_cookie'            => I('post.qtk_cookie', '', 'trim'),
                'update_qtkcookie_time' => time(),
                'remark'                => I('post.remark', '', 'trim'),
            );
            if ($tmp['cookie'] == '' || $tmp['taobao_access_token'] == '' || $tmp['qtk_cookie'] == '') {
                $this->ajaxReturn(array('code'=>-1,'msg'=>'代理的基本信息不能为空'));
            }
            $res = M('proxy')->add($tmp);
            if ($res) {
                S('tdk_proxy_data', null);
                $this->success('添加成功', U('Config/agentInfo'));
                exit;
            } else {
                $this->error('添加失败', U('Config/agentInfo'));
                exit;
            }
        }
        $this->display();
    }

    /**
     * 编辑代理信息
     */
    public function agentEdit() {
        $id = I('get.id', '', 'trim');
        // 广告信息
        $list = M('proxy')->where(array('id' => $id))->find();
        // 所有广告分类
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 代理信息编辑
     */
    public function doEdit(){
        $id = I('post.id', '', 'trim');
        $tmp = array(
            'cookie'              => I('post.cookie', '', 'trim'),
            'taobao_access_token' => I('post.taobao_access_token', '', 'trim'),
            'qtk_cookie'          => I('post.qtk_cookie', '', 'trim'),
            'remark'              => I('post.remark', '', 'trim'),
        );

        if ($tmp['cookie'] == '' || $tmp['taobao_access_token'] == '' || $tmp['qtk_cookie'] == '') {
            $this->ajaxReturn(array('code'=>-1,'msg'=>'代理的基本信息不能为空'));
        }
        $proxy_data = M('proxy')->where(array('id' => $id))->find();
        if (strcmp($proxy_data['cookie'], $tmp['cookie']) != 0) {
            $tmp['update_cookie_time'] = time();
        }
        if (strcmp($proxy_data['taobao_access_token'], $tmp['taobao_access_token']) != 0) {
            $tmp['update_token_time'] = time();
        }
        if (strcmp($proxy_data['qtk_cookie'], $tmp['qtk_cookie']) != 0) {
            $tmp['update_qtkcookie_time'] = time();
        }

        $res = M('proxy')->where(array('id' => $id))->save($tmp);
        if ($res !== false) {
            S('tdk_proxy_data', null);
            $this->ajaxReturn(array('code'=>0,'msg'=>'代理的基本信息编辑成功'));
            exit;
        } else {
            $this->ajaxReturn(array('code'=>-1,'msg'=>'代理的基本信息编辑失败'));
        }
    }
}