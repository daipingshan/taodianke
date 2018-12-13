<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/28 0028
 * Time: 上午 9:14
 */

namespace Data\Controller;

/**
 * 商品管理
 * Class ItemsController
 *
 * @package AppAdmin\Controller
 */
class ItemsController extends CommonController {

    /**
     * 券的基本信息的接口
     */
    private $taobao_union_formal_url = 'https://uland.taobao.com/cp/coupon?activityId=ppp&itemId=ddd&src=cd_cdll';

    /**
     * 群采集商品信息
     */
    public function qunCollect() {
        $content = I('get.content', '', 'trim,urldecode');
        $qq      = I('get.qq', '', 'trim,urldecode');

        $res = array('msg' => '添加失败', 'code' => -1);
        if (empty($content)) {
            $res['msg'] = '请输入完整的信息';
        }

        $result = $this->_checkContent($content);

        $result['data']['qq'] = $qq;
        if ($result['code'] == -1) {
            $res['msg'] = $result['msg'];
        }

        if ($result['data']['num_iid']) {
            $count = M('items')->where(array('num_iid' => $result['data']['num_iid']))->count();
            if ($count) {
                $res['msg'] = '该商品已在商品库，不能重复采集！';
            } else {
                $add_res = M('items')->add($result['data']);
                if ($add_res) {
                    $res = array('msg' => '添加成功', 'code' => 0);
                }
            }
        }

        dump($res);
        exit;
    }

    /**
     * 检测用户输入的内容
     */
    protected function _checkContent($content) {
        $return_data = array('code' => -1, 'msg' => 'error', 'data' => '');

        //	按照5行规则进行解析
        $exp_reg = "/(.+)[\n|\s]+(.+)[\n|\s]+(.+)[\n|\s]+(.+)[\n|\s]+(.*)/";
        preg_match($exp_reg, $content, $result);
        if (count($result) < 5) {
            $return_data['msg'] = '推广信息不合法';
            return $return_data;
        }

        //	商品名称
        $title = $result[1];
        //	商品文案
        $intro = $result[5];

        //	商品价格，券后价
        $price_exp_reg = "/([0-9]+\.*[0-9]*)/";
        preg_match_all($price_exp_reg, $result[2], $price_res);
        $price        = $price_res[0][0];
        $coupon_price = $price_res[0][1];

        if (empty($price) || empty($coupon_price)) {
            $return_data['msg'] = '原价或券价不能为空';
            return $return_data;
        }
        $quan = $price - $coupon_price;
        //	券地址
        $http_exp_reg = "/((http|https):[\/]{2}[a-zA-Z\d\.#\?\/\=\&\_]*)/";
        preg_match($http_exp_reg, $result[3], $http_res);
        if (!isset($http_res[0]) || !$http_res[0]) {
            $return_data['msg'] = '领券链接不合法';
            return $return_data;
        }
        $click_url = $http_res[0];

        //	activity_id
        $activity_exp_reg = "/(activityId|activity_id|activityid)=([a-z|\d]{20,})/";
        preg_match($activity_exp_reg, $click_url, $activity);
        if (!isset($activity[1]) || count($activity[1]) != 1) {
            $return_data['msg'] = '领券链接缺少活动ID';
            return $return_data;
        }

        //	获取商品id
        preg_match($http_exp_reg, $result[4], $http_item_res);
        $taobao_id = "/id=(\d{10,20})/";
        if (strpos($http_item_res[0], 's.click.taobao.com')) {
            $ture_url_data = $this->_getTaobaoTrueUrl($http_item_res[0]);
            $taobao_url    = urldecode($ture_url_data['res_url']);
            if ($ture_url_data['type'] == 'coupon') {
                $taobao_id = "/itemId=(\d{10,20})/";
            }
        } else {
            $taobao_url = $http_item_res[0];
        }
        preg_match($taobao_id, $taobao_url, $item);
        if (!isset($item[1]) || !$item[1]) {
            $return_data['msg'] = '商品链接中不存在商品编号，无法转链！';
            return $return_data;
        }

        //	获取券的基本信息
        $coupon_start_time = $coupon_end_time = $volume = 0;
        $shop_type         = $nick = '';
        $httpObj           = new \Common\Org\Http();
        $url               = str_replace('ppp', $activity[2], $this->taobao_union_formal_url);
        $url               = str_replace('ddd', $item[1], $url);

        $coupon_res = $httpObj->get($url);
        $data       = json_decode($coupon_res, true);

        if ($data['success'] == true) {
            //  销量  volume
            $volume = $data['result']['item']['biz30Day'];
            //  商品类型
            $shop_type = $data['result']['item']['tmall'] == 0 ? 'C' : 'B';
            //  券开始时间  coupon_start_time
            $coupon_start_time = strtotime($data['result']['effectiveStartTime']);
            //  券结束时间
            $coupon_end_time = strtotime($data['result']['effectiveEndTime']);
        }

        $quan_url  = 'http://taoquan.taobao.com/coupon/unify_apply.htm?sellerId=' . $info['seller_id'] . '&activityId=' . $activity[2];
        $q_url     = 'http://h5.m.taobao.com/ump/coupon/detail/index.html?sellerId=' . $info['seller_id'] . '&activityId=' . $activity[2];
        $click_url = 'https://uland.taobao.com/coupon/edetail?activityId=' . $activity[2] . '&pid=' . C('PID') . '&itemId=' . $item[1] . '&src=cd_cdll';

        $return_data = array(
            'code' => 1,
            'msg'  => 'ok',
            'data' => array(
                'num_iid'           => $item[1],
                'dataoke_id'        => 0,
                'pass'              => 0,
                'click_url'         => $click_url,
                'quan_url'          => $quan_url,
                'q_url'             => $q_url,
                'title'             => $title,
                'intro'             => $intro,
                'activity_id'       => $activity[2],
                'uid'               => 3,
                'uname'             => 'tongyong',
                'price'             => $price,
                'coupon_price'      => $coupon_price,
                'coupon_start_time' => $coupon_start_time,
                'quan'              => $quan,
                'coupon_end_time'   => $coupon_end_time,
                'starttime'         => $data['result']['effectiveStartTime'],
                'endtime'           => $data['result']['effectiveEndTime'],
                'volume'            => $volume,
                'shop_type'         => $shop_type,
                'add_time'          => time()
            )
        );
        return $return_data;
    }

    /**
     * 添加搜索记录
     */
    public function addSearchHotWord() {
        $tagsArr = $this->_getCacheTags();
        $data    = array();
        $day = date('Ymd');
        foreach ($tagsArr as $word => $num) {
            $data[] = array('day' => $day, 'word' => $word, 'num' => $num);
        }
        if ($data) {
            M('search_hot_word')->addAll($data);
        }
        $this->_delCacheTags();
    }
}