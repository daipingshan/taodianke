<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/14 0014
 * Time: 上午 10:52
 */

namespace Data\Controller;

use \Common\Org\DaTaoKe as DTK;

/**
 * 大淘客商品采集器
 * Class DaTaoKeApiController
 *
 * @package Data\Controller
 */
class DaTaoKeApiController extends CommonController {

    /**
     * DaTaoKeController constructor.
     */
    public function __construct() {
        parent::__construct();
        set_time_limit(600);
    }

    /**
     * 获取商品数据
     */
    public function addData() {
        //大淘客数据抓取
        $obj  = new DTK();
        $data = array();
        $time = time();
        for ($i = 1; $i < 11; $i++) {
            $res = $obj->getMoreData($i);
            foreach ($res as $key => $val) {
                if (time() - $time > 590) {
                    exit();
                }
                $count = M('items')->where(array('num_iid' => $val['num_iid']))->count('id');
                if (!$count) {
                    $data[$val['num_iid']] = $val;
                }
            }
            usleep(910000);
        }

        if ($data) {
            M('items')->addAll(array_values($data));
        }
        echo "success";
    }

    /**
     * 获取商品数据
     */
    public function addTopData() {
        //大淘客数据抓取
        $obj  = new DTK();
        $data = array();
        $time = time();
        $res  = $obj->getTopData();
        M('items')->where(array('coupon_type' => 5, 'ordid' => array('lt', 1000)))->setInc('ordid', 200);
        foreach ($res as $key => $val) {
            if (time() - $time > 1190) {
                exit();
            }
            $info = M('items')->field('id')->where(array('num_iid' => $val['num_iid']))->find();
            if (!$info) {
                $val['ordid']          = $key + 11;
                $val['coupon_type']    = 5;
                $data[$val['num_iid']] = $val;
                $this->_getTagsStr($val['title']);
            } else {
                M('items')->where(array('id' => $info['id']))->save(array('coupon_type' => 5, 'volume' => $val['volume'], 'ordid' => $key + 11));
            }
        }
        if ($data) {
            M('items')->addAll(array_values($data));
        }
        echo "success";
    }

    /**
     * 获取商品详情数据
     */
    public function addDesc() {
        //大淘客数据抓取
        $obj   = new DTK();
        $where = array('desc' => '0');
        $list  = M('items')->field('id,num_iid')->where($where)->limit(200)->order('id desc')->select();
        $time  = time();
        foreach ($list as $v) {
            if (time() - $time > 170) {
                exit();
            }
            $data = $obj->getOtherData($v['num_iid']);
            M('items')->where(array('id' => $v['id']))->save($data);
            usleep(300000);
        }
        echo "success";
    }

    /**
     * 处理下线商品
     */
    public function updateData() {
        $id    = S('tdk_id') ? S('tdk_id') : 0;
        $where = array('id' => array('gt', $id), 'dataoke_id' => array('gt', 0));
        $data  = M('items')->field('id,num_iid,dataoke_id')->where($where)->limit(300)->order('id asc')->select();
        $start = time();
        $obj   = new DTK();
        foreach ($data as $k => $v) {
            $end = time();
            if ($end - $start > 298) {
                S('tdk_id', $v['id']);
            }
            $item_res = $obj->isItemOnline($v['dataoke_id']);
            if ($item_res['status'] == 0) {
                M('items')->where(array('dataoke_id' => $v['dataoke_id']))->delete();
            } else {
                if ($item_res['data']) {
                    $save_data = $item_res['data'];
                    if ($v['coupon_type'] == 4 && $save_data['coupon_price'] > 9.9) {
                        $save_data['coupon_type'] = 1;
                    }
                    $save_data['last_query_dataoke_time'] = time();
                    M('items')->where(array('id' => $v['id']))->save($save_data);
                }
            }
            usleep(910000);
        }
        $last_id = isset($v['id']) && $v['id'] ? $v['id'] : S('tdk_id');
        S('tdk_id', (int)$last_id);
        if (count($data) < 300) {
            S('tdk_id', 0);
        }
        echo 'success';
    }

    /**
     * 重置手工提单的商品排序
     */
    public function clearSortItem() {
        M('items')->where(array('ordid' => array('neq', 9999), 'coupon_type' => array('neq', 5)))->save(array('ordid' => 9999));
        M('items')->where(array('coupon_end_time' => array('elt', time())))->delete();
    }
}