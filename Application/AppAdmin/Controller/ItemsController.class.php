<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/28 0028
 * Time: 上午 9:14
 */

namespace AppAdmin\Controller;

/**
 * 商品管理
 * Class ItemsController
 *
 * @package AppAdmin\Controller
 */
class ItemsController extends CommonController {

    /**
     * 商品列表
     */
    public function index() {
        $model     = M('items');
        $title     = I('get.title', '', 'trim');
        $num_iid   = I('get.num_iid', '', 'trim');
        $cate_id   = I('get.cate_id', 0, 'int');
        $jing      = I('get.jing', 0, 'int');
        $shop_type = I('get.shop_type', 0, 'trim');
        $where     = array();
        if ($title) {
            $where['title'] = array('like', "%{$title}%");
        }
        if ($num_iid) {
            $where['num_iid'] = $num_iid;
        }
        if ($cate_id) {
            if ($cate_id == 28) {
                $where['coupon_type'] = 4;
            } else if ($cate_id == 1029) {
                $where['shop_type'] = 'J';
            } else {
                $where['cate_id'] = $cate_id;
            }
        }
        if ($shop_type) {
            $where['shop_type'] = $shop_type;
        }
        if ($jing == 1) {
            $where['handpick_time'] = array('neq', 0);
        } elseif ($jing == 2) {
            $where['handpick_time'] = 0;
        }

        $count     = $model->where($where)->count('id');
        $page      = $this->pages($count, $this->limit);
        $data      = $model->where($where)->order('ordid asc,id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($data as $key => $item) {
            $data[$key]['title_replace'] = str_ireplace(array('"', "'"), ' ', $item['title']);
        }
        $cate      = M('items_cate')->where(array('pid' => 18, 'status' => 1))->getField('id,name');
        $shop_type = array('B' => '天猫', 'J' => '楚楚街', 'D' => '京东', 'C' => '淘宝');
        $this->assign(array('page' => $page->show(), 'data' => $data, 'count' => $count, 'cate' => $cate, 'shop_type' => $shop_type));
        $this->display();
    }

    /**
     * 设置精选商品
     */
    public function setHandpick() {
        $id      = I('get.id', 0, 'int');
        $type    = I('get.type', 'Y', 'trim');
        $is_push = I('get.is_push', 'N', 'trim');
        $title   = I('get.title', '', 'trim');

        $info = M('items')->find($id);
        if (!$id || !$info) {
            $this->error('商品信息不存在！');
        }
        if ($type == 'Y') {
            $time = time();
        } else {
            $time = 0;
        }
        $res = M('items')->save(array('id' => $id, 'handpick_time' => $time));
        if ($res !== false) {
            if ($is_push == 'Y') {
                $goods_type = 'tdk';
                if ($info['shop_type'] == 'J') {
                    $goods_type = 'chuchujie';
                }
                $this->_sendJPush($title, 'all', array('num_iid' => $info['num_iid'], 'goods_type' => $goods_type), 'all');
                $this->success('设置成功,推送成功');
            } else {
                $this->success('设置成功');
            }
        } else {
            $this->success('设置失败！');
        }
    }

    /**
     * 删除商品
     */
    public function delItems() {
        $num_iid = I('post.num_iid', '', 'trim');
        $res     = M('items')->where(array('num_iid' => $num_iid))->delete();
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 商品分类
     */
    public function cate() {
        $model = M('items_cate');
        $where = array('pid' => 18);
        $count = $model->where($where)->count('id');
        $page  = $this->pages($count, $this->limit);
        $data  = $model->where($where)->order('ordid asc,id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign(array('page' => $page->show(), 'data' => $data));
        $this->display();
    }

    /**
     * 添加分类
     */
    public function addCate() {
        if (IS_POST) {
            $name   = I('post.name', '', 'trim');
            $image  = I('post.image', '', 'trim');
            $sort   = I('post.sort', 255, 'int');
            $status = I('post.status', 1, 'int');
            if (!$name) {
                $this->error('请输入商品分类！');
            }
            if (!$image) {
                $this->error('请上传商品分类图片！');
            }
            $data = array('name' => $name, 'cateimg' => $image, 'ordid' => $sort, 'status' => $status, 'pid' => 18, 'spid' => '18|');
            $res  = M('items_cate')->add($data);
            if ($res) {
                S('tdk_items_cate', null);
                $this->success('添加成功', U('Items/cate'));
            } else {
                $this->error('添加失败！');
            }
        } else {
            $this->display();
        }

    }

    public function updateCate() {
        if (IS_POST) {
            $id     = I('post.id', 0, 'int');
            $name   = I('post.name', '', 'trim');
            $image  = I('post.image', '', 'trim');
            $sort   = I('post.sort', 255, 'int');
            $status = I('post.status', 1, 'int');
            if (!$id) {
                $this->error('请求参数不完整！');
            }
            if (!$name) {
                $this->error('请输入商品分类！');
            }
            if (!$image) {
                $this->error('请上传商品分类图片！');
            }
            $data = array('id' => $id, 'name' => $name, 'cateimg' => $image, 'ordid' => $sort, 'status' => $status);
            $res  = M('items_cate')->save($data);
            if ($res !== false) {
                S('tdk_items_cate', null);
                $this->success('修改成功', U('Items/cate'));
            } else {
                $this->error('修改失败！');
            }
        } else {
            $id   = I('get.id', 1, 'int');
            $info = M('items_cate')->where(array('pid' => 18))->find($id);
            if (!$id || !$info) {
                $this->redirect('cate');
            }
            $this->assign('info', $info);
            $this->display();
        }

    }

    /**
     * 采集商品
     */
    public function searchItem() {
        $id = I('get.id', '', 'trim');
        if ($id) {
            $tao_obj = new \Common\Org\DaTaoKe();
            $res     = $tao_obj->getItem($id);
            $success = $error = $info = '';
            if ($res['result']) {
                $sort = rand(1, 10);
                $info = $res['result'];
                $item = M('items')->where(array('num_iid' => $info['GoodsID']))->find();
                if ($item) {
                    if ($item['ordid'] != 9999) {
                        $error = '您已经提交过此产品，产品排序为' . $item['ordid'];
                    } else {
                        $save_data = array('id' => $item['id'], 'ordid' => $sort);
                        $add_res   = M('items')->save($save_data);
                        if ($add_res) {
                            M('items')->where(array('ordid' => array('lt', 1000), 'id' => array('neq', $item['id']), 'coupon_type' => array('neq', 5)))->setInc('ordid', 3);
                            $success = "更新成功，排序结果为第{$sort}位";
                        } else {
                            $error = '更新失败！';
                        }
                    }
                } else {
                    $sort          = rand(1, 10);
                    $info['ordid'] = $sort;
                    $data          = $tao_obj->getItemData($info, true);
                    $add_res       = M('items')->add($data);
                    if ($add_res) {
                        M('items')->where(array('ordid' => array('lt', 1000), 'id' => array('neq', $add_res), 'coupon_type' => array('neq', 5)))->setInc('ordid', 3);
                        $success = "采集成功，排序结果为第{$sort}位";
                    } else {
                        $error = '采集失败！';
                    }
                }
            } else {
                $error = '未在大淘客中寻找到此款产品,或访问受限';
            }
            $this->assign(array('error' => $error, 'success' => $success, 'info' => $info));
        }
        $this->display();
    }

    /**
     * app内部地址
     */
    public function appUrl() {
        $this->display();
    }

    /**
     * 手工采集
     */
    public function manualCollection() {
        if (IS_AJAX) {
            $num_iid         = I('post.num_iid', '', 'trim');
            $click_url       = I('post.click_url', '', 'trim');
            $coupon_end_time = I('post.coupon_end_time', '', 'trim');
            $price           = I('post.price', '', 'float');
            $coupon_price    = I('post.coupon_price', '', 'float');
            $commission_rate = I('post.commission_rate', '', 'trim');
            $cate_id         = I('post.cate_id', 0, 'int');
            $title           = I('post.title', '', 'trim');
            $intro           = I('post.intro', '', 'trim');
            $is_set          = I('post.is_set', 0, 'int');
            $is_send         = I('post.is_send', 0, 'int');
            $send_title      = I('post.send_title', '', 'trim');
            if (!$num_iid) {
                $this->error('商品ID不存在');
            }
            $count = M('items')->where(array('num_iid' => $num_iid))->count();
            if ($count) {
                $this->error('该商品已在商品库，不能重复采集！');
            }
            $obj      = new \Common\Org\TaoBaoApi();
            $item_res = $obj->getTaoBaoItemInfo($num_iid);
            if ($item_res['code'] == -1) {
                $this->error('商品信息不存在');
            }
            $info = $item_res['data'];
            if (!$click_url) {
                $this->error('商品优惠券链接不能为空');
            }
            $activity_reg = "/(activityId|activity_id|activityid)=([a-z|\d]{20,})/";
            preg_match($activity_reg, $click_url, $res);
            $activity_id = '';
            if (isset($res[2]) && $res[2]) {
                $activity_id = $res[2];
            }
            if (!$activity_id) {
                $this->error('优惠券活动ID不存在');
            }
            if (strtotime($coupon_end_time) + 86399 <= time()) {
                $this->error('优惠券结束时间不能小于当前时间');
            }
            if (!$price) {
                $this->error('商品正价不能不空');
            }
            if (!$coupon_price) {
                $this->error('券后价不能为空');
            }
            if ($coupon_price > $price) {
                $this->error('券后价不能大于商品正价');
            }
            if (!$commission_rate) {
                $this->error('佣金比率不能为空');
            }
            if ($commission_rate >= 100) {
                $this->error('佣金比率不能超过100');
            }
            if (!$cate_id) {
                $this->error('商品分类必须选择');
            }
            if (!$title) {
                $this->error('商品标题不能为空');
            }
            if (!$intro) {
                $this->error('推广文案不能为空');
            }
            if ($is_send == 1 && !$send_title) {
                $this->error('推送内容不能为空');
            }
            $handpick_time = 0;
            if ($is_set == 1) {
                $handpick_time = time();
            }
            if ($coupon_price <= 9.9) {
                $coupon_type = 4;
            } else {
                $coupon_type = 1;
            }
            $coupon_url     = 'http://taoquan.taobao.com/coupon/unify_apply.htm?sellerId=' . $info['seller_id'] . '&activityId=' . $activity_id;
            $mobile_url     = 'http://h5.m.taobao.com/ump/coupon/detail/index.html?sellerId=' . $info['seller_id'] . '&activityId=' . $activity_id;
            $click_url      = 'https://uland.taobao.com/coupon/edetail?activityId=' . $activity_id . '&pid=' . C('PID') . '&itemId=' . $num_iid . '&src=cd_cdll';
            $quan           = $price - $coupon_price;
            $commission     = $coupon_price * ($commission_rate / 100);
            $data           = array(
                'quanurl'           => $coupon_url,//优惠券地址
                'qurl'              => $mobile_url, //短链接
                'quan_url'          => $click_url,
                'activity_id'       => $activity_id,
                'snum'              => 5000, //剩余优惠券
                'lnum'              => 0, //已领取优惠卷
                'quan'              => $quan, //优惠券金额
                'starttime'         => date("Y-m-d", time()),//设置当前时间
                'endtime'           => $coupon_end_time,//结束时间
                'price'             => $price, //正常售价
                'intro'             => $intro, //文案
                'coupon_rate'       => (int)($coupon_price / $price) * 100,
                'sellerId'          => $info['seller_id'], //卖家ID
                'volume'            => $info['volume'], //销量
                'commission_rate'   => $commission_rate * 100, //佣金比例
                'commission'        => $commission, //佣金
                'title'             => $title, //标题
                'click_url'         => $click_url, //领券链接，内含pid
                'num_iid'           => $num_iid, //淘宝商品ID
                'dataoke_id'        => 0,  //大淘客商品ID
                'pic_url'           => $info['pict_url'],
                'coupon_price'      => $coupon_price, //使用优惠券后价格
                'shop_type'         => 'B',
                'passname'          => '已通过',
                'coupon_type'       => $coupon_type,
                'uid'               => 2, //插入信息用户ID
                'uname'             => 'tongyong',
                'isq'               => 1,//单品券
                'tags'              => 0, //标签
                'pass'              => 1, //是否上线
                'coupon_end_time'   => strtotime($coupon_end_time) + 86399,
                'cate_id'           => $cate_id, //分类
                'coupon_start_time' => time(),
                'ordid'             => 9999, //商品排序
                'desc'              => 0,
                'item_url'          => $info['item_url'],
                'handpick_time'     => $handpick_time,
                'nick'              => $info['nick'],
            );
            $item_insert_id = M('items')->add($data);
            if ($item_insert_id) {
                if ($is_send && $send_title) {
                    //$pid = 'mm_121610813_25332120_118252738';
                    //$this->_sendJPush($send_title, array('alias' => array($pid)), array('num_iid' => $num_iid, 'goods_type' => 'tdk'), 'all');
                    $this->_sendJPush($send_title, 'all', array('num_iid' => $num_iid, 'goods_type' => 'tdk'), 'all');
                }
                $this->success('添加成功');
            }
        } else {
            $content = I('get.content', '', 'trim,urldecode');
            if ($content) {
                $success = $error = '';
                $info    = array();
                $id      = 0;
                if (preg_match('/^\d{10,20}/', $content)) {
                    $id = $content;
                } else {
                    if (stripos($content, 'http') == 0) {
                        $exp_reg = "/(\?id|\&id)\=(\d{10,20})/";
                        preg_match($exp_reg, $content, $reg);
                        if (isset($reg[2]) && $reg[2]) {
                            $id = $reg[2];
                        }
                    } else {
                        $result = $this->_checkContent($content);
                        if ($result['code'] == -1) {
                            $error = $result['msg'];
                        } else {
                            $id = $result['data']['id'];
                            $this->assign('item_content', $result['data']);
                        }
                    }
                }
                if (!$error) {
                    if ($id) {
                        $count = M('items')->where(array('num_iid' => $id))->count();
                        if ($count) {
                            $error = '该商品已在商品库，不能重复采集！';
                        } else {
                            $obj      = new \Common\Org\TaoBaoApi();
                            $item_res = $obj->getTaoBaoItemInfo($id);
                            if ($item_res['code'] == -1) {
                                $error = "商品信息不存在";
                            }
                            $info = $item_res['data'];
                        }
                    } else {
                        $error = '商品ID不存在';
                    }
                }
                $this->assign(array('error' => $error, 'success' => $success, 'info' => $info));
            }
            $cate = array(
                20 => '女装',
                21 => '母婴',
                22 => '美妆',
                23 => '家具',
                24 => '鞋包',
                25 => '美食',
                26 => '文体车品',
                27 => '数码家电',
                29 => '男装',
                30 => '内衣',
            );
            $this->assign('cate', $cate);
            $this->display();
        }
    }

    /**
     * 检测用户输入的内容
     */
    protected function _checkContent($content) {
        $return_data = array('code' => -1, 'msg' => 'error', 'data' => '');
        $exp_reg     = "/(.+)[\n|\s]+(.+)[\n|\s]+(.+)[\n|\s]+(.+)[\n|\s]+(.*)/";
        preg_match($exp_reg, $content, $result);
        if (count($result) < 5) {
            $return_data['msg'] = '推广信息不合法';
            return $return_data;
        }
        $title         = $result[1];
        $intro         = $result[5];
        $price_exp_reg = "/([0-9]+\.*[0-9]*)/";
        preg_match_all($price_exp_reg, $result[2], $price_res);
        $price        = $price_res[0][0];
        $coupon_price = $price_res[0][1];
        $http_exp_reg = "/((http|https):[\/]{2}[a-zA-Z\d\.#\?\/\=\&\_]*)/";
        preg_match($http_exp_reg, $result[3], $http_res);
        if (!isset($http_res[0]) || !$http_res[0]) {
            $return_data['msg'] = '领券链接不合法';
            return $return_data;
        }
        $click_url        = $http_res[0];
        $activity_exp_reg = "/(activityId|activity_id|activityid)=([a-z|\d]{20,})/";
        preg_match($activity_exp_reg, $click_url, $activity);
        if (!isset($activity[1]) || count($activity[1]) != 1) {
            $return_data['msg'] = '领券链接缺少活动ID';
            return $return_data;
        }
        preg_match($http_exp_reg, $result[4], $http_item_res);
        if (!isset($http_item_res[0]) || !$http_item_res[0] || strpos($http_item_res[0], 's.click.taobao.com')) {
            $return_data['msg'] = '商品链接不合法';
            return $return_data;
        }
        $item_url     = $http_item_res[0];
        $item_exp_reg = "/id=(\d{10,20})/";
        preg_match($item_exp_reg, $item_url, $item);
        if (!isset($item[1]) || !$item[1]) {
            $return_data['msg'] = '商品链接中不存在商品编号，无法转链！';
            return $return_data;
        }
        $return_data = array(
            'code' => 1,
            'msg'  => 'ok',
            'data' => array(
                'id'           => $item[1],
                'click_url'    => $click_url,
                'price'        => $price,
                'coupon_price' => $coupon_price,
                'title'        => $title,
                'intro'        => $intro
            ));
        return $return_data;
    }

    /**
     * 群采集商品列表
     */
    public function groupItemInfo() {
        $model     = M('items');
        $title     = I('get.title', '', 'trim');
        $num_iid   = I('get.num_iid', '', 'trim');
        $cate_id   = I('get.cate_id', 0, 'int');
        $jing      = I('get.jing', 0, 'int');
        $pass      = I('get.pass', 0, 'int');
        $shop_type = I('get.shop_type', 0, 'trim');
        $where     = array('uid' => '3');
        if ($title) {
            $where['title'] = array('like', "%{$title}%");
        }
        if ($num_iid) {
            $where['num_iid'] = $num_iid;
        }
        if ($cate_id) {
            if ($cate_id == 28) {
                $where['coupon_type'] = 4;
            } else if ($cate_id == 1029) {
                $where['shop_type'] = 'J';
            } else {
                $where['cate_id'] = $cate_id;
            }
        }
        if ($shop_type) {
            $where['shop_type'] = $shop_type;
        }
        if ($jing == 1) {
            $where['handpick_time'] = array('neq', 0);
        } elseif ($jing == 2) {
            $where['handpick_time'] = 0;
        }
        if ($pass == 1) {
            $where['pass'] = 1;
        }
        $count     = $model->where($where)->count('id');
        $page      = $this->pages($count, $this->limit);
        $data      = $model->where($where)->order('ordid asc,id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $cate      = M('items_cate')->where(array('pid' => 18, 'status' => 1))->getField('id,name');
        $shop_type = array('B' => '天猫', 'J' => '楚楚街', 'D' => '京东', 'C' => '淘宝');
        $this->assign(array('page' => $page->show(), 'data' => $data, 'count' => $count, 'cate' => $cate, 'shop_type' => $shop_type));
        $this->display();
    }

    /**
     * 群采集商品编辑
     */
    public function groupItemInfoEdit() {
        $id    = I('get.id', 0, 'int');
        $model = M('items');
        if (IS_AJAX) {
            $num_iid         = I('post.num_iid', '', 'trim');
            $click_url       = I('post.click_url', '', 'trim');
            $coupon_end_time = I('post.coupon_end_time', '', 'trim');
            $price           = I('post.price', 0, 'float');
            $coupon_price    = I('post.coupon_price', 0, 'float');
            $commission_rate = I('post.commission_rate', 0, 'float');
            $cate_id         = I('post.cate_id', 0, 'float');
            $title           = I('post.title', '', 'trim');
            $intro           = I('post.intro', '', 'trim');
            $is_set          = I('post.is_set', 0, 'int');
            $is_send         = I('post.is_send', 0, 'int');
            $send_title      = I('post.send_title', '', 'trim');

            if (!$price) {
                $this->error('商品正价不能不空');
            }
            if (!$coupon_price) {
                $this->error('券后价不能为空');
            }
            if ($coupon_price > $price) {
                $this->error('券后价不能大于商品正价');
            }
            if (!$commission_rate) {
                $this->error('佣金比率不能为空');
            }
            if ($commission_rate >= 100) {
                $this->error('佣金比率不能超过100');
            }
            if (!$cate_id) {
                $this->error('商品分类必须选择');
            }
            if (!$title) {
                $this->error('商品标题不能为空');
            }
            if (!$intro) {
                $this->error('推广文案不能为空');
            }
            if ($is_send == 1 && !$send_title) {
                $this->error('推送内容不能为空');
            }
            $handpick_time = 0;
            if ($is_set == 1) {
                $handpick_time = time();
            }
            if ($coupon_price <= 9.9) {
                $coupon_type = 4;
            } else {
                $coupon_type = 1;
            }

            $commission   = $coupon_price * ($commission_rate / 100);
            $data         = array(
                'quan_url'        => $click_url,
                'starttime'       => date("Y-m-d", time()),//设置当前时间
                'endtime'         => $coupon_end_time,//结束时间
                'price'           => $price, //正常售价
                'intro'           => $intro, //文案
                'coupon_rate'     => (int)($coupon_price / $price) * 100,
                'commission_rate' => $commission_rate * 100, //佣金比例
                'commission'      => $commission, //佣金
                'title'           => $title, //标题
                'click_url'       => $click_url, //领券链接，内含pid
                'dataoke_id'      => 0,  //大淘客商品ID
                'passname'        => '已通过',
                'coupon_type'     => $coupon_type,
                'uname'           => 'tongyong',
                'isq'             => 1,//单品券
                'tags'            => 0, //标签
                'pass'            => 1, //是否上线
                'coupon_end_time' => strtotime($coupon_end_time),
                'cate_id'         => $cate_id, //分类
                'handpick_time'   => $handpick_time,
            );
            $item_edit_id = $model->where(array('num_iid' => $num_iid))->save($data);
            if ($item_edit_id) {
                if ($is_send && $send_title) {
                    //$pid = 'mm_121610813_25332120_118252738';
                    //$this->_sendJPush($send_title, array('alias' => array($pid)), array('num_iid' => $num_iid, 'goods_type' => 'tdk'), 'all');
                    $this->_sendJPush($send_title, 'all', array('num_iid' => $num_iid, 'goods_type' => 'tdk'), 'all');
                }
                $this->success('编辑成功');
            } else {
                $this->error('编辑失败');
            }

        }

        $where     = array('uid' => '3', 'id' => $id);
        $data      = $model->where($where)->find();
        $cate      = M('items_cate')->where(array('pid' => 18, 'status' => 1))->getField('id,name');
        $shop_type = array('B' => '天猫', 'J' => '楚楚街', 'D' => '京东', 'C' => '淘宝');
        $this->assign(array('data' => $data, 'cate' => $cate, 'shop_type' => $shop_type));
        $this->display();
    }

    /**
     * 群采集商品删除
     */
    public function groupItemInfoDel() {
        $num_iid = I('post.num_iid', '', 'trim');
        $res     = M('items')->where(array('num_iid' => $num_iid))->delete();
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 搜索词频分析
     */
    public function searchTagList() {
        $type = I('get.type', 'tag', 'trim');
        $day  = I('get.day', 3, 'int');
        if ($type == 'tag') {
            $data = $this->_getTagList($day);
        } else {
            $data = $this->_getContentList($day);
        }
        $this->assign(array('type' => $type, 'day' => $day, 'data' => $data));
        $this->display();
    }

    /**
     * 获取分词数据
     *
     * @param $day
     * @return array
     */
    protected function _getTagList($day) {
        $cache_tags = $this->_getCacheTags();
        switch ($day) {
            case 1 :
                $where = array();
                break;
            case 2 :
                $where = array('day' => date('Ymd', strtotime("-1 days")));
                break;
            case 3 :
                $where = array('day' => array('egt', date("Ymd", strtotime("-2 days"))));
                break;
            case 4 :
                $where = array('day' => array('egt', date("Ymd", strtotime("-3 days"))));
                break;
            case 5 :
                $where = array('day' => array('egt', date("Ymd", strtotime("-4 days"))));
                break;
            case 6 :
                $where = array('day' => array('egt', date("Ymd", strtotime("-5 days"))));
                break;
            case 7 :
                $where = array('day' => array('egt', date("Ymd", strtotime("-6 days"))));
                break;
            default :
                $where = array();
                break;
        }
        $db_data = array();
        if ($where) {
            $word_data = M('search_hot_word')->field('word,sum(num) as total')->where($where)->group('word')->order('total desc')->limit(300)->select();
            foreach ($word_data as $hot_word) {
                $db_data[$hot_word['word']] = $hot_word['total'];
            }
        }
        $data = array_merge($db_data, $cache_tags);
        arsort($data);
        return array_slice($data, 0, 200);
    }

    /**
     * 获取分词数据
     *
     * @param $day
     * @return array
     */
    protected function _getContentList($day) {
        switch ($day) {
            case 1 :
                $where = array('add_time' => array('gt', strtotime(date('Y-m-d'))));
                break;
            case 2 :
                $where = array('add_time' => array('gt', strtotime("-1 days")));
                break;
            case 3 :
                $where = array('add_time' => array('gt', strtotime("-2 days")));
                break;
            case 4 :
                $where = array('add_time' => array('gt', strtotime("-3 days")));
                break;
            case 5 :
                $where = array('add_time' => array('gt', strtotime("-4 days")));
                break;
            case 6 :
                $where = array('add_time' => array('gt', strtotime("-5 days")));
                break;
            case 7 :
                $where = array('add_time' => array('gt', strtotime("-6 days")));
                break;
            default :
                $where = array('add_time' => array('gt', strtotime(date('Y-m-d'))));
                break;
        }
        $data    = array();
        $db_data = M('search_list')->field('search_keyword_md5,search_keyword as word,count(id) as total,group_concat(user_id) as user_ids_str')->where($where)->group('search_keyword_md5')->order('total desc')->limit(200)->select();
        foreach ($db_data as $val) {
            $user_num           = count(array_unique(explode(',', $val['user_ids_str'])));
            $data[$val['word']] = $val['total'] . "({$user_num}人次)";
        }
        return $data;
    }
}