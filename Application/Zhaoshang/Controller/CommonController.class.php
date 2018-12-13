<?php

namespace Zhaoshang\Controller;

use Common\Controller\CommonBusinessController;

/**
 * Class CommonAction
 */
class CommonController extends CommonBusinessController {
 
    protected $user = null;
    protected $uid = null;
    protected $reqnum = 20;
    protected $checkUser = true;
    
    // 构造方法
    public function __construct() {
        parent::__construct();
        
        // 同步提交错误统一处理
        $this->__initMessage(); 
        
        // 验证是否登录
        if($this->checkUser){
             $this->check_user_login();
        }
       
    }
    
    private function check_user_login(){
        $this->user = session(C('SAVE_USER_KEY'));        
        if (is_null($this->user)) {
            //跳转到认证网关
            redirect(U(C('USER_AUTH_GATEWAY')));
        }
        $this->uid = ternary($this->user['id'], '');
    }

    private function __initMessage() {
        $uid = md5($this->_getUserId());
        $error = cookie('error_' . $uid);
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
        
        
        $module_name = strtolower(MODULE_NAME);
        $controller_name = strtolower(CONTROLLER_NAME);
        $action_name = strtolower(ACTION_NAME);
        $uri = "$module_name/$controller_name/$action_name";
        // 无权限 无登录r认证的url
        if(isset($this->auth_config['NO_AUTH_NO_LOGIN_URI'][$uri])){
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
            $user = session(C('SAVE_USER_KEY'));
            $this->uid = ternary($user['id'], '');
        }
        return $this->uid;
    }

    protected function redirect_message($url, $data = array()) {
        $uid = md5($this->_getUserId());
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
     * @param $size
     * @param string $unit
     * @param int $decimals
     *
     * @return string
     */
    protected function _byteFormat($size,$unit='B',$decimals = 2){
        $units = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);

        $value = 0;
        if ($size > 0) {
            if (!array_key_exists($unit, $units)) {
                $pow = floor(log($size)/log(1024));
                $unit = array_search($pow, $units);
            }
            $value = ($size/pow(1024,floor($units[$unit])));
        }

        if (!is_numeric($decimals) || $decimals < 0) {
            $decimals = 2;
        }
        return sprintf('%.' . $decimals . 'f '.$unit, $value);
    }

    /**
     * 输出函数，如果启用debug,输出日志
     *
     * @param array  $result     输出数据
     * @param int    $errCode    错误code
     * @param string $proType    处理类型
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
        $data = $this->_checkData($data);
        $this->data = $data;
    }

    /**
     * 设置错误信息
     *
     * @param int    $error      错误code
     * @param string $proType    处理类型
     */
    private function setError($error = 0, $proType = null, $msg = '') {
        $this->code = $error;
        $this->msg = $this->ef->getErrMsg($error, $proType) . $msg;
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
}
