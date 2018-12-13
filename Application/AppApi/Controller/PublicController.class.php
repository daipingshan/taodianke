<?php

namespace AppApi\Controller;

class PublicController extends CommonController {

    /**
     * @var bool
     */
    protected $checkUser = false;

    /**
     * 用户登录
     */
    public function login() {
        $username = I('post.username', '', 'trim');
        $password = I('post.password', '', 'trim');
        $user     = D('AppApi/User');
        $this->_checkblank(array('username', 'password'));
        $res = $user->login($username, $password);
        if (isset($res['error'])) {
            $this->outPut(null, -1, null, ternary($res['error'], ''));
        }

        if (empty($res['pid']) && $this->version < 3) {
            $this->outPut(null, -2, null, '你的账户尚未认证，请联系客服完成认证。 客服电话 4000 998 433');
        }

        if ((empty($res['bank_account']) || empty($res['real_name'])) && $this->version < 3) {
            $this->outPut(null, -1, null, '您的提现信息不全！请联系管理员补充');
        }

        $uid = $res['id'];

        $token = $this->_createToken($uid);
        $where = array(
            'mobile' => trim($username),
        );
        M('user')->where($where)->save(array('token' => $token));
        $data = array(
            'token' => $token,
        );
        S('tdk_' . $res['token'], null);
        $this->outPut($data, 0);

    }

    /**
     *   发送短信的接口
     *   key  1  注册   2  忘记密码
     *
     * @param  mobile   手机号
     */
    public function sendCode() {
        $key    = I('post.key', '', 'trim');
        $mobile = I('post.mobile', '', 'trim');

        if ('1' == $key) {
            $id = M('user')->where(array('mobile' => $mobile))->getField('id');
            if ($id) {
                $this->outPut(null, -1, null, '你的号码已经注册，请重新输入!');
            }
        } else if ('2' == $key) {
            $id = M('user')->where(array('mobile' => $mobile))->getField('id');
            if (!$id) {
                $this->outPut(null, -1, null, '你的号码尚未注册!');
            }
        }

        $code = rand(1000, 9999);
        $res  = array(
            'mobile'      => $mobile,
            'create_time' => time(),
            'code'        => $code,
            'action'      => 'register',
        );

        $sms_id = M('sms')->where(array('mobile' => $mobile))->find();
        if ($sms_id) {
            $res['num'] = $sms_id['num'] + 1;
            M('sms')->where(array('mobile' => $mobile))->save($res);
        } else {
            $res['num'] = '0';
            M('sms')->add($res);
        }
        $sms  = new \Common\Org\sendSms();
        $data = $sms->sendMsg($mobile, "您的短信验证码为:{$code}");
        if ($data) {
            $data = getPromptMessage('发送成功', 'success', 1);
            $this->outPut($data, 0);
        } else {
            $this->outPut(null, -1, null, '发送失败,请稍后重试!');
        }
    }

    /**
     *   注册登陆接口
     *
     * @param   key        1，注册 2，忘记密码
     * @param   mobile     手机号码
     * @param   code       验证码
     * @param   password   密码
     * @param   compass    重复密码
     */
    public function registerForget() {
        $param = array(
            'key'      => I('post.key', '', 'trim'),
            'mobile'   => I('post.mobile', '', 'trim'),
            'referee'  => I('post.referee', '', 'trim'),
            'code'     => I('post.code', '', 'trim'),
            'password' => I('post.password', '', 'trim'),
            'compass'  => I('post.compass', '', 'trim'),
        );

        $code = M('sms')->where(array('mobile' => $param['mobile']))->getField('code');
        // 先判断验证码对不对
        if ($code != $param['code']) {
            $this->outPut(null, -1, null, '验证码有误，请重新获取验证码!');
        }

        $user = M('user');
        $info = $user->where(array('mobile' => $param['mobile']))->find();
        // 判断key值
        if ($param['key'] == '1') {
            // 注册
            if ($info) {
                $this->outPut(null, -1, null, '用户已经存在，您可以去登录！');
            } else {
                if ($param['password']) {
                    $data  = array(
                        'mobile'   => $param['mobile'],
                        'password' => md5($param['password']),
                        'referee'  => $param['referee'],
                        'reg_time' => time()
                    );
                    $users = $user->add($data);
                    if ($users) {
                        $this->outPut($users, 0);
                    } else {
                        $this->outPut(null, -1, null, '注册失败，请稍后重试!');
                    }
                }
            }
        } elseif ($param['key'] == '2') {
            // 忘记密码
            if ($info) {
                if ($param['password'] == $param['compass']) {
                    $data  = array('password' => md5($param['password']));
                    $users = $user->where(array('mobile' => $param['mobile']))->save($data);
                    if ($users) {
                        $this->outPut($users, 0);
                    } else {
                        $this->outPut(null, -1, null, '密码重置失败，请稍后重试!');
                    }
                }
            } else {
                $this->outPut(null, -1, null, '该手机号尚未注册，建议您注册新用户!');
            }
        } else {
            $this->outPut(null, -1, null, '非法操作，请重试！');
        }
    }

    /**
     * 用户未授权
     */
    public function auth() {
        $this->assign('act', 'auth');
        $this->display();
    }

    /**
     * 动态启动加载页面
     */
    public function appLoadInit() {
        $notice         = M('article')->field('id,title,info,add_time')->where(array('cate_id' => 1, 'status' => 1))->order('id desc')->find();
        $notice['info'] = strip_tags(html_entity_decode(htmlspecialchars_decode($notice['info'])));
        $notice['url']  = C('api_domain_url') . U('/Share/info', array('id' => $notice['id']));
        $data           = array('url' => '', 'md5_file' => '', 'action_url' => '', 'new_notice' => $notice, 'topic_url' => C('wechat_mp_domain_url') . U('/Topic/detail'));
        $img            = $this->_getAdImg(16);
        shuffle($img);
        $load_image     = $img[0];
        if ($load_image && $load_image['img']) {
            $data['url']        = $load_image['img'];
            $data['md5_file']   = md5_file($load_image['img']);
            $data['action_url'] = $load_image['url'];
        }
        $this->outPut($data, 0);
    }

    /***
     * 检测更新
     */
    public function checkUpdate() {
        $plat    = I('get.plat', '', 'trim');
        $app_ver = I('get.ver', '', 'trim');
        $config  = C('AppUpdateAndroid');
        if ($plat == 'ios') {
            $config = C('AppUpdateIos');
        }
        $service_ver = ternary($config['version'], '');
        $is_upgrade  = 'N';
        if ($app_ver && strcmp($service_ver, $app_ver) > 0) {
            $is_upgrade = 'Y';
        }
        $data = array(
            'version'      => $service_ver,
            'is_force'     => ternary($config['is_force'], ''),
            'is_upgrade'   => $is_upgrade,
            'description'  => ternary($config['description'], ''),
            'download_url' => ternary($config['url'], ''),
        );
        $this->outPut($data, 0);
    }

    /**
     * 用户注册协议
     */
    public function registerAgreement() {
        $this->display();
    }
}
