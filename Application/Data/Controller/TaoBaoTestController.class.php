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
class TaoBaoTestController extends CommonController {

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
        'opQZy1XRiD-7Jl3jo7ate99pcID0', //马
        'opQZy1dV3rcNYzUQNxANq2QOhgx4', //郑
        'opQZy1Q3wSnI2xkJJtFvanJnz5aI', //董
        'opQZy1a9iVq4hdB-hShAKimr_LIA', //代
        //'opQZy1XIlLVksKufsDzzN0Acg5W8', //刘
    );

    /**
     * key = 1    代表三多网络
     * key = 969  代表企业级代理 【姚威代理】
     * key = 1052 代表企业级代理 【杨凌代理】
     * pid 第一段
     *
     * @var array
     */
    protected $pid_data = array(
        1   => 'mm_121610813_22448587_79916379',
        969 => 'mm_34444738_30376073_112018592',
    );

    /**
     * 检测pid 的 真实性
     *
     * @var array
     */
    protected $checkPid = array( 1, 969, 1052 );

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
        foreach ($this->pid_data as $key => $val) {
            $pid_data       = explode('_', $val);
            $start          = date('Y-m-d', strtotime('-1 days'));
            $end            = date('Y-m-d');
            $url            = sprintf($this->down_url, '1', '', $start, $end);
            $filename       = "/home/order_log/order_{$pid_data[1]}.xls";
            $res            = $this->_downFile($url, $key, $filename);
            $third_url      = sprintf($this->third_down_url, '2', '', $start, $end);
            $third_filename = "/home/order_log/order_third_{$pid_data[1]}.xls";
            $third_res      = $this->_downFile($third_url, $key, $third_filename);
            if ($res['code'] == 1) {
                $_data = $this->_createData($filename, $key);
                foreach ($_data as &$v) {
                    $v[] = $val;
                }
                $data = array_merge($data, $_data);
            }
            if ($third_res['code'] == 1) {
                $_data = $this->_createData($third_filename, $key);
                foreach ($_data as &$v) {
                    $v[] = $val;
                }
                $data = array_merge($data, $_data);
            }
        }
        if ($data) {
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
     * 获取淘宝联盟订单数据
     */
    public function getEndOrder() {
        $data = array();
        foreach ($this->pid_data as $key => $val) {
            $pid_data       = explode('_', $val);
            $start          = date('Y-m-d', strtotime('-1 days'));
            $end            = date('Y-m-d');
            $url            = sprintf($this->down_url, '3', '3', $start, $end);
            $filename       = "/home/order_log/order_end_{$pid_data[1]}.xls";
            $res            = $this->_downFile($url, $type, $filename);
            $third_url      = sprintf($this->down_url, '4', '3', $start, $end);
            $third_filename = "/home/order_log/order_end_third_{$pid_data[1]}.xls";
            $third_res      = $this->_downFile($third_url, $key, $third_filename);
            if ($res['code'] == 1) {
                $_data = $this->_createData($filename, $key);
                foreach ($_data as &$v) {
                    $v[] = $val;
                }
                $data = array_merge($data, $_data);
            }
            if ($third_res['code'] == 1) {
                $_data = $this->_createData($third_filename, $key);
                foreach ($_data as &$v) {
                    $v[] = $val;
                }
                $data = array_merge($data, $_data);
            }
        }
        if ($data) {
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
     * 获取淘宝联盟失效订单
     */
    public function getDelOrder() {
        $data = array();
        foreach ($this->pid_data as $key => $val) {
            $pid_data       = explode('_', $val);
            $start          = date("Y-m-d", strtotime("-18 days"));
            $end            = date("Y-m-d", strtotime("-15 days"));
            $url            = sprintf($this->down_url, '1', '13', $start, $end);
            $filename       = "/home/order_log/order_del_{$pid_data[1]}.xls";
            $res            = $this->_downFile($url, $type, $filename);
            $third_url      = sprintf($this->down_url, '2', '13', $start, $end);
            $third_filename = "/home/order_log/order_del_third_{$pid_data[1]}.xls";
            $third_res      = $this->_downFile($third_url, $key, $third_filename);
            if ($res['code'] == 1) {
                $_data = $this->_createData($filename, $key);
                foreach ($_data as &$v) {
                    $v[] = $val;
                }
                $data = array_merge($data, $_data);
            }
            if ($third_res['code'] == 1) {
                $_data = $this->_createData($third_filename, $key);
                foreach ($_data as &$v) {
                    $v[] = $val;
                }
                $data = array_merge($data, $_data);
            }
        }
        if ($data) {
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
     * @param $url
     * @param $type
     * @param $filename
     * @return array
     */
    private function _downFile($url, $type, $filename) {
        $obj    = new Http();
        $cookie = $this->_getCookie($this->pid_data[$type]);
        $data   = $obj->downFile($url, $cookie['cookie'], $filename);
        $name   = array( 1 => '三多网络', 969 => '姚威代理' );
        if (!file_exists($filename)) {
            $log_data = array(
                'time' => date('Y-m-d H:i:s'),
                'file' => $filename,
                'name' => $name[$type],
                'pid'  => $this->pid_data[$type],
                'msg'  => 'cookie失效，无法下载！'
            );
            file_put_contents('/home/order_log/down_order_cookie_fail.log', var_export($log_data, true)."\r\n", FILE_APPEND);
            $content = "您好：{$name[$type]}cookie已掉线";
            foreach ($this->weChatUser as $val) {
                $this->_sendMsg($content, $val);
            }
            return array( 'code' => -1, 'msg' => 'cookie失效！' );
        }
        if ($data['error']) {
            $data['time'] = date('Y-m-d H:i:s');
            $data['pid']  = $this->pid_data[$type];
            $data['name'] = $name[$type];
            file_put_contents('/home/order_log/down_order_fail.log', var_export($data, true)."\r\n", FILE_APPEND);
            return array( 'code' => -1, 'msg' => '下载数据失败！' );
        }
        return array( 'code' => 1, 'msg' => '下载数据成功！' );
    }

    /**
     * @param $filename
     * @param $type
     * @return array
     */
    private function _createData($filename, $type) {
        require_once(APP_PATH."/Common/Org/PHPExcel.class.php");
        require_once(APP_PATH."/Common/Org/PHPExcel/IOFactory.php");
        $reader   = \PHPExcel_IOFactory::createReader('Excel5');
        $PHPExcel = $reader->load($filename); // 载入excel文件
        $obj      = $PHPExcel->getSheet(0);// 读取第一個工作表
        if ($obj) {
            $data = $obj->toArray();
            unset($data[0]);
            $return_data = array_values($data);
            return $return_data;
        } else {
            $name = array( 1 => '三多网络', 969 => '姚威代理' );
            $data = array(
                'time' => date('Y-m-d H:i:s'),
                'file' => $filename,
                'name' => $name[$type],
                'pid'  => $this->pid_data[$type],
            );
            file_put_contents('/home/order_log/cookie_fail.log', var_export($data, true), FILE_APPEND);
            $content = "您好：{$name[$type]}cookie已掉线";
            foreach ($this->weChatUser as $val) {
                $this->_sendMsg($content, $val);
            }
            return array();
        }
    }

    /**
     * @param $param
     * @return array
     */
    private function _saveData($param) {
        var_dump($param);

        exit;
        if (!$param) {
            return array( 'code' => -1, 'msg' => '下载数据为空' );
        }
        $data         = array();
        $t            = 1;
        $order_status = array( '订单失效' => 'fail', '订单结算' => 'settle', '订单成功' => 'success', '订单付款' => 'paid' );
        foreach ($param as $key => $val) {
            $pid_arr = explode('_', $val[30]);
            if ($val[24] == $param[$key - 1][24]) {
                $t++;
            } else {
                $t = 1;
            }
            $pid        = $pid_arr[0]."_".$pid_arr[1]."_".$val[26]."_".$val[28];
            $pay_status = $order_status[$val[8]];
            $info       = M('order')->where(array( 'order_id' => $val[24], 'order_num' => $t ))->field('id,pay_status')->find();
            if ($info) {
                if ($info['pay_status'] != $pay_status) {
                    $update_data = array(
                        'commission_rate' => round(str_replace("%", "", $val[10]), 2),
                        'fee'             => $val[13],
                        'total_money'     => $val[12],
                        'pay_status'      => $pay_status,
                        'earning_time'    => $val[16],
                    );
                    M('order')->where(array( 'id' => $info['id'] ))->save($update_data);
                    $update_log_data[] = $update_data;
                }
                continue;
            }
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
            $data[]          = array(
                'order_id'        => $val[24],
                'item_id'         => $val[3],
                'number'          => $val[6],
                'order_num'       => $t,
                'title'           => $val[2],
                'commission_rate' => $commission_rate,
                'share_rate'      => $share_rate,
                'fee'             => $val[13],
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
        }
        if (!$data) {
            return array( 'code' => -1, 'msg' => '此次下载数据与上次下载的数据没有变化' );
        }
        $data = array_reverse($data);
        $res  = M('order')->addAll($data);
        if ($res) {
            return array( 'code' => 1, 'msg' => '添加订单成功' );
        } else {
            return array( 'code' => -1, 'msg' => '添加订单失败' );
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
            $img = "http:".$data['data']['pageList'][0]['pictUrl'];
        }
        return $img;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function _getItemImg($id) {
        $img = M('items')->where(array( 'num_iid' => $id ))->getField('pic_url');
        return $img;
    }

    /**
     * 检测pid
     */
    protected function _checkPid() {
        $type = I('get.type', 1, 'int');
        if (in_array($type, $this->checkPid)) {
            return true;
        } else {
            return false;
        }
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
        $url       = $url."?access_token={$token}";
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

    /**
     * 测试发送cookie掉线消息
     */
    public function testSendCookie() {
        $content = "您好：三多网络cookie已掉线";
        foreach ($this->weChatUser as $val) {
            $this->_sendMsg($content, $val);
        }
    }

}