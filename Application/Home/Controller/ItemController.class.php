<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/21 0021
 * Time: 上午 11:41
 */

namespace Home\Controller;

/**
 * 商品
 * Class ItemController
 *
 * @package Home\Controller
 */
class ItemController extends CommonController {

    //  杨他她9.9包邮分类
    private $ytt_nine = 28;

    //  楚楚街商品特殊标识分类ID 1029
    private $ccj_cate_id = 1029;

    //  淘店客商品最后修改时间
    private $tdk_item_last_edit_time = 600;

    /**
     * 商品列表
     */
    public function index() {
        $cate_id = I('get.cate_id', 0, 'int');
        $sort    = I('get.sort', 0, 'int');
        $search  = I('get.search', '', 'trim');
        list($start_price, $end_price, $commission, $sale_num) = @explode('-', $search);
        $where = array('pass' => 1);
        $query = $filter = '';
        if ($cate_id == $this->ytt_nine) {
            $where['coupon_type'] = '4';
            $query                = "coupon_type:'4'";
        } elseif ($cate_id == $this->ccj_cate_id) {
            $where['shop_type'] = 'J';
            $query              = "shop_type:'J'";
        } else {
            if ($cate_id) {
                $where['cate_id'] = $cate_id;
                $query            = "cate_id:'{$cate_id}'";
            }
        }
        if ($start_price && $end_price) {
            $where['coupon_price'] = array('between', array($start_price, $end_price));
            $filter                = "coupon_price >= {$start_price} AND coupon_price <= {$end_price}";
        } else {
            if ($start_price) {
                $where['coupon_price'] = array('egt', $start_price);
                $filter                = "coupon_price >= {$start_price}";
            }
            if ($end_price) {
                $where['coupon_price'] = array('elt', $end_price);
                $filter                = "coupon_price <= {$end_price}";
            }
        }
        if ($commission) {
            $where['commission_rate'] = array('gt', (int)$commission * 100);
            if ($filter) {
                $filter .= ' AND commission >= ' . (int)$commission * 100;
            } else {
                $filter = 'commission >= ' . (int)$commission * 100;
            }
        }
        if ($sale_num) {
            $where['volume'] = array('gt', (int)$sale_num);
            if ($filter) {
                $filter .= ' AND volume >= ' . (int)$sale_num;
            } else {
                $filter = 'volume >= ' . (int)$sale_num;
            }
        }
        switch ($sort) {
            case 1:
                //按时间排序 asc
                $order     = 'id desc';
                $sort_data = array(array('key' => 'id', 'val' => 0));
                break;
            case 2:
                //按价格排序正序
                $order     = 'coupon_price asc';
                $sort_data = array(array('key' => 'coupon_price', 'val' => 1));
                break;
            case 3:
                //按销量排序
                $order     = 'volume desc';
                $sort_data = array(array('key' => 'volume', 'val' => 0));
                break;
            default:
                $order     = 'ordid asc,id desc';
                $sort_data = array(array('key' => 'ordid', 'val' => 1), array('key' => 'id', 'val' => 0));
        }
        if ($this->openSearchStatus === true) {
            $count = $this->_getOpenSearchCount($query, $filter, 'www');
            $page  = $this->pages($count, $this->limit);
            $data  = $this->_getOpenSearchList($query, $sort_data, $filter, $page->firstRow, $page->listRows, 'www');
        } else {
            $count = M('items')->where($where)->count('id');
            $page  = $this->pages($count, $this->limit);
            $data  = $this->_getItemsList($where, $order, $page->firstRow, $page->listRows, 'www');
        }
        $cate_data = S('tdk_cate_num');
        if (!$cate_data || $cate_data['time'] <= time()) {
            $cate = $this->_getItemCate();
            foreach ($cate as &$val) {
                if ($val['cate_id'] == 28) {
                    $query = "coupon_type:'4'";
                } else if ($val['cate_id'] == 1029) {
                    $query = "coupon_type:'5'";
                } else {
                    $query = "cate_id:'{$val['cate_id']}'";
                }
                $val['num'] = $this->_getOpenSearchCount($query, null, 'www');
            }
            $cate_data = array('time' => time() + 600, 'data' => $cate);
            S('tdk_cate_num', $cate_data);
        }
        $cate   = $cate_data['data'];
        $num    = $this->_getOpenSearchCount(null, null, 'www');
        $assign = array(
            'cate'        => $cate,
            'cate_id'     => $cate_id,
            'sort'        => $sort,
            'data'        => $data,
            'page'        => $page->show(),
            'act'         => 'item',
            'count'       => $num,
            'start_price' => $start_price ? $start_price : '',
            'end_price'   => $end_price ? $end_price : '',
            'commission'  => $commission ? $commission : '',
            'sale_num'    => $sale_num ? $sale_num : '',
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 9块9包邮商品
     */
    public function freeShipping() {
        $sort  = I('get.sort', 0, 'int');
        $where = array('pass' => 1, 'coupon_type' => 4);
        $query = "coupon_type:'4'";
        switch ($sort) {
            case 1:
                //按时间排序 asc
                $order     = 'id desc';
                $sort_data = array(array('key' => 'id', 'val' => 0));
                break;
            case 2:
                //按价格排序正序
                $order     = 'coupon_price asc';
                $sort_data = array(array('key' => 'coupon_price', 'val' => 1));
                break;
            case 3:
                //按销量排序
                $order     = 'volume desc';
                $sort_data = array(array('key' => 'volume', 'val' => 0));
                break;
            default:
                $order     = 'ordid asc,id desc';
                $sort_data = array(array('key' => 'ordid', 'val' => 1), array('key' => 'id', 'val' => 0));
        }
        if ($this->openSearchStatus === true) {
            $count = $this->_getOpenSearchCount($query, null, 'www');
            $page  = $this->pages($count, $this->limit);
            $data  = $this->_getOpenSearchList($query, $sort_data, null, $page->firstRow, $page->listRows, 'www');
        } else {
            $count = M('items')->where($where)->count('id');
            $page  = $this->pages($count, $this->limit);
            $data  = $this->_getItemsList($where, $order, $page->firstRow, $page->listRows, 'www');
        }
        $assign = array(
            'sort' => $sort,
            'data' => $data,
            'page' => $page->show(),
            'act'  => 'free',
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 热销
     */
    public function hot() {
        $sort  = I('get.sort', 0, 'int');
        $where = array('pass' => 1, 'coupon_type' => 5);
        $query = "coupon_type:'5'";
        switch ($sort) {
            case 1:
                //按时间排序 asc
                $order     = 'id desc';
                $sort_data = array(array('key' => 'id', 'val' => 0));
                break;
            case 2:
                //按价格排序正序
                $order     = 'coupon_price asc';
                $sort_data = array(array('key' => 'coupon_price', 'val' => 1));
                break;
            case 3:
                //按销量排序
                $order     = 'volume desc';
                $sort_data = array(array('key' => 'volume', 'val' => 0));
                break;
            default:
                $order     = 'ordid asc,id desc';
                $sort_data = array(array('key' => 'ordid', 'val' => 1), array('key' => 'id', 'val' => 0));
        }
        if ($this->openSearchStatus === true) {
            $count = $this->_getOpenSearchCount($query, null, 'www');
            $page  = $this->pages($count, $this->limit);
            $data  = $this->_getOpenSearchList($query, $sort_data, null, $page->firstRow, $page->listRows, 'www');
        } else {
            $count = M('items')->where($where)->count('id');
            $page  = $this->pages($count, $this->limit);
            $data  = $this->_getItemsList($where, $order, $page->firstRow, $page->listRows, 'www');
        }
        $assign = array(
            'sort' => $sort,
            'data' => $data,
            'page' => $page->show(),
            'act'  => 'hot',
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 精品推荐
     */
    public function handpick() {
        $sort   = I('get.sort', 0, 'int');
        $where  = array('pass' => 1, 'handpick_time' => array('gt', 0));
        $query  = '';
        $filter = 'handpick_time > 0';
        switch ($sort) {
            case 1:
                //按时间排序 asc
                $order     = 'id desc';
                $sort_data = array(array('key' => 'id', 'val' => 0));
                break;
            case 2:
                //按价格排序正序
                $order     = 'coupon_price asc';
                $sort_data = array(array('key' => 'coupon_price', 'val' => 1));
                break;
            case 3:
                //按销量排序
                $order     = 'volume desc';
                $sort_data = array(array('key' => 'volume', 'val' => 0));
                break;
            default:
                $order     = 'handpick_time desc,id desc';
                $sort_data = array(array('key' => 'handpick_time', 'val' => 0), array('key' => 'id', 'val' => 0));
        }
        if ($this->openSearchStatus === true) {
            $count = $this->_getOpenSearchCount($query, $filter, 'www');
            $page  = $this->pages($count, $this->limit);
            $data  = $this->_getOpenSearchList($query, $sort_data, $filter, $page->firstRow, $page->listRows, 'www');
        } else {
            $count = M('items')->where($where)->count('id');
            $page  = $this->pages($count, $this->limit);
            $data  = $this->_getItemsList($where, $order, $page->firstRow, $page->listRows, 'www');
        }
        $assign = array(
            'data' => $data,
            'page' => $page->show(),
            'act'  => 'handpick',
            'sort' => $sort,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 商品搜索
     *
     * @param    keyword string
     * @param    page    int
     * @return   data    array
     */
    public function search() {
        $keyword_str = I('get.keyword', '', 'trim,urldecode');
        $page        = I('get.page', 1, 'int');
        $sort        = I('get.sort', 0, 'int');
        switch ($sort) {
            case 1:
                //按时间排序 asc
                $order     = 'id desc';
                $sort_data = array(array('key' => 'id', 'val' => 0));
                break;
            case 2:
                //按价格排序正序
                $order     = 'coupon_price asc';
                $sort_data = array(array('key' => 'coupon_price', 'val' => 1));
                break;
            case 3:
                //按销量排序
                $order     = 'volume desc';
                $sort_data = array(array('key' => 'volume', 'val' => 0));
                break;
            default:
                $order     = 'ordid asc,id desc';
                $sort_data = array(array('key' => 'ordid', 'val' => 1), array('key' => 'id', 'val' => 0));
        }

        if ($sort == 0 && $page == 1) {
            $keyword = $this->_getTagsStr($keyword_str, 'user', $this->uid, true);
        } else {
            $keyword = $this->_getTagsStr($keyword_str, 'user', $this->uid, false);
        }

        $where['title|num_iid'] = array('like', '%' . $keyword . '%');
        $query                  = "keyword:'{$keyword}'";
        if ($this->openSearchStatus === true) {
            $count      = $this->_getOpenSearchCount($query, null, 'www');
            $total_page = ceil($count / $this->reqnum);
            if ($total_page >= $page) {
                $start_num = ($page - 1) * $this->reqnum;
                $data      = $this->_getOpenSearchList($query, $sort_data, null, $start_num, $this->limit);
                if ($count < 20) {
                    $all_data = $this->_getTaoBaoSearch($keyword, $page, $sort, $this->limit);
                    $data     = array_merge($data, $all_data);
                }
            } else {
                if ($count < 20) {
                    $total_page = 0;
                }
                $data = $this->_getTaoBaoSearch($keyword, $page - $total_page, $sort, $this->limit);
            }
        } else {
            $count      = M('items')->where($where)->count();
            $total_page = ceil($count / $this->reqnum);
            if ($total_page >= $page) {
                $start_num = ($page - 1) * $this->reqnum;
                $data      = $this->_getItemsList($where, $order, $start_num, $this->limit);
                if ($count < 20) {
                    $all_data = $this->_getTaoBaoSearch($keyword, $page, $sort, $this->limit);
                    $data     = array_merge($data, $all_data);
                }
            } else {
                if ($count < 20) {
                    $total_page = 0;
                }
                $data = $this->_getTaoBaoSearch($keyword, $page - $total_page, $sort, $this->limit);
            }
        }
        $assign = array(
            'sort'    => $sort,
            'data'    => $data,
            'page'    => $page,
            'keyword' => $keyword_str,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 商品详情
     */
    public function detail() {
        $num_iid = I('get.num_iid', '', 'trim');
        $type    = I('get.type', '', 'trim');
        switch ($type) {
            case 'chuchujie' :
                $res = $this->_getCCJDetail($num_iid);
                break;
            case 'tdk' :
                $res = $this->_getTDKDetail($num_iid);
                break;
            case 'qw' :
                $res = $this->_getQWDetail($num_iid);
                break;
            default:
                $res = array('status' => -1, 'info' => '非法请求！');
                break;
        }
        if ($res['status'] != 1) {
            $this->error($res['info']);
        }
        $cate      = S('tdk_items_cate');
        $cate_id   = $res['data']['cate_id'];
        $cate_name = $cate[$cate_id]['name'];
        $where     = array('pass' => 1);
        $query     = '';
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
            $more_data = $this->_getOpenSearchList($query, $sort, null, 0, 3);
        } else {
            $more_data = $this->_getItemsList($where, $order, 0, 3);
        }
        if ($res['data']['type'] != 'chuchujie' && $this->uid) {
            $res['data']['template'] = $this->_createTemplate($res['data'], $type);
        }
        $zone = array();
        if ($this->uid) {
            $zone = $this->_getZone();
        }
        $this->assign(array('cate_name' => $cate_name, 'info' => $res['data'], 'more_data' => $more_data, 'type' => $type, 'zone' => $zone, 'template' => $this->_getTemplate()));
        $this->display();
    }

    /**
     * 楚楚街相关业务
     *
     * @param $id
     */
    protected function _getCCJDetail($id) {
        $info = M('items')->where(array('num_iid' => $id, 'shop_type' => 'J'))->find();

        if (!$info) {
            return array('status' => -1, 'info' => '商品信息不存在！');
        }
        if (!$info['platform_info']) {
            return array('status' => -1, 'info' => '商品信息不完整！');
        }

        $platform_info = unserialize($info['platform_info']);
        $res           = getShareUrl($platform_info['ad_id'], $platform_info['item_id'], $this->wei_xin_pid);
        if ($res['status'] != 1) {
            return array('status' => -1, 'info' => '获取优惠券失败！', 'data' => $info);
        }
        $share_url        = $res['data']['couponShare']['discount_url'] ? $res['data']['couponShare']['discount_url'] : $res['data']['productShare']['detail_url'];
        $info['kou_ling'] = $share_url;
        $info['url']      = $share_url;
        return array('status' => 1, 'data' => $info);
    }

    /**
     *
     * 大淘客相关业务
     *
     * @param $id
     * @param null $pid
     * @return array\
     */
    protected function _getTDKDetail($id, $pid = null) {
        if ($pid) {
            $this->pid = $pid;
        }
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
            return array('status' => -1, 'info' => '商品信息不存在！');
        }
        //检测商品在线状态
        if (time() - $info['last_query_dataoke_time'] > $this->tdk_item_last_edit_time) {
            $res = $this->_checkItemsStatus($info['dataoke_id']);
            if ($res['status'] != 0) {
                return $res;
            }
        }
        //迷离获取淘口令
        $mi_li_status = C('MILI.is_use');
        if ($mi_li_status == 'Y') {
            $res = $this->_miLiHighApply($id, $this->pid, $info);
            if ($res['status'] != 0) {
                if ($res['status'] == 1) {
                    $info['kou_ling'] = $res['data']['kou_ling'];
                    $info['url']      = $res['data']['url'];
                    $res              = array('status' => 1, 'data' => $info);
                }
                $res['data'] = $info;
                return $res;
            }
        }
        // 轻淘客获取淘口令
        $qtk_status = C('QTK.is_use');
        if ($qtk_status == "Y") {
            $res = $this->_getQtkData($info, $this->pid);
            if ($res['status'] != 0) {
                $info['kou_ling'] = $res['data']['data']['kou_ling'];
                $info['url']      = $res['data']['data']['url'];
                $res              = array('status' => 1, 'data' => $info);
                return $res;
            }
        }
        // 官方api获取淘口令
        $url_data  = explode("&", $info['quan_url']);
        $click_url = $url_data[0] . "&pid=" . $this->pid . "&" . $url_data[2] . "&" . $url_data[3];
        $obj       = new \Common\Org\TaoBaoApi();
        $res       = $obj->getApiPass($click_url, $info['title'], $info['pic_url']);
        if ($res['code'] == -1) {
            return array('status' => -1, 'info' => '获取优惠券失败！');
        }
        $info['kou_ling']        = $res['kou_ling'];
        $info['url']             = $res['url'];
        $info['commission_rate'] = computedPrice($info['commission_rate'], 1);
        $info['commission']      = computedPrice($info['commission']);
        return array('status' => 1, 'data' => $info);
    }

    /**
     * @param $id
     * @param null $pid
     * @return array
     */
    protected function _getQWDetail($id, $pid = null) {
        if ($pid) {
            $this->pid = $pid;
        }
        $url               = "https://detail.tmall.com/item.htm?id={$id}";
        $obj               = new \Common\Org\TaoBaoApi();
        $res               = $obj->search($url);
        $info              = $res['data'][0];
        $info['click_url'] = "https://uland.taobao.com/coupon/edetail?&pid={$this->pid}&itemId={$id}&src=cd_cdll";
        //迷离获取淘口令
        $mi_li_status = C('MILI.is_use');
        if ($mi_li_status == 'Y') {
            $res = $this->_miLiHighApply($id, $this->pid, $info);
            if ($res['status'] != 1) {
                return array('status' => -1, 'info' => '获取优惠券失败！');
            }
            $info['kou_ling'] = $res['data']['kou_ling'];
            $info['url']      = $res['data']['url'];
        } else {
            $res = $obj->getApiPass($info['click_url'], $info['title'], $info['pic_url']);
            if ($res['code'] != 1) {
                return array('status' => -1, 'info' => '获取优惠券失败！');
            }
            $info['kou_ling'] = $res['kou_ling'];
            $info['url']      = $res['url'];
        }
        return array('status' => 1, 'data' => $info);
    }

    /**
     * 组装模板数据
     *
     * @param $info
     * @param $type
     * @return array|mixed
     */
    protected function _createTemplate($info, $type) {
        $data        = $this->_getTemplate();
        $long_url    = C('wechat_mp_domain_url') . '/Item/index/id/' . $info['id'] . '/uid/' . $this->uid . '/kou_ling/' . urlencode($info['kou_ling']) . '/type/' . $type . '.html';
        $short_url   = getShortUrl($long_url);
        $search_key  = array('#标题#', '#券后价#', '#原价#', '#券金额#', '#领券链接#', '#销量#', '#文案#', '#淘口令#', "\n");
        $replace_key = array($info['title'], $info['coupon_price'], $info['price'], $info['quan'], $short_url, $info['volume'], $info['intro'], $info['kou_ling'], '<br>');
        foreach ($data as &$val) {
            $val = str_replace($search_key, $replace_key, $val);
        }
        return $data;
    }

    /**
     * 获取用户推广位列表
     */
    protected function _getZone() {
        if (!$this->uid) {
            return array();
        }
        $data = S('tdk_zone_' . $this->uid);
        if (!$data) {
            $data = M('zone')->field('id as zone_id,zone_name,pid,dwxk_adsense_id,is_default')->where(array('uid' => $this->uid))->index('zone_id')->select();
            if ($data) {
                S('tdk_zone_' . $this->uid, $data);
            }
        }
        return $data;
    }

    public function getTemplate() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id      = I('post.item_id', '', 'trim');
        $type         = I('post.type', '', 'trim');
        $template_key = I('post.template_key', 0, 'int');
        $pid          = I('post.pid', '', 'trim');
        if ($type == 'tdk') {
            $res = $this->_getTDKDetail($item_id, $pid);
        } else {
            $res = $this->_getQwDetail($item_id, $pid);
        }
        if ($res['status'] != 1) {
            $this->error($res['info']);
        }
        $info      = $res['data'];
        $template  = $this->_getTemplate();
        $long_url  = C('wechat_mp_domain_url') . U('item/index', array('id' => $item_id, 'uid' => $this->uid, 'kou_ling' => $info['kou_ling'], 'type' => $type));
        $short_url = getShortUrl($long_url);
        if (!isset($template[$template_key]) || !$template[$template_key]) {
            $this->error('模板不存在！');
        }
        $search_key  = array('#标题#', '#券后价#', '#原价#', '#券金额#', '#领券链接#', '#销量#', '#文案#', '#淘口令#', "\n");
        $replace_key = array($info['title'], $info['coupon_price'], $info['price'], $info['quan'], $short_url, $info['volume'], $info['intro'], $info['kou_ling'], '<br>');
        $content     = str_replace($search_key, $replace_key, $template[$template_key]);
        $this->success($content);
    }

    /**
     * 一键转链功能
     */
    public function oneKeyTurnChain() {
        if (IS_AJAX) {
            $content             = I('post.content', '', 'trim');
            $res                 = $this->_turnChain($content, $this->pid);
            $res['data']['tips'] = '';
            if ($res['code'] == 1) {
                if ($res['data']['commission'] > 0) {
                    $res['data']['tips'] = "有{$res['data']['coupon_money']}元优惠券，可得佣金约{$res['data']['commission']}元";
                } else if ($res['data']['commission_rate'] > 0) {
                    $res['data']['tips'] = "佣金比例约{$res['data']['commission_rate']}%";
                }
            }
            $this->ajaxReturn($res);
        } else {
            $assign = array('act' => 'one_key');
            $this->assign($assign);
            $this->display();
        }
    }


    /**
     * 设置模板
     */
    public function setTemplate() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $content = I('post.content', '', 'trim');
        $key     = I('post.key', 0, 'int');
        if (!$content) {
            $this->error('模板内容不能为空！');
        }
        $data       = $this->_getTemplate();
        $data[$key] = $content;
        S('tdk_template_' . $this->uid, $data, 86400 * 365);
        $this->success('保存成功');
    }
}