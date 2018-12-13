<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2017/11/6
 * Time: 16:12
 */

namespace Home\Controller;

use Common\Org\Http;
use Think\Exception;

/**
 * 淘宝授权登陆
 * Class TaoBaoAuthController
 *
 * @package AppApi\Controller
 */
class TaoBaoAuthController extends CommonController {

    /**
     * 淘宝
     */
    public function index() {
        $code = I('get.code', '', 'trim');
        if (!$code) {
            $url = "https://oauth.taobao.com/authorize?response_type=code&client_id=23837662&redirect_uri=http://www.taodianke.com/Home/TaoBaoAuth/index&state=state&view=wap";
            header("Location:{$url}");
        } else {
            $url       = 'https://oauth.taobao.com/token';
            $post_data = array(
                'client_id'     => '23837662',
                'client_secret' => '5a578243baf4b1566391ca55a807d7d0',
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => 'http://www.taodianke.com/Home/TaoBaoAuth/index'
            );
            $res       = $this->curl($url, $post_data);
            $data = json_decode($res,true);
            dump($data);exit;
        }
    }

    public function curl($url, $postFields = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->readTimeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->readTimeout);
        }
        if ($this->connectTimeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, "top-sdk-php");
        //https 请求
        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if (is_array($postFields) && 0 < count($postFields)) {
            $postBodyString = "";
            $postMultipart  = false;
            foreach ($postFields as $k => $v) {
                if (!is_string($v))
                    continue;

                if ("@" != substr($v, 0, 1))//判断是不是文件上传
                {
                    $postBodyString .= "$k=".urlencode($v)."&";
                } else//文件上传用multipart/form-data，否则用www-form-urlencoded
                {
                    $postMultipart = true;
                    if (class_exists('\CURLFile')) {
                        $postFields[$k] = new \CURLFile(substr($v, 1));
                    }
                }
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart) {
                if (class_exists('\CURLFile')) {
                    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
                } else {
                    if (defined('CURLOPT_SAFE_UPLOAD')) {
                        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                    }
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            } else {
                $header = array( "content-type: application/x-www-form-urlencoded; charset=UTF-8" );
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        }
        $reponse = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new Exception($reponse, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }
}