<?php

/**
 * Created by PhpStorm.
 * User: zhoujz
 * Date: 15-3-13
 * Time: 上午11:42
 */

namespace Common\Org;

/**
 * Class OSS
 *
 * @package Common\Org
 */
class WeiXin {

    const URL             = 'https://api.weixin.qq.com/sns/';
    const GET_QR_URL      = 'https://api.weixin.qq.com/cgi-bin';
    const SHOW_QR_URL     = 'https://mp.weixin.qq.com/cgi-bin/showqrcode';
    const SEND_COUPON     = 'https://api.weixin.qq.com/cgi-bin/message/template/send';
    const CALLBACK_URL    = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    protected $app_id     = '';
    protected $app_secret = '';

    /**
     * 构造函数
     * WeiXin constructor.
     *
     * @param array $option
     */
    public function __construct($option = array()) {
        if (!isset($option['app_id']) || empty($option['app_id'])) {
            $option['app_id'] = C('WEIXIN_BASE.app_id');
        }
        if (!isset($option['app_secret']) || empty($option['app_secret'])) {
            $option['app_secret'] = C('WEIXIN_BASE.app_secret');
        }
        $this->app_id     = $option['app_id'];
        $this->app_secret = $option['app_secret'];
    }

    /**
     * /**
     * 获取微信用户基本信息
     *
     * @param $code
     * @return array|mixed
     */
    public function getToken($code) {

        // 参数判断
        if (!trim($code)) {
            return array('error' => '微信授权码不能为空！');
        }

        $uri  = 'oauth2/access_token';
        $data = array(
            'appid'      => $this->app_id,
            'secret'     => $this->app_secret,
            'code'       => trim($code),
            'grant_type' => 'authorization_code',
        );
        $obj  = new Http();
        $res  = $obj->get($uri, $data);
        if (is_string($res)) {
            $res = json_decode($res, true);
        }
        if ($res === null || isset($res['errcode'])) {
            return array('error' => '微信token获取失败！');
        }

        return $res;
    }

    /**
     * 获取微信用户基本信息
     *
     * @param string $code
     * @return array|mixed
     */
    public function getWeiXinUserInfo($code = '') {
        list($token, $openid) = @ explode('|', $code);
        $uri  = 'userinfo';
        $data = array(
            'access_token' => $token, // $acccessToken['access_token'],
            'openid'       => $openid, // $acccessToken['openid'],
        );
        $obj  = new Http();
        $res  = $obj->get($uri, $data);
        if (is_string($res)) {
            $res = json_decode($res, true);
        }
        if ($res === null || isset($res['errcode'])) {
            return array('error' => '微信用户信息获取失败！');
        }
        return $res;
    }

    /**
     * 获取二维码
     *
     * @param type $pid
     * @param type $token
     * @return string
     */
    public function getQRImageUrl($pid, $token) {
        $quert    = array(
            //'expire_seconds' => 1800,
            'action_name' => 'QR_LIMIT_STR_SCENE',
            'action_info' => array(
                'scene' => array(
                    'scene_str' => $pid
                )
            ),
        );
        $url      = self::GET_QR_URL . "/qrcode/create?access_token=" . $token;
        $http     = new Http();
        $res_temp = $http->post($url, json_encode($quert));
        $res      = json_decode($res_temp, true);

        if (isset($res['ticket'])) {
            return self::SHOW_QR_URL . '?ticket=' . $res['ticket'];
        } else {
            return '';
        }
    }

    /**
     * 获取token
     *
     * @return string
     */
    public function getAccessToken() {
        // 获取token
        $http  = new Http();
        $url   = self::GET_QR_URL . '/token'; //?appid=wx71ef1edff818d209&secret=6ca61e882c27a18dfd063882d80f96bf';
        $data  = array(
            'appid'      => $this->app_id,
            'secret'     => $this->app_secret,
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
     * 联合登陆回调
     *
     * @param $code
     * @return type
     */
    public function getWeChatInfo($code) {
        $token = $this->getToken($code, 'pc');
        if (isset($token['error']) && $token['error']) {
            return $token;
        }
        $str = $token['access_token'] . '|' . $token['openid'];
        return $this->getWeiXinUserInfo($str);
    }

}
