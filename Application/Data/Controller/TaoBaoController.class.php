<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/20 0020
 * Time: 上午 10:42
 */

namespace Data\Controller;

use Common\Org\Http;

/**
 * 获取淘宝订单数据
 * Class TaoBaoApiController
 *
 * @package Data\Controller
 */
class TaoBaoController extends CommonController {

    /**
     * @var string
     */
    protected $appId = 'wx38a0adba37145fe0';

    /**
     * @var string
     */
    protected $appSecret = '4b5d39d3104cc756f19209aa36093f35';

    /**
     * 微信用户发送
     *
     * @var array
     */
    protected $weChatUser = array(
        //'opQZy1XRiD-7Jl3jo7ate99pcID0', //马
        //'opQZy1dV3rcNYzUQNxANq2QOhgx4', //郑
        'opQZy1Q3wSnI2xkJJtFvanJnz5aI', //董
        'opQZy1a9iVq4hdB-hShAKimr_LIA', //代
        //'opQZy1XIlLVksKufsDzzN0Acg5W8', //刘
    );

    /**
     * key = 1    代表三多网络
     * key = 969  代表企业级代理 【姚威代理】
     * pid 第一段
     *
     * @var array
     */
    protected $pid_data = array(
        1   => 'mm_121610813_22448587_79916379',
        //969 => 'mm_34444738_30376073_112018592', //已不再合作
    );

    /**
     * @var string
     */
    private $search_url = "http://pub.alimama.com/items/search.json?q=%s&_t=%s&auctionTag=&toPage=%s&perPageSize=%s&shopTag=yxjh&t=%s&_tb_token_=&pvid=10_49.221.62.102_4720_1496801283153";

    /**
     * @var string
     */
    protected $down_url = "http://pub.alimama.com/report/getTbkPaymentDetails.json?spm=a219t.7664554.1998457203.10.1b1ff9e1XNv6tR&queryType=%s&payStatus=%s&DownloadID=DOWNLOAD_REPORT_INCOME_NEW&startTime=%s&endTime=%s";

    /**
     * @var string
     */
    protected $third_down_url = "http://pub.alimama.com/report/getTbkThirdPaymentDetails.json?spm=a219t.7664554.1998457203.230.6e62f2cfp0UddZ&queryType=%s&payStatus=%s&DownloadID=DOWNLOAD_REPORT_TK3_PUB&startTime=%s&endTime=%s";

    /**
     * @var string
     */
    protected $refund_down_url = "http://pub.alimama.com/report/getNewTbkRefundPaymentDetails.json?refundType=1&searchType=3&DownloadID=DOWNLOAD_EXPORT_CPSPAYMENT_REFUND_OVERVIEW&startTime=%s&endTime=%s";

    /**
     * @var string
     */
    protected $third_refund_down_url = "http://pub.alimama.com/report/getNewTbkRefundPaymentDetails.json?refundType=2&searchType=3&DownloadID=DOWNLOAD_EXPORT_CPSPAYMENT_REFUND_OVERVIEW&startTime=%s&endTime=%s";

    /**
     * 设置页面超时时间
     * TaoBaoApiController constructor.
     */
    public function __construct() {
        parent::__construct();
        set_time_limit(3000);
    }

    /**
     * 获取淘宝联盟订单数据
     */
    public function getOrder() {
        $data = array();
        $start          = I('get.start','','trim') ? I('get.start','','trim') : date('Y-m-d', strtotime('-1 days'));
        $end            = I('get.end','','trim') ? I('get.end','','trim') : date('Y-m-d');
        foreach ($this->pid_data as $uid => $pid) {
            $pid_data       = explode('_', $pid);
            $url            = sprintf($this->down_url, '1', '', $start, $end);
            $filename       = "/home/order_log/order_{$pid_data[1]}.xls";
            $res            = $this->_downFile($url, $uid, $filename);
            sleep(2);
            $third_url      = sprintf($this->third_down_url, '2', '', $start, $end);
            $third_filename = "/home/order_log/order_third_{$pid_data[1]}.xls";
            $third_res      = $this->_downFile($third_url, $uid, $third_filename);
            if ($res['code'] == 1) {
                $_data = $this->_createData($filename, $uid);
                foreach ($_data as &$v) {
                    $v[] = $pid;
                }
                $data = array_merge($data, $_data);
            }
            if ($third_res['code'] == 1) {
                $_data = $this->_createData($third_filename, $uid);
                foreach ($_data as &$v) {
                    $v[] = $pid;
                }
                $data = array_merge($data, $_data);
            }
            sleep(2);
        }
        if ($data) {
            /**
             * 获取宅喵生活的订单，单独保存
             */
            $zm_orders = array();
            $zm_siteids = explode(',', C('zm_tao_bao_site_id'));
            $taobao_ids = array();
            foreach ($data as $key => $order) {
                if ($order[30] == $this->pid_data[1] && in_array($order[26], $zm_siteids)) {
                    $taobao_ids[] = $order[3];
                    $zm_orders[] = $order;
                    unset($data[$key]);
                }
            }
            if (!empty($zm_orders)) {
                $pics = M('items')->where(array('num_iid' => array('in', $taobao_ids)))->getField('num_iid,pic_url');
                foreach ($zm_orders as $key => $order) {
                    if (isset($pics[$order[3]])) {
                        $zm_orders[$key][] = $pics[$order[3]];
                    } else { //订单量大可不获取图片
                        $zm_orders[$key][] = '';
                        // if ($order[9] == '天猫') {
                        //     $goods_url = "https://detail.tmall.com/item.htm?id={$order[3]}";
                        // } else {
                        //     $goods_url = "https://item.taobao.com/item.htm?id={$order[3]}";
                        // }
                        // $zm_orders[$key][] = $this->_getImg($goods_url);
                    }
                }

                krsort($zm_orders);
                $order_str = "<?php \nreturn " . var_export(array_values($zm_orders), true) . "\n?>";
                file_put_contents('/home/order_log/zm_new_order.php', $order_str);
            }

            $res = $this->_saveData($data);
            if ($res['code'] == 1) {
                echo "success";
            } else {
                echo "error";
            }
        } else {
            echo "error";
        }
    }

    /**
     * 获取淘宝联盟已结算订单数据
     */
    public function getEndOrder() {
        $data = array();
        $start          = I('get.start','','trim') ? I('get.start','','trim') : date('Y-m-d', strtotime('-1 days'));
        $end            = I('get.end','','trim') ? I('get.end','','trim') : date('Y-m-d');
        foreach ($this->pid_data as $uid => $pid) {
            $pid_data       = explode('_', $pid);
            $url            = sprintf($this->down_url, '3', '3', $start, $end);
            $filename       = "/home/order_log/order_end_{$pid_data[1]}.xls";
            $res            = $this->_downFile($url, $uid, $filename);
            $third_url      = sprintf($this->third_down_url, '4', '3', $start, $end);
            $third_filename = "/home/order_log/order_end_third_{$pid_data[1]}.xls";
            $third_res      = $this->_downFile($third_url, $uid, $third_filename);
            if ($res['code'] == 1) {
                $_data = $this->_createData($filename, $uid);
                foreach ($_data as &$v) {
                    $v[] = $pid;
                }
                $data = array_merge($data, $_data);
            }
            if ($third_res['code'] == 1) {
                $_data = $this->_createData($third_filename, $uid);
                foreach ($_data as &$v) {
                    $v[] = $pid;
                }
                $data = array_merge($data, $_data);
            }
        }
        if ($data) {
            /**
             * 获取宅喵生活的已结算订单，单独保存
             */
            $zm_orders = array();
            $zm_siteids = explode(',', C('zm_tao_bao_site_id'));
            foreach ($data as $key => $order) {
                if ($order[30] == $this->pid_data[1] && in_array($order[26], $zm_siteids)) {
                    $zm_orders[] = $order;
                    unset($data[$key]);
                }
            }
            if (!empty($zm_orders)) {
                krsort($zm_orders);
                $order_str = "<?php \nreturn " . var_export(array_values($zm_orders), true) . "\n?>";
                file_put_contents('/home/order_log/zm_settle_order.php', $order_str);
            }

            usort($data, 'compare_create_time');
            $res = $this->_saveData($data);
            if ($res['code'] == 1) {
                echo "success";
            } else {
                echo "error";
            }
        } else {
            echo "success";
        }
    }

    /**
     * 获取淘宝联盟失效订单
     */
    public function getDelOrder() {
        $hour = date('H');
        if (0 == $hour % 4) {
            $start_day = '-18';
            $end_day = '-15';
        } else {
            $start_day = '-1';
            $end_day = '0';
        }

        $data = array();
        foreach ($this->pid_data as $uid => $pid) {
            $pid_data       = explode('_', $pid);
            $start          = date("Y-m-d", strtotime("{$start_day} days"));
            $end            = date("Y-m-d", strtotime("{$end_day} days"));
            $url            = sprintf($this->down_url, '1', '13', $start, $end);
            $filename       = "/home/order_log/order_del_{$pid_data[1]}.xls";
            $res            = $this->_downFile($url, $uid, $filename);
            $third_url      = sprintf($this->third_down_url, '2', '13', $start, $end);
            $third_filename = "/home/order_log/order_del_third_{$pid_data[1]}.xls";
            $third_res      = $this->_downFile($third_url, $uid, $third_filename);
            if ($res['code'] == 1) {
                $_data = $this->_createData($filename, $uid);
                foreach ($_data as &$v) {
                    $v[] = $pid;
                }
                $data = array_merge($data, $_data);
            }
            if ($third_res['code'] == 1) {
                $_data = $this->_createData($third_filename, $uid);
                foreach ($_data as &$v) {
                    $v[] = $pid;
                }
                $data = array_merge($data, $_data);
            }
        }
        if ($data) {
            /**
             * 获取宅喵生活的失效订单，单独保存
             */
            $zm_orders = array();
            $zm_siteids = explode(',', C('zm_tao_bao_site_id'));
            foreach ($data as $key => $order) {
                if ($order[30] == $this->pid_data[1] && in_array($order[26], $zm_siteids)) {
                    $zm_orders[] = $order;
                    unset($data[$key]);
                }
            }
            if (!empty($zm_orders)) {
                krsort($zm_orders);
                $order_str = "<?php \nreturn " . var_export(array_values($zm_orders), true) . "\n?>";
                file_put_contents('/home/order_log/zm_fail_order.php', $order_str);
            }

            $res = $this->_saveData($data);
            if ($res['code'] == 1) {
                echo "success";
            } else {
                echo "error";
            }
        } else {
            echo "error";
        }
    }

    /**
     * 获取维权订单
     */
    public function getRefundOrder() {
        $data = array();
        $start          = I('get.start','','trim') ? I('get.start','','trim') : date('Y-m-d', strtotime('-1 days'));
        $end            = I('get.end','','trim') ? I('get.end','','trim') : date('Y-m-d');
        foreach ($this->pid_data as $uid => $pid) {
            $pid_data       = explode('_', $pid);
            $url            = sprintf($this->refund_down_url, $start, $end);
            $filename       = "/home/order_log/order_refund_{$pid_data[1]}.xls";
            $res            = $this->_downFile($url, $uid, $filename);
            $third_url      = sprintf($this->third_refund_down_url, $start, $end);
            $third_filename = "/home/order_log/order_refund_third_{$pid_data[1]}.xls";
            $third_res      = $this->_downFile($third_url, $uid, $third_filename);
            if ($res['code'] == 1) {
                $_data = $this->_createData($filename, $uid, 'refund');
                $data  = array_merge($data, $_data);
            }
            if ($third_res['code'] == 1) {
                $_data = $this->_createData($third_filename, $uid, 'refund');
                $data  = array_merge($data, $_data);
            }
        }
        if ($data) {
            /**
             * 获取宅喵生活的维权订单，单独保存
             */
            $zm_orders = array();
            $zm_siteids = explode(',', C('zm_tao_bao_site_id'));
            foreach ($data as $key => $order) {
                if ($order[30] == $this->pid_data[1] && in_array($order[26], $zm_siteids)) {
                    $zm_orders[] = $order;
                    unset($data[$key]);
                }
            }
            if (!empty($zm_orders)) {
                krsort($zm_orders);
                $order_str = "<?php \nreturn " . var_export(array_values($zm_orders), true) . "\n?>";
                file_put_contents('/home/order_log/zm_refund_order.php', $order_str);
            }

            $this->_saveRefundData($data);
        }
        echo "success";
    }

    /**
     * @param $url
     * @param $uid
     * @param $filename
     * @return array
     */
    private function _downFile($url, $uid, $filename) {
        $obj    = new Http();
        $cookie = $this->_getCookie($this->pid_data[$uid]);
        $data   = $obj->downFile($url, $cookie['cookie'], $filename);
        $name   = array(1 => '三多网络', 969 => '姚威代理');
        if (!file_exists($filename)) {
            $log_data = array(
                'time' => date('Y-m-d H:i:s'),
                'file' => $filename,
                'name' => $name[$uid],
                'pid'  => $this->pid_data[$uid],
                'msg'  => 'cookie失效，无法下载！'
            );
            file_put_contents('/home/order_log/down_order_cookie_fail.log', var_export($log_data, true) . "\r\n", FILE_APPEND);
            $content = "您好：{$name[$uid]}cookie已掉线";
            foreach ($this->weChatUser as $val) {
                $this->_sendMsg($content, $val);
            }
            return array('code' => -1, 'msg' => 'cookie失效！');
        }
        if ($data['error']) {
            $data['time'] = date('Y-m-d H:i:s');
            $data['pid']  = $this->pid_data[$uid];
            $data['name'] = $name[$uid];
            file_put_contents('/home/order_log/down_order_fail.log', var_export($data, true) . "\r\n", FILE_APPEND);
            return array('code' => -1, 'msg' => '下载数据失败！');
        }
        return array('code' => 1, 'msg' => '下载数据成功！');
    }

    /**
     * @param $filename
     * @param $uid
     * @param string $type
     * @return array
     * @throws \PHPExcel_Reader_Exception
     */
    private function _createData($filename, $uid, $type = 'order') {
        require_once(APP_PATH . "/Common/Org/PHPExcel.class.php");
        require_once(APP_PATH . "/Common/Org/PHPExcel/IOFactory.php");
        $reader   = \PHPExcel_IOFactory::createReader('Excel5');
        $PHPExcel = $reader->load($filename); // 载入excel文件
        $obj      = $PHPExcel->getSheet(0);// 读取第一個工作表
        if ($obj) {
            $data = $obj->toArray();
            $a = array_shift($data);
            if ('技术服务费比率' == $a[19]) {
                foreach ($data as $key => $value) {
                    unset($value[19]);
                    $data[$key] = array_values($value);
                }
            }
            return $data;
        } else {
            if ($type == 'order') {
                $name = array(1 => '三多网络', 969 => '姚威代理');
                $data = array(
                    'time' => date('Y-m-d H:i:s'),
                    'file' => $filename,
                    'name' => $name[$uid],
                    'pid'  => $this->pid_data[$uid],
                );
                file_put_contents('/home/order_log/cookie_fail.log', var_export($data, true), FILE_APPEND);
                $content = "您好：{$name[$uid]}cookie已掉线";
                foreach ($this->weChatUser as $val) {
                    $this->_sendMsg($content, $val);
                }
            }
            return array();
        }
    }

    /**
     * @param $param
     * @return array
     */
    private function _saveData($param) {
        if (!$param) {
            return array('code' => -1, 'msg' => '下载数据为空');
        }

        $format_update_orders = array(); //待更新的订单。默认所有订单都视为需要更新。把订单做成以订单号为键，以订单为值的二维数组
        $add_orders   = array(); //待新增的订单
        $order_ids    = array(); //所有订单号
        $order_status = array('订单失效' => 'fail', '订单结算' => 'settle', '订单成功' => 'success', '订单付款' => 'paid');
        foreach ($param as $key => $val) {
            $order_ids[] = $val[24];
            $pid_arr     = explode('_', $val[30]);

            $pid        = $pid_arr[0] . "_" . $pid_arr[1] . "_" . $val[26] . "_" . $val[28];
            $pay_status = $order_status[$val[8]];
            if ($val[9] == '天猫') {
                $goods_url = "https://detail.tmall.com/item.htm?id={$val[3]}";
            } else {
                $goods_url = "https://item.taobao.com/item.htm?id={$val[3]}";
            }
            $img = $this->_getItemImg($val[3]);
            if (!$img) {
                $img = $this->_getImg($goods_url);
            }
            $commission_rate = round(str_replace("%", "", $val[10]), 2);
            $share_rate      = round(str_replace("%", "", $val[11]), 2);
            $new_order       = array(
                'order_id'        => $val[24],
                'item_id'         => $val[3],
                'number'          => $val[6],
                'order_num'       => 1,
                'title'           => $val[2],
                'commission_rate' => computedPrice($commission_rate, 1),
                'share_rate'      => $share_rate,
                'fee'             => computedPrice($val[13]),
                'price'           => $val[7],
                'total_money'     => $val[12],
                'create_time'     => $val[0],
                'pay_status'      => $pay_status,
                'order_type'      => $val[9],
                'action_url'      => $goods_url,
                'earning_time'    => $val[16],
                'pid'             => $pid,
                'img'             => $img,
            );

            if (isset($format_update_orders[$val[24]])) {
                $order_num = count($format_update_orders[$val[24]]) + 1;
                $new_order['order_num'] = $order_num;
                $format_update_orders[$val[24]][] = $new_order;
            } else {
                $format_update_orders[$val[24]] = array($new_order);
            }
        }

        $model   = M('order');
        $exist_orders = $model->where(array('order_id' => array('in', $order_ids)))->select();
        $format_exist_orders = array();
        foreach ($exist_orders as $db_order) {
            if (isset($format_exist_orders[$db_order['order_id']])) {
                $format_exist_orders[$db_order['order_id']]['num'] ++;
            } else {
                $format_exist_orders[$db_order['order_id']]['num'] = 1;
            }
        }

        //找出所有需要新增的订单
        foreach ($format_update_orders as $order_id => $v) {
            if (isset($format_exist_orders[$order_id])) {
                $new_order_num = count($v) - $format_exist_orders[$order_id]['num'];
                if ($new_order_num > 0) {
                    $add_orders = array_merge($add_orders, array_slice($v, 0, $new_order_num));
                }
            } else {
                $add_orders = array_merge($add_orders, $v);
            }
        }

        foreach ($exist_orders as $db_order) {
            if (isset($format_update_orders[$db_order['order_id']])) {
                foreach ($format_update_orders[$db_order['order_id']] as $key => $order) {
                    //商品和数量相同 并且 状态不一致的做更新操作
                    if ($db_order['item_id'] == $order['item_id'] && $db_order['number'] == $order['number'] && $db_order['pay_status'] != $order['pay_status']) {
                        $update_data = array(
                            'commission_rate' => $order['commission_rate'],
                            'fee'             => $order['fee'],
                            'total_money'     => $order['total_money'],
                            'pay_status'      => $order['pay_status'],
                            'earning_time'    => $order['earning_time'],
                        );
                        $model->where(array('id' => $db_order['id']))->save($update_data);
                        //unset($format_update_orders[$db_order['order_id']][$key]);
                    }
                }
            }
        }

        if (empty($add_orders)) {
            return array('code' => 1, 'msg' => '成功');
        } else {
            $data = array_values($add_orders);
            $res  = M('order')->addAll($add_orders);
            if ($res) {
                return array('code' => 1, 'msg' => '添加订单成功');
            } else {
                return array('code' => -1, 'msg' => '添加订单失败');
            }
        }
    }

    /**
     * 保存退款订单
     *
     * @param $data
     */
    protected function _saveRefundData($data) {
        $temp_data = $where_data = array();
        foreach ($data as $val) {
            $order_id = $val[0];
            if ($val[0] == $val[1]) {
                $order_num = 1;
            } else {
                $order_num = substr($val[1], 0, -6) - substr($val[0], 0, -6);
            }
            if ($val[4] > 0) {
                $temp_data[$order_id . '_' . $order_num] = array('refund_money' => $val[3], 'fee' => $val[4]);
                $where_data[]                            = $order_id;
            }
        }
        if ($where_data) {
            $model   = M('order');
            $db_data = $model->field('id,order_id,order_num,total_money,fee')->where(array('order_id' => array('in', $where_data), 'pay_status' => 'settle'))->select();
            foreach ($db_data as $v) {
                if (isset($temp_data[$v['order_id'] . '_' . $v['order_num']]) && $temp_data[$v['order_id'] . '_' . $v['order_num']]) {
                    $total_money = ($v['total_money'] - $temp_data[$v['order_id'] . '_' . $v['order_num']]['refund_money']) <= 0.01 ? 0 : ($v['total_money'] - $temp_data[$v['order_id'] . '_' . $v['order_num']]['refund_money']);
                    $fee         = ($v['fee'] - $temp_data[$v['order_id'] . '_' . $v['order_num']]['fee']) <= 0.01 ? 0 : ($v['fee'] - $temp_data[$v['order_id'] . '_' . $v['order_num']]['fee']);
                    $pay_status  = $total_money > 0 ? 'settle' : 'refund';
                    $model->where(array('id' => $v['id']))->save(array('pay_status' => $pay_status, 'total_money' => $total_money, 'fee' => $fee));
                }
            }
        }
    }

    /**
     * @param $url
     * @return string
     */
    protected function _getImg($url) {
        $key  = urlencode($url);
        $temp = microtime(true) * 1000;
        $temp = explode('.', $temp);
        $end  = $temp[0] + 8;
        $url  = sprintf($this->search_url, $key, $temp[0], 1, 1, $end);
        $http = new Http();
        $data = json_decode($http->get($url), true);
        $img  = "";
        if ($data['data']['head']['status'] == 'OK') {
            $img = "http:" . $data['data']['pageList'][0]['pictUrl'];
        }
        return $img;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function _getItemImg($id) {
        $img = M('items')->where(array('num_iid' => $id))->getField('pic_url');
        return $img;
    }


    /**
     * @param $content
     * @param $open_id
     * @return mixed
     */
    protected function _sendMsg($content, $open_id, $info = null) {
        $url    = "https://api.weixin.qq.com/cgi-bin/message/template/send";
        $date   = date('Y-m-d H:i:s');
        $remark = "请尽快处理！";
        if ($info === null) {
            $info = 'cookie已掉线';
        }
        $sendData  = array(
            'touser'      => $open_id,
            'template_id' => '7vj3HPmf-o_oFXG18M97HOL84az6AzAgVvh2UpEGXAk',
            'topcolor'    => '#7B68EE',
            'data'        => array(
                'first'    => array(
                    'value' => urlencode($content),
                    'color' => '#743A3A',
                ),
                'keyword1' => array(
                    'value' => urlencode($info),
                    'color' => '#FF0000',
                ),
                'keyword2' => array(
                    'value' => urlencode($date),
                    'color' => '#C4C400',
                ),
                'remark'   => array(
                    'value' => urlencode($remark),
                    'color' => '#008000'
                ),
            )
        );
        $json_data = urldecode(json_encode($sendData));
        $token     = $this->_getToken();
        $url       = $url . "?access_token={$token}";
        $obj       = new Http();
        $res       = json_decode($obj->post($url, $json_data));
        if ($res->errcode == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检测cookie有效期
     */
    public function checkCookieExpire() {
        $content = "1、淘宝access_token即将过期！\\n2、轻淘客cookie即将过期！\\n3、CRM邮购招商部门cookie即将过期！\\n4、大微信客cookie即将过期！";
        $info    = 'cookie即将过期';
        foreach ($this->weChatUser as $v) {
            $this->_sendMsg($content, $v, $info);
        }
    }


}