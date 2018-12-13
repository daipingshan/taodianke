<?php

namespace Touch\Controller;

use Common\Org\Http;

class IndexController extends CommonController {

    //  每页数据
    protected $page_size = 20;
    //  杨他她9.9包邮分类
    private $ytt_nine = 28;
    //  楚楚街商品特殊标识分类ID 1029
    private $ccj_cate_id = 1029;
    //  热销
    private $hot = 1000;

    /**
     * 获取用户的基本信息
     */
    public function callBackUrl() {
        if (isset($_GET['code']) && trim($_GET['code'])) {
            $code       = trim($_GET['code']);
            $state      = trim($_GET['state']);
            $app_id     = C('WEIXIN_BASE.app_id');
            $app_secret = C('WEIXIN_BASE.app_secret');
            $token_url  = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $app_id . '&secret=' . $app_secret . '&code=' . $code . '&grant_type=authorization_code';
            $http       = new Http();
            $res        = json_decode($http->get($token_url), true);
            if (isset($res['openid']) && $res['openid']) {
                $openid = $res['openid'];
                //  登陆用户的基本信息
                $user = M('wxuser')->where(array('openid' => $openid))->find();
                if ($user) {
                    //  代理id
                    $proxy_id = M('user')->where(array('pid' => $user['proxy_pid']))->getField('id');
                    session('proxy_id', $proxy_id);
                } else {
                    session('proxy_id', 1);
                }
                session('wx_user_openid', $openid);
                if ($state == 1) {
                    //  个人商城首页地址
                    $this->redirect('Index/index', array('uid' => session('proxy_id')));
                } elseif ($state == 2) {
                    //  直播间首页地址
                    $this->redirect('Index/zhiBoJian');
                } elseif ($state == 3) {
                    $this->redirect('Coupon/index');
                }
            }
        }
    }

    /**
     * 直播间首页地址
     */
    public function zhiBoJian() {
        $user_id = intval(session('proxy_id'));
        if ($user_id == 0) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
                $this->_WeChatLogin(2);
            } else {
                session('proxy_id', 1);
            }
        }
        $this->assign('proxy_id', session('proxy_id'));
        $this->display();
    }

    /**
     * 获取用户信息
     *
     * @param $uid
     * @return array
     */
    public function getUser() {
        $wxuser = M('wxuser')->where(array('openid' => session('wx_user_openid')))->field('id,nickname,headimgurl')->find();
        $this->ajaxReturn(array('code' => 0, 'user' => $wxuser, 'msg' => '用户数据获取成功'));
    }

    /**
     * 获取热销的产品
     *
     * @param $type
     * @return array
     */
    public function getHotGoods() {
        $proxy_id = session('proxy_id');
        if (!$proxy_id) {
            $proxy_id = 0;
        }
        $zbj_data = S('zbj_data');
        if (!$zbj_data) {
            $query                = " coupon_type:'5' ";
            $where['coupon_type'] = '5';
            $sort                 = array(array('key' => 'ordid', 'val' => 1), array('key' => 'id', 'val' => 0));
            if ($this->openSearchStatus == true) {
                $items_list = $this->_getOpenSearchList($query, $sort, null, 0, 50);
            } else {
                $order      = 'ordid asc, id desc';
                $items_list = M('items')->order($order)->limit(0, 50)->select();
            }
            $zbj_data = S('zbj_data', $items_list, 86400);
        }
        foreach ($zbj_data as &$val) {
            $val['url'] = U('Item/index', array('id' => $val['id'], 'uid' => $proxy_id, 'type' => 'tdk'));
        }
        if (!empty($zbj_data)) {
            $this->ajaxReturn(array('code' => 0, 'data' => $zbj_data));
        } else {
            $this->ajaxReturn(array('code' => -1, 'data' => ''));
        }
    }

    /**
     * 首页列表
     *
     * @param $uid
     * @param $page
     * @return array|false|mixed|\PDOStatement|string|\think\Collection
     */
    public function index() {
        $open_id     = session('wx_user_openid');
        $is_day_sign = 0;
        if (!$open_id) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
                $this->_WeChatLogin(1);
            }
        } else {
            $wx_user_id = intval(M('wxuser')->where(array('openid' => $open_id))->getField('id'));
            if ($wx_user_id) {
                $is_day_sign = M('coupon')->where(array('wxuser_id' => $wx_user_id, 'add_time' => array('egt', strtotime(date('Y-m-d')))))->count();
            }
        }
        $uid       = I('get.uid', 0, 'int');
        $p         = I('get.p', 1, 'int');
        $this->uid = $uid;
        $query     = '';
        $where     = array();
        $sort      = array(array('key' => 'ordid', 'val' => 1), array('key' => 'id', 'val' => 0));
        $start     = 0;
        if ($p > 1) {
            $start = $this->page_size * ($p - 1);
        }
        if ($this->openSearchStatus == true) {
            $items_list = $this->_getOpenSearchList($query, $sort, null, $start, $this->page_size);
        } else {
            $order      = 'ordid asc, id desc';
            $items_list = M('items')->where($where)->order($order)->limit($start . ',' . $this->page_size)->select();
        }
        foreach ($items_list as &$val) {
            $val['item_url'] = U('Item/index', array('id' => $val['id'], 'uid' => $uid, 'type' => 'tdk'));
        }
        if (IS_AJAX) {
            $this->ajaxReturn(array('code' => 1, 'data' => $items_list, 'msg' => 'ok'));
        }

        // 商品分类的数据
        $cate = $this->_getItemCate();

        $count   = M('items')->where($where)->count();
        $maxpage = ceil($count / $this->page_size);

        $this->assign('items_list', $items_list);
        $this->assign('ajaxurl', U('Index/index', array('p' => $p)));
        $this->assign('maxpage', $maxpage);
        $this->assign('cate', $cate);
        $this->assign('uid', $uid);
        $this->assign('version', $this->version);
        $this->assign('is_day_sign', $is_day_sign);
        $this->display();
    }

    /**
     * 获取分类列表页
     *
     * @param $key
     * @param $uid
     * @return mixed
     */
    public function getCateList() {
        $cate = I('get.cate', '', 'trim');
        $uid  = I('get.uid', 0, 'int');
        $p    = I('get.p', 1, 'int'); //页码

        $this->uid = $uid;

        $where = array('pass' => 1);
        $query = '';
        if ($cate == $this->ytt_nine) {
            $where['coupon_type'] = '4';
            $query                = "coupon_type:'4'";
        } elseif ($cate == $this->ccj_cate_id) {
            $where['shop_type'] = 'J';
            $query              = "shop_type:'J'";
        } elseif ($cate == $this->hot) {
            $where['coupon_type'] = '5';
            $query                = "coupon_type:'5'";
            $name                 = '热销';
        } else {
            if ($cate) {
                $where['cate_id'] = $cate;
                $query            = "cate_id:'{$cate}'";
            }
        }

        $sort  = array(array('key' => 'ordid', 'val' => 1), array('key' => 'id', 'val' => 0));
        $start = 0;
        if ($p > 1) {
            $start = $this->page_size * ($p - 1);
        }
        if ($this->openSearchStatus == true) {
            $items_list = $this->_getOpenSearchList($query, $sort, null, $start, $this->page_size);
        } else {
            $order      = 'ordid asc, id desc';
            $items_list = M('items')->where($where)->order($order)->limit($start . ',' . $this->page_size)->select();
        }
        foreach ($items_list as &$val) {
            $val['item_url'] = U('Item/index', array('id' => $val['id'], 'uid' => $uid, 'type' => 'tdk'));
        }
        if (IS_AJAX) {
            $this->ajaxReturn(array('code' => 1, 'data' => $items_list, 'msg' => 'ok'));
        }

        //  总共的页数
        $count   = M('items')->where($where)->count();
        $maxpage = ceil($count / $this->page_size);

        if (empty($name)) {
            $name = M('items_cate')->where(array('id' => $cate))->getField('name');
        }

        $this->assign('items_list', $items_list);
        $this->assign('ajaxurl', U('Index/getCateList', array('p' => 1)));
        $this->assign('uid', $uid);
        $this->assign('name', $name);
        $this->assign('maxpage', $maxpage);
        $this->assign('cate', $cate);
        $this->assign('version', $this->version);
        if ($cate == 28) {
            $act = 'two';
        } else if ($cate == 1000) {
            $act = 'three';
        } else {
            $act = 'one';
        }
        $this->assign('act', $act);
        $this->display();
    }

    /**
     * 用户签到
     */
    public function daySign() {
        if (!IS_AJAX) {
            $this->error('非法请求!');
        }
        if (!strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $this->error('该功能仅支持淘店客公众号下使用!');
        }
        $open_id = session('wx_user_openid');
        if (!$open_id) {
            $this->error('当前状态已失效，请刷新页面！');
        }
        $wx_user_id = intval(M('wxuser')->where(array('openid' => $open_id))->getField('id'));
        if ($wx_user_id == 0) {
            $this->error('用户信息不存在无法签到！');
        }
        $is_day_sign = M('coupon')->where(array('wxuser_id' => $wx_user_id, 'add_time' => array('egt', strtotime(date('Y-m-d')))))->count();
        if ($is_day_sign > 0) {
            $this->error('今天已经签过到了，请明天再来！');
        }
        $count = M('coupon')->where(array('wxuser_id' => $wx_user_id, 'status' => 0, 'end_time' => array('gt', time())))->count();
        if ($count == 0) {
            $num = rand(10, 20);
        } else if ($count == 1) {
            $num = rand(5, 40);
        } else {
            $num = rand(3, 100);
        }
        $rate            = C('WEIXIN_DAYSIGN.rate') ? : 10;
        $limit_money     = round($num / $rate * 10);
        $money           = sprintf("%.1f", ($num / 10));
        $coupon_num      = rand(10000000, 99999999);
        $is_check_coupon = M('coupon')->where(array('coupon_sn' => $coupon_num))->count();
        while ($is_check_coupon) {
            $coupon_num      = rand(10000000, 99999999);
            $is_check_coupon = M('coupon')->where(array('coupon_sn' => $coupon_num))->count();
        }
        $data = array(
            'wxuser_id'   => $wx_user_id,
            'coupon_sn'   => $coupon_num,
            'start_time'  => time(),
            'end_time'    => strtotime(date("Y-m-d", strtotime("+7 days"))) - 1,
            'money'       => $money,
            'limit_money' => $limit_money,
            'add_time'    => time(),
        );
        $res  = M('coupon')->add($data);
        if ($res) {
            $this->success(array('money' => $money, 'limit_money' => $limit_money));
        } else {
            $this->error('签到失败！');
        }
    }

}