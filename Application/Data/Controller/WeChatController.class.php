<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/18 0018
 * Time: 下午 4:14
 */

namespace Data\Controller;

use Common\Org\Http;
use think\Exception;

/**
 * 大微信客数据采集器
 * Class WeChatController
 *
 * @package Data\Controller
 */
class WeChatController extends CommonController {

    /**
     * 大微信客app_id
     *
     * @var string
     */
    private $app_id = "cc8d74f0f1bda570cb2be9bd53319c81d0";

    /**
     * 大微信客密匙
     *
     * @var string
     */
    private $app_pass = "nkyuysv8YSbyjzQKiuIluvrA1YCTABul2TAIGY78xEfyieWd";

    /**
     * token
     *
     * @var string
     */
    private $token = "";

    /**
     * 请求地址
     *
     * @var string
     */
    private $host = "https://openapi.daweixinke.com";

    /**
     * url
     * @var string
     */
    private $public_url = "https://www.daweixinke.com/sqe.php";

    /**
     * 构造函数
     * WeChatController constructor.
     */
    public function __construct() {
        parent::__construct();
        set_time_limit(600);
    }

    /**
     * 是否需要判断token
     * @param $act
     */
    public function _checkToken($act) {
        $token = S('weChat_token');
        if (!$token) {
            $this->redirect('getCode', array('act' => $act));
        } else {
            if (time() > $token['time']) {
                $res = $this->_getRefreshToken($token['refresh_token']);
                if ($res == false) {
                    exit('获取token失败！无法获取数据');
                }
            } else {
                $this->token = $token['token'];
            }
        }
    }

    /**
     * 微信授权登陆
     */
    public function getCode() {
        $code = I('get.code', '', 'trim');
        $act  = I('get.act', 'getApiData', 'trim');
        if (!$code) {
            $redirect_url = "http://tao.taodianke.com/Data/WeChat/getCode";
            $url          = $this->host . "/index.php/Auth/Auth/auth?app_id={$this->app_id}&redirect_uri={$redirect_url}&response_type=code&state=$act";
            header("Location: $url");
            exit;
        } else {
            $res = $this->_getAccToken($code);
            if ($res == false) {
                exit('获取token失败！无法获取数据');
            }
            $this->redirect($act);
        }
    }

    /**
     * @param $code
     * @return bool
     */
    protected function _getAccToken($code) {
        $data = array(
            'app_id'        => $this->app_id,
            'app_secret'    => $this->app_pass,
            'code'          => $code,
            'response_type' => 'access_token'
        );
        $url  = $this->host . "/index.php/Auth/Auth/token";
        $http = new \Common\Org\Http();
        $res  = $http->post($url, $data);
        $res  = json_decode($res, true);
        if ($res['status'] == 1) {
            $session_data = array(
                'token'         => $res['data']['access_token'],
                'time'          => time() + 7200,
                'refresh_token' => $res['data']['refresh_token'],
            );
            S('weChat_token', $session_data);
            $this->token = $res['data']['access_token'];
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $refresh_token
     * @return bool
     */
    protected function _getRefreshToken($refresh_token) {
        $data = array(
            'app_id'        => $this->app_id,
            'refresh_token' => $refresh_token,
            'response_type' => 'refresh_token'
        );
        $url  = $this->host . "/index.php/Auth/Auth/refresh";
        $http = new \Common\Org\Http();
        $res  = $http->post($url, $data);
        $res  = json_decode($res, true);
        if ($res['status'] == 1) {
            $session_data = array(
                'token'         => $res['data']['access_token'],
                'time'          => time() + 7200,
                'refresh_token' => $res['data']['refresh_token'],
            );
            session('weChat_token', $session_data);
            $this->token = $res['data']['access_token'];
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取选品数据
     */
    public function getApiData() {
        $this->_checkToken('getApiData');
        $page     = I('get.page', 1, 'int');
        $url      = $this->host . "/index.php/Api/items/getItemsData";
        $get_data = array('app_id' => $this->app_id, 'access_token' => $this->token, 'limit' => 50, 'page' => $page);
        $http     = new \Common\Org\Http();
        $res      = $http->get($url, $get_data);
        $res      = json_decode($res, true);
        if ($res['status'] != 1) {
            file_put_contents('/home/order_log/weChat_api.log', var_export($res, true) . "\r\n", FILE_APPEND);
        }
        $data = $res['data']['list'];
        dump($data);
        exit;
    }

    /**
     * 获取下线商品
     */
    public function getOfflineData() {
        $this->_checkToken('getOfflineData');
        $url           = $this->host . "/index.php/Api/items/getItemsData";
        $start_item_id = S('tdk_start_items_id') ? S('tdk_start_items_id') : 0;
        $list          = M('items')->field('id,activity_id,platform_info')->where(array('id' => array('egt', $start_item_id), 'shop_type' => 'J'))->limit(501)->select();
        if (count($list) == 501) {
            $end = array_pop($list);
        }
        $del_data = array();
        foreach ($list as $v) {
            usleep(300000);
            $platform_info = unserialize($v['platform_info']);
            $get_data      = array('app_id' => $this->app_id, 'access_token' => $this->token, 'coupon_id' => $v['activity_id'], 'ad_id' => $platform_info['ad_id']);
            $http          = new \Common\Org\Http();
            $res_temp           = $http->get($url, $get_data);
            $res           = json_decode($res_temp, true);
            if ($res['status'] != 1) {
                $res['time'] = date('Y-m-d H:i:s');
                if ($res['status'] == 404) {
                    $del_data[] = $v['id'];
                    //$this->_addLog('weChat_offline.log', var_export($get_data, true) . '|' . $res_temp);
                }
            } else {
                $v['time'] = date('Y-m-d H:i:s');
            }
        }
        if ($del_data) {
            M('items')->delete($del_data);
        }
        if (isset($end) && $end) {
            S('tdk_start_items_id', $end['id']);
        } else {
            S('tdk_start_items_id', null);
        }
        echo "success";
    }

    /**
     * 获取推广物料接口
     */
    public function getExtensionData() {
        $this->_checkToken('getExtensionData');
        $page     = I('get.page', 1, 'int');
        $url      = $this->host . "/index.php/Api/items/getCCKItemList";
        $get_data = array('app_id' => $this->app_id, 'access_token' => $this->token, 'limit' => 20, 'page' => $page);
        $http     = new \Common\Org\Http();
        $res      = $http->get($url, $get_data);
        $res      = json_decode($res, true);
        if ($res['status'] != 1) {
            $res['time'] = date('Y-m-d H:i:s');
            $this->_addLog('weChat_extension.log', $res);
        }
        $data = $res['data']['list'];
        dump($data);
        exit;
    }

    /**
     * 获取分享URL
     */
    public function getShareUrl() {
        $url       = $this->public_url . '?s=/CCKItem/addCCKItem';
        $post_data = array(
            'data[adId]'       => '170831153723001I0866666851995357',
            'data[itemId]'     => 'hUHSfNHtm5q9HF0tr8aKDrtrxt89KP51975197',
            'data[adZoneId]'   => '29828',
            'data[add_source]' => 'home',
            'data[isVideo]'    => 0,
            'data[platform]'   => 'web',
        );
        $cookie    = C('DWXK.cookie');
        $http      = new Http();
        $res       = $http->postCookie($url, $cookie, $post_data);
        $res       = json_decode($res, true);
        dump($res);
        exit;
    }

    /**
     * 获取订单信息
     */
    public function getPayOrder() {
        //订单状态 1代表订单未退款查询
        $order_status = 1;
        //订单类型 1代表订单支付日期查询
        $order_type = 1;
        //订单支付日期开始时间
        $start_time = date('Y-m-d', strtotime('-1 days'));
        //订单支付日期结束时间
        $end_time   = date('Y-m-d');
        $order_data = $this->_getOrder($order_status, $order_type, $start_time, $end_time);
        if ($order_data) {
            $data = array();
            foreach ($order_data as $val) {
                $count = M('order')->where(array('order_id' => $val['third_tradeno']))->count('id');
                if ($count > 0) {
                    continue;
                }
                $num = substr($val['product_id'], 0, 3);
                if ($num == 100) {
                    $url = "http://ccj.chuchutong.com/details/detail.html?id={$val['product_id']}";
                } else {
                    $url = "http://m.chuchutong.com/details/detail.html?id={$val['product_id']}";
                }
                switch ($val['order_status_code']) {
                    case 2 :
                        $pay_status = 'paid';
                        break;
                    case 3 :
                        $pay_status = 'paid';
                        break;
                    case 4 :
                        $pay_status = 'paid';
                        break;
                    case 6 :
                        $pay_status = 'settle';
                        break;
                    default :
                        $pay_status = 'fail';
                        break;
                }
                $data[] = array(
                    'order_id'        => $val['third_tradeno'],
                    'item_id'         => $val['product_id'],
                    'number'          => $val['sale_num'],
                    'order_num'       => 1,
                    'title'           => $val['ad_name'],
                    'commission_rate' => str_replace('%', '', $val['cck_rate']),
                    'share_rate'      => 100,
                    'fee'             => $val['commission'],
                    'price'           => $val['item_price'],
                    'total_money'     => $val['item_price'] * $val['sale_num'],
                    'create_time'     => $val['order_pay_time'],
                    'pay_status'      => $pay_status,
                    'shop_type'  => 'J',
                    'order_type'      => '楚楚街',
                    'action_url'      => $url,
                    'earning_time'    => $val['order_settlement_time'] ? $val['order_settlement_time'] : '',
                    'pid'             => $val['sign_id'],
                    'img'             => $val['image_urls_head']
                );
            }
            if ($data) {
                $res = M('order')->addAll($data);
                if ($res) {
                    $log = array('time' => date('Y-m-d H:i:s'), 'info' => 'add success');
                    $this->_addLog('weChat_pay_order.log', $log);
                    echo 'add success';
                } else {
                    $log = array('time' => date('Y-m-d H:i:s'), 'info' => 'fail', 'error' => M('order')->getDbError());
                    $this->_addLog('weChat_pay_order.log', $log);
                    echo 'add fail';
                }
            } else {
                $log = array('time' => date('Y-m-d H:i:s'), 'info' => 'not have new data');
                $this->_addLog('weChat_pay_order.log', $log);
                echo "not have new data";
            }
        } else {
            $log = array('time' => date('Y-m-d H:i:s'), 'info' => 'not have data');
            $this->_addLog('weChat_pay_order.log', $log);
            echo "not have data";
        }
    }

    /**
     * 获取订单信息
     */
    public function getSettleOrder() {
        //订单状态 1代表订单未退款查询
        $order_status = 1;
        //订单类型 1代表订单结算日期查询
        $order_type = 3;
        //订单结算日期开始时间
        $start_time = date('Y-m-d', strtotime('-1 days'));
        //订单结算日期结束时间
        $end_time   = date('Y-m-d');
        $order_data = $this->_getOrder($order_status, $order_type, $start_time, $end_time);
        if ($order_data) {
            foreach ($order_data as $val) {
                $count = M('order')->where(array('order_id' => $val['third_tradeno'], 'pay_status' => 'settle'))->count('id');
                if ((int)$count == 1) {
                    continue;
                }
                $pay_status            = 'settle';
                $save_data             = array(
                    'pay_status'   => $pay_status,
                    'earning_time' => $val['order_settlement_time'],
                );
                $res                   = M('order')->where(array('order_id' => $val['third_tradeno']))->save($save_data);
                $save_data['order_id'] = $val['third_tradeno'];
                if ($res) {
                    $log = array('time' => date('Y-m-d H:i:s'), 'info' => 'save success', 'data' => $save_data);
                    $this->_addLog('weChat_settle_order.log', $log);
                } else {
                    $log = array('time' => date('Y-m-d H:i:s'), 'info' => 'save fail', 'data' => $save_data, 'error' => M('order')->getDbError());
                    $this->_addLog('weChat_settle_order.log', $log);
                }
            }
            echo 'save success';
        } else {
            $log = array('time' => date('Y-m-d H:i:s'), 'info' => 'not have data');
            $this->_addLog('weChat_settle_order.log', $log);
            echo "not have data";
        }
    }

    /**
     * 获取订单信息
     */
    public function getRefundOrder() {
        //订单状态 1代表订单已退款查询
        $order_status = 2;
        //订单类型 1代表订单支付日期查询
        $order_type = 1;
        //订单支付日期开始时间
        $start_time = date('Y-m-d', strtotime('-20 days'));
        //订单支付日期结束时间
        $end_time   = date('Y-m-d');
        $order_data = $this->_getOrder($order_status, $order_type, $start_time, $end_time);
        if ($order_data) {
            foreach ($order_data as $val) {
                $count = M('order')->where(array('order_id' => $val['third_tradeno'], 'pay_status' => 'fail'))->count('id');
                if ((int)$count == 1) {
                    continue;
                }
                $pay_status            = 'fail';
                $save_data             = array(
                    'pay_status' => $pay_status,
                );
                $res                   = M('order')->where(array('order_id' => $val['third_tradeno']))->save($save_data);
                $save_data['order_id'] = $val['third_tradeno'];
                if ($res) {
                    $log = array('time' => date('Y-m-d H:i:s'), 'info' => 'save success', 'data' => $save_data);
                    $this->_addLog('weChat_refund_order.log', $log);
                } else {
                    $log = array('time' => date('Y-m-d H:i:s'), 'info' => 'save fail', 'data' => $save_data, 'error' => M('order')->getDbError());
                    $this->_addLog('weChat_refund_order.log', $log);
                }
            }
            echo 'save success';
        } else {
            $log = array('time' => date('Y-m-d H:i:s'), 'info' => 'not have data');
            $this->_addLog('weChat_refund_order.log', $log);
            echo "not have data";
        }
    }

    /**
     * 获取订单
     * @param $order_status
     * @param $order_type
     * @param $start_time
     * @param $end_time
     * @return array
     */
    protected function _getOrder($order_status, $order_type, $start_time, $end_time) {
        $url    = $this->public_url . '?s=/Order/viewCCKOrderDetail';
        $cookie = C('DWXK.cookie');
        $page   = I('get.page', 1, 'int');
        $status = true;
        $data   = array();
        while ($status === true) {
            $post_data = array(
                'data[page]'     => 1,
                'data[pageNum]'  => 20,
                'data[status]'   => $order_status,
                'data[type]'     => $order_type,
                'data[start]'    => $start_time,
                'data[end]'      => $end_time,
                'data[platform]' => 'web',
            );
            $http      = new Http();
            $res       = $http->postCookie($url, $cookie, $post_data);
            $res       = json_decode($res, true);

            if ($res['status'] != 1) {
                $res['time'] = date('Y-m-d H:i:s');
                $this->_addLog('weChat_order_fail.log', $res);
            }
            $temp_data = $res['data']['data'];
            $data      = array_merge($data, $temp_data);
            if (count($temp_data) < 20) {
                $status = false;
            } else {
                $page++;
            }
        }
        return $data;
    }

    /**
     * 方案一（每个分类采集固定分页或总数的百分比）
     * 采集商品数据
     */
    public function getData() {
        $url       = $this->public_url . '?s=/CCKItem/getAdItemList';
        $page_size = 20;
        $cookie    = C('DWXK.cookie');
        $http      = new Http();
        $data      = $add_data = array();
        for ($i = 1; $i <= 14; $i++) {
            if ($i == 11) {
                continue;
            }
            for ($j = 1; $j <= 5; $j++) {
                usleep(800000);
                list($t1, $t2) = explode(' ', microtime());
                $time      = $t2 . ceil($t1 * 1000);
                $post_data = array(
                    'data[page]'         => $j,
                    'data[pageNum]'      => $page_size,
                    'data[order_status]' => 9,
                    'data[random]'       => $time,
                    'data[c1]'           => -$i,
                    'data[platform]'     => 'web'
                );
                $res       = $http->postCookie($url, $cookie, $post_data);
                $res       = json_decode($res, true);
                if ($res['status'] != 1) {
                    file_put_contents('/home/order_log/weChat_get_data.log', var_export($res, true) . "\r\n", FILE_APPEND);
                    continue;
                }
                foreach ($res['data']['data'] as $val) {
                    $data[$val['product_id']] = array(
                        'item_id' => $val['product_id']
                    );
                }
                if (count($res['data']['data']) != $page_size) {
                    break;
                }
            }
        }
        $model = M('items');
        foreach ($data as $k => $v) {
            $count = $model->where(array('item_id' => $v['num_iid'], 'shop_type' => 'J'))->count('id');
            if ($count > 0) {
                continue;
            }
            $add_data[$v['item_id']] = $v;
        }
        if ($add_data) {
            if (count($add_data) > 500) {
                $add_chunk_data = array_chunk($add_data, 500);
                $model->startTrans();
                try {
                    foreach ($add_chunk_data as $t) {
                        $model->addAll(array_values($t));
                    }
                    $model->commit();
                    echo "add success";
                } catch (\Exception $e) {
                    $model->rollback();
                    echo "add error";
                }
            } else {
                try {
                    $model->addAll(array_values($add_data));
                    echo "add success";
                } catch (\Exception $e) {
                    echo "add error";
                }
            }
        } else {
            echo 'no have data';
        }
    }

    /**
     * 方案二（每个分类全部采集）
     * 采集商品数据
     */
    public function addData() {
        $url       = $this->public_url . '?s=/CCKItem/getAdItemList';
        $page_size = 20;
        $cookie    = C('DWXK.cookie');
        $http      = new Http();
        $data      = $add_data = array();
        $num       = S('tdk_cate_id') ? S('tdk_cate_id') : 1;
        $num       = $num == 11 ? 12 : $num;
        $status    = true;
        $page      = 1;
        $time      = time();
        if ($num > 14) {
            die("not have cate");
        }
        while ($status == true) {
            if (time() - $time > 590) {
                $this->_addLog('WeChat_add_data_time_out.log', array('status' => 'time out', 'num' => $num, 'time' => date('Y-m-d H:i:s')));
                break;
            }
            usleep(800000);
            list($t1, $t2) = explode(' ', microtime());
            $time      = $t2 . ceil($t1 * 1000);
            $post_data = array(
                'data[page]'         => $page,
                'data[pageNum]'      => $page_size,
                'data[order_status]' => 9,
                'data[random]'       => $time,
                'data[c1]'           => -$num,
                'data[platform]'     => 'web'
            );
            $res       = $http->postCookie($url, $cookie, $post_data);
            $res       = json_decode($res, true);
            if ($res['status'] != 1) {
                $res['time'] = date('Y-m-d H:i:s');
                $this->_addLog('WeChat_add_data.log', $res);
                break;
            }
            $this->_createData($res['data']['data'], $data);
            if (count($res['data']['data']) != $page_size) {
                $status = false;
            }
            $page++;
        }
        $model = M('items');
        foreach ($data as $k => $v) {
            $count = $model->where(array('num_iid' => $v['num_iid'], 'shop_type' => 'J'))->count('id');
            if ($count > 0) {
                continue;
            }
            $add_data[$v['num_iid']] = $v;
        }

        if ($add_data) {
            if (count($add_data) > 500) {
                $add_chunk_data = array_chunk($add_data, 500);
                $model->startTrans();
                try {
                    foreach ($add_chunk_data as $t) {
                        $model->addAll(array_values($t));
                    }
                    $model->commit();
                    $this->_addLog('WeChat_add_data.log', array('status' => 'success', 'num' => $num, 'time' => date('Y-m-d H:i:s')));
                    echo "add success";
                } catch (\Exception $e) {
                    $model->rollback();
                    $this->_addLog('WeChat_add_data.log', array('status' => 'fail', 'num' => $num, 'time' => date('Y-m-d H:i:s')));
                    echo "add fail";
                }
            } else {
                try {
                    $model->addAll(array_values($add_data));
                    $this->_addLog('WeChat_add_data.log', array('status' => 'success', 'num' => $num, 'time' => date('Y-m-d H:i:s')));
                    echo "add success";
                } catch (\Exception $e) {
                    $this->_addLog('WeChat_add_data.log', array('status' => 'fail', 'num' => $num, 'time' => date('Y-m-d H:i:s')));
                    echo "add fail";
                }
            }
        } else {
            $this->_addLog('WeChat_add_data.log', array('status' => 'no have data', 'num' => $num, 'time' => date('Y-m-d H:i:s')));
            echo 'not have data';
        }
        $num++;
        if ($num > 14) {
            S('tdk_cate_id', 1);
        } else {
            S('tdk_cate_id', $num);
        }
    }

    /**
     * 获取分类ID
     * @param $key
     */
    protected function _getCateId($key) {
        $cate_data = array(
            -1  => 20,
            -2  => 24,
            -3  => 24,
            -4  => 22,
            -5  => 25,
            -6  => 23,
            -7  => 26,
            -8  => 21,
            -9  => 27,
            -10 => 27,
            -11 => 28,
            -12 => 24,
            -13 => 30,
            -14 => 29,
        );
        if (isset($cate_data[$key])) {
            return $cate_data[$key];
        }
        return 10000;
    }

    /**
     * @param $data
     * @param $return_data
     */
    protected function _createData($data, &$return_data) {
        foreach ($data as $val) {
            if ($val['status'] == 4) {
                continue;
            }
            $cate_key        = $val['c1'];
            $coupon_price    = $val['ad_coupon_price'];
            $cate_id         = $this->_getCateId($cate_key);
            $coupon_id       = $val['coupon_id'];
            $product_id      = $val['product_id'];
            $ad_id           = $val['ad_id'];
            $item_id         = $val['item_id'];
            $is_video        = $val['is_video'];
            $detail_url      = $val['detailUrl'];
            $img             = $val['image_urls_head'];
            $commission_rate = trim(str_replace('%', '', $val['cck_rate'])) * 100;

            if ($commission_rate < 1800 && $val['commission'] < 5) { //对于佣金比例且佣金额过低的，暂不保存
                continue;
            }

            $temp_url_data   = explode('.', $detail_url);
            $coupon_url      = "{$temp_url_data[0]}.chuchutong.com/details/getcoupons.html?couponId={$coupon_id}&productId={$product_id}";
            if ($val['ad_coupon_price'] <= 9.9) {
                $cate_key = -11;
            }
            $return_data[$val['product_id']] = array(
                'quanurl'           => $coupon_url,//优惠券地址
                'qurl'              => $coupon_url, //短链接
                'quan_url'          => $coupon_url,
                'item_url'          => $detail_url,
                'activity_id'       => $coupon_id,
                'snum'              => $val['receive_num'], //剩余优惠券
                'lnum'              => $val['used_num'], //已领取优惠卷
                'quan'              => $val['money'], //优惠券金额
                'starttime'         => $val['start_time'],//设置当前时间
                'endtime'           => $val['end_time'],//结束时间
                'price'             => $val['ad_price'], //正常售价
                'intro'             => $val['ad_name'], //文案
                'coupon_rate'       => (int)($val['ad_price'] / $coupon_price) * 100,
                'sellerId'          => $val['app_shop_id'], //卖家ID
                'volume'            => $val['sales_num'], //销量
                'commission_rate'   => $commission_rate, //佣金比例
                'commission'        => $val['commission'], //佣金
                'title'             => $val['ad_name'], //标题
                'click_url'         => $coupon_url, //领券链接，内含pid
                'num_iid'           => $product_id, //淘宝商品ID
                'dataoke_id'        => 0,  //大淘客商品ID
                'pic_url'           => $img,
                'coupon_price'      => $coupon_price, //使用优惠券后价格
                'shop_type'         => 'J',
                'passname'          => '已通过',
                'coupon_type'       => $cate_key == -11 ? 4 : 1,
                'uid'               => 1, //插入信息用户ID
                'uname'             => 'tongyong',
                'isq'               => 1,//单品券
                'tags'              => 0, //标签
                'pass'              => 1, //是否上线
                'coupon_end_time'   => strtotime($val['end_time']),
                'cate_id'           => $cate_id, //分类
                'coupon_start_time' => time(),
                'ordid'             => 9999, //商品排序
                'desc'              => 0,
                'platform_info'     => serialize(array('ad_id' => $ad_id, 'item_id' => $item_id, 'is_video' => $is_video)),
            );
        }
    }
}