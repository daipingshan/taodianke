<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/3 0003
 * Time: 下午 5:39
 */

namespace AppApi\Controller;

use Common\Org\OpenSearch;
use Common\Org\AliYunOss as OSS;
use Common\Org\TaoBaoApi;

class HttpApiController extends CommonController {

    /**
     * @var bool
     */
    protected $checkUser = false;

    // 迷离团队请求配置参数
    private $mili_param = 'item_id=%s&adzone_id=%s&platform=1&site_id=%s&token=%s';

    // 迷离团队的url
    private $mili_url = 'http://tbapi.00o.cn/highapi.php';

    /**
     * 获取一个模拟淘宝分享后的商品链接
     */
    private function _getTaobaoClickUrl($item_id, $pid) {
        $now_ms        = intval(microtime(true) * 1000);
        $now           = intval($now_ms / 1000);
        $share_time_ms = $now_ms - mt_rand(30000, 90000);
        $ip            = get_client_ip();
        return 'https://item.taobao.com/item.htm?id=' . $item_id . '&ali_trackid=2:' . $pid . ':' . $now . '_' . mt_rand(100, 300) . '_' . mt_rand(100000000, 9999999999) . '&pvid=10_' . $ip . '_763_' . $share_time_ms;
    }

    /*
    *   申请高佣
    */
    public function highApply() {
        $id          = I('get.id', '', 'trim');
        $activity_id = I('get.activity_id', '', 'trim');
        $pid         = 'mm_121610813_23840080_86378481';
        // 没有券的商品id
        //$id = '536840511622';
        $token      = '70002100813678567ed6b7a6e169063ac4a98374122699fcc5e25aa7190d54b45206a833199378718';
        $httpObj    = new \Common\Org\Http();
        $user_info  = explode('_', $pid);
        $proxy_data = $this->_getProxyData($user_info[1]);
        $map        = sprintf($this->mili_param, $id, $user_info[3], $user_info[2], $token);
        $mi_li_res  = $httpObj->post($this->mili_url, $map);
        $res        = json_decode($mi_li_res, true);

        if ($res && $res['result']['data']['coupon_click_url']) {
            if ($activity_id) {
                $click_url = $res['result']['data']['coupon_click_url'] . '&activityId=' . $activity_id . '&pid=' . $pid . '&itemId=' . $id;
            } else {
                $click_url = $res['result']['data']['coupon_click_url'];
            }
            $coupon_click_res = explode('?', $click_url);
            //  查询优惠券是否有效
            $url        = 'https://uland.taobao.com/cp/coupon?' . $coupon_click_res[1];
            $coupon_res = $httpObj->get($url);
            $data       = json_decode($coupon_res, true);
            if ($data['success'] == true) {
                //  优惠券状态   2  失效，  0有效
                $ret_status = $data['result']['retStatus'];
                if ($ret_status != 0) {
                    $click_url = 'https://detail.tmall.com/item.htm?id=' . $id . '&ali_trackid=2:' . $pid . ':1511313765_258_1089094541&pvid=10_49.221.62.198_602_1511311988514&tbpm=1';
                }
            }
        }
        echo '商品链接:';
        dump($click_url);
        exit;
    }

    /**
     * 获取全网数据
     */
    public function search() {
        $k    = I('param.k', '', 'trim');
        $uid  = I('param.uid', 0, 'int');
        $p    = I('param.p', 1, 'int');
        $size = I('param.size', 10, 'int');
        $obj  = new \Common\Org\TaoBaoApi();
        $data = $obj->search($k, $uid, $p, $size);
        echo json_encode($data);
        exit;
    }

    /**
     * 申请高佣
     */
    public function getApply() {
        $goods_id   = I('param.goods_id', '', 'trim');
        $rate       = I('param.rate', '', 'trim');
        $parent_pid = I('param.parent_pid', '', 'trim');
        $obj        = new \Common\Org\TaoBaoApi();
        $cookie     = $this->_getCookie($parent_pid);
        $data       = $obj->submitHighApply($cookie, $goods_id, $rate);
        echo json_encode($data);
        exit;
    }

    /**
     * 获取淘口令
     */
    public function getPass() {
        $goods_id   = I('param.goods_id', '', 'trim');
        $pid        = I('param.pid', '', 'trim');
        $parent_pid = I('param.parent_pid', '', 'trim');
        $obj        = new \Common\Org\TaoBaoApi();
        $cookie     = $this->_getCookie($parent_pid);
        $data       = $obj->getPass($goods_id, $cookie, $pid);
        echo json_encode($data);
        exit;
    }

    /**
     * 测试获取淘口令
     */
    public function testPass() {

        $post_data = array('goods_id' => '548639269568', 'cookie' => $this->_getCookie('mm_121610813_22448587_79916379'), 'pid' => 'mm_121610813_22448587_79916379');;
        $obj  = new \Common\Org\TaoBaoApi();
        $data = $obj->getPass($post_data['goods_id'], $post_data['cookie'], $post_data['pid']);
        dump($data);
        exit;
    }

    /**
     * 测试获取淘口令
     */
    public function testApply() {
        $post_data = array('goods_id' => '40270514785', 'cookie' => $this->_getCookie(), 'rate' => '4.0');
        $obj       = new \Common\Org\TaoBaoApi();
        $data      = $obj->submitHighApply($post_data['cookie'], $post_data['goods_id'], $post_data['rate'], '全网商品', 1090);
        dump($data);
        exit;
    }

    /**
     * 测试获取淘口令
     */
    public function testSearch() {
        $obj  = new \Common\Org\TaoBaoApi();
        $data = $obj->search('风扇', 919, 1, 50);
        dump($data);
        exit;
    }

    /**
     * 测试config
     */
    public function testConfig() {
        $data = S('tdk_config');
        dump($data);
    }

    /**
     * 测试config
     */
    public function mysqlConfig() {
        $content = M('config')->getFieldById(1, 'content');
        $data    = unserialize($content);
        dump($data);
    }

    /**
     * 清理文件缓存
     */
    public function clearData() {
        $key = I('get.key', '', 'trim');
        if ($key) {
            S($key, null);
        }
    }

    /**
     * 测试server
     */
    public function testServer() {
        $_temp = explode('.php', $_SERVER['PHP_SELF']);
        echo rtrim(str_replace($_SERVER['HTTP_HOST'], '', $_temp[0] . '.php'), '/');
        dump($_SERVER);
    }

    /**
     * openSearch 搜索测试
     */
    public function openSearch() {
        $obj     = new OpenSearch();
        $keyword = "keyword:'飞利浦牙刷'";
        $sort    = array('key' => 'id', 'val' => 1);
        $count   = $this->_getOpenSearchCount($keyword, null);
        echo $count;
        exit;
        $res = $obj->search($keyword, $sort);
        dump($res);
        exit;
    }

    /**
     * 数据库大小写测试
     */
    public function mysqlTest() {
        $info = M('OrderCommission')->where('user_id=1090')->find();
        echo M('OrderCommission')->_sql();
        dump($info);
    }

    /**
     * 测试post请求
     */
    public function postSearch() {
        $url       = "http://api.taodianke.com/Item/search?V=2";
        $post_data = array(
            'keyword'   => '无纺布洗碗布',
            'page'      => 1,
            'token'     => 'ff9bf1f2456af154d2372532bdee36f52ef3ef15967d4b4007fc6f45',
            'condition' => 0,
        );
        $obj       = new \Common\Org\Http();
        $res       = $obj->post($url, $post_data);
        echo $res;
        exit;
    }

    public function testUpload() {
        $this->display();
    }

    /**
     * 测试阿里云上传
     */
    public function testOss() {
        $obj = new OSS();
        $res = $obj->uploadObject();
        dump($res);
        exit;
    }

    public function testSaveOss() {
        $file = './Uploads/kuaidian0707.jpg';
        $obj  = new OSS();
        $res  = $obj->saveObject($file, '123.jpg');
        dump($res);
        exit;
    }

    public function testDelOss() {
        $file = '2017/0925/123.jpg';
        $obj  = new OSS();
        $res  = $obj->deleteObject($file);
        dump($res);
        exit;
    }

    public function getTBInfo() {
        $id     = '556274004266';
        $TaoBao = new \Common\Org\TaoBaoApi();
        $res    = $TaoBao->getTaoBaoItemInfo($id);
        dump($res);
        exit;
    }

    public function getShortUrl() {
        $url    = 'https://uland.taobao.com/coupon/edetail?e=OGIskQfUHSXsbecaumMgZCHHYDHB%2FbxqgksdnUjL3C4UiOh8EqgsEQ8p54O5FL0li7tYfYNDVk2dUPWa9HJcY5Q5wfGz%2Fu%2BNAtY9WDAhVfMu2U1SwL8p1muFqp8TFaHMonv6QcvcARY%3D&traceId=0ab2513115093423498148459e_id=1782952693&activity_id=b32be90fd7fc407093ebc323c34062ad ';
        $TaoBao = new \Common\Org\TaoBaoApi();
        $res    = $TaoBao->getShortUrl($url);
        dump($res);
        exit;
    }

    public function getTags() {
        $str  = "真怕有一天我们再次成为交叉线，我想那时就再也不可能回归了，快乐永远是拿痛苦做代价，你现在多幸福，多快乐，你以后就会越伤心越难过，不想发生";
        $data = $this->_getTagsStr($str);
        dump($data);
    }

    public function getCache() {
        $data = $this->_getTags();
        dump($data);
        exit;
    }

    public function redis() {
    }

    public function getMoreItem() {
        $obj = new TaoBaoApi();
        $obj->getMoreItem();
    }

}