<?php

namespace Fxapi\Controller;

class PublicController extends CommonController {

    protected $checkUser = false;

    /**
     * 用户登录
     */
    public function login() {
        $username = I('post.username', '', 'trim');
        $password = I('post.password', '', 'trim');
        $user = D('User');
        $res = array('error' => '');
        $this->_checkblank(array('username', 'password'));
        $res = $user->defaultLogin($username, $password);
        if (isset($res['error'])) {
            $this->_writeDBErrorLog($res, $user, 'api');
            $this->outPut(null, -1, null, ternary($res['error'], ''));
        }
        if (!$res) {
            $this->_writeDBErrorLog($res, $user, 'api');
            $this->outPut(null, 1022);
        }

        $uid = $res['id'];
        $token = $this->_createToken($uid);

        $data = array(
            'token' => $token,
        );

        $this->outPut($data, 0, null);
    }

    /**
     * 用户注册 手机号注册
     */
    public function regist() {
        $username = I('post.username', '', 'trim');
        $password = I('post.password', '', 'trim');
        $realname = I('post.realname', '', 'trim');
        $phone    = I('post.phone', '', 'trim');
        $address  = I('post.address', '', 'trim');
        $model = D('User');
        $error = [];
        $user = $model->where(['username'=>$username])->find();
        if ($user) {
            $error[] = '用户名已被占用！';
        } else {
            if (strlen($username) < 6) {
                $error[] = (count($error)+1).'.用户名太短！';
            } else if (strlen($username) > 16) {
                $error[] = (count($error)+1).'.用户名太长！';
            }
            if (strlen($password) < 6) {
                $error[] = (count($error)+1).'.密码太短！';
            } else if (strlen($password) > 16) {
                $error[] = (count($error)+1).'.密码太长！';
            }
            if (!preg_match('/^[\x80-\xff]{4,16}$/',$realname)) {
                $error[] = (count($error)+1).'.请输入2~8个汉字姓名！';
            }
            if (!checkMobile($phone)) {
                $error[] = (count($error)+1).'.手机号码错误！';
            }
            if ($address == '') {
                $error[] = (count($error)+1).'.地址不能为空！';
            } 
        }
        $data = [
            'username' => $username,
            'password' => encryptPwd($password),
            'realname' => $realname,
            'phone'    => $phone,
            'address'  => $address
        ];
        if (!empty($error)) {
            $this->outPut(null, -1, null, implode('|', $error));
        } else {
            $uid = $model->add($data);
            if (!$uid) {
                $error[] = '注册失败！';
            }
        }
        $this->outPut(['uid'=>$uid], 0, null, '注册成功');
    }
    public function  Getshop(){
        $seller_id = I('get.seller_id', '', 'trim');
        $activityid = I('get.activityid', '', 'trim');
        //https://market.m.taobao.com/apps/aliyx/coupon/detail.html?wh_weex=true&
        //$qurl = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $seller_id . "&activity_id=" . $activityid;
        $qurl = "https://market.m.taobao.com/apps/aliyx/coupon/detail.html?wh_weex=true&seller_id=" . $seller_id . "&activity_id=" . $activityid;

        //$qurl='';
        //$sources =file_get_contents($qurl);
        $Taobaoke = new \Common\Org\Taobaoke();
        $sources=$Taobaoke->taobaoke($qurl);
        $title = get_word($sources, '<title>', '<\/title>');

        $source = get_word($sources, '<div class="coupon-info">', '<\/div>');

        $quan = get_word($sources, '<dt>', '<\/dt>');

        $quantitle = $title . ' ' . $quan;

        $quantitle = mb_convert_encoding($quantitle, "GBK", "UTF-8");

        $quantitle = urlencode($quantitle);

        $info = array();

        $info['snum'] = get_word($source, '<span class="rest">', '<\/span>');

        $info['lnum'] = get_word($source, '<span class="count">', '<\/span>');

        $info['jprice'] = get_word($source, '<dt>', '元');

        $info['mprice'] = get_word($source, '单笔满', '元');

        $info['starttime'] = get_word($source, '有效期:', '至');

        $info['endtime'] = get_word($source, '至', '<\/dd>');

        $info['pcurl'] = "http://taoquan.taobao.com/coupon/unify_apply.htm?sellerId=" . $seller_id . "&activityId=" . $activityid;

        $info['qurl'] = "http://h5.m.taobao.com/ump/coupon/detail/index.html?sellerId=" . $seller_id . "&activityId=" . $activityid;

        $info['quan_url'] = "http://uland.taobao.com/coupon/edetail?activityId=" . $activityid . "&pid=" . $hightpid . "&itemId=" . $iid . "&src=cd_cdll";

        $this->outPut($info, 0, null);
    }

}
