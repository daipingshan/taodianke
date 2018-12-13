<?php

namespace Admin\Controller;

use Common\Controller\CommonBusinessController;
use Common\Org\TranslateSDK;

/**
 * Class CommonAction
 */
class CommonController extends CommonBusinessController {

    protected $user          = null;
    protected $uid           = null;
    protected $reqnum        = 20;
    protected $checkUser     = true;
    protected $pid           = 'mm_29479672_14502011_57114279';
    public    $http_referer  = "https://mp.toutiao.com";
    public    $http_time_out = 30;

    // 构造方法
    public function __construct() {
        $zero1   = strtotime(date("y-m-d h:i:s")); //当前时间  ,注意H 是24小时 h是12小时
        $zero2   = strtotime("2017-11-11 00:00:00");  //过年时间，不能写2014-1-21 24:00:00  这样不对
        $guonian = ceil(($zero2 - $zero1) / 86400); //60s*60min*24h
        parent::__construct();

        // 同步提交错误统一处理
        $this->__initMessage();

        // 验证是否登录
        if ($this->checkUser) {
            $this->check_user_login();
        }
        $auth_list = C('AUTH_LIST');
        $this->assign('auth_list', $auth_list);
        $group_id = $this->user['group_id'];
        $this->assign('menu_list', $auth_list[$group_id]['data']);
        $this->assign('guonian', $guonian);
    }

    private function check_user_login() {
        $this->user = session(C('SAVE_USER_KEY'));
        if (is_null($this->user)) {
            //跳转到认证网关
            redirect(U(C('USER_AUTH_GATEWAY')));
        }
        if ($this->user['pid']) {
            $this->pid = $this->user['pid'];
        }
        $this->uid = ternary($this->user['id'], '');
    }

    private function __initMessage() {
        $uid     = md5($this->_getUserId());
        $error   = cookie('error_' . $uid);
        $success = cookie('success_' . $uid);
        if (trim($error)) {
            $this->assign('error', base64_decode(str_replace(array('%2b', ' '), '+', urldecode($error))));
            cookie('error_' . $uid, null, -1);
        }
        if (trim($success)) {
            $this->assign('success', base64_decode(str_replace(array('%2b', ' '), '+', urldecode($success))));
            cookie('success_' . $uid, null, -1);
        }
    }

    private function __auth() {


        $module_name     = strtolower(MODULE_NAME);
        $controller_name = strtolower(CONTROLLER_NAME);
        $action_name     = strtolower(ACTION_NAME);
        $uri             = "$module_name/$controller_name/$action_name";
        // 无权限 无登录r认证的url
        if (isset($this->auth_config['NO_AUTH_NO_LOGIN_URI'][$uri])) {
            return true;
        }

        $this->user = session(C('SAVE_USER_KEY'));
        if (is_null($this->user)) {
            //跳转到认证网关
            redirect(U(C('USER_AUTH_GATEWAY')));
        }
        $this->assign('user_info', $this->user);

        // 权限判断
        $res = $this->auth->auth_check_access($this->user['id'], $this->auth_config);
        if (!$res) {
            if (IS_AJAX) {
                $this->ajaxReturn(array('error' => '无权限该操作', 'info' => '无权限该操作', 'code' => -1, 'status' => -1));
            }
            $this->redirect_message(U('Index/index'), array('error' => base64_encode('无权限访问！')));
        }
    }

    protected function _register($class_name) {
        if (isset($this->auth_config['OPEN_AUTH_RULE_REGISTER']) && $this->auth_config['OPEN_AUTH_RULE_REGISTER']) {
            $this->auth->register($class_name);
        }
    }

    /**
     * @return bool
     */
    public function isLogin() {
        return is_null($this->user);
    }

    /**
     * 获取登录用户id
     */
    protected function _getUserId() {
        if (empty($this->uid)) {
            $user      = session(C('SAVE_USER_KEY'));
            $this->uid = ternary($user['id'], '');
        }
        return $this->uid;
    }

    protected function redirect_message($url, $data = array()) {
        $uid     = md5($this->_getUserId());
        $ex_time = 3600 * 24;
        if (isset($data['error']) && trim($data['error'])) {
            cookie('error_' . $uid, base64_encode($data['error']), $ex_time);
        }
        if (isset($data['success']) && trim($data['success'])) {
            cookie('success_' . $uid, base64_encode($data['success']), $ex_time);
        }
        redirect($url);
    }

    /**
     * 检测用户是否签到
     */
    protected function _isDaySign() {
        $where = array('uname' => $this->user['realname'], 'day' => date('Y-m-d'), 'time>0');
        $count = M('qd')->where($where)->count();
        $this->assign('isDaySign', $count ? 1 : 0);
    }

    /**
     * 字节转换
     *
     * @param $size
     * @param string $unit
     * @param int $decimals
     *
     * @return string
     */
    protected function _byteFormat($size, $unit = 'B', $decimals = 2) {
        $units = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);

        $value = 0;
        if ($size > 0) {
            if (!array_key_exists($unit, $units)) {
                $pow  = floor(log($size) / log(1024));
                $unit = array_search($pow, $units);
            }
            $value = ($size / pow(1024, floor($units[$unit])));
        }

        if (!is_numeric($decimals) || $decimals < 0) {
            $decimals = 2;
        }
        return sprintf('%.' . $decimals . 'f ' . $unit, $value);
    }

    /**
     * 输出函数，如果启用debug,输出日志
     *
     * @param array $result   输出数据
     * @param int $errCode    错误code
     * @param string $proType 处理类型
     */
    public function outPut($result = null, $errCode, $proType = null, $msg = '') {
        $this->setData(is_null($result) ? '' : $result);

        $this->setError($errCode, $proType, $msg);
        $this->setResult();

        ob_clean();
        echo json_encode($this->result);
        if (C('SER_LOG')) {
            $this->logOut($result);
        }
        exit;
    }

    /**
     * 设置输出结果
     *
     */
    private function setResult() {
        $this->result = array('code' => $this->code, 'msg' => $this->msg, 'hasnext' => $this->hasNext, 'data' => $this->data);
    }

    /**
     * 设置数据
     *
     */
    private function setData($data) {
        //新增数据过滤转换   daipingshan 2015-04-14
        $data       = $this->_checkData($data);
        $this->data = $data;
    }

    /**
     * 设置错误信息
     *
     * @param int $error      错误code
     * @param string $proType 处理类型
     */
    private function setError($error = 0, $proType = null, $msg = '') {
        $this->code = $error;
        $this->msg  = $this->ef->getErrMsg($error, $proType) . $msg;
    }

    /**
     * 日志输出
     *
     */
    protected function logOut($result) {
        \Think\Log::write('start-------------------------------------------start', \Think\Log::INFO);
        \Think\Log::write('访问页面：' . $_SERVER['PHP_SELF'], \Think\Log::INFO);
        \Think\Log::write('请求方法：' . $_SERVER['REQUEST_METHOD'], \Think\Log::INFO);
        \Think\Log::write('通信协议：' . $_SERVER['SERVER_PROTOCOL'], \Think\Log::INFO);
        \Think\Log::write('请求时间：' . date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), \Think\Log::INFO);
        \Think\Log::write('用户代理：' . $_SERVER['HTTP_USER_AGENT'], \Think\Log::INFO);
        \Think\Log::write('CONTENT_TYPE：' . $_SERVER['CONTENT_TYPE'], \Think\Log::INFO);
        \Think\Log::write('提交数据：', \Think\Log::INFO);
        foreach ($_REQUEST as $key => $value) {
            \Think\Log::write($key . "=" . $value, \Think\Log::INFO);
        }
        \Think\Log::write('输出结果：', \Think\Log::INFO);
        foreach ($result as $key => $value) {
            \Think\Log::write($key . "=" . $value, \Think\Log::INFO);
        }

        if (count($_FILES) != 0) {
            \Think\Log::write('提交文件：', \Think\Log::INFO);
            foreach ($_FILES as $key => $value) {
                \Think\Log::write($key . "=" . $value['type'], \Think\Log::INFO);
            }
        }

        \Think\Log::write('end-----------------------------------------------end', \Think\Log::INFO);
    }


    /**
     * @param $url
     * @param array $params
     * @param $cookie
     * @return bool|mixed
     */
    protected function _get($url, $params = array(), $cookie = '') {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        $header = array(
            'Accept:application/json, text/javascript, */*; q=0.01',
            'Content-Type:application/x-www-form-urlencoded; charset=UTF-8',
            'Referer: ' . $this->http_referer,
            'X-Requested-With:XMLHttpRequest',
        );
        if ($cookie) {
            $header[] = "cookie:{$cookie}";
        }
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        if (!empty($params)) {
            if (strpos($url, '?') !== false) {
                $url .= "&" . http_build_query($params);
            } else {
                $url .= "?" . http_build_query($params);
            }
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->http_time_out);
        $sContent = curl_exec($oCurl);
        $aStatus  = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * @param string $url
     * @param array $params
     * @param $cookie
     * @return bool|mixed
     */
    protected function _post($url = '', $params = array(), $cookie = '') {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        $header = array(
            'Accept:application/json, text/javascript, */*; q=0.01',
            'Content-Type:application/x-www-form-urlencoded; charset=UTF-8',
            'Referer: ' . $this->http_referer,
            'X-Requested-With:XMLHttpRequest',
        );
        if ($cookie) {
            $header[] = "cookie:{$cookie}";
        }
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->http_time_out);
        $sContent = curl_exec($oCurl);
        $aStatus  = curl_getinfo($oCurl);

        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * @param string $url
     * @param array $params
     * @param $cookie
     * @return bool|mixed
     */
    protected function _file($url = '', $params = array(), $cookie = '') {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        $header = array(
            'Accept:application/json, text/javascript, */*; q=0.01',
            'Referer: ' . $this->http_referer,
            'X-Requested-With:XMLHttpRequest',
        );
        if ($cookie) {
            $header[] = "cookie:{$cookie}";
        }
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->http_time_out);
        $sContent = curl_exec($oCurl);
        $aStatus  = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * 二维数组排序
     *
     * @param $data
     * @param $sort_order_field
     * @return mixed
     */
    protected function _arraySort($data, $sort_order_field) {
        if (!$data) {
            return array();
        }
        foreach ($data as $val) {
            $key_arrays[] = $val[$sort_order_field];
        }
        array_multisort($key_arrays, SORT_ASC, SORT_NUMERIC, $data);
        return $data;
    }

    /**
     * 获取头条默认账号信息
     */
    protected function _getTopLineCookie() {
        $data = S('top_line_cookie');
        if (!$data) {
            $data = M('top_line_account')->getFieldById(1, 'cookie');
            if ($data) {
                S('top_line_cookie', $data);
            }
        }
        return $data;
    }

    /**
     * 获取头条默认账号信息
     */
    protected function _getUserData() {
        $data = S('sale_user_data');
        if (!$data) {
            $data = M('tmuser')->select();
            if ($data) {
                S('sale_user_data', $data);
            }
        }
        return $data;
    }

    /**
     * 百度翻译
     */
    public function translate() {
        if (!IS_AJAX) {
            $this->error(array('msg' => '非法请求！'));
        }
        $content = I('post.content', '', 'trim');
        if (!$content) {
            $this->error(array('msg' => '翻译内容不能为空！'));
        }
        $obj = new TranslateSDK();
        $res = $obj->translate($content, 'zh', 'en');
        $res = $obj->translate($res['trans_result'][0]['dst'], 'en', 'zh');
        $this->success(array('msg' => '翻译成功', 'content' => $res['trans_result'][0]['dst']));
    }


}
