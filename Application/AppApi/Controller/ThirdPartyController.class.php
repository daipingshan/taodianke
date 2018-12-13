<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/1/19
 * Time: 15:46
 */

namespace AppApi\Controller;

use Common\Org\DaTaoKe;

/**
 * Class ThirdPartyController
 *
 * @package AppApi\Controller
 */
class ThirdPartyController extends CommonController {

    /**
     * @var bool
     */
    protected $checkUser = false;

    /**
     * 获取商品列表
     */
    public function getItems() {
        $where     = I('post.where', '', 'trim,json_decode');
        $order     = I('post.order', '', 'trim,json_decode');
        $start_num = I('post.start_num', 0, 'int');
        $limit     = I('post.limit', 20, 'int');
        $field     = 'id,num_iid,title,price,coupon_price,pic_url,quan as coupon_money,volume as sale_num,commission_rate,commission';
        $data      = M('items')->field($field)->where($where)->where(array('pass' => 1, 'shop_type' => array('in', array('B', 'C'))))->limit($start_num, $limit)->order($order)->select();
        die(json_encode(array('status' => 1, 'data' => $data)));
    }

    /**
     * 下线商品
     */
    public function getItemDetail() {
        $num_iid = I('get.num_iid', '', 'trim');
        if (!$num_iid) {
            die(json_encode(array('status' => 0, 'info' => '商品编号不能为空')));
        }
        $field = 'id,num_iid,title,price,coupon_price,pic_url,quan as coupon_money,volume as sale_num,commission_rate,commission,dataoke_id,activity_id,click_url';
        $info  = M('items')->field($field)->where(array('num_iid' => $num_iid))->find();
        if (empty($info)) {
            die(json_encode(array('status' => -1, 'info' => '优惠券已被领完，请选择其他商品')));
        }
        $dtkObj   = new DaTaoKe();
        $item_res = $dtkObj->isItemOnline($info['dataoke_id']);
        if ($item_res['code'] == -1) {
            M('items')->where(array('dataoke_id' => $num_iid))->delete();
            die(json_encode(array('status' => -1, 'info' => '优惠券已被领完，请选择其他商品')));
        } else {
            if ($item_res['data']) {
                $save_data   = $item_res['data'];
                $coupon_type = M('items')->where(array('dataoke_id' => $info['dataoke_id']))->getField('coupon_type');
                if ($coupon_type == 4 && $save_data['coupon_price'] > 9.9) {
                    $save_data['coupon_type'] = 1;
                }
                $save_data['last_query_dataoke_time'] = time();
                M('items')->where(array('dataoke_id' => $info['dataoke_id']))->save($save_data);
            }
            die(json_encode(array('status' => 1, 'info' => 'ok', 'data' => $info)));
        }
    }

    /**
     * 全网搜索适配
     */
    public function search() {
        $keyword = I('request.keyword', '', 'trim');
        $page    = I('request.page', 1, 'int');
        $sort    = I('request.sort', 0, 'int');
        $limit   = I('request.limit', 20, 'int');
        $obj     = new \Common\Org\TaoBaoApi();
        $res     = $obj->search($keyword, 0, $page, $limit, $sort);
        $result  = $res['data'] ? $res['data'] : array();
        $data    = array();
        foreach ($result as $v) {
            $data[] = array(
                'id'              => $v['num_iid'],
                'num_iid'         => $v['num_iid'],
                'title'           => $v['title'],
                'price'           => $v['price'],
                'coupon_price'    => $v['coupon_price'],
                'pic_url'         => $v['pic_url'],
                'coupon_money'    => $v['price'] - $v['coupon_price'],
                'sale_num'        => $v['volume'],
                'commission_rate' => $v['commission_rate'],
                'commission'      => $v['yong_jin'],
                'shop_type'       => $v['title'],
            );
        }
        die(json_encode(array('status' => 1, 'info' => 'ok', 'data' => $data)));
    }
}