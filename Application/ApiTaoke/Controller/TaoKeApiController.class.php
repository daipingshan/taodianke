<?php
/**
 * Created by PhpStorm.
 * User: superegoliu
 * Date: 2016/12/13
 * Time: 15:15
 */

namespace ApiTaoke\Controller;


class TaoKeApiController extends CommonController {
    protected $checkUser = false;

    public function index() {
        $time  = time() - 8600;
        $model = M('items');
        $where = "UNIX_TIMESTAMP(endtime)< {$time} and isq>0 and (pass=1 or pass=0) and mobile=''";
        $count = $model->where($where)->field('endtime,id')->select();
        var_dump($count);
        exit;
        $item['endtime'] = '2017-03-10';
        $shi             = substr($item['endtime'], 0, 10);
        $fen             = substr($item['endtime'], 10, 8);
        echo $shi . ' ' . $fen;
        exit;
        $model = M('items');
        $list  = $model->order('id desc')->limit(0, 20)->select();

        $this->outPut($list, 0, null);
    }

    //获取自己提报的券号
    public function Gettbjuan() {
        $map['qq']       = array('neq', '');
        $map['mobile']   = array('neq', '');
        $map['qurl']     = array('neq', '');
        $map['pass']     = array('eq', '1');
        $map['passname'] = '已通过';
        $model           = M('items');
        $items           = $model->where($map)->field('id,num_iid,qurl,endtime')->select();
        if ($items) {
            $this->outPut($items, 0, null);
        } else {
            $this->outPut(null, -1, null, '无团单');
        }
    }

    //获取券号
    public function GetDatequan() {
        $time          = time();
        $map['isq']    = array('gt', 0);
        $map['pass']   = 1;
        $map['qq']     = array('eq', '');
        $map['mobile'] = array('eq', '');
        //UNIX_TIMESTAMP()
        $model  = M('items');
        $where  = "UNIX_TIMESTAMP(endtime)>= {$time} and isq>0 and (pass=1 or pass=0) and mobile=''";
        $count  = $model->where($where)->count();
        $offset = I('param.offset', 0, 'intval');
        $limit  = I('param.limit', 20000, 'intval');
        $id     = I('param.id', 0, 'intval');
        if ($id) {
            $map['id'] = array('lt', $id);
        }
        $items = $model->where($where)->field('id,num_iid,qurl,endtime')->order('id desc')->limit($offset, $limit)->select();
        $data  = [
            'list'   => $items,
            'offset' => $offset,
            'limit'  => $limit,
            'count'  => $count
        ];
        if ($items) {
            $this->outPut($data, 0, null);
        } else {
            $this->outPut(null, -1, null, '无团单');
        }
    }

    /***
     *
     * 获取过期券号
     */
    public function GetGuoqiquan() {
        $time          = time() - 8600;
        $map['isq']    = array('gt', 0);
        $map['pass']   = 1;
        $map['qq']     = array('eq', '');
        $map['mobile'] = array('eq', '');
        //UNIX_TIMESTAMP()
        $model = M('items');
        $where = "UNIX_TIMESTAMP(endtime)< {$time} and isq>0 and (pass=1 or pass=0) and mobile=''";
        $count = $model->where($where)->delete();
        if ($count) {
            $this->outPut(null, 0, null, '过期券号删除成功' . $count . '个券号删除');
        } else {
            $this->outPut(null, -1, null, '过期券号删除失败');
        }
    }

    //更新券号数量
    public function UpdateQuanhao() {
        $goods     = I('post.quan'); //券号格式json
        $goods_arr = @json_decode($goods, true);
        if ($goods_arr === null) {
            $goods     = base64_decode(str_replace(array('%2b', ' '), '+', urldecode($goods)));
            $goods_arr = @json_decode($goods, true);
        }
    }

    //单个券号更新
    public function Updatequn() {
        $id   = I('post.id');//编号id
        $iid  = I('post.idd');//商品id
        $snum = I('post.snum');//剩余多少张券号
        $lnum = I('post.lnum');//已领券号数量
        //$quan=I('post.quan');//卷号地址
        $jprice    = I('post.jprice');//券号金额
        $mprice    = I('post.mprice');//满多少钱可用
        $endtime   = I('post.endtime');//卷号结束时间
        $starttime = I('post.starttime');//卷号开始时间
        //file_put_contents('/tmp/taobaoke.log',var_export($_POST, true).'||',FILE_APPEND);
        //$this->outPut(null, 0, null, '更新成功');
        $model = M('items');
        if ($iid && $snum) {
            $itme['snum'] = $snum;
            $itme['lnum'] = $lnum;
            //$itme['quan'] = $quan;
            $itme['mprice']    = $mprice;
            $itme['starttime'] = $starttime;
            $itme['endtime']   = $endtime;
            $where['id']       = $id;
            $where['num_iid']  = $iid;
            $res               = $model->where($where)->save($itme);
            //file_put_contents('/tmp/taobaoke.log',var_export($model->getLastSql(), true).'||',FILE_APPEND);
            if ($res) {
                $this->outPut(null, 0, null, '更新券号成功');
            } else {
                $this->outPut(null, -1, null, '更新券号失败');
            }
        } else {
            $this->outPut(null, -1, null, '缺少参数');
        }
    }

    public function Updatesxquanho() {
        $id                = I('post.id');//编号id
        $iid               = I('post.idd');//商品id
        $data              = array();
        $data['pass']      = 2;
        $data['passname']  = '待结算';
        $model             = M('items');
        $where['num_iid']  = $iid;
        $where['id']       = $id;
        $where['pass']     = 1;
        $where['passname'] = '已通过';
        $res               = $model->where($where)->save($data);
        //file_put_contents('/tmp/taobaoke.log',var_export($model->getLastSql(), true).'||',FILE_APPEND);
        if ($res) {
            $this->outPut(null, 0, null, '更新成功');
        } else {
            $this->outPut(null, -1, null, '更新失败');
        }
    }

    /***
     *
     *
     *
     * 更新第三方券号
     */
    public function Updatedsquanho() {
        $id               = I('post.id');//编号id
        $iid              = I('post.idd');//商品id
        $data             = array();
        $data['tk']       = 0;
        $data['pass']     = 0;
        $data['status']   = 'noisq';
        $model            = M('items');
        $where['num_iid'] = $iid;
        $where['id']      = $id;
        $res              = $model->where($where)->save($data);
        //file_put_contents('/tmp/taobaoke.log',var_export($model->getLastSql(), true).'||',FILE_APPEND);
        if ($res) {
            $this->outPut(null, 0, null, '更新成功');
        } else {
            $this->outPut(null, -1, null, '更新失败');
        }
    }

    /**跟新订单
     *
     *
     *
     */
    public function Updateorder() {
        $order_id              = I('post.order_id');//订单编号
        $title                 = I('post.title');//标题
        $itemid                = I('post.itemid');//商品id
        $discount_rate         = I('post.discount_rate');//收入比率
        $share_rate            = I('post.share_rate');//分成比率
        $fee                   = I('post.fee');//效果评估
        $price                 = I('post.price');//商品单价
        $number                = I('post.number');//数量
        $total_fee             = I('post.total_fee');//付款金额
        $create_time           = I('post.create_time');//订单创建时间
        $click_time            = I('post.click_time');//订单单击时间
        $payStatus             = I('post.payStatus');//订单状态（订单支付 订单失效 订单结算）
        $order_type            = I('post.order_type');//商品类型 （天猫 淘宝）
        $auctionUrl            = I('post.auctionUrl');//商品地址
        $earningTime           = I('post.earningTime');//结算时间
        $img                   = I('post.img');//图片地址
        $pid                   = I('post.pid');//结算时间
        $model                 = M('order_data');
        $where['order_id']     = $order_id;
        $where['itemid']       = $itemid;
        $data['title']         = $title;
        $data['itemid']        = $itemid;
        $data['discount_rate'] = $discount_rate;
        $data['share_rate']    = $share_rate;
        $data['fee']           = $fee;
        $data['price']         = $price;
        $data['number']        = $number;
        $data['total_fee']     = $total_fee;
        $data['create_time']   = $create_time;
        $data['click_time']    = $click_time;
        $data['payStatus']     = $payStatus;
        $data['order_type']    = $order_type;
        $data['auctionUrl']    = $auctionUrl;
        $data['earningTime']   = $earningTime;
        $data['pid']           = C('PID') . $pid;
        if ($img) {
            $data['img'] = $img;
        }
        $res = $model->where($where)->save($data);
        if ($res) {
            $this->outPut(null, 0, null, '更新成功');
        } else {
            $resd = $model->where($where)->find();
            if ($resd) {
                $this->outPut(null, 0, null, '成功数据存在');
            } else {
                $data['order_id'] = $order_id;
                $ress             = $model->add($data);
                if ($ress) {
                    $this->outPut(null, 0, null, '插入成功');
                }
            }

        }
    }

    /***
     *
     *
     *
     */
    public function NewUpdateorder() {
        $goods     = I('post.josn');//标题
        $goods     = base64_decode(str_replace(array('%2b', ' '), '+', urldecode($goods)));
        $goods_arr = @json_decode($goods, true);
        if (is_array($goods_arr)) {
            $data = $goods_arr['dingdan'];
        }
        unset($data[0]);
        $data = array_values($data);
        $jgg  = '';
        foreach ($data as $ke => $vad) {

            $jgg .= self::NewUpdate($vad);

        }
        $this->outPut(null, 0, null, $jgg);
    }

    public function NewUpdate($date) {
        usleep(50000);
        $order_id      = $date['F25'];//I('post.order_id');//订单编号
        $title         = $date['F3'];//I('post.title');//标题
        $itemid        = $date['F4'];//I('post.itemid');//商品id
        $discount_rate = $date['F11'];//I('post.discount_rate');//收入比率
        $share_rate    = $date['F12'];//I('post.share_rate');//分成比率
        $fee           = $date['F14'];//I('post.fee');//效果评估
        $price         = $date['F8'];//I('post.price');//商品单价
        $number        = $date['F7'];//I('post.number');//数量
        $total_fee     = $date['F13'];//I('post.total_fee');//付款金额
        $create_time   = $date['F1'];//I('post.create_time');//订单创建时间
        $click_time    = $date['F2'];//I('post.click_time');//订单单击时间
        $payStatus     = $date['F9'];//I('post.payStatus');//订单状态（订单支付 订单失效 订单结算）
        $order_type    = $date['F10'];//I('post.order_type');//商品类型 （天猫 淘宝）
        if ($order_type == '天猫') {
            $auctionUrl = 'https://detail.tmall.com/item.htm?id' . $itemid;//I('post.auctionUrl');//商品地址
        } else {
            $auctionUrl = 'https://item.taobao.com/item.htm?id' . $itemid;//I('post.auctionUrl');//商品地址
        }

        $earningTime           = $date['F17'];//I('post.earningTime');//结算时间
        $img                   = '';//I('post.img');//图片地址
        $pid                   = $date['F27'] . '_' . $date['F29'];//I('post.pid');//结算时间
        $model                 = M('order_data');
        $where['order_id']     = $order_id;
        $where['itemid']       = $itemid;
        $data['title']         = $title;
        $data['itemid']        = $itemid;
        $data['discount_rate'] = $discount_rate;
        $data['share_rate']    = $share_rate;
        $data['fee']           = $fee;
        $data['price']         = $price;
        $data['number']        = $number;
        $data['total_fee']     = $total_fee;
        $data['create_time']   = $create_time;
        $data['click_time']    = $click_time;
        $data['payStatus']     = $payStatus;
        $data['order_type']    = $order_type;
        $data['auctionUrl']    = $auctionUrl;
        $data['earningTime']   = $earningTime;
        $data['pid']           = C('PID') . $pid;
        if ($img) {
            $data['img'] = $img;
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

    /***
     * 更新结算订单信息
     *
     *
     *
     */
    public function Updatejsorder() {
        $order_id            = I('post.order_id');//订单编号
        $itemid              = I('post.itemid');//商品id
        $payStatus           = I('post.payStatus');//订单状态（订单支付 订单失效 订单结算）
        $earningTime         = I('post.earningTime');//结算时间
        $model               = M('order_data');
        $where['order_id']   = $order_id;
        $where['itemid']     = $itemid;
        $data['payStatus']   = $payStatus;
        $data['earningTime'] = $earningTime;
        $res                 = $model->where($where)->save($data);
        if ($res) {
            $this->outPut(null, 0, null, '更新成功');
        } else {
            $this->outPut(null, 0, null, '状态未变化');
        }
    }

    /**
     *
     * 更新失效订单
     */
    public function Updatesxorder() {
        $order_id          = I('post.order_id');//订单编号
        $itemid            = I('post.itemid');//商品id
        $payStatus         = I('post.payStatus');//订单状态（订单支付 订单失效 订单结算）
        $model             = M('order_data1');
        $where['order_id'] = $order_id;
        $where['itemid']   = $itemid;
        $data['payStatus'] = $payStatus;
        $res               = $model->where($where)->save($data);
        if ($res) {
            $this->outPut(null, 0, null, '更新成功');
        } else {
            $this->outPut(null, 0, null, '状态未变化');
        }
    }

    /***
     *
     *
     * 更新特卖订单情况
     */
    public function Updateorder1() {
        $order_id              = I('post.order_id');//订单编号
        $title                 = I('post.title');//标题
        $itemid                = I('post.itemid');//商品id
        $discount_rate         = I('post.discount_rate');//收入比率
        $share_rate            = I('post.share_rate');//分成比率
        $fee                   = I('post.fee');//效果评估
        $price                 = I('post.price');//商品单价
        $number                = I('post.number');//数量
        $total_fee             = I('post.total_fee');//付款金额
        $create_time           = I('post.create_time');//订单创建时间
        $click_time            = I('post.click_time');//订单单击时间
        $payStatus             = I('post.payStatus');//订单状态（订单支付 订单失效 订单结算）
        $order_type            = I('post.order_type');//商品类型 （天猫 淘宝）
        $auctionUrl            = I('post.auctionUrl');//商品地址
        $earningTime           = I('post.earningTime');//结算时间
        $img                   = I('post.img');//图片地址
        $pid                   = I('post.pid');//结算时间
        $username              = I('post.username');//广告位名称
        $model                 = M('order_data1');
        $where['order_id']     = $order_id;
        $where['itemid']       = $itemid;
        $data['title']         = $title;
        $data['itemid']        = $itemid;
        $data['discount_rate'] = $discount_rate;
        $data['share_rate']    = $share_rate;
        $data['fee']           = $fee;
        $data['price']         = $price;
        $data['number']        = $number;
        $data['total_fee']     = $total_fee;
        $data['create_time']   = $create_time;
        $data['click_time']    = $click_time;
        $data['payStatus']     = $payStatus;
        $data['order_type']    = $order_type;
        $data['auctionUrl']    = $auctionUrl;
        $data['earningTime']   = $earningTime;
        $data['pid']           = C('PID') . $pid;
        if ($img) {
            $data['img'] = $img;
        }
        $data['username'] = $username;
        $res              = $model->where($where)->save($data);
        if ($res) {
            $this->outPut(null, 0, null, '更新成功');
        } else {
            $resd = $model->where($where)->find();
            if ($resd) {
                $this->outPut(null, 0, null, '成功数据存在');
            } else {
                $data['order_id'] = $order_id;
                $ress             = $model->add($data);
                if ($ress) {
                    $this->outPut(null, 0, null, '插入成功');
                }
            }

        }
    }

    /***
     * 发送微信通知
     */
    public function SendWxmassage() {

        $id               = I('post.id');//编号id
        $iid              = I('post.idd');//商品id
        $snum             = I('post.snum');//剩余多少张券号
        $lnum             = I('post.lnum');//已领券号数量
        $jprice           = I('post.jprice');//券号金额
        $mprice           = I('post.mprice');//满多少钱可用
        $endtime          = I('post.endtime');//卷号结束时间
        $starttime        = I('post.starttime');//卷号开始时间
        $model            = M('items');
        $where['num_iid'] = $iid;
        $where['id']      = $id;
        $res              = $model->where($where)->find();
        file_put_contents('/tmp/taobaoke.log', var_export($res['uid'], true) . '||', FILE_APPEND);
        if ($res) {
            $user = M('user');
            $ress = $user->where(array('id' => $res['uid']))->find();
            file_put_contents('/tmp/taobaoke.log', var_export($user->getLastSql(), true) . '||', FILE_APPEND);
            if ($ress['openid']) {
                $WxObj    = new \Common\Org\WeiXin();
                $countent = $ress['username'] . '你提报的产品：' . $res['title'] . '\r\n券号已领取' . $lnum . '张\r\n剩余：' . $snum . '\r\n券号金额：' . $jprice . '\r\n券号有效期：' . $starttime . '----' . $endtime . '\r\n请及时联系商家加券,商家电话：' . $res['mobile'] . '\r\n联系QQ：' . $res['qq'];
                $token_id = S('token');
                if ($token_id) {

                } else {
                    $token_id = $WxObj->getAccessToken();
                    S('token', $token_id, 3500);
                }
                $data['url']      = 'http://m.51miao88.cn/item/index/id/' . $id . '.html';
                $data['token_id'] = $token_id;
                $data['wxuser']   = $ress['openid'];//'o1NeDjvenV0z7wZ1XCDfCVXoUsms';
                $data['countent'] = $countent;
                $WxObj->sendVerifyUser($data);
                $this->outPut(null, 0, null, '更新成功');
            } else {
                $this->outPut(null, -1, null, $res['username'] . '没有绑定微信');
            }
        } else {
            $this->outPut(null, -1, null, '商品不存在');
        }
    }

    /****
     *
     *
     * 发生检测大淘客信息
     */
    public function SendchekWxmassage() {

        $id               = I('post.id');//编号id
        $iid              = I('post.idd');//商品id
        $model            = M('items');
        $where['num_iid'] = $iid;
        $where['id']      = $id;
        $res              = $model->where($where)->find();
        if ($res && $res['pass'] == 1) {
            $user = M('user');
            $ress = $user->where(array('id' => $res['uid']))->find();
            if ($ress['openid']) {
                $WxObj    = new \Common\Org\WeiXin();
                $countent = $ress['username'] . '你提报的产品：' . $res['title'] . '\r\n 有可能是被下架请联系自己部门负责人做处理。商品id:' . $id;
                $token_id = S('token');
                if ($token_id) {

                } else {
                    $token_id = $WxObj->getAccessToken();
                    S('token', $token_id, 3500);
                }
                $data['url']      = 'http://m.51miao88.cn/item/index/id/' . $id . '.html';
                $data['token_id'] = $token_id;
                $data['wxuser']   = $ress['openid'];//'o1NeDjvenV0z7wZ1XCDfCVXoUsms';
                $data['countent'] = $countent;
                $WxObj->sendVerifyUser($data);
                $this->outPut(null, 0, null, '更新成功');
            } else {
                $this->outPut(null, -1, null, $res['username'] . '没有绑定微信');
            }
        } else {
            $this->outPut(null, -1, null, '商品不存在');
        }
    }

    /*
     *
     *
     * 获取订单情况
     */
    public function GetOrder() {
        redirect(U('/Admin/Public/login'));
        $current_month = date('Y-m-d');
        $ntime         = strtotime($current_month);
        $etime         = strtotime($current_month) + 86399;
        $model         = M('order_data1');
        $where         = array(// 'UNIX_TIMESTAMP('')'=>array(),
        );
        /*$activities_where = array(
            'UNIX_TIMESTAMP(create_time)' => array('lt', $ntime),
            'UNIX_TIMESTAMP(create_time)' => array('gt', $etime)
        );*/
        $activities_where = "UNIX_TIMESTAMP(create_time)>=" . $ntime . " and UNIX_TIMESTAMP(create_time)<=" . $etime . "";
        $data             = $model->where($activities_where)->order('create_time desc')->select();
        $tadaycout        = $model->where($activities_where)->field('username,sum(fee) as cout')->group('username')->order('cout desc')->select();
        //
        // var_dump($data);exit;
        //取昨天时间
        $sdate = date("Y-m-d", strtotime("-1 day"));
        $ntime = strtotime($sdate);
        $etime = strtotime($sdate) + 86399;
        $where = "UNIX_TIMESTAMP(create_time)>=" . $ntime . " and UNIX_TIMESTAMP(create_time)<=" . $etime . "";
        $cout  = $model->where($where)->field('username,sum(fee) as cout')->group('username')->order('cout desc')->select();
        //
        $this->assign('data', $data);
        $this->assign('cout', $cout);
        $this->assign('tadaycou', $tadaycout);
        $this->assign('sdate', $sdate);
        $this->assign('nwtime', $current_month);
        $this->display();
    }

    public function GetYsdOrder() {
        $model = M('order_data1');
        // var_dump($data);exit;
        //取昨天时间
        $sdate = date("Y-m-d", strtotime("-1 day"));
        $ntime = strtotime($sdate);
        $etime = strtotime($sdate) + 86399;
        $where = "UNIX_TIMESTAMP(create_time)>=" . $ntime . " and UNIX_TIMESTAMP(create_time)<=" . $etime . "";
        $cout  = $model->where($where)->field('username,sum(fee) as cout')->group('username')->order('cout desc')->select();
        //
        $data = $model->where($where)->order('create_time desc')->select();
        $this->assign('data', $data);
        $this->assign('cout', $cout);
        $this->assign('sdate', $sdate);
        $this->display();
    }

    /**
     *
     *
     * 更新佣金
     */
    public function Updateyongji() {
        $partner_name = I('post.partner_name');//商家名字
        $shop_name    = I('post.shop_name');//商品标题
        $type         = I('post.type');//类型
        $fee          = I('post.fee');//佣金比率
        $shop_url     = I('post.shop_url');//地址
        $id           = I('post.id');//长地址
        if ($id) {
            $shop_url = $shop_url . '&id=' . $id;
        }
        $model                = M('shop');
        $where['shop_name']   = $shop_name;
        $where['shop_url']    = $shop_url;
        $data['partner_name'] = $partner_name;
        $data['shop_name']    = $shop_name;
        $data['type']         = $type;
        $data['fee']          = $fee;
        $data['shop_url']     = $shop_url;
        $res                  = $model->where($where)->save($data);
        if ($res) {
            $this->outPut(null, 0, null, '更新成功');
        } else {
            $resd = $model->where($where)->find();
            if ($resd) {
                $this->outPut(null, 0, null, '成功数据存在');
            } else {
                $ress = $model->add($data);
                if ($ress) {
                    $this->outPut(null, 0, null, '插入成功');
                }
            }

        }
    }

    public function Getyongji() {
        $type  = I('get.type');//类型
        $model = M('shop');
        if ($type) {
            $where['type'] = $type;
        }
        $data = $model->where($where)->select();
        $this->assign('data', $data);
        $this->display();
    }

    /***
     *
     *
     * 特卖数据库
     */
    public function Temaiku() {
        $model = M('order_data1');
        $data  = $model->field('auctionUrl,title,img,click_time,total_fee,discount_rate,COUNT(*) as cout')->group('title')->order('cout desc')->select();
        $this->assign('data', $data);
        $this->display();
    }

    /****
     *
     *
     * 更新cookies
     */
    public function UpdateCookies($cook = '') {
        $model = M('setting');
        $cookk = I('post.cook');//类型
        if ($cookk) {
            $cook = $cookk;
        }
        if ($cook) {
            $res = $model->where(array('name' => 'cookie'))->save(array('data' => $cook));
            if ($res) {
                //$this->outPut(null, 0, null, '更新cookie成功');
            } else {
                // $this->outPut(null, 0, null, '更新cookie失败，可能cookies一样');
            }
        } else {
            // $this->outPut(null, 0, null, 'cookie不能为空');
        }

    }

    /*
     *
     *
     * 更新文章
     */
    public function InsertNews() {
        $type_id                             = I('post.type_id');//文章编号
        $title                               = I('post.title');//标题
        $click_num                           = I('post.click_num');//点击量
        $source_url                          = I('post.source_url');//头条文章链接
        $item_id                             = I('post.item_id');//文章id
        $display_url                         = I('post.display_url');//详细地址
        $behot_time                          = I('post.behot_time');//上线时间
        $detail_play_effective_count         = I('post.detail_play_effective_count');//阅读数量
        $create_user_id                      = I('post.create_user_id');//发布文章id
        $model                               = M('news');
        $where['item_id']                    = $item_id;
        $data['type_id']                     = $type_id;
        $data['title']                       = $title;
        $data['click_num']                   = $click_num;
        $data['source_url']                  = $source_url;
        $data['add_time']                    = time();
        $data['item_id']                     = $item_id;
        $data['display_url']                 = $display_url;
        $data['behot_time']                  = $behot_time;
        $data['detail_play_effective_count'] = $detail_play_effective_count;
        $data['create_user_id']              = $create_user_id;
        $res                                 = $model->where($where)->save($data);
        if ($res) {
            $this->outPut(null, 0, null, '更新成功');
        } else {
            $resd = $model->where($where)->find();
            if ($resd) {
                $this->outPut(null, 0, null, '成功数据存在');
            } else {
                $ress = $model->add($data);
                self::InsertoNews($display_url, $ress);
                if ($ress) {
                    $this->outPut(null, 0, null, '插入成功');
                }
            }

        }
    }

    public function InsertoNews($url, $newid) {
        $model = M('news_item');
        //$url='https://temai.snssdk.com/article/feed/index?id=2563747';
        //$newid=1;
        $data = self::news($url);

        //var_dump($data['title'][0]);exit;
        if ($data[0] == 1) {
            $cout  = $data[1];
            $jdate = array();
            foreach ($cout as $key => &$vad) {
                $jdate[$key]['news_id'] = $newid;
                $jdate[$key]['title']   = $cout[$key]['name'];
                $jdate[$key]['image']   = $cout[$key]['img'];
                $jdate[$key]['content'] = $cout[$key]['description'];
                $jdate[$key]['price']   = $cout[$key]['price'];
                $jdate[$key]['url']     = $cout[$key]['real_url'];
            }
            //file_put_contents('/tmp/taobaoke.log',var_export($jdate, true).'||',FILE_APPEND);
            $model->addAll($jdate);
        } else {
            if ($data) {
                $count = count($data['title']);
                for ($i = 0; $i < $count; $i++) {
                    $fdate[$i]['news_id'] = $newid;
                    $fdate[$i]['title']   = $data['title'][$i];
                    $fdate[$i]['image']   = $data['img'][$i];
                    $fdate[$i]['content'] = $data['product'][$i];
                    $fdate[$i]['price']   = $data['price'][$i];
                    $fdate[$i]['url']     = $data['href'][$i];
                }
                if ($fdate) {
                    $model->addAll($fdate);
                }
            }
        }

    }

    public function GettaodianOrder() {
        $current_month = date('Y-m-d');
        $ntime         = strtotime($current_month);
        $etime         = strtotime($current_month) + 86399;
        $model         = M('order_data');
        $where         = array(// 'UNIX_TIMESTAMP('')'=>array(),
        );
        /*$activities_where = array(
            'UNIX_TIMESTAMP(create_time)' => array('lt', $ntime),
            'UNIX_TIMESTAMP(create_time)' => array('gt', $etime)
        );*/
        $user             = M('user')->where("pid is not null")->getField('pid,username');
        $activities_where = "UNIX_TIMESTAMP(create_time)>=" . $ntime . " and UNIX_TIMESTAMP(create_time)<=" . $etime . "";
        $data             = $model->where($activities_where)->order('create_time desc')->select();
        foreach ($data as $key => &$vad) {
            $vad['username'] = $user[$vad['pid']];
        }
        $tadaycout = $model->where($activities_where)->field('pid,sum(fee) as cout')->group('pid')->order('cout desc')->select();
        foreach ($tadaycout as $key => &$val) {
            $val['username'] = $user[$val['pid']];
        }
        //
        // var_dump($data);exit;
        //取昨天时间
        $sdate = date("Y-m-d", strtotime("-1 day"));
        $ntime = strtotime($sdate);
        $etime = strtotime($sdate) + 86399;
        $where = "UNIX_TIMESTAMP(create_time)>=" . $ntime . " and UNIX_TIMESTAMP(create_time)<=" . $etime . "";
        $cout  = $model->where($where)->field('pid,sum(fee) as cout')->group('pid')->order('cout desc')->select();
        foreach ($cout as $key => &$vall) {
            $vall['username'] = $user[$vall['pid']];
        }
        //
        $this->assign('data', $data);
        $this->assign('cout', $cout);
        $this->assign('tadaycou', $tadaycout);
        $this->assign('sdate', $sdate);
        $this->assign('nwtime', $current_month);
        $this->display();
    }

    public function GetYsdtaodianOrder() {
        $model = M('order_data');
        // var_dump($data);exit;
        //取昨天时间
        $sdate = date("Y-m-d", strtotime("-1 day"));
        $ntime = strtotime($sdate);
        $etime = strtotime($sdate) + 86399;
        $user  = M('user')->where("pid is not null")->getField('pid,username');
        $where = "UNIX_TIMESTAMP(create_time)>=" . $ntime . " and UNIX_TIMESTAMP(create_time)<=" . $etime . "";
        $cout  = $model->where($where)->field('pid,sum(fee) as cout')->group('pid')->order('cout desc')->select();
        foreach ($cout as $key => &$vall) {
            $vall['username'] = $user[$vall['pid']];
        }
        //
        $data = $model->where($where)->order('create_time desc')->select();
        foreach ($data as $key => &$vad) {
            $vad['username'] = $user[$vad['pid']];
        }
        $this->assign('data', $data);
        $this->assign('cout', $cout);
        $this->assign('sdate', $sdate);
        $this->display();
    }

    public function news($url) {
        //header("Content-type: text/html; charset=utf-8");
        if ($url) {

        } else {
            $url = I('get.type');
            //$url = 'https://temai.snssdk.com/article/feed/index?id=2142501';
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//若PHP编译时不带openssl则需要此行
        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        // 4. 释放curl句柄
        curl_close($ch);
        /****2017.5.8修改
         * $reg = '/<figure>\s.*<h2 .* _price="(\d.*?\.00)" .* _href="(.*)" .*>(.*)<\/h2>\s.*<figcaption>(.*)<\/figcaption>\s.*<img alt-src=\'(.*)\'>\s.*<\/figure>/';
         * preg_match_all($reg,$output,$data);
         * $arr = array('price'=>$data[1],'href'=>$data[2],'title'=>$data[3],'product'=>$data[4],'img'=>$data[5]);
         * return $arr;
         * *****/
        $regg = '/\<textarea id\=\"gallery\-data\-textarea\" style\=\"(.*)\">([.\S\s]*?)\<\/textarea\>/i';
        preg_match_all($regg, $output, $datacout);
        $dataa = json_decode($datacout[2][0], true);
        //<title>邻居告诉我，买灯就买3、7款，好打理不说，便宜又高档</title>
        if (is_array($dataa)) {
            $arr[0] = 1;
            $arr[1] = $dataa;
            return $arr;
        } else {
            $reg = '/<figure>\s.*<h2 .* _price="(\d.*?\.00)" .* _href="(.*)" .*>(.*)<\/h2>\s.*<figcaption>(.*)<\/figcaption>\s.*<img alt-src=\'(.*)\'>\s.*<\/figure>/';
            preg_match_all($reg, $output, $data);

            $arr = array('price' => $data[1], 'href' => $data[2], 'title' => $data[3], 'product' => $data[4], 'img' => $data[5]);
            return $arr;
        }
    }

    public function ZhuangUrl() {
        $url = I('get.url', '', 'trim');
        // 验证
        $error = [];
        if ($url == '') {
            $error[] = '查询的商品地址不能为空';
        }
        if (!empty($error)) {
            $this->outPut(null, -1, null, implode('|', $error));
        }
        $Taobaoke = new \Common\Org\Taobaoke();
        $url      = $Taobaoke->ZhuangtaoUrl($url);
        /*if(strstr($url,'taobao.com')||strstr($url,'tmall.com')){
            $url=explode('?',$url);
            $http=$url[0];
            $url=$url[1];
            $id=explode('id=',$url);
            $idd=explode('&',$id[1]);
            $idd=$idd[0];
            $urll=$http.'?id='.$idd;
            $url['id']=$idd;
            $url['url']=$urll;
        }else{
            $urll='';
        }*/
        var_dump($url);
        exit;
        //return $url;
    }

    public function set_proxy_cookie() {
        $proxy  = M('proxy');
        $cookie = I('post.cookie', null, 'htmlspecialchars');
        $pid    = I('post.pid', null, 'htmlspecialchars');
        self::UpdateCookies($cookie);
        if ($pid != null AND $cookie != null) {

            $data['cookie']             = $cookie;
            $data['update_cookie_time'] = time();
            $where['pid']               = $pid;
            if ($proxy->where($where)->save($data)) {
                if ($pid == 'mm_121610813_22448587_79916379') {
                    //S('ZM_tao_bao_alliance_cookie', $cookie);

                    $http     = new \Common\Org\Http();
                    $url = 'http://api.zhaimiaosh.com/Public/updatePartnerCookie';
                    $http->post($url, array('cookie' => htmlspecialchars_decode($cookie), 'pid' => 'mm_121610813_42450934_227788032'));
                }
                $this->outPut("OK", 1);
            }
        }
        $this->outPut("错误：未成功写入COOKIE", -1);
    }
}