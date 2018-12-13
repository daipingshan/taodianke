<?php

namespace Admin\Controller;

/**
 * 公共类
 */
class PublicController extends CommonController {

    /**
     * @var bool 是否验证uid
     */
    protected $checkUser = false;

    public function index() {

        redirect(U('Public/login'));
    }

    public function login() {
        $ip  = get_client_ip();
        $ipp = $ip;
        $ipc = explode('.', $ip);
        $ipd = $ipc[0] . '.' . $ipc[1] . '.' . $ipc[2];
        $t   = time();
        $t   = date("Y-m-d H:i:s", $t);

        if (IS_POST) {
            $username = I('post.username', '', 'trim');
            $password = I('post.password', '', 'trim');
            if ($ipd == '49.221.62' || $ipd == '1.86.244' || $ipd == ' 36.47.137' || $ipd == '113.134.75' || $username == 'admin' || $ipd == '183.1.103' || $ipd == '183.1.102' || $ipd == '125.89.209' || $username == '可儿' || $username == '杨丹' || $username == '果1果1' || $username == '果1果2' || $username == '果1果3' || $username == '程若曦' || $username == '林萍' || $username == '黑娃' || $username == '小芳' ) {

            } else {
                file_put_contents('/tmp/temai.log', $t . var_export(I('post.username', '', 'trim') . $ipp, true) . '||' . $ip, FILE_APPEND);
                $this->redirect_message(U('Public/login'), array('error' => '此账户不能在外网登陆！'));
            }

            if (!$username) {
                $this->redirect_message(U('Public/login'), array('error' => '用户名不能为空！'));
            }
            if (!$password) {
                $this->redirect_message(U('Public/login'), array('error' => '密码不能为空！'));
            }

            $map = array(
                'name'     => $username,
                'password' => encryptPwd($password)
            );

            $res = M('tmuser')->field('id,name,pid,sale_account_ids,top_line_account_ids,group_id')->where($map)->find();
            if (!$res) {
                $this->redirect_message(U('Public/login'), array('error' => '用户名或密码错误！'));

            }
            if ($res['pid']) {
                $this->pid = $res['pid'];
            }
            session(C('SAVE_USER_KEY'), $res);

            redirect(U('Index/index'));
            die();

        }
        $this->display();
    }

    public function logout() {
        session_destroy();
        redirect(U('Public/login'));
    }

    /**
     * 环信测试
     */
    public function test_easemob() {
        $ease_mob = new \Common\Org\Easemob();
        $res      = $ease_mob->send_message_txt('merchant_1', 'user_4', '欢迎来到O(∩_∩)O哈哈哈~');
        var_dump($res);
    }

    /***
     *
     *
     *
     */
    public function NewUpdateorder() {
        $goods = I('post.josn');//标题
        $type  = I('post.type');//标题
        file_put_contents('/tmp/fxg.log', var_export($type, true) . '||', FILE_APPEND);
        $goods     = base64_decode(str_replace(array('%2b', ' '), '+', urldecode($goods)));
        $goods_arr = @json_decode($goods, true);
        file_put_contents('/tmp/fxg.log', var_export($goods_arr, true) . '||', FILE_APPEND);
        if (is_array($goods_arr)) {
            $data = $goods_arr;
        }
        //unset($data[0]);
        $data = array_values($data);
        $jgg  = '';
        foreach ($data as $ke => $vad) {

            $jgg .= self::NewUpdate($vad, $type);

        }
        $this->outPut(null, 0, null, $jgg);
    }

    /***
     *
     *
     */
    public function Updateorder() {
        $goods = I('post.josn');//标题
        $type  = I('post.type');//标题
        file_put_contents('/tmp/fxg.log', var_export($type, true) . '||', FILE_APPEND);
        $goods     = base64_decode(str_replace(array('%2b', ' '), '+', urldecode($goods)));
        $goods_arr = @json_decode($goods, true);
        file_put_contents('/tmp/fxg.log', var_export($goods_arr, true) . '||', FILE_APPEND);
        if (is_array($goods_arr)) {
            $data = $goods_arr;
        }
        //unset($data[0]);
        $data = array_values($data);
        $jgg  = '';
        foreach ($data as $ke => $vad) {

            $jgg .= self::Update($vad, $type);

        }
        $this->outPut(null, 0, null, $jgg);
    }

    public function Update($date, $type) {
        usleep(50000);
        $order_id             = $date['order_id'];//I('post.total_fee');//付款金额
        $model                = M('fxg');
        $where['order_id']    = $order_id;
        $data['order_status'] = '订单退款';

        $res = $model->where($where)->save($data);
        if ($res) {
            return date("m-d H:i:s") . "-" . $order_id . "更新成功 \r\n";
            //$this->outPut(null, 0, null, '更新成功');
        } else {

            return date("m-d H:i:s") . "-" . $order_id . "操作失败 \r\n";
        }
    }

    /**
     * @param $date
     * @param $type
     * @return string
     */
    public function NewUpdate($date, $type) {
        usleep(50000);
        $pay_time               = $date['pay_time'];//I('post.order_id');//订单编号
        $order_status           = $date['order_status'];//I('post.title');//标题
        $commodity_info         = $date['commodity_info'];//I('post.itemid');//商品id
        $goods_id               = get_word($commodity_info, 'id=', '&');
        $order_source           = $date['order_source'];//I('post.discount_rate');//收入比率
        $order_money            = $date['order_money'];//I('post.share_rate');//分成比率
        $profit_percent         = $date['profit_percent'];//I('post.fee');//效果评估
        $income                 = $date['income'];
        $complete_time          = $date['complete_time'];//I('post.price');//商品单价
        $author_id              = $date['author_id'];//I('post.number');//数量
        $order_id               = $date['order_id'];//I('post.total_fee');//付款金额
        $commodity_name         = $date['commodity_name'];//I('post.create_time');//订单创建时间
        $article_title          = $date['article_title'];//I('post.click_time');//订单单击时间
        $author_settle          = $date['author_settle'];//I('post.payStatus');//订单状态（订单支付 订单失效 订单结算）
        $author_income          = $date['author_income'];//I('post.order_type');//商品类型 （天猫 淘宝）
        $model                  = M('fxg');
        $where['order_id']      = $order_id;
        $data['pay_time']       = $pay_time;
        $data['order_status']   = $order_status;
        $data['commodity_info'] = $commodity_info;
        $data['order_source']   = $order_source;
        $data['order_money']    = $order_money;
        $data['profit_percent'] = $profit_percent;
        $data['income']         = $income;
        $data['complete_time']  = $complete_time;
        $data['author_id']      = $author_id;

        $data['commodity_name'] = $commodity_name;
        $data['article_title']  = $article_title;
        $data['author_settle']  = $author_settle;
        $data['author_income']  = $author_income;
        $data['goods_id']       = $goods_id;
        // 增加代理pid
        $name = M('tmnews')->where(array('title' => $article_title))->find();
        if ($name) {
            $data['name']    = $name['name'];
            $data['news_id'] = $name['id'];
            $data['user_id'] = $name['user_id'];
        } else {
            //$data['name']='';
        }
        //file_put_contents('/tmp/taobaoke.log',var_export($data, true).'||',FILE_APPEND);

        $res = $model->where($where)->save($data);
        if ($res) {
            return date("m-d H:i:s") . "-" . $order_id . "更新成功 \r\n";
            //$this->outPut(null, 0, null, '更新成功');
        } else {
            $resd = $model->where($where)->find();
            if ($resd) {
                return date("m-d H:i:s") . "-" . $order_id . "成功数据存在\r\n";
                //$this->outPut(null, 0, null, '成功数据存在');
            } else {
                $data['order_id'] = $order_id;
                $ress             = $model->add($data);
                if ($ress) {
                    return date("m-d H:i:s") . "-" . $order_id . "插入成功\r\n";
                    //$this->outPut(null, 0, null, '插入成功');
                }
            }

        }
    }

    public function InsertProduct() {
        $goods     = I('post.josn');//标题
        $type      = I('post.type');//标题
        $goods     = base64_decode(str_replace(array('%2b', ' '), '+', urldecode($goods)));
        $goods_arr = @json_decode($goods, true);
        if (is_array($goods_arr)) {
            $data = $goods_arr;
        }
        $data = array_values($data);
        //file_put_contents('/tmp/fxg.log',var_export($type, true).'||',FILE_APPEND);
        foreach ($data as $ke => $vad) {
            $date[$ke]['platform_sku_id'] = $vad['platform_sku_id'];
            $date[$ke]['sku_url']         = $vad['sku_url'];
            $date[$ke]['sku_title']       = $vad['sku_title'];
            $date[$ke]['sku_price']       = $vad['sku_price'];
            $date[$ke]['figure']          = $vad['figure'];
            $date[$ke]['shop_name']       = $vad['shop_name'];
            $date[$ke]['shop_url']        = $vad['shop_url'];
            $date[$ke]['month_sell_num']  = $vad['month_sell_num'];
            $date[$ke]['cos_fee']         = $vad['cos_info']['cos_fee'];
            $date[$ke]['cos_ratio']       = $vad['cos_info']['cos_ratio'];
        }
        $model = M('product');
        //file_put_contents('/tmp/fxg.log',var_export($date, true).'||',FILE_APPEND);
        if ($type == 0) {
            $jgg = $model->where('1')->delete();
        }
        $jgg = $model->addAll($date);
        $this->outPut(null, 0, null, $jgg);
    }
    public function cs() {
        $goods = 'W4ZLl8fU%2BuMegUbSVEg5pv7u9yExvfwu2%2BJyJ9J1Op5jDAexSJH486buv0Bra8PJe5DPMLUNoXCVnBXbA71Nc8gGbnP8Qhxnpp6hNZgqGx%2BCG%2BR3afeJGffYT7jIhkqMZkWXaluqarYUaR4uC8JSmEZTcdKS8oQPO3SV33DYvIycmghk3LHrO5q1ZL87aNIsO%2FmhnanMb7MmWhsvQfs6EJ8k8AMXWsOlS0RL5P%2Fkj0sp83O1XQSZujjv3vdLXU5k1Thl%2Brux%2Babcv1wy8SOZC77Xma1hK%2F9X4n2greaTJVOP%2F9L6sgCPkmwr8I2YjuRIvuIwE2cMapDYteBcRUXmOoQCZUy2A9U3';
        $goods     = base64_decode(str_replace(array('%2b', ' '), '+', urldecode($goods)));
        var_dump($goods);exit;
        $goods_arr = @json_decode($goods, true);

        if (is_array($goods_arr)) {
            $data = $goods_arr;
        }
        //unset($data[0]);
        $data = array_values($data);

    }
}

