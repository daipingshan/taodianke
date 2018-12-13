<?php
namespace Proxy\Controller;
use Common\Controller\CommonBusinessController;

class CommonController extends CommonBusinessController {
    public $userinfo;
    // 自动登录
    public function _initialize(){
        //判断用户是否已经登录
        if (!isset($_SESSION['Auth_user'])) {
            redirect(U('/Proxy/login/'));
        }
        $this->userinfo = M('user')->where('id = "'.$_SESSION['Auth_user'].'"')->find();
        $_SESSION['pid'] = $this->userinfo['pid'];

        $this->assign('username',$this->userinfo['username']);
        $this->assign('uid', $_SESSION['Auth_user']);
    }


    /**
     * 非ajax分页方法
     * totalRows  总数量
     * listRows   每页条数
     * map        查询条件
     *
     * @return string
     */
    protected function pages($totalRows, $listRows, $map = array(), $rollPage = 5){
        $Page = new \Think\Page($totalRows, $listRows, '', MODULE_NAME . '/' . ACTION_NAME);
        if($map && IS_POST){
            foreach($map as $key => $val){
                $val             = urlencode($val);
                $Page->parameter .= "$key=" . urlencode($val) . '&';
            }
        }
        if($rollPage > 0){
            $Page->rollPage = $rollPage;
        }
        $Page->setConfig('header', '条信息');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('last', '末页');
        $Page->setConfig(
            'theme', '<div style="float: right"><ul class=pagination><li>%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE%</li></ul></div>'
        );
        return $Page;
    }
    /**
     * 极光推送
     *
     * @param string $alert        要推送的内容
     * @param        string        or array $audience 推送设备对象  要发广播可直接设置为字符串 all 也可以是别名、标签、注册ID、分群、广播等
     * @param array  $extras       扩展字段，键值对
     * @param        string        or array $platform 要推送的平台 all android ios winphone
     * @param string $title        推送的标题
     *
     * @return string or array
     */
    protected function _sendJPush($alert, $audience = 'all', $extras = array(), $platform = 'all') {
        $app_key                 = C('JPUSH.app_key');
        $master_secret           = C('JPUSH.app_secret');
        $push_url                = 'https://api.jpush.cn/v3/push';
        $payload                 = array();
        $payload['platform']     = $platform;
        $payload['audience']     = $audience;
        $payload['notification'] = array( //推送内容
            'alert'    => $alert, //内容
            'android'  => array(
                'extras' => $extras
            ),
            'ios'      => array(
                'extras' => $extras
            ),
            'winphone' => array(
                'extras' => $extras
            )
        );
        $payload['options']      = array(
            'apns_production' => true
        );
        $ch                      = curl_init();
        curl_setopt($ch, CURLOPT_URL, $push_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        // 设置User-Agent
        curl_setopt($ch, CURLOPT_USERAGENT, 'weicang');
        // 连接建立最长耗时
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        // 请求最长耗时
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 设置Basic认证
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $app_key . ':' . $master_secret);
        // 设置Post参数
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        // 设置headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Connection: Keep-Alive'
        ));

        // 执行请求
        $output     = curl_exec($ch);
        $response   = array();
        $error_code = curl_errno($ch);
        if ($error_code) {
            return '错误代码' . $error_code;
        } else {
            $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header_text = substr($output, 0, $header_size);
            $body        = substr($output, $header_size);
            $headers     = array();
            foreach (explode("\r\n", $header_text) as $i => $line) {
                if (!empty($line)) {
                    if ($i === 0) {
                        $headers['http_code'] = $line;
                    } else if (strpos($line, ": ")) {
                        list ($key, $value) = explode(': ', $line);
                        $headers[$key] = $value;
                    }
                }
            }
            $response['headers']   = $headers;
            $response['body']      = $body;
            $response['http_code'] = $httpCode;
        }
        curl_close($ch);
        return $response;
    }

}
?>
