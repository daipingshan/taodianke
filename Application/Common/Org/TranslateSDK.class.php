<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2017/12/24
 * Time: 11:20
 */

namespace Common\Org;


class TranslateSDK {

    /**
     * @var string
     */
    private $app_id = "20180112000114537";

    /**
     * @var string
     */
    private $app_sec_key = "xVsNsTf43gPLtQvpx7BM";

    /**
     * @var string
     */
    private $url = "http://api.fanyi.baidu.com/api/trans/vip/translate";

    /**
     * 构造函数
     * TranslateSDK constructor.
     */
    public function __construct() {
        $data = array(
            array('app_id' => '20171223000107857', 'app_sec_key' => 'gnFm9B7mWJNwlgwvP8Fx'),
            array('app_id' => '20180112000114537', 'app_sec_key' => 'xVsNsTf43gPLtQvpx7BM'),
            array('app_id' => '20180112000114542', 'app_sec_key' => '8SXG_aRhRyU1z_KCJqQH'),
            array('app_id' => '20180112000114546', 'app_sec_key' => 'p8viUcDpm9yd8OHDKRgp'),
            array('app_id' => '20180112000114557', 'app_sec_key' => 'ngJkVET_2LzDhuoRDn6u'),
            array('app_id' => '20180112000114597', 'app_sec_key' => 'Zr_TUW5I9u3psHx7gK_I'),
        );

        $day = date('d');
        $key = $day % count($data);
        if (isset($data[$key]) && $data[$key]) {
            $this->app_id      = $data[$key]['app_id'];
            $this->app_sec_key = $data[$key]['app_sec_key'];
        }
    }

    //翻译入口
    public function translate($query, $from, $to) {
        $args         = array(
            'q'     => $query,
            'appid' => $this->app_id,
            'salt'  => rand(10000, 99999),
            'from'  => $from,
            'to'    => $to,

        );
        $args['sign'] = $this->_buildSign($query, $this->app_id, $args['salt'], $this->app_sec_key);
        $httpObj      = new Http();
        $ret          = $httpObj->post($this->url, $args);
        $ret          = json_decode($ret, true);
        return $ret;
    }

    /**
     * @param $query
     * @param $appID
     * @param $salt
     * @param $secKey
     * @return string
     */
    protected function _buildSign($query, $appID, $salt, $secKey) {
        $str = $appID . $query . $salt . $secKey;
        $ret = md5($str);
        return $ret;
    }


}