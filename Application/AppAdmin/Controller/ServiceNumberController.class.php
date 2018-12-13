<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2017/12/19
 * Time: 9:45
 */

namespace AppAdmin\Controller;

use Common\Org\Http;
use Think\Exception;

/**
 *
 * 百姓网服务号管理
 * Class ServiceNumberController
 *
 * @package AppAdmin\Controller
 */
class ServiceNumberController extends CommonController {


    /**
     * 设置菜单
     */
    private $menu_create = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s";

    /**
     * 服务号列表
     */
    public function index() {
        $data = M('bx_service')->select();
        foreach ($data as $key => $val) {
            $data[$key]['num']     = M('bx_user')->where(array('service_id' => $val['id'], 'status' => 1))->count();
            $data[$key]['del_num'] = M('bx_user')->where(array('service_id' => $val['id'], 'status' => 0))->count();
        }
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 编辑关注提示语
     */
    public function updateService() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $type       = I('post.type', '', 'trim');
        $service_id = I('post.id', 0, 'int');
        $info       = M('bx_service')->find($service_id);
        if (!$service_id || !$info) {
            $this->error('账号信息不存在，无法编辑关注提示语！');
        }
        $content = I('post.content', '', 'trim');
        if ($type == 'menu') {
            if (!$content) {
                $this->error('底部菜单不能为空！');
            }
            $access_token = $this->_getAccessToken($info);
            if (empty($access_token)) {
                $this->error('获取token失败，请重试！');
            }
            M('bx_service')->save(array('id' => $service_id, 'access_token' => $access_token, 'access_token_expire_time' => time() + 7200));
            $httpObj = new Http();
            $res     = json_decode($httpObj->post(sprintf($this->menu_create, $access_token), $content), true);
            if ($res['errcode'] != 0) {
                $this->error('access_token失效，请稍后重试！');
            }
            $save_data = array('id' => $service_id, 'menu' => $content);
        } else {
            if (!$content) {
                $this->error('关注提示语不能为空！');
            }
            $auto_reply = I('post.auto_reply', '', 'trim');
            if (!$auto_reply) {
                $this->error('自动回复语不能为空！');
            }
            $save_data = array('id' => $service_id, 'content' => $content, 'auto_reply' => $auto_reply);
        }
        $res = M('bx_service')->save($save_data);
        if ($res !== false) {
            $msg = $type == 'menu' ? '设置成功' : '修改成功';
            $this->success($msg);
        } else {
            $msg = $type == 'menu' ? '设置成功，数据修改失败' : '修改失败';
            $this->success($msg);
        }
    }

    /**
     * 关键字回复功能
     */
    public function keyword() {
        $service_id = I('get.service_id', 0, 'int');
        $info       = M('bx_service')->find($service_id);
        if (!$service_id || !$info) {
            $this->error('账号信息不存在，无法管理关键字回复功能！');
        }
        $data = M('bx_keyword')->where(array('service_id' => $service_id))->select();
        $this->assign('data', $data);
        $this->assign('service_id', $service_id);
        $this->display();
    }

    /**
     * 添加关键字
     */
    public function addKeyword() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $service_id = I('post.service_id', 0, 'int');
        $info       = M('bx_service')->find($service_id);
        if (!$service_id || !$info) {
            $this->error('账号信息不存在，无法关键字信息！');
        }
        $keyword = I('post.keyword', '', 'trim');
        $content = I('post.content', '', 'trim');
        if (!$keyword) {
            $this->error('关键字不能为空！');
        }
        if (!$content) {
            $this->error('回复内容不能为空！');
        }
        $data = array('service_id' => $service_id, 'keyword' => $keyword, 'content' => $content);
        $res  = M('bx_keyword')->add($data);
        if ($res) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败！');
        }
    }

    /**
     * 删除关键字
     */
    public function delKeyword() {
        $id   = I('get.id', 0, 'int');
        $info = M('bx_keyword')->find($id);
        if (!$id || !$info) {
            $this->error('关键字信息不存在，无法删除！');
        }
        $res = M('bx_keyword')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }


    /**
     * 推广员列表
     */
    public function generalize() {
        $service_id = I('get.service_id', 0, 'int');
        $info       = M('bx_service')->find($service_id);
        if (!$service_id || !$info) {
            $this->error('账号信息不存在，无法管理推广信息！');
        }
        $model = M('bx_generalize');
        $where = array('service_id' => $service_id);
        $count = $model->where($where)->count('id');
        $page  = $this->pages($count, $this->limit);
        $data  = $model->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($data as $key => $val) {
            $data[$key]['all_num']       = M('bx_user')->where(array('service_id' => $val['service_id'], 'generalize_id' => $val['id'], 'status' => 1))->count();
            $data[$key]['all_del_num']   = M('bx_user')->where(array('service_id' => $val['service_id'], 'generalize_id' => $val['id'], 'status' => 0))->count();
            $data[$key]['today_num']     = M('bx_user')->where(array('service_id' => $val['service_id'], 'generalize_id' => $val['id'], 'status' => 1, 'add_time' => array('gt', strtotime(date('Y-m-d')))))->count();
            $data[$key]['today_del_num'] = M('bx_user')->where(array('service_id' => $val['service_id'], 'generalize_id' => $val['id'], 'status' => 0, 'add_time' => array('gt', strtotime(date('Y-m-d')))))->count();
        }
        $this->assign(array('data' => $data, 'page' => $page->show(), 'service_id' => $service_id));
        $this->display();
    }

    /**
     * 添加推广员信息
     */
    public function addGeneralize() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $service_id = I('post.service_id', 0, 'int');
        $info       = M('bx_service')->find($service_id);
        if (!$service_id || !$info) {
            $this->error('账号信息不存在，无法添加推广信息！');
        }
        $name = I('post.name', '', 'trim');
        if (!$name) {
            $this->error('推广人名称不能为空！');
        }
        $have_id = M('bx_generalize')->where(array('name' => $name))->getField('id');
        if ($have_id > 0) {
            $this->error('推广位名称已存在，请重新填写！');
        }
        $access_token    = $info['access_token'];
        $is_update_token = false;
        if ($info['access_token_expire_time'] < time()) {
            $is_update_token = true;
            $access_token    = $this->_getAccessToken($info);
            if (empty($access_token)) {
                $this->error('获取token失败，请重试！');
            }
        }
        $count = M('bx_generalize')->where(array('service_id' => $service_id))->count();
        $count++;
        $service_num = $service_id . "_" . $count;
        $img_url     = $this->_getQRImageUrl($service_num, $access_token);
        if (empty($img_url)) {
            $this->error('获取二维码失败，请重试！');
        }
        $model = M();
        $model->startTrans();
        try {
            $add_data = array('name' => $name, 'service_id' => $service_id, 'service_num' => $service_num, 'img_url' => $img_url, 'add_time' => time());
            M('bx_generalize')->add($add_data);
            if ($is_update_token === true) {
                M('bx_service')->save(array('id' => $service_id, 'access_token' => $access_token, 'access_token_expire_time' => time() + 7200));
            }
            $model->commit();
            $this->success('添加成功');
        } catch (\Exception $e) {
            $model->rollback();
            $this->success('添加失败！');
        }
    }

    /**
     * 下载图片
     */
    public function downImg() {
        $id   = I('get.id', 0, 'int');
        $info = M('bx_generalize')->find($id);
        if (!$info['img_url']) {
            $this->error('图片不存在无法下载！');
        }
        $file_name = $info['img_url'];//图片链接
        $mime      = 'application/force-download';
        header('Pragma: public'); // required
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename=' . $info['service_num'] . ".png");
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');
        readfile($file_name); // push it out
    }

    /**
     * 推广员列表
     */
    public function user() {
        $generalize_id = I('get.id', 0, 'int');
        $info          = M('bx_generalize')->find($generalize_id);
        if (!$generalize_id || !$info) {
            $this->error('推官员信息不存在，无法查看用户数据！');
        }
        $model = M('bx_user');
        $where = array('service_id' => $info['service_id'], 'generalize_id' => $generalize_id);
        $time  = I('get.time', '', 'trim');
        $type  = I('get.type', '', 'trim');
        if ($time) {
            list($start_time, $end_time) = explode('-', $time);
            $where['add_time'] = array('between', array(strtotime($start_time), strtotime("$end_time +1 days") - 1));
        }
        if ($type == "down") {
            $data = $model->where($where)->order('id desc')->select();
            foreach ($data as &$val) {
                if ($val['status'] == 1) {
                    $val['status'] = '已关注';
                } else {
                    $val['status'] = '已取消';
                }
                if ($val['sex'] == 1) {
                    $val['sex'] = "男";
                } elseif ($val['sex'] == 2) {
                    $val['sex'] = "女";
                } else {
                    $val['sex'] = "保密";
                }
                $val['add_time'] = date('Y-m-d H:i:s', $val['add_time']);
            }
            $row = array(
                'nickname' => '微信昵称',
                'sex'      => '用户性别',
                'address'  => '用户地址',
                'add_time' => '关注时间',
                'status'   => '关注状态',
            );
            if ($time) {
                $file_name = $info['name'] . '-关注用户' . $time . '下载';
            } else {
                $file_name = $info['name'] . '-关注用户下载';
            }
            download_xls($data, $row, $file_name);
        } else {
            $count       = $model->where($where)->count('id');
            $page        = $this->pages($count, $this->limit);
            $data        = $model->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
            $all_num     = $model->where($where)->where(array('status' => 1))->count();
            $all_del_num = $model->where($where)->where(array('status' => 0))->count();
            $this->assign(array('data' => $data, 'page' => $page->show(), 'time' => $time, 'all_num' => $all_num, 'all_del_num' => $all_del_num, 'service_id' => $info['service_id'], 'generalize_id' => $generalize_id));
            $this->display();
        }

    }


    /**
     * @param $info
     * @return string
     */
    protected function _getAccessToken($info) {
        // 获取token
        $http  = new Http();
        $url   = "https://api.weixin.qq.com/cgi-bin/token";
        $data  = array(
            'appid'      => $info['app_id'],
            'secret'     => $info['app_secret'],
            'grant_type' => 'client_credential'
        );
        $res   = $http->get($url, $data);
        $token = json_decode($res, true);
        if (isset($token['access_token'])) {
            return $token['access_token'];
        } else {
            return '';
        }
    }

    /**
     * @param $num
     * @param $token
     * @return string
     */
    protected function _getQRImageUrl($num, $token) {
        $param    = array(
            'action_name' => 'QR_LIMIT_STR_SCENE',
            'action_info' => array(
                'scene' => array(
                    'scene_str' => $num
                )
            ),
        );
        $url      = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $token;
        $http     = new Http();
        $res_temp = $http->post($url, json_encode($param));
        $res      = json_decode($res_temp, true);
        if (isset($res['ticket'])) {
            return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $res['ticket'];
        } else {
            return '';
        }
    }

}