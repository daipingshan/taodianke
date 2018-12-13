<?php

namespace Store\Controller;

class ItemController extends CommonController {

    //  淘店客商品最后修改时间
    private $tdk_item_last_edit_time = 600;
    // 三多网络微信推广id
    protected $wei_xin_pid = 63559;
    //  微信跳转到淘宝url地址
    protected $wechat_to_taobao = 'https://t.asczwa.com/taobao?backurl=';

    /**
     * 获取商品详情
     *
     * @param $uid
     * @param $id
     * @return mixed
     */
    public function index() {
        $id       = I('get.id', '', 'trim');
        $type     = I('get.type', '', 'trim');
        $uid      = I('get.uid', '', 'intval');
        $kou_ling = I('get.kou_ling', '', 'trim,urldecode');

        $this->uid = $uid;

        if ($this->openSearchStatus == true) {
            $query  = "id:'{$id}'";
            $detail = $this->_getOpenSearchList($query, null, null, 0, 1, 'touch');

            if (!empty($detail)) {
                $info = array_pop($detail);
            } else {
                $info = array();
            }
        } else {
            $info = M('items')->where(array('id' => $id))->find();
        }

        if ($type == 'tdk' && empty($info)) {
            $jumpUrl = U('Index/index', array('uid' => $this->uid));
            $this->assign('jumpUrl', $jumpUrl);
            $this->assign('waitSecond', 3);
            $this->display('Public/offLine');
            exit;
        }

        $user            = M('user')->where(array('id' => $this->uid))->find();
        $dwxk_adsense_id = $user['dwxk_adsense_id'];
        $pid             = $user['pid'];
        if (!$user['dwxk_adsense_id']) {
            $dwxk_adsense_id = $this->wei_xin_pid;
        }
        if (!$user['pid']) {
            $pid = C('PID');
        }

        switch ($info['shop_type']) {
            case 'J' :
                $res = $this->_getCCJDetail($info['num_iid'], $dwxk_adsense_id);
                break;
            case 'B' :
            case 'C' :
                $res = $this->_getTDKDetail($info['num_iid'], $pid, $kou_ling);
                break;
            default:
                $res = $this->_getQWDetail($id, $this->uid, $pid);
                break;
        }

        if ($res['status'] != 1) {
            if ($kou_ling && !empty($kou_ling)) {
                $error_msg = $kou_ling;
            } else {
                $error_msg = $res['info'];
            }
            $this->assign('info', $error_msg);
            $this->display('Public/error');
            exit;
        }
        if (!$info) {
            $info = $res['data'];
        }
        if ($kou_ling && !empty($kou_ling)) {
            $info['tao_kou_ling'] = $kou_ling;
        } else {
            $info['tao_kou_ling'] = $res['data']['item_quan'];
        }
        if ($info['shop_type'] == 'J') {
            $info['high_commision_url'] = $res['data']['high_commision_url'];
        } else {
            $info['high_commision_url'] = $this->wechat_to_taobao . urlencode($res['data']['high_commision_url']);
        }

        $tou = 'weixin';
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
            $tou = 'other';
        };

        $this->assign('item', $info);
        $this->assign('tou', $tou);
        $this->assign('uid', $this->uid);
        $this->assign('version', $this->version);
        $this->display();
    }

    /**
     * 楚楚街相关业务
     *
     * @param $id
     */
    protected function _getCCJDetail($id, $dwxk_adsense_id) {
        $info = M('items')->where(array('num_iid' => $id, 'shop_type' => 'J'))->find();
        if (!$info) {
            return array('status' => -1, 'info' => '商品信息不存在！');
        }
        if (!$info['platform_info']) {
            return array('status' => -1, 'info' => '商品信息不完整！');
        }
        $platform_info = unserialize($info['platform_info']);
        $res           = getShareUrl($platform_info['ad_id'], $platform_info['item_id'], $dwxk_adsense_id);
        if ($res['status'] != 1) {
            return array('status' => -1, 'info' => '此优惠券获取失败！', 'data' => $info);
        }
        $data['item_quan'] = $data['high_commision_url'] = $res['data']['couponShare']['discount_url'];

        return array('status' => 1, 'data' => $data);
    }

    /**
     * 大淘客相关业务
     *
     * @param $id
     */
    protected function _getTDKDetail($num_iid, $high_pid, $kou_ling) {
        $info = M('items')->where(array('num_iid' => $num_iid))->find();
        if (!$info) {
            return array('status' => -1, 'info' => '商品信息不存在！');
        }
        //检测商品在线状态
        if (time() - $info['last_query_dataoke_time'] > $this->tdk_item_last_edit_time && $info['dataoke_id'] > 0) {
            $res = $this->_checkItemsStatus($info['dataoke_id']);
            if ($res['status'] != 0) {
                return $res;
            }
        }

        $res = $this->_getHighApply($num_iid, $high_pid, $info);
        if ($res['status'] == 0) {
            $data['item_quan']          = $res['data']['kou_ling'];
            $data['high_commision_url'] = $res['data']['high_commision_url'];
        } else {
            $obj                        = new \Common\Org\TaoBaoApi();
            $res                        = $obj->getApiPass($info['click_url'], $info['title'], $info['pic_url']);
            $data['item_quan']          = $res['kou_ling'];
            $data['high_commision_url'] = $info['click_url'];
        }

        if ($kou_ling) {
            $data['item_quan'] = $kou_ling;
        }
        return array('status' => 1, 'data' => $data);
    }

    /**
     * 获取高佣接口
     *
     * @param $id
     * @param $high_pid
     * @param $info
     * @return mixed
     */
    protected function _getHighApply($id, $high_pid, $info) {
        $high_apply_res = array('status' => -1, 'data' => '', 'msg' => '高佣申请失败');
        //  调用迷离接口
        $mi_li_status = C('MILI.is_use');
        if ($mi_li_status == 'Y') {
            $res = $this->_miLiHighApply($id, $high_pid, $info);
            //  $res['data']['kou_ling'] 淘口令  $res['data']['url']  高佣url
            if ($res['status'] != 0) {
                $high_apply_res['status'] = 0;
                $high_apply_res['data']   = array(
                    'high_commision_url' => $res['data']['url'],
                    'kou_ling'           => $res['data']['kou_ling'],
                );
                $high_apply_res['msg']    = '迷离接口高佣申请成功';
                return $high_apply_res;
            }
        }

        // 轻淘客获取淘口令
        $qtk_status = C('QTK.is_use');
        if ($qtk_status == "Y") {
            $res = $this->_getQtkData($info, $high_pid);
            if ($res['status'] != 0) {
                $high_apply_res['status'] = 0;
                $high_apply_res['data']   = array(
                    'high_commision_url' => $res['data']['data']['url'],
                    'kou_ling'           => $res['data']['data']['kou_ling'],
                );
                $high_apply_res['msg']    = '轻淘客接口高佣申请成功';
                return $high_apply_res;
            }
        }
        return $high_apply_res;
    }

    /**
     * 全网相关业务
     *
     * @param $id
     * @return array
     */
    protected function _getQWDetail($id, $uid, $pid) {
        $url               = "https://detail.tmall.com/item.htm?id={$id}";
        $obj               = new \Common\Org\TaoBaoApi();
        $res               = $obj->search($url);
        $info              = $res['data'][0];
        $info['click_url'] = "https://uland.taobao.com/coupon/edetail?&pid={$pid}&itemId={$id}&src=cd_cdll";
        //迷离获取淘口令
        $mi_li_status = C('MILI.is_use');
        if ($mi_li_status == 'Y') {
            $res = $this->_miLiHighApply($id, $pid, $info);
            if ($res['status'] != 1) {
                return array('status' => -1, 'info' => '获取优惠券失败！');
            }
            $info['item_quan'] = $res['data']['kou_ling'];
            $info['url']       = $res['data']['url'];
        } else {
            $res = $obj->getApiPass($info['click_url'], $info['title'], $info['pic_url']);
            if ($res['code'] != 1) {
                return array('status' => -1, 'info' => '获取优惠券失败！');
            }
            $info['item_quan'] = $res['kou_ling'];
            $info['url']       = $res['url'];
        }

        return array('status' => 1, 'data' => $info);
    }

    /**
     * 获取搜索列表页
     *
     * @param $key
     * @param $uid
     * @return mixed
     */
    public function search() {
        $keyword_str = I('get.keyword', '', 'trim');
        $uid     = I('get.uid', '', 'intval');
        $p       = I('get.p', 1, 'intval');

        $query             = '';
        $type              = 'tdk';
        $where             = array();
        $is_show_dwxk_data = M('user')->where(array('id' => $uid))->getField('dwxk_adsense_id');
        if (!$is_show_dwxk_data) {
            $query              = "(shop_type:'B' OR shop_type:'C')";
            $where['shop_type'] = array('in', 'B,C');
        }

        if ($p == 1) {
            $keyword = $this->_getTagsStr($keyword_str, 'user', $uid, true);
        } else {
            $keyword = $this->_getTagsStr($keyword_str, 'user', $uid, false);
        }

        if (!empty($keyword)) {
            $query                  = "keyword:'{$keyword}'";
            $where['title|num_iid'] = array('like', '%' . $keyword . '%');
        }

        $start = 0;
        if ($p > 1) {
            $start = $this->page_size * ($p - 1);
        }
        $sort = array(array('key' => 'ordid', 'val' => 1), array('key' => 'id', 'val' => 0));

        if ($this->openSearchStatus == true) {
            $item = $this->_getOpenSearchList($query, $sort, null, $start, $this->page_size, 'touch');
        } else {
            $order = 'ordid asc, id desc';
            $item  = M('items')->where($where)->order($order)->limit($start . ',' . $this->page_size)->select();
        }
        if (!$item) {
            $sort = '';
            $item = $this->_getTaoBaoSearch($keyword, $p, $sort, $this->page_size);
            $type = 'qw';
        }
        foreach ($item as &$val) {
            $val['item_url'] = U('Item/index', array('id' => $val['id'], 'uid' => $uid, 'type' => $type));
        }
        if (IS_AJAX) {
            $this->ajaxReturn(array('code' => 1, 'uid' => $uid, 'data' => $item, 'msg' => 'ok'));
        }

        //  总共的页数
        $count   = M('items')->where($where)->count();
        $maxpage = ceil($count / $this->page_size);
        $this->assign('item', $item);
        $this->assign('uid', $uid);
        $this->assign('keyword', $keyword_str);
        $this->assign('maxpage', $maxpage);
        $this->assign('version', $this->version);
        $this->assign('ajaxurl', U('Item/search', array('p' => 1)));
        $this->display();
    }

}