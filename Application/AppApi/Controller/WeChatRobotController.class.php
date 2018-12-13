<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2017/11/15
 * Time: 17:12
 */

namespace AppApi\Controller;

use Common\Org\JPush;
use Common\Org\WeChatRobot as Robot;

/**
 * Class WeChatController
 *
 * @package Api\Controller
 */
class WeChatRobotController extends CommonController {

    /**
     * @var string
     */
    protected $pid = '';

    /**
     * WeChatRobotController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pid = $this->_getPid();
    }


    /**
     * 获取二维码地址
     */
    public function getQRString() {
        $obj = new Robot($this->pid);
        $res = $obj->getQRUrl();
        if ($res['status'] == 0) {
            $data = array( 'qr_string' => $res['url'] );
            $this->outPut($data, 0);
        }
        $this->outPut(null, $res['status'], null, $res['info']);
    }

    /**
     * 微信登录
     */
    public function checkLogin() {
        $obj = new Robot($this->pid);
        $res = $obj->isLogin();
        if ($res['status'] == -1) {
            $this->outPut(null, -1, null, $res['info']);
        }
        $this->outPut(null, 0);
    }

    /**
     * 发送消息
     */
    public function send() {
        ob_clean();
        set_time_limit(90);
        $type      = I('post.type', 'text', 'trim');
        $text      = I('post.text', '', 'trim');
        $pic_url   = I('post.pic_url', '', 'trim');
        $send_name = I('post.send_name', '', 'trim');
        if (!$send_name) {
            $this->outPut(null, -1, null, '群名称不能为空！');
        }
        $send_data = explode('|__|__|', $send_name);
        $obj       = new Robot($this->pid);
        switch ($type) {
            case 'text':
                if (!$text) {
                    $this->outPut(null, -1, null, '发送内容不能为空！');
                }
                foreach ($send_data as $key => $val) {
                    usleep(rand(800000, 2000000));
                    $this->_sendPush('正在发送至->【'.$val.'】');
                    $obj->send($val, $text);
                }
                break;
            case 'pic' :
                if (!$pic_url) {
                    $this->outPut(null, -1, null, '发送图片不能为空！');
                }
                foreach ($send_data as $key => $val) {
                    usleep(rand(800000, 2000000));
                    $this->_sendPush('正在发送至->【'.$val.'】');
                    $obj->send($val, $pic_url, 'img');
                }
                break;
            case 'pic_text':
                if (!$text) {
                    $this->outPut(null, -1, null, '发送内容不能为空！');
                }
                if (!$pic_url) {
                    $this->outPut(null, -1, null, '发送图片不能为空！');
                }
                foreach ($send_data as $key => $val) {
                    usleep(rand(800000, 2000000));
                    $this->_sendPush('正在发送至->【'.$val.'】');
                    $obj->send($val, $pic_url, 'img');
                    $obj->send($val, $text);
                }
                break;
        }
        $this->_sendPush('群发完成', 'wechat_send_end');
        $this->outPut(null, 0);
    }

    /**
     * 群发消息
     */
    public function mass() {
        ob_clean();
        set_time_limit(90);
        $type      = I('post.type', 'text', 'trim');
        $text      = I('post.text', '', 'trim');
        $pic_url   = I('post.pic_url', '', 'trim');
        $send_name = I('post.send_name', '', 'trim');
        $send_data = json_decode($send_name, true);
        if (!$send_name || !$send_data) {
            $this->outPut(null, -1, null, '群名称不能为空！');
        }
        $obj = new Robot($this->pid);
        switch ($type) {
            case 'text':
                if (!$text) {
                    $this->outPut(null, -1, null, '发送内容不能为空！');
                }
                foreach ($send_data as $key => $val) {
                    usleep(rand(800000, 2000000));
                    $this->_sendPush('正在发送至->【'.$val['NickName'].'】');
                    $obj->sendMass($val['UserName'], $text);
                }
                break;
            case 'pic' :
                if (!$pic_url) {
                    $this->outPut(null, -1, null, '发送图片不能为空！');
                }
                foreach ($send_data as $key => $val) {
                    usleep(rand(800000, 2000000));
                    $this->_sendPush('正在发送至->【'.$val['NickName'].'】');
                    $obj->sendMass($val['UserName'], $pic_url);
                }
                break;
            case 'pic_text':
                if (!$text) {
                    $this->outPut(null, -1, null, '发送内容不能为空！');
                }
                if (!$pic_url) {
                    $this->outPut(null, -1, null, '发送图片不能为空！');
                }
                foreach ($send_data as $key => $val) {
                    usleep(rand(800000, 2000000));
                    $this->_sendPush('正在发送至->【'.$val['NickName'].'】');
                    $obj->sendMass($val['UserName'], $pic_url, 'img');
                    $obj->sendMass($val['UserName'], $text);
                }
                break;
        }
        $this->_sendPush('群发完成', 'wechat_send_end');
        $this->outPut(null, 0);
    }

    /**
     * 推送消息
     *
     * @param $content
     * @param string $type
     */
    protected function _sendPush($content, $type = "text") {
        $data    = array( 'type' => $type );
        $pushObj = new JPush();
        $pushObj->push($content, $data, $this->pid, 'all', 'msg', 300);
    }

    /**
     * 获取用户pid
     */
    protected function _getPid() {
        return M('user')->getFieldById($this->uid, 'pid');
    }

}