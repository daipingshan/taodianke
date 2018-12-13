<?php

namespace AppAdmin\Controller;

use Common\Org\Http;

class WechatController extends CommonController {

    /**
     * 淘店客公众号配置
     */
    public function index() {
        $content = M('config')->getFieldById(1, 'content');
        $content = unserialize($content);
        $this->assign('content', $content);
        $this->display();
    }

    /**
     * 宅猫生活公众号配置
     */
    public function publicNumberConfig() {
        $content = M('config')->getFieldById(1, 'content');
        $content = unserialize($content);
        $this->assign('content', $content);
        $this->display();
    }

    /**
     * 编辑系统设置
     */
    public function editConfig() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $content = M('config')->getFieldById(1, 'content');
        $content = unserialize($content);
        $data    = I('post.', '', 'trim');

        if (isset($data['WEIXIN_MP'])) {
            if (empty($data['WEIXIN_MP']['share_bg_url'])) {
                $data['WEIXIN_MP']['share_bg_update_time'] = $content['WEIXIN_MP']['share_bg_update_time'];
            } else {
                $data['WEIXIN_MP']['share_bg_update_time'] = time();
            }
        }

        if (isset($data['STORE_WEIXIN_MP'])) {
            if (empty($data['STORE_WEIXIN_MP']['share_bg_url'])) {
                $data['STORE_WEIXIN_MP']['share_bg_update_time'] = $content['STORE_WEIXIN_MP']['share_bg_update_time'];
            } else {
                $data['STORE_WEIXIN_MP']['share_bg_update_time'] = time();
            }
        }

        if (is_array($content)) {
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
     * 设置公众号底部菜单
     */
    public function setFooterMenu() {
        $footer_menu  = I('post.', '', 'trim');
        $access_token = null;
        if (isset($footer_menu['WEIXIN_BASE'])) {
            if (empty($footer_menu['WEIXIN_BASE']['footer_menu'])) {
                $this->error('底部菜单内容不能为空！');
            }
            $post_data    = $footer_menu['WEIXIN_BASE']['footer_menu'];
            $access_token = $this->_getWeChatAccessToken();
        } else if ($footer_menu['STORE_WEIXIN_BASE']) {
            $access_token = $this->_getWeChatAccessToken('store_weChat_access_token');
            if (empty($footer_menu['STORE_WEIXIN_BASE']['footer_menu'])) {
                $this->error('底部菜单内容不能为空！');
            }
            $post_data = $footer_menu['STORE_WEIXIN_BASE']['footer_menu'];
        } else {
            $this->error('请求参数不合法');
        }
        if (empty($access_token)) {
            $this->error('access_token获取失败！');
        }
        $httpObj   = new Http();
        $url       = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s";
        $menu_data = json_decode($httpObj->post(sprintf($url, $access_token), $post_data), true);
        if ($menu_data['errcode'] === 0) {
            $content = M('config')->getFieldById(1, 'content');
            $content = unserialize($content);
            if (isset($footer_menu['WEIXIN_BASE'])) {
                $content['WEIXIN_BASE']['footer_menu'] = $post_data;
            } else {
                $content['STORE_WEIXIN_BASE']['footer_menu'] = $post_data;
            }
            $post_content = serialize($content);
            M('config')->where(array('id' => 1))->save(array('content' => $post_content));
            $this->success('底部菜单设置成功');
        } else {
            $this->error($menu_data['errmsg']);
        }
    }

    /**
     * 用户基本信息管理
     */
    public function getCustomerInfo() {
        $param = array(
            'proxy_pid' => I('get.proxy_pid', '', 'trim'),
            'nickname'  => I('get.nickname', '', 'trim'),
            'add_time'  => I('get.add_time', '', 'trim'),
        );
        $where = array();
        if ($param['proxy_pid']) {
            $where['proxy_pid'] = $param['proxy_pid'];
        }
        if ($param['nickname']) {
            $where['nickname'] = $param['nickname'];
        }
        if ($param['add_time']) {
            $where['add_time'] = $param['add_time'];
        }
        // 分页
        $count    = M('wxuser')->where($where)->field('id')->count('id');
        $page     = $this->pages($count, 10);
        $customer = M('wxuser')->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $user     = $user_ids = array();
        foreach ($customer as $k => $v) {
            $user_ids[$v['proxy_pid']] = $v['proxy_pid'];
        }
        if ($user_ids) {
            $user = M('user')->where(array('pid' => array('in', array_keys($user_ids))))->field('pid, real_name')->index('pid')->select();
        }
        $this->assign('page', $page->show());
        $this->assign('customer', $customer);
        $this->assign('user', $user);
        $this->display();

    }

}