<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/8 0008
 * Time: 下午 2:37
 */

namespace Data\Controller;

use Common\Org\Http;
use Common\Org\simple_html_dom as Html;

/**
 * 大淘客采集数据接口
 * Class DaTaoKeController
 *
 * @package Data\Controller
 */
class DaTaoKeController extends CommonController {

    /**
     * @var string
     */
    protected $appId = 'wx38a0adba37145fe0';

    /**
     * @var string
     */
    protected $appSecret = '4b5d39d3104cc756f19209aa36093f35';

    /**
     * @var string
     */
    protected $get_url = "http://www.dataoke.com/tk_zs/quan-order.html";

    /**
     * cookie 数据
     *
     * @var string
     */
    protected $cookie = array();
    /**
     * @var null
     */
    protected $Http = null;

    /**
     * 微信用户发送
     *
     * @var array
     */
    protected $weChatUser = array(
        'opQZy1Q3wSnI2xkJJtFvanJnz5aI', //董
        'opQZy1a9iVq4hdB-hShAKimr_LIA', //代
    );

    /**
     * DaTaoKeController constructor.
     */
    public function __construct() {
        parent::__construct();
        set_time_limit(300);
        $this->cookie = $this->_getDepartmentCookie();
        $this->Http   = new Http();
    }

    /**
     * 获取预告的商品
     */
    public function getFuture() {
        $url     = $this->get_url;
        $cookie  = (array)$this->cookie;
        $data    = $add_data = array();
        $status  = 0;
        $page    = 1;
        $is_next = array('ye_lang' => 0, 'xiao_liu' => 0, 'sheng_tian' => 0);
        while ($status == 0) {
            $param = array('type' => 'sh_2', 'cate' => 2, 'page' => $page);
            foreach ($cookie as $key => $val) {
                if ($is_next[$val['name']] == 0) {
                    usleep(300000);
                    $res        = $this->Http->getCookie($url, $val['cookie'], $param);
                    $goods_data = $this->_createData($res, 'ing', $val);
                    if ($goods_data) {
                        $data = array_merge($data, $goods_data);
                    } else {
                        $is_next[$val['name']] = 1;
                    }
                }
            }
            if ($is_next['ye_lang'] == 1 && $is_next['xiao_liu'] == 1 && $is_next['sheng_tian'] == 1) {
                $status = 1;
            }
            $page++;
        }

        //查找已在数据库里的商品
        $dataoke_ids = array();
        foreach ($data as $k => $v) {
            if ($v['add_time'] < 1502726400) { //2017 08 15前的商品不抓取
                unset($data[$k]);
                continue;
            }

            $dataoke_ids[] = $v['dataoke_id'];
        }
        $deals = M('deal')->where(array('dataoke_id' => array('in', $dataoke_ids)))->index('dataoke_id,id')->select();

        foreach ($data as $v) {
            if (isset($deals[$v['dataoke_id']])) {
                continue;
            }
            if ($v['dataoke_remark']) {
                $admin_id      = M('admin')->where(array('full_name' => $v['dataoke_remark']))->getField('id');
                $v['admin_id'] = (int)$admin_id;
            } else {
                $v['admin_id'] = 0;
            }
            $add_data[] = $v;
        }
        if ($add_data) {
            $res = M('deal')->addAll($add_data);
            if ($res) {
                $log_data = array(
                    'time' => date('Y-m-d H:i:s'),
                    'msg'  => '【进行中】添加成功',
                );
                echo "success";
            } else {
                $log_data = array(
                    'time' => date('Y-m-d H:i:s'),
                    'msg'  => '【进行中】添加失败',
                );
                echo "error";
            }
        } else {
            $log_data = array(
                'time' => date('Y-m-d H:i:s'),
                'msg'  => '【进行中】没有最新数据',
            );
            echo "error";
        }
        file_put_contents('/home/order_log/dataoke.log' . date('Ymd'), var_export($log_data, true) . "\r\n", FILE_APPEND);
    }

    /**
     * 获取进行中商品
     */
    public function getIng() {
        $url     = $this->get_url;
        $cookie  = (array)$this->cookie;
        $data    = $add_data = array();
        $status  = 0;
        $page    = 1;
        $is_next = array('ye_lang' => 0, 'xiao_liu' => 0, 'sheng_tian' => 0);
        while ($status == 0) {
            $param = array('type' => 'sh_2', 'page' => $page);
            foreach ($cookie as $key => $val) {
                if ($is_next[$val['name']] == 0) {
                    usleep(300000);
                    $res        = $this->Http->getCookie($url, $val['cookie'], $param);
                    $goods_data = $this->_createData($res, 'ing', $val);
                    if ($goods_data) {
                        $data = array_merge($data, $goods_data);
                    } else {
                        $is_next[$val['name']] = 1;
                    }
                }
            }
            if ($is_next['ye_lang'] == 1 && $is_next['xiao_liu'] == 1 && $is_next['sheng_tian'] == 1) {
                $status = 1;
            }
            $page++;
        }

        //查找已在数据库里的商品
        $dataoke_ids = array();
        foreach ($data as $k => $v) {
            if ($v['add_time'] < 1502726400) { //2017 08 15前的商品不抓取
                unset($data[$k]);
                continue;
            }

            $dataoke_ids[] = $v['dataoke_id'];
        }
        $deals = M('deal')->where(array('dataoke_id' => array('in', $dataoke_ids)))->index('dataoke_id,id')->select();

        foreach ($data as $v) {
            if (isset($deals[$v['dataoke_id']])) {
                continue;
            }

            if ($v['dataoke_remark']) {
                $admin_id      = M('admin')->where(array('full_name' => $v['dataoke_remark']))->getField('id');
                $v['admin_id'] = (int)$admin_id;
            } else {
                $v['admin_id'] = 0;
            }
            $add_data[] = $v;
        }
        if ($add_data) {
            $res = M('deal')->addAll($add_data);
            if ($res) {
                $log_data = array(
                    'time' => date('Y-m-d H:i:s'),
                    'msg'  => '【进行中】添加成功',
                );
                echo "success";
            } else {
                $log_data = array(
                    'time' => date('Y-m-d H:i:s'),
                    'msg'  => '【进行中】添加失败',
                );
                echo "error";
            }
        } else {
            $log_data = array(
                'time' => date('Y-m-d H:i:s'),
                'msg'  => '【进行中】没有最新数据',
            );
            echo "error";
        }
        file_put_contents('/home/order_log/dataoke.log' . date('Ymd'), var_export($log_data, true) . "\r\n", FILE_APPEND);
    }

    /**
     * 获取已结束商品
     */
    public function getEnd() {
        $url    = $this->get_url;
        $cookie = (array)$this->cookie;
        $data   = $update_data = $add_data = array();
        for ($i = 1; $i <= 10; $i++) {
            $param = array('type' => 'invalid', 'cate' => 1, 'page' => $i);
            foreach ($cookie as $key => $val) {
                usleep(300000);
                $res        = $this->Http->getCookie($url, $val['cookie'], $param);
                $goods_data = $this->_createData($res, 'end', $val);
                if ($goods_data) {
                    $data = array_merge($data, $goods_data);
                }
            }
        }

        //查找已在数据库里的商品
        $dataoke_ids = array();
        foreach ($data as $k => $v) {
            if ($v['add_time'] < 1502726400) { //2017 08 15前的商品不抓取
                unset($data[$k]);
                continue;
            }

            $dataoke_ids[] = $v['dataoke_id'];
        }
        $deals = M('deal')->where(array('dataoke_id' => array('in', $dataoke_ids)))->index('dataoke_id,status')->select();

        foreach ($data as $v) {
            if (isset($deals[$v['dataoke_id']])) {
                if ($deals[$v['dataoke_id']] == 'ing') {
                    $update_data[] = array(
                        'status'               => $v['status'],
                        'status_update_reason' => $v['status_update_reason'],
                        'dataoke_id'           => $v['dataoke_id']
                    );
                }
            } else {
                if (!empty($v['dataoke_remark'])) {
                    $admin_id      = M('admin')->where(array('full_name' => $v['dataoke_remark']))->getField('id');
                    $v['admin_id'] = (int)$admin_id;
                } else {
                    $v['admin_id'] = 0;
                }
                $add_data[] = $v;
            }
        }

        foreach ($update_data as $u) {
            M('deal')->where(array('dataoke_id' => $u['dataoke_id']))->save($u);
        }
        $log_data = array(
            'time' => date('Y-m-d H:i:s'),
            'msg'  => '【已结束】处理数据',
        );
        file_put_contents('/home/order_log/dataoke.log' . date('Ymd'), var_export($log_data, true) . "\r\n", FILE_APPEND);

        //把拒绝的商品信息保存
        if (!empty($add_data)) {
            $res = M('deal')->addAll($add_data);
            if ($res) {
                $log_data = array(
                    'time' => date('Y-m-d H:i:s'),
                    'msg'  => '【拒绝】添加成功',
                );
                echo "success";
            } else {
                $log_data = array(
                    'time' => date('Y-m-d H:i:s'),
                    'msg'  => '【拒绝】添加失败' . var_export($res, true),
                );
                echo "error";
            }
        }
        file_put_contents('/home/order_log/dataoke.log' . date('Ymd'), var_export($log_data, true) . "\r\n", FILE_APPEND);
    }

    /**
     * 获取拒绝商品
     */
    public function getReject() {
        return; //已合并到获取已结束商品里
        $url    = $this->get_url;
        $cookie = (array)$this->cookie;
        $data   = $add_data = array();
        for ($i = 1; $i <= 10; $i++) {
            $param = array('type' => 'invalid', 'cate' => 2, 'page' => $i);
            foreach ($cookie as $key => $val) {
                usleep(300000);
                $res        = $this->Http->getCookie($url, $val['cookie'], $param);
                $goods_data = $this->_createData($res, 'reject', $val);
                if ($goods_data) {
                    $data = array_merge($data, $goods_data);
                }
            }
        }
        foreach ($data as $v) {
            if ($v['add_time'] < strtotime('2017-08-15 00:00:00')) {
                continue;
            }
            $count = M('deal')->where(array('dataoke_id' => $v['dataoke_id']))->count();
            if ($count) {
                continue;
            }
            if ($v['dataoke_remark']) {
                $admin_id      = M('admin')->where(array('full_name' => $v['dataoke_remark']))->getField('id');
                $v['admin_id'] = (int)$admin_id;
            } else {
                $v['admin_id'] = 0;
            }
            $add_data[] = $v;
        }
        if ($add_data) {
            $res = M('deal')->addAll($add_data);
            if ($res) {
                $log_data = array(
                    'time' => date('Y-m-d H:i:s'),
                    'msg'  => '【拒绝】添加成功',
                );
                echo "success";
            } else {
                $log_data = array(
                    'time' => date('Y-m-d H:i:s'),
                    'msg'  => '【拒绝】添加失败',
                );
                echo "error";
            }
        } else {
            $log_data = array(
                'time' => date('Y-m-d H:i:s'),
                'msg'  => '【拒绝】没有最新数据',
            );
            echo "error";
        }
        file_put_contents('/home/order_log/dataoke.log', var_export($log_data, true) . "\r\n", FILE_APPEND);
    }

    /**
     * 创建数据
     *
     * @param $res
     * @param $type
     * @return array
     */
    protected function _createData($res, $type, $cate) {
        $html = new Html();
        $html->load($res);
        $data = array();
        foreach ($html->find('table[width=100%] tr td') as $k => $e) {
            $t = $k % 10;
            $p = intval($k / 10);
            if ($p < 1) {
                continue;
            }
            $key = $p - 1;

            switch ($t) {
                case 0:
                    $add_time               = trim($e->plaintext);
                    $add_time               = mb_substr($add_time, '0', '19');
                    $data[$key]['add_time'] = strtotime($add_time);
                    break;
                case 1:
                    $a   = trim($e->innertext);
                    $reg = '/<a href="(.*)" target=".*" .*>[\s|\n]*<img src="(.*)" width=".*" .* \/>[\s|\n]*<\/a>/i';
                    preg_match_all($reg, $a, $a_data);
                    list($_, $tao_bao_item_id) = explode('=', $a_data[1][0]);
                    $data[$key]['img_url']        = trim($a_data[2][0]);
                    $data[$key]['taobao_url']     = trim($a_data[1][0]);
                    $data[$key]['taobao_item_id'] = trim($tao_bao_item_id);
                    break;
                case 2:
                    $price = trim($e->plaintext);
                    preg_match('/\d+\.*\d*/', $price, $after_price);
                    $data[$key]['coupon_after_price'] = $after_price[0];
                    break;
                case 3:
                    $commission_content = trim($e->innertext);
                    $reg                = '/<div .*>[\s|\n]*(.*)<\/div>/i';
                    preg_match_all($reg, $commission_content, $div_data);
                    if (!$div_data[1][0]) {
                        $reg = '/<div .*>[\s|\n]*(.*)<br><a href=\'(.*)\' target=\'.*\' .*>.*<\/a>[\s|\n]*<\/div>/i';
                        preg_match_all($reg, $commission_content, $div_data);
                    }
                    list($commission_text, $commission_ratio) = explode('：', $div_data[1][0]);
                    if ($commission_text == '营销计划') {
                        $commission = 'ying_xiao';
                    } else if ($commission_text == '通用') {
                        $commission = 'tong_yong';
                    } else if ($commission_text == '鹊桥') {
                        $commission = 'que_qiao';
                    } else {
                        $commission = 'ding_xiang';
                    }
                    if (isset($div_data[2]) && $div_data[2][0]) {
                        $data[$key]['commission_alimama_url'] = $div_data[2][0];
                    } else {
                        $data[$key]['commission_alimama_url'] = '';
                    }
                    $commission_ratio               = substr(trim($commission_ratio), 0, -1);
                    $data[$key]['commission']       = $commission;
                    $data[$key]['commission_ratio'] = $commission_ratio;
                    break;
                case 4:
                    $coupon_info = trim($e->innertext);
                    if ($type == 'ing') {
                        $reg = '/(.*)<br\/>(.*)<br\/>(.*)<br\/>[\s|\n]*.*<a href="(.*)" target=".*" .*>.*<\/a>/i';
                        preg_match_all($reg, $coupon_info, $coupon);
                        $coupon_money                 = trim($coupon[1][0]);
                        $coupon_num                   = trim($coupon[2][0]);
                        $coupon_time                  = trim($coupon[3][0]);
                        $data[$key]['get_coupon_url'] = trim($coupon[4][0]);
                        preg_match('/\d+\.*\d*/', $coupon[1][0], $coupon_money);
                        $data[$key]['coupon_money'] = $coupon_money[0];
                        preg_match('/\d+/', $coupon[2][0], $coupon_num);
                        $data[$key]['coupon_num'] = $coupon_num[0];
                        $sPattern                 = "/\b\d{4}[-]\d{2}[-]\d{2}\s*\d{2}[:]\d{2}[:]\d{2}\b/i";
                        preg_match($sPattern, $coupon_time, $aMatch);
                        $data[$key]['end_time'] = strtotime($aMatch[0]);
                    } else {
                        $reg = '/<a href="(.*)" target=".*" .*>.*<\/a>/i';
                        preg_match_all($reg, $coupon_info, $coupon);
                        $data[$key]['get_coupon_url'] = trim($coupon[1][0]);
                    }
                    break;
                case 5:
                    $data[$key]['copy_writer'] = trim($e->innertext);
                    break;
                case 6:
                    break;
                case 7:
                    $online_time               = trim($e->plaintext);
                    $data[$key]['online_time'] = strtotime($online_time);
                    break;
                case 8:
                    if ($type == 'ing') {
                        $content = trim($e->innertext);
                        $reg     = '/<a href=\'(.*)\' target=\'.*\' .*>.*<\/a>/i';
                        preg_match_all($reg, $content, $da_tao_ke_url);
                        $data[$key]['status']      = 'ing';
                        $data[$key]['dataoke_url'] = trim($da_tao_ke_url[1][0]);
                    } else {
                        $status_update_reason               = trim($e->plaintext);
                        $data[$key]['status_update_reason'] = $status_update_reason;
                        $data[$key]['status']               = 'reject';

                        if ($type == 'end') {
                            $data[$key]['status'] = 'finished';

                            if ('审核拒绝' == mb_substr($status_update_reason, 0, 4)) {
                                $data[$key]['status'] = 'reject';
                            }
                        } else if ($type == 'reject') {
                            $data[$key]['status'] = 'reject';
                        }
                    }
                    break;
                case 9:
                    $content = trim($e->innertext);
                    $reg     = '/<a id=\'zsbeizhu_([0-9]*)\' class=\'zsbeizhu\' title=\'(.*)\'>备注<\/a>/i';
                    preg_match_all($reg, $content, $remark);
                    $data[$key]['dataoke_id']     = trim($remark[1][0]);
                    $data[$key]['dataoke_remark'] = trim($remark[2][0]);
                    break;
            }

            if (!isset($data[$key]['status_update_reason'])) {
                $data[$key]['status_update_reason'] = '';
            }

            $data[$key]['department_id'] = $cate['id'] == 3 ? 4 : $cate['id'];
            $data[$key]['type']          = $cate['name'] == 'xiao_liu' ? 'sheng_tian' : $cate['name'];
        }
        return $data;
    }

    /**
     * 获取各个部门的Cookie
     *
     * @return array|mixed|string|void
     */
    protected function _getDepartmentCookie() {
        $data = S('tdk_department_cookie');
        if (!$data) {
            //邮购招商部门总id
            $parent_id = 1;
            $data      = M('department')->field('id,cookie,dataoke_account_info as name')->index('name')->where(array('parent_id' => $parent_id))->select();
            S('tdk_department_cookie', $data);
        }
        return $data;
    }

    /**
     * 更新失效未抓取到的商品
     */
    public function updateEndDeal() {
        $end_time = strtotime(date('Y-m-d'));
        $where    = array('status' => 'ing', 'end_time' => array('lt', $end_time));
        $data     = M('deal')->where($where)->index('id')->select();
        if ($data) {
            $res = M('deal')->where(array('id' => array('in', array_keys($data))))->save(array('status' => 'finished', 'status_update_reason' => '商品失效'));
            if ($res) {
                $this->_addLog('dtk_update_end_deal.log', array('time' => date('Y-m-d H:i:s'), 'info' => $data));
            } else {
                $this->_addLog('dtk_update_end_deal.log', array('time' => date('Y-m-d H:i:s'), 'info' => $data, 'error' => M('deal')->getDbError()));
            }
        } else {
            $this->_addLog('dtk_update_end_deal.log', array('time' => date('Y-m-d H:i:s'), 'info' => '没有需要更新的数据'));
        }
    }

    /**
     * 商品到期发送消息提醒
     */
    public function dealEndSendMessage() {
        $where = array('d.claim_status' => 'Y', 'd.status' => 'finished', 'd.end_time' => array('egt', strtotime(date('Y-m-d'))));
        $list  = M('deal')
            ->alias('d')
            ->where($where)
            ->field('d.taobao_item_id as tao_bao_id,d.admin_id,d.merchant_id,a.full_name,a.open_id,m.shop_name,d.status_update_reason as remark')
            ->join('left join ytt_admin a ON a.id = d.admin_id')
            ->join('left join ytt_merchant m ON m.id = d.merchant_id')
            ->select();
        $_data = $data = array();
        foreach ($list as $v) {
            $_data[$v['admin_id']][$v['merchant_id']][] = $v;
        }
        foreach ($_data as $val) {
            foreach ($val as $_v) {
                $key_one = '淘宝ID：【';
                $key_two = date('Y-m-d', strtotime('-1 days'));
                foreach ($_v as $row) {
                    $key_one .= $row['tao_bao_id'] . '、';
                }
                $key_one   = mb_substr($key_one, 0, -1) . '】';
                $remark    = $_v[0]['remark'];
                $open_id   = $_v[0]['open_id'];
                $num       = count($_v);
                $temp_data = array(
                    'top_content' => "{$_v[0]['full_name']}你好，你的商家【{$_v[0]['shop_name']}】有{$num}件商品已结束，请尽快处理！",
                    'key_one'     => $key_one,
                    'key_two'     => $key_two,
                    'remark'      => $remark,
                );
                $data[]    = array('open_id' => $open_id, 'data' => $temp_data);
            }
        }
        if ($data) {
            foreach ($data as $_row) {
                $this->_sendMsg($_row['data'], $_row['open_id']);
            }
        }
        echo 'success';
    }

    /**
     * 检测cookie是否有效
     */
    public function checkCookie() {
        $url           = $this->get_url;
        $cookie        = (array)$this->cookie;
        $param         = array('type' => 'sh_2', 'page' => 1);
        $name_data     = array('xiao_liu' => '小刘联盟', 'sheng_tian' => '胜天联盟', 'ye_lang' => '野狼联盟！');
        $cookie_status = true;
        $name          = '';
        foreach ($cookie as $key => $val) {
            $res        = $this->Http->getCookie($url, $val['cookie'], $param);
            $goods_data = $this->_createData($res, 'ing', $val);
            if (count($goods_data) == 0) {
                $cookie_status = false;
                $name          = $name_data[$val['name']];
                break;
            }
        }
        if ($cookie_status === false) {
            $send_data = array(
                'top_content' => "CRM商品抓取COOKIE掉线",
                'key_one'     => $name,
                'key_two'     => date('Y-m-d H:i:s'),
                'remark'      => '请及时处理！！！',
            );
            foreach ($this->weChatUser as $v) {
                $this->_sendMsg($send_data, $v);
            }
            $data = array('time' => date('Y-m-d H:i:s'), 'msg' => 'cookie掉线', 'name' => $name);
            //$this->_addLog('crm_cookie.log', $data);
        } else {
            $data = array('time' => date('Y-m-d H:i:s'), 'msg' => 'cookie正常');
        }
    }

    /**
     * 商品到期发送消息提醒
     */
    public function dealIngSendMessage() {
        $where = array('d.claim_status' => 'Y', 'd.status' => 'ing', 'end_time' => array('lt', strtotime('+1 days')));
        $list  = M('deal')
            ->alias('d')
            ->where($where)
            ->field('d.taobao_item_id as tao_bao_id,d.admin_id,d.merchant_id,a.full_name,a.open_id,m.shop_name,d.status_update_reason as remark')
            ->join('left join ytt_admin a ON a.id = d.admin_id')
            ->join('left join ytt_merchant m ON m.id = d.merchant_id')
            ->select();
        $_data = $data = array();
        foreach ($list as $v) {
            $_data[$v['admin_id']][$v['merchant_id']][] = $v;
        }
        foreach ($_data as $val) {
            foreach ($val as $_v) {
                $key_one = '淘宝ID：【';
                $key_two = date('Y-m-d');
                foreach ($_v as $row) {
                    $key_one .= $row['tao_bao_id'] . '、';
                }
                $key_one   = mb_substr($key_one, 0, -1) . '】';
                $remark    = '商品即将到期，请跟踪处理';
                $open_id   = $_v[0]['open_id'];
                $num       = count($_v);
                $temp_data = array(
                    'top_content' => "{$_v[0]['full_name']}你好，你的商家【{$_v[0]['shop_name']}】有{$num}件商品即将到期，请尽快处理！",
                    'key_one'     => $key_one,
                    'key_two'     => $key_two,
                    'remark'      => $remark,
                );
                $data[]    = array('open_id' => $open_id, 'data' => $temp_data);
            }
        }
        if ($data) {
            foreach ($data as $_row) {
                $this->_sendMsg($_row['data'], $_row['open_id']);
            }
        }
        echo 'success';
    }

    /**
     * @param $content
     * @param $open_id
     * @return mixed
     */
    protected function _sendMsg($data, $open_id) {
        $url       = "https://api.weixin.qq.com/cgi-bin/message/template/send";
        $sendData  = array(
            'touser'      => $open_id,
            'template_id' => 'hdFdudHwNh_maqXl7z7cjh0w2S2KRcmxtbv30JeM3fo',
            'topcolor'    => '#7B68EE',
            'data'        => array(
                'first' => array(
                    'value' => urlencode($data['top_content']),
                    'color' => '#743A3A',
                ),

                'keyword1' => array(
                    'value' => urlencode($data['key_one']),
                    'color' => '#FF0000',
                ),
                'keyword2' => array(
                    'value' => urlencode($data['key_two']),
                    'color' => '#C4C400',
                ),
                'remark'   => array(
                    'value' => urlencode($data['remark']),
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
     * 记录日志
     *
     * @param $file_name
     * @param $data
     */
    protected function _addLog($file_name, $data) {
        $path = "/home/order_log/{$file_name}";
        file_put_contents($path, var_export($data, true) . "\r\n", FILE_APPEND);
    }
}