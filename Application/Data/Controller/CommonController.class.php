<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2016/12/28
 * Time: 15:07
 */

namespace Data\Controller;

use Common\Controller\CommonBusinessController;
use Common\Org\Http;

/**
 * Class CommonAction
 */
class CommonController extends CommonBusinessController {

    /**
     * 构造函数
     *
     * @access public
     */
    function __construct() {
        parent:: __construct();
    }

    /**
     * 获取token
     */
    protected function _getToken() {
        $data = S('access_token');
        if ($data['expire_time'] < time()) {
            $url          = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $obj          = new Http();
            $res          = $obj->get($url);
            $res          = json_decode($res, true);
            $access_token = $res['access_token'];
            if ($access_token) {
                $data['expire_time']  = time() + 7200;
                $data['access_token'] = $access_token;
                S('access_token', $data);
            }
        } else {
            $access_token = $data['access_token'];
        }
        return $access_token;
    }

}