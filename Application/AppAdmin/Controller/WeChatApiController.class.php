<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2017/12/19
 * Time: 13:41
 */

namespace AppAdmin\Controller;

use Common\Org\Http;

/**
 * 百姓网二维码验证
 *
 * Class WeChatApiController
 *
 * @package AppAdmin\Controller
 */
class WeChatApiController extends CommonController {

    /**
     * 不验证用户登录功能
     *
     * @var bool
     */
    public $checkUser = false;

    /**
     * 是否验证token
     *
     * @var bool
     */
    protected $checkToken = false;

    /**
     * @var array
     */
    protected $postData = array();

    /**
     * WeChatApiController constructor.
     */
    public function _initialize() {
        $this->postData = $GLOBALS["HTTP_RAW_POST_DATA"];
    }

    /**
     * 验证Token
     */
    protected function _checkToken() {
        $signature  = $_GET["signature"];
        $timestamp  = $_GET["timestamp"];
        $nonce      = $_GET["nonce"];
        $service_id = $_GET['service_id'];
        $token      = M('bx_service')->getFieldById($service_id, 'token');
        if ($token) {
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);
            ob_clean();
            if ($tmpStr == $signature) {
                echo $_GET["echostr"];
            } else {
                echo 'token验证失败';
            }
        } else {
            echo '账号信息不存在';
        }
    }

    /**
     * 微信回调
     */
    public function index() {
        if ($this->checkToken === true) {
            $this->_checkToken();
        } else {
            if (!empty($this->postData) && $this->postData) {
                //解析数据
                $postObj = simplexml_load_string($this->postData, 'SimpleXMLElement', LIBXML_NOCDATA);
                //消息类型
                $form_MsgType = $postObj->MsgType;
                $service_id   = $_GET['service_id'];
                $service_info = M('bx_service')->find($service_id);
                if ($form_MsgType == 'event') {
                    $this->_getEvent($postObj, $service_info);
                } else if ($form_MsgType == 'text') {
                    $this->_getText($postObj, $service_info);
                }
            }
        }
    }

    /**
     * @param $postObj
     * @param $service_info
     * @return bool
     */
    protected function _getEvent($postObj, $service_info) {
        //发送消息方ID
        $fromUsername = $postObj->FromUserName;//openid
        //接收消息方ID
        $toUsername = $postObj->ToUserName;
        //获取事件类型
        $form_Event = $postObj->Event;
        //关注带有扫描事件
        $event_key = $postObj->EventKey;//传进来的参数  用户的pid
        //  获取用户基本信息,扫描二维码的用户的openid

        $openid    = $this->_xmlToArr($fromUsername);
        $user_info = $this->_getUserInfo($openid, $service_info);
        $msg       = $service_info['content'];
        //订阅事件
        if ($form_Event == "subscribe") {
            if ($event_key == '') {
                $this->_getTextTpl($fromUsername, $toUsername, time(), 'text', $msg);
            }
            $service_num   = str_ireplace('qrscene_', '', $event_key);
            $generalize_id = M('bx_generalize')->where(array('service_num' => $service_num))->getField('id');
            // 查找用户是否存在
            $model = M('bx_user');
            $user  = $model->where(array('open_id' => $openid, 'service_id' => $service_info['id']))->find();
            if (!$user) {
                $user_data = array(
                    'service_id'    => $service_info['id'],
                    'generalize_id' => $generalize_id,
                    'nickname'      => $user_info->nickname,
                    'open_id'       => $openid,
                    'sex'           => $user_info->sex,
                    'head_img_url'  => $user_info->headimgurl,
                    'address'       => $user_info->country . "-" . $user_info->province . "-" . $user_info->city,
                    'add_time'      => time(),
                );

                $model->add($user_data);
            } else {
                $model->save(array('id' => $user['id'], 'status' => 1));
            }
            $this->_getTextTpl($fromUsername, $toUsername, time(), 'text', $msg);
        } else if ($form_Event == 'unsubscribe') {
            // 查找用户是否存在
            $model = M('bx_user');
            $user  = $model->where(array('open_id' => $openid, 'service_id' => $service_info['id']))->find();
            if ($user) {
                $model->save(array('id' => $user['id'], 'status' => 0));
            }
        }
    }

    /**
     * @param $postObj
     * @param $service_info
     */
    protected function _getText($postObj, $service_info) {
        //发送消息方ID
        $fromUsername = $postObj->FromUserName;
        //接收消息方ID
        $toUsername = $postObj->ToUserName;
        $content    = $postObj->Content;
        $data       = M('bx_keyword')->where(array('service_id' => $service_info['id']))->select();
        $is_send    = false;
        foreach ($data as $val) {
            if (strstr($content, $val['keyword'])) {
                $is_send = true;
                $this->_getTextTpl($fromUsername, $toUsername, time(), 'text', $val['content']);
            }
        }
        if ($is_send === false) {
            $this->_getTextTpl($fromUsername, $toUsername, time(), 'text', $service_info['auto_reply']);
        }
    }

    /**
     * Convert a SimpleXML object into an array (last resort).
     *
     * @param object $xml
     * @param bool $root Should we append the root node into the array
     * @return array|string
     */
    protected function _xmlToArr($xml, $root = true) {
        if (!$xml->children()) {
            return (string)$xml;
        }
        $array = array();
        foreach ($xml->children() as $element => $node) {
            $totalElement = count($xml->{$element});
            if (!isset($array[$element])) {
                $array[$element] = "";
            }
            // Has attributes
            if ($attributes = $node->attributes()) {
                $data = array('attributes' => array(), 'value' => (count($node) > 0) ? $this->xmlToArr($node, false) : (string)$node);
                foreach ($attributes as $attr => $value) {
                    $data['attributes'][$attr] = (string)$value;
                }
                if ($totalElement > 1) {
                    $array[$element][] = $data;
                } else {
                    $array[$element] = $data;
                }
                // Just a value
            } else {
                if ($totalElement > 1) {
                    $array[$element][] = $this->xmlToArr($node, false);
                } else {
                    $array[$element] = $this->xmlToArr($node, false);
                }
            }
        }
        if ($root) {
            return array($xml->getName() => $array);
        } else {
            return $array;
        }

    }

    /**
     * @param $openid
     * @param $service_info
     * @return mixed
     */
    protected function _getUserInfo($openid, $service_info) {
        $access_token = $service_info['access_token'];
        if ($service_info['expire_time'] < time()) {
            $access_token = $this->_getAccessToken($service_info);
            M('bx_service')->save(array('id' => $service_info['id'], 'access_token' => $access_token, 'access_token_expire_time' => time() + 7200));
        }
        $token_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        $httpObj   = new Http();
        $user_info = json_decode($httpObj->post($token_url));
        return $user_info;
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
     * @param $fromUserName
     * @param $toUsername
     * @param $time
     * @param $MsgType
     * @param $content
     */
    protected function _getTextTpl($fromUserName, $toUsername, $time, $MsgType, $content = '') {
        ob_clean();
        if ($content != '') {
            echo "<xml>
                    <ToUserName><![CDATA[$fromUserName]]></ToUserName>
                    <FromUserName><![CDATA[$toUsername]]></FromUserName>
                    <CreateTime>$time</CreateTime>
                    <MsgType><![CDATA[$MsgType]]></MsgType>
                    <Content><![CDATA[$content]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>";
        } else {
            echo "<xml>
                    <ToUserName><![CDATA[$fromUserName]]></ToUserName>
                    <FromUserName><![CDATA[$toUsername]]></FromUserName>
                    <CreateTime>$time</CreateTime>
                    <MsgType><![CDATA[$MsgType]]></MsgType>
                    <FuncFlag>0</FuncFlag>
                    </xml>";
        }
        exit;
    }
}