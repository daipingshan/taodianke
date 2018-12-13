<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2016/12/28
 * Time: 15:42
 */

namespace AppApi\Controller;

/**
 * Class ItemController
 *
 * @package Api\Controller
 */
class ItemController extends CommonController {
    //  杨他她9.9包邮分类
    private $ytt_nine = 28;

    //  楚楚街商品特殊标识分类ID 1029
    private $hot_cate_id = 1029;

    //  淘店客商品最后修改时间
    private $tdk_item_last_edit_time = 600;

    /**
     * 获取商品列表
     *
     * @param   cate_id    int
     * @param   condition  string
     * @param   page       int
     * @return  data       array
     */
    public function index() {
        $cate_id   = I('get.cate_id', 0, 'int');
        $condition = I('get.condition', 0, 'int');
        $page      = I('get.page', 1, 'int');
        $page--;
        if (!$cate_id) {
            $this->outPut(null, -1, null, '缺少商品分类ID！');
        }
        $where = array('pass' => 1);

        if ($cate_id == $this->ytt_nine) {
            $where['coupon_type'] = '4';
            $query                = "coupon_type:'4'";
        } elseif ($cate_id == $this->hot_cate_id) {
            $query = "";
        } else {
            $where['cate_id'] = $cate_id;
            $query            = "cate_id:'{$cate_id}'";
        }
        switch ($condition) {
            case 1:
                //按时间排序 asc
                $order = 'id desc';
                $sort  = array(array('key' => 'id', 'val' => 0));
                break;
            case 2:
                //按佣金排序
                $order = 'commission_rate desc';
                $sort  = array(array('key' => 'commission_rate', 'val' => 0));
                break;
            case 3:
                //按销量排序
                $order = 'volume desc';
                $sort  = array(array('key' => 'volume', 'val' => 0));
                break;
            case 5 :
                //按价格排序
                $order = 'coupon_price asc';
                $sort  = array(array('key' => 'coupon_price', 'val' => 1));
                break;
            default:
                $order = 'ordid asc,id desc';
                $sort  = array(array('key' => 'ordid', 'val' => 1), array('key' => 'id', 'val' => 0));
        }
        $start_num = $page * $this->reqnum;
        if ($this->openSearchStatus === true) {
            $data = $this->_getOpenSearchList($query, $sort, null, $start_num, $this->reqnum + 1);
        } else {
            $data = $this->_getItemsList($where, $order, $start_num, $this->reqnum + 1);
        }
        $this->setHasNext(false, $data);
        $this->outPut($data, 0);
    }

    /**
     * 商品搜索
     *
     * @param    keyword string
     * @param    page    int
     * @return   data    array
     */
    public function search() {
        $keyword             = I('request.keyword', '', 'trim');
        $page                = I('request.page', 1, 'int');
        $condition           = I('request.condition', 0, 'int');
        $is_full_web_product = I('request.is_full_web_product', 'Y', 'trim');
        $like                = null;
        if (!$keyword) {
            $this->outPut(null, -1, null, '缺少商品关键字！');
        }
        $where = array('pass' => 1);
        switch ($condition) {
            case 2 :
                $order     = 'commission_rate desc';
                $open_sort = array(array('key' => 'commission_rate', 'val' => 0));
                $sort      = 1;
                break;
            case 3 :
                $order     = 'volume desc';
                $open_sort = array(array('key' => 'volume', 'val' => 0));
                $sort      = 9;
                break;
            case 5 :
                $order     = 'coupon_price asc';
                $open_sort = array(array('key' => 'coupon_price', 'val' => 1));
                $sort      = 4;
                break;
            default:
                $order     = 'ordid asc,id desc';
                $open_sort = array(array('key' => 'ordid', 'val' => 1), array('key' => 'id', 'val' => 0));
                $sort      = 0;
        }
        if ($condition == 0 && $page == 1) {
            $keyword = $this->_getTagsStr($keyword, 'user', $this->uid, true);
        } else {
            $keyword = $this->_getTagsStr($keyword, 'user', $this->uid, false);
        }
        $where['title|num_iid'] = array('like', '%' . $keyword . '%');
        $query                  = "keyword:'{$keyword}'";
        if ($this->openSearchStatus === true) {
            $count      = $this->_getOpenSearchCount($query, null);
            $total_page = ceil($count / $this->reqnum);
            if ($is_full_web_product == 'Y') {
                if ($total_page >= $page) {
                    $start_num = ($page - 1) * $this->reqnum;
                    $data      = $this->_getOpenSearchList($query, $open_sort, null, $start_num, $this->reqnum + 1);
                    if ($count < 7) {
                        $all_data = $this->_getTaoBaoSearch($keyword, $page, $sort);
                        $data     = array_merge($data, $all_data);
                    }
                } else {
                    if ($count < 7) {
                        $total_page = 0;
                    }
                    $data = $this->_getTaoBaoSearch($keyword, $page - $total_page, $sort);
                }
            } else {
                $start_num = ($page - 1) * $this->reqnum;
                $data      = $this->_getOpenSearchList($query, $open_sort, null, $start_num, $this->reqnum + 1);
            }
        } else {
            $count      = M('items')->where($where)->count();
            $total_page = ceil($count / $this->reqnum);
            if ($is_full_web_product == 'Y') {
                if ($total_page >= $page) {
                    $start_num = ($page - 1) * $this->reqnum;
                    $data      = $this->_getItemsList($where, $order, $start_num, $this->reqnum + 1);
                    if ($count < 7) {
                        $all_data = $this->_getTaoBaoSearch($keyword, $page, $sort);
                        $data     = array_merge($data, $all_data);
                    }
                } else {
                    if ($count < 7) {
                        $total_page = 0;
                    }
                    $data = $this->_getTaoBaoSearch($keyword, $page - $total_page, $sort);
                }
            } else {
                $start_num = ($page - 1) * $this->reqnum;
                $data      = $this->_getItemsList($where, $order, $start_num, $this->reqnum + 1);
            }
        }
        $this->outPut($data, 0);
    }

    /**
     * 换取商品详情
     * @method    get
     *
     * @param     id    string
     * @param     type  string
     * @return    data  array
     */
    public function detail() {
        $id      = I('get.id', 0, 'trim');
        $zone_id = I('get.zone_id', 0, 'intval');
        $type    = I('get.type', 'tdk', 'trim');
        switch ($type) {
            case 'chuchujie' :
                if ($zone_id) {
                    $zones = $this->_getZone();
                    if (isset($zones[$zone_id]) && $zones[$zone_id]['dwxk_adsense_id']) {
                        $this->dwxk_adsense_id = $zones[$zone_id]['dwxk_adsense_id'];
                    }
                }
                $res = $this->_getCCJDetail($id);
                break;
            case 'tdk' :
                if ($zone_id) {
                    $zones = $this->_getZone();
                    if (isset($zones[$zone_id]) && $zones[$zone_id]['pid']) {
                        $this->pid = $zones[$zone_id]['pid'];
                    }
                }
                $res = $this->_getTDKDetail($id);
                break;
            case 'qw' :
                if ($zone_id) {
                    $zones = $this->_getZone();
                    if (isset($zones[$zone_id]) && $zones[$zone_id]['pid']) {
                        $this->pid = $zones[$zone_id]['pid'];
                    }
                }
                $res = $this->_getQWDetail($id);
                break;
            default:
                $res = array('status' => -1, 'info' => '非法请求！');
                break;
        }
        if ($res['status'] != 1) {
            $this->outPut(null, $res['status'], null, $res['info']);
        }
        $this->outPut($res['data'], 0);
    }

    /**
     *    申请高佣的操作
     *
     * @param   id      string
     * @param   rate    float
     * @param   type    string
     * @return  result  string
     */
    public function highApply() {
        $id   = I('get.id', '', 'trim');
        $rate = I('get.rate', 0, 'float');
        if (!$id) {
            $id = I('get.goodid', '', 'trim');
        }
        $item         = M('items')->where(array('num_iid' => $id))->find();
        $mi_li_status = C('MILI.is_use');
        if ($mi_li_status == 'Y' && $item) {
            $this->outPut(null, 0, null, '申请高佣成功');
        }
        $this->outPut(null, 0, null, '申请高佣成功');
        $qtk_status = C('QTK.is_use');
        if ($qtk_status == 'Y' && ($item['uname'] == 'yingxiao' || $item['uname'] == 'wfa')) {
            $this->outPut(null, 0, null, '申请高佣成功');
        }
        $type = isset($item['uname']) ? '淘店客商品' : '全网商品';
        $obj  = new \Common\Org\TaoBaoApi();
        $obj->submitHighApply($this->_getCookie(), $id, $rate, $type, $this->uid);
        $this->outPut(null, 0, null, '申请高佣成功');
    }

    /**
     * 获取同类相似商品
     */
    public function similar() {
        $num_iid = I('get.num_iid', '', 'trim');
        $cate_id = M('items')->where(array('num_iid' => $num_iid))->getField('cate_id');
        $where   = array('pass' => 1);
        $query   = '';
        $limit   = 10;
        if ($cate_id) {
            $where['cate_id'] = $cate_id;
            $where['num_iid'] = array('neq', $num_iid);
            $query            = "cate_id:'{$cate_id}'";
            $order            = 'id desc';
            $sort             = array(array('key' => 'id', 'val' => 0));
        } else {
            $order = 'handpick_time desc, id desc';
            $sort  = array(array('key' => 'handpick_time', 'val' => 0), array('key' => 'id', 'val' => 0));
        }
        if ($this->openSearchStatus === true) {
            $data = $this->_getOpenSearchList($query, $sort, null, 0, $limit);
        } else {
            $data = $this->_getItemsList($where, $order, 0, $limit);
        }
        $this->outPut($data, 0);
    }

    /**
     * 获取指定类型商品
     */
    public function special() {
        $type   = I('get.type', 'collect', 'trim');
        $where  = array('pass' => 1);
        $query  = '';
        $page   = I('get.page', 1, 'int');
        $order  = 'ordid asc,id desc';
        $sort   = array(array('key' => 'ordid', 'val' => 1), array('key' => 'id', 'val' => 0));
        $select = true;
        $filter = null;
        switch ($type) {
            case 'collect' :
                $start_num = ($page - 1) * $this->reqnum;
                $num_iid   = M('items_like')->where(array('uid' => $this->uid))->index('num_iid')->field('num_iid')->order('id desc')->limit($start_num, $this->reqnum + 1)->select();
                if (!$num_iid) {
                    $select = false;
                } else {
                    $where['num_iid'] = array('in', array_keys($num_iid));
                    $query            = "num_iid:'" . implode("' OR num_iid:'", array_keys($num_iid)) . "'";
                }
                $page = 1;
                break;
            case 'new' :
                $order = 'id desc';
                $sort  = array(array('key' => 'id', 'val' => 0));
                break;
            case 'hot' :
                $where['coupon_type'] = 5;
                $query                = "coupon_type:'5'";
                break;
            case 'high_commission' :
                $order = 'commission_rate desc';
                $sort  = array(array('key' => 'commission_rate', 'val' => 0));
                break;
            case 'handpick' :
                $where['handpick_time'] = array('gt', 0);
                $filter                 = 'handpick_time > 0';
                $order                  = 'handpick_time desc';
                $sort                   = array(array('key' => 'handpick_time', 'val' => 0));
                break;
            case 'nine_nine' :
                $where['coupon_type'] = 4;
                $query                = "coupon_type:'4'";
                break;
            default :
                break;
        }
        if ($select === false) {
            $this->outPut(array(), 0);
        }
        $start_num = ($page - 1) * $this->reqnum;
        if ($this->openSearchStatus === true) {
            $data = $this->_getOpenSearchList($query, $sort, $filter, $start_num, $this->reqnum + 1);
        } else {
            $data = $this->_getItemsList($where, $order, $start_num, $this->reqnum + 1);
        }
        $this->setHasNext(false, $data);
        $this->outPut($data, 0);
    }

    /**
     * 楚楚街相关业务
     *
     * @param $id
     */
    protected function _getCCJDetail($id) {
        $info = M('items')->where(array('num_iid' => $id, 'shop_type' => 'J'))->find();
        if (!$info) {
            return array('status' => -404, 'info' => '商品已没有高佣，已下线！');
        }
        if (!$info['platform_info']) {
            return array('status' => -1, 'info' => '商品信息不完整！');
        }
        $user_ad_id    = $this->dwxk_adsense_id;
        $platform_info = unserialize($info['platform_info']);
        $res           = getShareUrl($platform_info['ad_id'], $platform_info['item_id'], $user_ad_id);
        if ($res['status'] != 1) {
            return array('status' => -1, 'info' => '获取优惠券失败！');
        }
        $share_url = $res['data']['couponShare']['discount_url'] ? $res['data']['couponShare']['discount_url'] : $res['data']['productShare']['detail_url'];
        $count     = M('items_like')->where(array('num_iid' => $id, 'uid' => $this->uid))->count();
        $is_like   = $count > 0 ? "Y" : 'N';
        $fee_rate  = $this->_getFeeRate();
        if ($fee_rate < 100) {
            $yong_jin = sprintf("%.2f", $info['commission'] * $fee_rate / 100) . '元';
        } else {
            $yong_jin = $info['commission'] . '元';
        }
        $data = array(
            'is_like'         => $is_like,
            'intro'           => $info['intro'],
            'url'             => $share_url,
            'more_url'        => $share_url,
            'qrcode_url'      => $share_url,
            'desc'            => $info['desc'],
            'detail_url'      => $info['item_url'],
            'kou_ling'        => $share_url,
            'pic_url'         => $info['pic_url'],
            'title'           => $info['title'],
            'price'           => $info['price'],
            'coupon_price'    => $info['coupon_price'],
            'quan'            => $info['quan'],
            'volume'          => $info['volume'],
            'uname'           => $info['uname'],
            'goods_type'      => 'chuchujie',
            'num_iid'         => $info['num_iid'],
            'commission_rate' => $info['commission_rate'] / 100,
            'yong_jin'        => $yong_jin,
        );
        return array('status' => 1, 'data' => $data);
    }

    /**
     * 大淘客相关业务
     *
     * @param $id
     */
    protected function _getTDKDetail($id) {
        if ($this->openSearchStatus == true) {
            $query  = "num_iid:'{$id}'";
            $detail = $this->_getOpenSearchList($query, null, null, 0, 1);
            if (!empty($detail)) {
                $info = array_pop($detail);
            } else {
                $info = array();
            }
        } else {
            $info = M('items')->where(array('num_iid' => $id, 'shop_type' => array('in', 'B,C')))->find();
        }
        if (!$info) {
            return array('status' => -404, 'info' => '商品已没有高佣，已下线！');
        }
        //检测商品在线状态
        if (time() - $info['last_query_dataoke_time'] > $this->tdk_item_last_edit_time && $info['dataoke_id'] > 0) {
            $res = $this->_checkItemsStatus($info['dataoke_id']);
            if ($res['status'] != 0) {
                return $res;
            }
        }
        //迷离获取淘口令
        $mi_li_status = C('MILI.is_use');
        //  pid  自己的pid   parent_pid   顶级用户的pid
        if ($mi_li_status == 'Y') {
            $res = $this->_miLiHighApply($id, $this->pid, $info);
            if ($res['status'] != 0) {
                if ($res['status'] == 1) {
                    $mi_li_res = $this->_createDetailData($info, $res['data']['kou_ling'], $res['data']['url']);
                    $res       = array('status' => 1, 'data' => $mi_li_res);
                }
                return $res;
            }
        }
        // 轻淘客获取淘口令
        $qtk_status = C('QTK.is_use');
        if ($qtk_status == "Y") {
            $res = $this->_getQtkData($info, $this->pid);
            if ($res['status'] != 0) {
                $qtk_res = $this->_createDetailData($info, $res['data']['data']['kou_ling'], $res['data']['data']['url']);
                $res     = array('status' => 1, 'data' => $qtk_res);
                return $res;
            }
        }
        // 官方api获取淘口令
        $url_data  = explode("&", $info['quan_url']);
        $click_url = $url_data[0] . "&pid=" . $this->pid . "&" . $url_data[2] . "&" . $url_data[3];
        $obj       = new \Common\Org\TaoBaoApi();
        $res       = $obj->getApiPass($click_url, $info['title'], $info['pic_url']);
        if ($res['code'] == -1) {
            if (1235 == $this->uid) { //董广启账号
                return array('status' => -1, 'info' => $res['msg']);
            }
            return array('status' => -1, 'info' => '获取优惠券失败！');
        }
        $data = $this->_createDetailData($info, $res['kou_ling'], $click_url, true);
        return array('status' => 1, 'data' => $data);
    }

    /**
     * 全网相关业务
     *
     * @param $id
     * @return array
     */
    protected function _getQWDetail($id) {
        $obj       = new \Common\Org\TaoBaoApi();
        $item_info = $obj->getTaoBaoItemInfo($id);
        if ($item_info['code'] == 1) {
            $info['title']     = $item_info['data']['title'];
            $info['click_url'] = "https://uland.taobao.com/coupon/edetail?&pid={$this->pid}&itemId={$id}&src=cd_cdll";
            $info['pic_url']   = $item_info['data']['pict_url'];
        } else {
            $info['title']     = '点击下方打开获取淘宝优惠信息';
            $info['click_url'] = "https://uland.taobao.com/coupon/edetail?&pid={$this->pid}&itemId={$id}&src=cd_cdll";
            $info['pic_url']   = '';
        }
        //迷离获取淘口令

        $mi_li_status = C('MILI.is_use');
        if ($mi_li_status == 'Y') {
            $res = $this->_miLiHighApply($id, $this->pid, $info);
            if ($res['status'] != 1) {
                return array('status' => -1, 'info' => '获取优惠券失败！');
            }
            $kou_ling = $res['data']['kou_ling'];
            $url      = $res['data']['url'];
        } else {
            $res = $obj->getApiPass($info['click_url'], $info['title'], $info['pic_url']);
            if ($res['code'] != 1) {
                return array('status' => -1, 'info' => '获取优惠券失败！');
            }
            $kou_ling = $res['kou_ling'];
            $url      = $res['url'];
        }
        $long_url = C('wechat_mp_domain_url') . U('item/index', array('id' => $id, 'uid' => $this->uid, 'kou_ling' => $kou_ling, 'type' => 'qw'));
        //备用链接 第三方其他网站用的中转链接 紧急情况下可启用试试
        //$long_url = "https://exxx22.kuaizhan.com/?taowords={$kou_ling}&pic=" . base64_encode($info['pic_url']);
        $short_url  = getShortUrl($long_url);
        $desc       = $obj->getApiDesc($id);
        $detail_url = "https://h5.m.taobao.com/awp/core/detail.htm?id={$id}";
        $data       = array(
            'is_like'         => 'N',
            'intro'           => $info['title'],
            'url'             => $url,
            'more_url'        => $short_url,
            'qrcode_url'      => $short_url,
            'desc'            => $desc,
            'detail_url'      => $detail_url,
            'kou_ling'        => $kou_ling,
            'pic_url'         => $info['pic_url'],
            'title'           => $info['title'],
            'price'           => '',
            'coupon_price'    => '',
            'quan'            => '',
            'volume'          => '',
            'uname'           => 'tongyong',
            'goods_type'      => 'qw',
            'num_iid'         => $id,
            'commission_rate' => '',
            'yong_jin'        => '',
        );
        return array('status' => 1, 'data' => $data);
    }

    /**
     * 获取详情返回数据
     *
     * @param $item
     * @param $kou_ling
     * @return array
     */
    public function _createDetailData($item, $kou_ling, $high_apply_url, $from_type = null) {
        $count    = M('items_like')->where(array('num_iid' => $item['num_iid'], 'uid' => $this->uid))->count();
        $is_like  = $count > 0 ? "Y" : 'N';

        //分享商品链接优先使用快站域名
        if ('' != C('share_detail_kuaizhan_domain')) {
            /*$key = base64_encode(json_encode(array('tkl'=>$kou_ling,'image'=>$item['pic_url'])));
            $long_url = C('share_detail_kuaizhan_domain') . "?key={$key}";*/
            $long_url = C('share_detail_kuaizhan_domain') . "/?taowords={$kou_ling}&pic=" . base64_encode($item['pic_url']);
        } else {
            $long_url = C('wechat_mp_domain_url') . U('item/index', array('num_iid' => $item['num_iid'], 'uid' => $this->uid, 'kou_ling' => $kou_ling, 'type' => 'tdk'));
        }

        $short_url  = getShortUrl(urlencode($long_url));
        $detail_url = $item['item_url'] ? $item['item_url'] : 'https://h5.m.taobao.com/awp/core/detail.htm?id=' . $item['num_iid'];
        $kou_ling   = str_replace("《", "￥", $kou_ling);
        if ($from_type !== null) {
            $uname = $item['uname'];
        } else {
            $uname = 'tongyong';
        }
        $fee_rate = $this->_getFeeRate();
        if ($fee_rate < 100) {
            $yong_jin = sprintf("%.2f", $item['commission'] * $fee_rate / 100) . '元';
        } else {
            $yong_jin = $item['commission'] . '元';
        }
        $data = array(
            'is_like'         => $is_like,
            'intro'           => $item['intro'],
            'url'             => $high_apply_url,
            'more_url'        => $short_url,
            'qrcode_url'      => $short_url,
            'desc'            => $item['desc'],
            'detail_url'      => $detail_url,
            'kou_ling'        => $kou_ling,
            'pic_url'         => $item['pic_url'],
            'title'           => $item['title'],
            'price'           => $item['price'],
            'coupon_price'    => $item['coupon_price'],
            'quan'            => $item['quan'],
            'volume'          => $item['volume'],
            'uname'           => $uname,
            'goods_type'      => 'tdk',
            'num_iid'         => $item['num_iid'],
            'commission_rate' => $item['commission_rate'] / 100,
            'yong_jin'        => $yong_jin,
        );
        if ($item['shop_type'] != 'J') {
            $data['commission_rate'] = computedPrice($data['commission_rate'], 1);
            $data['yong_jin']        = computedPrice($data['yong_jin']);
        }
        return $data;
    }

    /**
     * 一键转链功能
     */
    public function oneKeyTurnChain() {
        $content = I('request.item_info', '', 'trim');
        $res     = $this->_turnChain($content, $this->pid);
        if ($res['code'] == -1) {
            $this->outPut(null, -1, null, $res['msg']);
        } else {
            $this->outPut($res['data'], 0);
        }
    }
}