<?php

namespace AppAdmin\Controller;

use Common\Controller\CommonBusinessController;

class CommonController extends CommonBusinessController {

    /**
     * @var int
     */
    protected $limit = 20;

    public $checkUser = true;

    // 自动登录
    public function _initialize() {
        //判断用户是否已经登录
        if ($this->checkUser) {
            if (!isset($_SESSION['auth_user'])) {
                redirect(U('/AppAdmin/login/'));
            } else {
                $this->assign('username', $_SESSION['username']);
                $this->assign('uid', $_SESSION['auth_user']);
            }
        }
        //缓存栏目
        $this->assign('nav_list_cache', S('nav_list' . $_SESSION['auth_user']));
        //系统管理员
        if ($_SESSION['role_id'] != 1) {
            $this->_check_admin_rote();
        }
        $controller_name = CONTROLLER_NAME;
        $action_name     = ACTION_NAME;
        $menu            = M('menu')->where(array('module_name' => $controller_name, 'action_name' => $action_name))->order('id desc')->find();
        $pid             = $menu['pid'] == 526 ? $menu['id'] : $menu['pid'];
        $this->assign('menu_pid', $pid);
    }

    private function _check_admin_rote() {
        $admin_id = $_SESSION['auth_user'];
        $role_id  = $_SESSION['role_id'];

        $menu                  = M('menu');
        $admin_auth            = M('admin_auth');
        $map['module_name']    = CONTROLLER_NAME;
        $map['action_name']    = ACTION_NAME;
        $map['taodianke_menu'] = 1;
        //访问栏目ID
        $menu_info                 = $menu->where($map)->find();
        $user_map['id']            = $admin_id;
        $admin_auth_map['role_id'] = $role_id;
        $admin_auth_map['menu_id'] = $menu_info['id'];
        //超管没有执行检查代码
        if ($menu_info['log'] == 1) {
            $this->addAdminLog();
        }
        //dump(__ACTION__ );
        //dump($admin_auth_map);
        if (!$admin_auth->where($admin_auth_map)->find()) {

            $this->error('无权限访问该页面', U('/AppAdmin/index/index'));
            exit();

        }
    }

    //log
    public function addAdminLog() {
        $data['ip']         = get_client_ip();
        $data['admin_id']   = $_SESSION['auth_user'];
        $data['user_name']  = $_SESSION['username'];
        $data['controller'] = CONTROLLER_NAME;
        $data['action']     = ACTION_NAME;
        $data['add_time']   = strtotime("now");

        foreach (I('param.') as $key => $value) {
            $data['log_info'] .= $key . '=>' . $value . ';';
        }

        if (!M('admin_log')->add($data)) {
            file_put_contents('/tmp/admin_log_error.log', var_export($data, true) . ' **  end', FILE_APPEND);
        }
    }

    /**
     * 清理文件缓存
     *
     * @param $key
     *
     * @return bool
     */
    protected function _clearData($key) {
        if (!$key) {
            return false;
        }
        $url  = "http://api.taodianke.com/HttpApi/clearData";
        $data = array('key' => $key);
        $obj  = new \Common\Org\Http();
        $obj->get($url, $data);
        return true;
    }

    /**
     * 获取部门列表
     */
    protected function _getDepartment() {
        $data = S('tdk_department');
        if (!$data) {
            //邮购招商部门总id
            $parent_id = 1;
            $res       = M('department')->field('id,department_name as name')->where(array('parent_id' => $parent_id))->select();
            $data      = array();
            foreach ($res as $val) {
                $data[$val['id']] = $val['name'];
            }
            S('tdk_department', $data);
        }
        return $data;
    }

    /**
     * 非ajax分页方法
     * totalRows  总数量
     * listRows   每页条数
     * map        查询条件
     *
     * @return string
     */
    protected function pages($totalRows, $listRows, $map = array(), $rollPage = 5) {
        $Page = new \Think\Page($totalRows, $listRows, '', MODULE_NAME . '/' . ACTION_NAME);
        if ($map && IS_POST) {
            foreach ($map as $key => $val) {
                $val = urlencode($val);
                $Page->parameter .= "$key=" . urlencode($val) . '&';
            }
        }
        if ($rollPage > 0) {
            $Page->rollPage = $rollPage;
        }
        $Page->setConfig('header', '条信息');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('last', '末页');
        $Page->setConfig(
            'theme', '<div style="float: left;margin: 15px 0;"><p>当前页可显示' . $listRows . '条数据 实际共有%TOTAL_ROW% %HEADER%</p></div><div style="float: right"><ul class=pagination><li>%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</li></ul></div>'
        );
        return $Page;
    }

    /**
     * 获取企业级账号信息
     *
     * @return array|mixed|string|void
     */
    protected function _getProxy() {
        $data = S('tdk_proxy');
        if (!$data) {
            $res  = M('proxy')->field('id,name')->select();
            $data = array();
            foreach ($res as $val) {
                $data[$val['id']] = $val['name'];
            }
            S('tdk_proxy', $data);
        }
        return $data;
    }

    /**
     * 极光推送
     *
     * @param string $alert        要推送的内容
     * @param        string        or array $audience 推送设备对象  要发广播可直接设置为字符串 all 也可以是别名、标签、注册ID、分群、广播等
     * @param array $extras        扩展字段，键值对
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

    /**
     * 异步获取部门下的管理员
     */
    public function ajaxAdminUser() {
        $department_id = I('get.department_id', 0, 'int');
        $data          = M('admin')->field('id,full_name as name')->where(array('department_id' => $department_id))->select();
        $this->ajaxReturn(array('status' => 0, 'data' => $data));
    }

    /**
     * 上传图片到本地
     */
    public function uploadImgLocal() {
        $domain_url       = C('web_url');
        $local_path       = '/Uploads/';
        $upload           = new \Think\Upload();
        $upload->autoSub  = false;
        $upload->replace  = true;
        $upload->saveName = 'wxqrcode_bg';
        $res              = $upload->upload();

        if (!$res) {// 上传错误提示错误信息
            $this->ajaxReturn(array('code' => 1, 'url' => '', 'msg' => '图片上传失败'));
        }
        $this->ajaxReturn(array('code' => 0, 'url' => $domain_url . $local_path . $res['img']['savename'], 'msg' => '图片上传成功'));
    }

}