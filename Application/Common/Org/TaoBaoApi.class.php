<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/1 0001
 * Time: 下午 5:15
 */

namespace Common\Org;

class TaoBaoApi {

    /**
     * 全网搜索商品地址
     *
     * @var string
     */
    private $search_url = "http://pub.alimama.com/items/search.json?q=%s&_t=%s&startPrice=0.4&toPage=%s&queryType=2&sortType=%s&auctionTag=&perPageSize=%s&shopTag=&t=%s&_tb_token_=test&pvid=10_49.221.62.102_4720_1496801283153";

    /**
     * 高佣计划地址
     *
     * @var string
     */
    private $high_url = 'http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=%s&t=%s&_tb_token_=%s&pvid=10_27.27.105.237_362_1469025253939';

    /**
     * 申请高佣地址
     *
     * @var string
     */
    private $submit_high_url = 'http://pub.alimama.com/pubauc/applyForCommonCampaign.json';

    /**
     * 获取淘口令以及优惠券链接
     *
     * @var string
     */
    private $kou_ling_url = "http://pub.alimama.com/common/code/getAuctionCode.json?auctionid=%s&adzoneid=%s&siteid=%s&scenes=1&t=%s&_tb_token_=%s&pvid=%s";

    /**
     * 获取商品图文详情
     *
     * @var string
     */
    private $desc_url = "http://hws.m.taobao.com/cache/mtop.wdetail.getItemDescx/4.1/?data=%7B%22item_num_id%22%3A%22ppp%22%7D";

    /**
     * @var TopClient|null
     */
    private $topClient = null;

    /**
     * @var null
     */
    private $httpObj = null;

    /**
     * @var string
     */
    //private $app_key = "23837662"; //淘店客
    private $app_key = "23836749"; //淘店客2

    /**
     * @var string
     */
    //private $app_pass = "5a578243baf4b1566391ca55a807d7d0"; //淘店客
    private $app_pass = "d92c5da07d9c99ee3b21e489c6b04dda"; //淘店客2

    /**
     * 构造函数
     * AliApi constructor.
     */
    public function __construct() {
        $this->httpObj = new \Common\Org\Http();
    }

    /**
     * 搜索商品
     *
     * @param     $keyword
     * @param     $uid
     * @param int $page
     * @param int $page_num
     * @return array
     */
    public function search($keyword, $uid = 0, $page = 1, $page_num = 20, $sort = 0) {
        if (!$keyword) {
            return array('code' => 1, 'msg' => '');
        }
        $temp     = microtime(true) * 1000;
        $temp     = explode('.', $temp);
        $end      = $temp[0] + 8;
        $url      = sprintf($this->search_url, $keyword, $temp[0], $page, $sort, $page_num, $end);
        $ali_data = json_decode($this->httpObj->get($url), true);
        $data     = array();
        foreach ($ali_data['data']['pageList'] as $k => $v) {
            //$v['tkMktStatus'] = null;  => 定向计划
            //$v['tkMktStatus'] = 1;     => 营销计划
            $coupon_price = round($v['zkPrice'] - $v['couponAmount'], 2);
            $ali_rate     = $v['tkSpecialCampaignIdRateMap'];
            if ($ali_rate && is_array($ali_rate)) {
                arsort($ali_rate);
                $rate = array_values($ali_rate);
            } else {
                $rate = array($v['tkRate']);
            }
            if ($rate[0] > $v['tkRate']) {
                $rate_status = "wfa";
            } else {
                $rate_status = "tongyong";
            }
            if ($uid) {

                $wap_url = C('wechat_mp_domain_url') . U('Index/index', array('uid' => $uid));
            } else {
                $wap_url = C('wechat_mp_domain_url');
            }
            $type = "全网搜索";
            if ($v['couponAmount'] == 0) {
                $status = 0;
            } else {
                $status = 1;
            }
            $data[] = array(
                'id'              => $v['auctionId'],
                'num_iid'         => $v['auctionId'],
                'title'           => strip_tags($v['title']),
                'intro'           => strip_tags($v['title']),
                'price'           => round($v['zkPrice'], 2),
                'coupon_price'    => $coupon_price,
                'pic_url'         => 'http:' . $v['pictUrl'],
                'uname'           => $rate_status,
                'shop_type'       => 1 == $v['userType'] ? 'B' : 'C',
                'is_coupon'       => $status,
                'type'            => $type,
                'quan'            => $v['couponAmount'],
                'snum'            => $v['couponLeftCount'],
                'lnum'            => $v['couponTotalCount'] - $v['couponLeftCount'],
                'volume'          => $v['biz30day'],
                'commission_rate' => computedPrice($v['tkRate'], 1),
                'rate'            => $v['tkRate'],
                'goods_type'      => 'qw',
                'url'             => $wap_url,
                'yong_jin'        => computedPrice(round(($coupon_price * $v['tkRate']) / 100, 2)),
                'coupon_money'    => $v['couponAmount'],
            );
        }
        return array('code' => 1, 'data' => $data);
    }

    /**
     * 申请高佣
     *
     * @param        $cookie
     * @param        $goods_id
     * @param        $commission_rate
     * @param string $type
     * @param int $uid
     * @return mixed
     */
    public function submitHighApply($cookie, $goods_id, $commission_rate, $type = '', $uid = 0) {
        if (!$cookie || !$goods_id || !$commission_rate) {
            return $commission_rate;
        }
        $temp = microtime(true) * 1000;
        $temp = explode('.', $temp);
        $desc = '淘宝客高手申请推广';
        $url  = sprintf($this->high_url, $goods_id, $temp[0], $cookie['token']);
        //  获取所有的计划列表
        $high_res   = json_decode($this->httpObj->getCookie($url, $cookie['cookie']), true);
        $high_data  = $high_res['data'];
        $high_count = count($high_data);
        if ($high_count == 0) {
            $log_data = array(
                'time'     => date('Y-m-d H:i:s'),
                'goods_id' => $goods_id,
                'type'     => $type,
                'uid'      => $uid,
                'rate'     => $commission_rate,
                'msg'      => '没有获取到佣金计划'
            );
            file_put_contents('/tmp/submit_rate_fail.log', var_export($log_data, true) . "\r\n", FILE_APPEND);
            return $commission_rate;
        }
        //  去除需要人工申请的
        for ($i = 0; $i < $high_count; $i++) {
            if ($high_data[$i]['Properties'] == '是') {
                unset($high_data[$i]);
            }
        }

        // 对返回的计划列表按照从高到低的顺序进行排序
        sort($high_data);
        $sort_high_count = count($high_data);

        if ($sort_high_count == 1) {
            $campId        = $high_data[0]['CampaignID'];
            $keeperId      = $high_data[0]['ShopKeeperID'];
            $submit_status = $high_data[0]['Exist'];
            $comRate       = $high_data[0]['commissionRate'];
        } else {
            $new_high_data = array();
            for ($j = 0; $j < $sort_high_count; $j++) {
                $new_high_data[] = $high_data[$j]['commissionRate'];
            }
            $temp_data     = array_unique($new_high_data);
            $k             = array_search(max($temp_data), $temp_data);
            $campId        = $high_data[$k]['CampaignID'];
            $keeperId      = $high_data[$k]['ShopKeeperID'];
            $submit_status = $high_data[$k]['Exist'];
            $comRate       = $high_data[$k]['commissionRate'];
        }

        //如果通用计划大于定向计划直接返回定向计划
        if ($commission_rate > $comRate) {
            $log_data = array(
                'time'     => date('Y-m-d H:i:s'),
                'goods_id' => $goods_id,
                'type'     => $type,
                'uid'      => $uid,
                'rate'     => $commission_rate,
                'max_rate' => $comRate,
                'status'   => $submit_status,
            );
            file_put_contents('/tmp/submit_rate.log', var_export($log_data, true) . "\r\n", FILE_APPEND);
            return $commission_rate;
        }

        // 如果已经申请过了
        if ($submit_status == 'true') {
            $log_data = array(
                'time'     => date('Y-m-d H:i:s'),
                'goods_id' => $goods_id,
                'type'     => $type,
                'uid'      => $uid,
                'rate'     => $commission_rate,
                'max_rate' => $comRate
            );
            file_put_contents('/tmp/submit_rate_success.log', var_export($log_data, true) . "\r\n", FILE_APPEND);
            return $comRate;
        }

        // 申请高佣的操作
        $submit_high_url = $this->submit_high_url;
        $submit_data     = array(
            '_tb_token_'  => $cookie['token'],
            'applyreason' => $desc,
            'campId'      => $campId,
            'keeperid'    => $keeperId,
            't'           => $temp[0],
        );

        $submit_data = http_build_query($submit_data);
        $res         = json_decode($this->httpObj->postCookie($submit_high_url, $cookie['cookie'], $submit_data), true);
        if ($res && $res['ok'] == true) {
            return $comRate;
        } else {
            $log_data = array(
                'time'     => date('Y-m-d H:i:s'),
                'goods_id' => $goods_id,
                'type'     => $type,
                'uid'      => $uid,
                'res'      => $res,
            );
            file_put_contents('/tmp/submit_apply.log', var_export($log_data, true) . "\r\n", FILE_APPEND);
            return $commission_rate;
        }
    }

    /**
     * 通过接口获取口令
     *
     * @param $url
     * @param $text
     * @return array
     */
    public function getApiPass($url, $text, $img) {
        if (!$url || !$text) {
            return array('code' => -1, 'msg' => '请求参数不合法');
        }
        include_once(__DIR__ . '/Taobaotop/TopClient.php');
        include_once(__DIR__ . '/Taobaotop/TopLogger.php');
        include_once(__DIR__ . '/Taobaotop/RequestCheckUtil.php');
        include_once(__DIR__ . '/Taobaotop/ResultSet.php');
        include_once(__DIR__ . '/Taobaotop/request/TbkTpwdCreateRequest.php');
        $c            = new \TopClient;
        $c->appkey    = $this->app_key;
        $c->secretKey = $this->app_pass;
        $req          = new \TbkTpwdCreateRequest;
        $req->setUrl($url);
        $req->setText($text);
        $req->setLogo($img);
        $result = $c->execute($req);
        $result = objectToArray($result);
        if ($result['data']['model']) {
            return array('code' => 1, 'kou_ling' => $result['data']['model'], 'url' => $url, 'msg' => '获取成功');
        } else {
            return array('code' => -1, 'kou_ling' => '', 'url' => '', 'msg' => '获取失败' . var_export($result, true));
        }
    }

    /**
     * 通过接口获取口令
     *
     * @param $url
     * @param $text
     * @return array
     */
    public function getUrlApiPass($url, $text, $img) {
        if (!$url || !$text) {
            return array('code' => -1, 'msg' => '请求参数不合法');
        }
        include_once(__DIR__ . '/Taobaotop/TopClient.php');
        include_once(__DIR__ . '/Taobaotop/TopLogger.php');
        include_once(__DIR__ . '/Taobaotop/RequestCheckUtil.php');
        include_once(__DIR__ . '/Taobaotop/ResultSet.php');
        include_once(__DIR__ . '/Taobaotop/request/WirelessShareTpwdCreateRequest.php');
        include_once(__DIR__ . '/Taobaotop/domain/GenPwdIsvParamDto.php');
        $c               = new \TopClient;
        $c->appkey       = $this->app_key;
        $c->secretKey    = $this->app_pass;
        $req             = new \WirelessShareTpwdCreateRequest;
        $pwd_param       = new \GenPwdIsvParamDto;
        $pwd_param->url  = $url;
        $pwd_param->text = $text;
        $pwd_param->logo = $img;
        $req->setTpwdParam(json_encode($pwd_param));
        $result = $c->execute($req);
        $result = objectToArray($result);
        if ($result['model']) {
            return array('code' => 1, 'kou_ling' => $result['model'], 'url' => $url, 'msg' => '获取成功');
        } else {
            return array('code' => -1, 'kou_ling' => '', 'url' => '', 'msg' => '获取失败');
        }
    }

    /**
     * 获取淘宝商品基本信息
     */
    public function getTaoBaoItemInfo($id) {
        if (!$id) {
            return array('code' => -1, 'msg' => '请求参数不合法');
        }
        include_once(__DIR__ . '/Taobaotop/TopClient.php');
        include_once(__DIR__ . '/Taobaotop/RequestCheckUtil.php');
        include_once(__DIR__ . '/Taobaotop/ResultSet.php');
        include_once(__DIR__ . '/Taobaotop/request/TbkItemInfoGetRequest.php');
        $c            = new \TopClient;
        $c->appkey    = $this->app_key;
        $c->secretKey = $this->app_pass;
        $req          = new \TbkItemInfoGetRequest;
        $req->setFields("num_iid,title,item_url,pict_url,reserve_price,zk_final_price,item_url,nick,seller_id,volume,cat_name");
        $req->setPlatform("1");
        $req->setNumIids($id);
        $res    = $c->execute($req);
        $result = objectToArray($res);
        if (isset($result['results']['n_tbk_item']) && $result['results']['n_tbk_item']) {
            return array('code' => 1, 'data' => $result['results']['n_tbk_item'], 'msg' => '获取成功!');
        } else {
            return array('code' => -1, 'msg' => '获取失败！');
        }
    }

    /**
     * @param $url
     * @return array
     */
    public function getShortUrl($url) {
        include_once(__DIR__ . '/Taobaotop/TopClient.php');
        include_once(__DIR__ . '/Taobaotop/RequestCheckUtil.php');
        include_once(__DIR__ . '/Taobaotop/ResultSet.php');
        include_once(__DIR__ . '/Taobaotop/domain/TbkSpreadRequest.php');
        include_once(__DIR__ . '/Taobaotop/request/TbkSpreadGetRequest.php');
        $c             = new \TopClient;
        $c->appkey     = $this->app_key;
        $c->secretKey  = $this->app_pass;
        $req           = new \TbkSpreadGetRequest;
        $requests      = new \TbkSpreadRequest;
        $requests->url = $url;
        $req->setRequests(json_encode($requests));
        $res    = $c->execute($req);
        $result = objectToArray($res);
        if (isset($result['results']['tbk_spread']['content']) && $result['results']['tbk_spread']['err_msg'] == 'OK') {
            return array('code' => 1, 'data' => $result['results']['tbk_spread']['content'], 'msg' => '获取成功!');
        } else {
            return array('code' => -1, 'msg' => '获取失败！');
        }
    }

    /**
     * 秘钥
     *
     * @param $key
     */
    public function getCouponInfo($key) {
        include_once(__DIR__ . '/Taobaotop/TopClient.php');
        include_once(__DIR__ . '/Taobaotop/TopLogger.php');
        include_once(__DIR__ . '/Taobaotop/RequestCheckUtil.php');
        include_once(__DIR__ . '/Taobaotop/ResultSet.php');
        include_once(__DIR__ . '/Taobaotop/request/TbkCouponGetRequest.php');
        $c            = new \TopClient;
        $c->appkey    = $this->app_key;
        $c->secretKey = $this->app_pass;
        $req          = new \TbkCouponGetRequest;
        $req->setMe($key);
        $resp = $c->execute($req);
        return $resp;
    }

    /**
     * 抓包获取淘口令
     *
     * @param $goods_id
     * @param $cookie
     * @param $pid
     * @return array
     */
    public function getPass($goods_id, $cookie, $pid) {
        if (!$goods_id || !$cookie || !$pid) {
            return array('code' => -1, 'msg' => '请求参数不合法');
        }
        $time       = microtime(true) * 1000;
        $time       = explode('.', $time);
        $reset      = explode('_', $pid);
        $ad_zone_id = $reset[3];
        $site_id    = $reset[2];
        $pv_id      = "10_49.221.62.99_2867_1499068335221";
        $url        = sprintf($this->kou_ling_url, $goods_id, $ad_zone_id, $site_id, $time[0], $cookie['token'], $pv_id);
        $res        = json_decode($this->httpObj->getCookie($url, $cookie['cookie']), true);
        if (!$res) {
            return array('code' => -1, 'msg' => 'cookie已失效');
        }
        if (!$res['data']['couponLink']) {
            $data = array('kou_ling' => $res['data']['taoToken'], 'url' => $res['data']['shortLinkUrl']);
        } else {
            $data = array('kou_ling' => $res['data']['couponLinkTaoToken'], 'url' => $res['data']['couponShortLinkUrl']);
        }
        return array('code' => 1, 'data' => $data);
    }

    /**
     * 获取商品图文详情
     *
     * @param $goods_id
     * @return string
     */
    public function getApiDesc($goods_id) {
        $desc = "";
        if (!$goods_id) {
            return $desc;
        }
        include_once(__DIR__ . '/Taobaotop/TopClient.php');
        require_once(__DIR__ . "/Taobaotop/ResultSet.php");
        require_once(__DIR__ . "/Taobaotop/RequestCheckUtil.php");
        require_once(__DIR__ . "/Taobaotop/request/TbkItemInfoGetRequest.php");
        $this->topClient            = new \TopClient();
        $req                        = new \TbkItemInfoGetRequest();
        $this->topClient->appkey    = $this->app_key;
        $this->topClient->secretKey = $this->app_pass;
        $req->setFields("small_images");
        $req->setPlatform("1");
        $req->setNumIids($goods_id);
        $result = $this->topClient->execute($req);
        $temp   = objectToArray($result);
        $data   = $temp['results']['n_tbk_item']['small_images'];
        foreach ($data['string'] as $val) {
            $desc .= '<img src="' . $val . '" style="display: inline-block;height: auto;max-width: 100%;">';
        }
        return $desc;
    }

    /**
     * 获取商品图文详情
     *
     * @param $goods_id
     * @return string
     */
    public function getDesc($goods_id) {
        $desc = "";
        if (!$goods_id) {
            return $desc;
        }
        $url     = str_replace('ppp', $goods_id, $this->desc_url);
        $content = $this->httpObj->get($url);
        $data    = json_decode($content, true);
        if (!$data) {
            return $desc;
        }
        $img = $data['data']['images'];
        $num = count($img);
        for ($i = 0; $i < $num; $i++) {
            $desc .= '<img src="' . $img[$i] . '" style="display: inline-block;height: auto;max-width: 100%;">';
        }
        return $desc;
    }

    /**
     * 淘口令转换url
     *
     * @param $kou_ling
     * @return array
     */
    public function getKouLingUrl($kou_ling) {
        if (!$kou_ling) {
            return array('status' => -1, 'info' => '淘口令不能为空！');
        }
        include_once(__DIR__ . '/Taobaotop/TopClient.php');
        include_once(__DIR__ . '/Taobaotop/TopLogger.php');
        include_once(__DIR__ . '/Taobaotop/RequestCheckUtil.php');
        include_once(__DIR__ . '/Taobaotop/ResultSet.php');
        include_once(__DIR__ . '/Taobaotop/request/WirelessShareTpwdQueryRequest.php');
        $c            = new \TopClient;
        $c->appkey    = $this->app_key;
        $c->secretKey = $this->app_pass;
        $req          = new \WirelessShareTpwdQueryRequest;
        $req->setPasswordContent($kou_ling);
        $result = $c->execute($req);
        $res    = objectToArray($result);
        return $res;
    }

}