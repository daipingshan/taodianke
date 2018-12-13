<?php

/**
 * Created by JetBrains PhpStorm.
 * User: runtoad
 * Date: 15-3-6
 * Time: 下午3:01
 *
 */

namespace Common\Controller;

use Common\Controller\CommonBaseController;
use Common\Org\Http;
use Common\Org\OpenSearch;
use Common\Org\TaoBaoApi;
use Common\Org\WeiXin;

class CommonBusinessController extends CommonBaseController {

    // 迷离团队请求配置参数
    private $mili_param = 'item_id=%s&adzone_id=%s&platform=1&site_id=%s&token=%s';
    // 迷离团队的url
    private $mili_url = 'http://tbapi.00o.cn/highapi.php';
    // 淘宝详情地址
    private $tao_bao_detail_url = "https://item.taobao.com/item.htm?id=%s&ali_trackid=%s&pvid=%s";

    /**
     * CommonBusinessController constructor.
     */
    public function __construct() {
        parent:: __construct();
    }

    /**
     * @param $mobile 为接收方手机号码，如有多个以英文逗号隔开
     * @param $msg    短信内容，最多1000个字
     * @return array (status=0 短信发送成功 status=0 发送失败 error为错误信息)
     */
    protected function _sms($mobile, $msg, $type = 'Jcsms', $source = 'api') {
        if ($type != 'voice' && $type != 'MonSend') {
            $type = 'Jcsms';
        }
        if (!checkMobile($mobile)) {
            $this->_writeLog($mobile . '--手机号码格式有误', 'EMERG', $source);
            return array('status' => -1, 'error' => '手机号码格式有误');
        }
        if (!trim($msg)) {
            $this->_writeLog($mobile . '--短信内容不能为空', 'EMERG', $source);
            return array('status' => -1, 'error' => '短信内容不能为空');
        }
        if ($type == 'voice') {
            $sendSms = new \Common\Org\sendVoices();
            $res     = $sendSms->sendVoice($mobile, $msg);
        } else {
            $sendSms = new \Common\Org\sendSms();
            $res     = $sendSms->sendMsg($mobile, $msg, $type);
        }
        if ($res['status'] == 1) {
            return array('status' => 0);
        } else {
            $this->_writeLog($mobile . '--短信发送失败', 'EMERG', $source);
            return array('status' => -1, 'error' => $res['data']);
        }
    }

    /**
     * @param $pid
     * @return array
     */
    protected function _getCookie($pid = array()) {
        if (!$pid) {
            $pid = 'mm_121610813_22448587_79916379';
        }
        $cookie     = M('proxy')->where(array('pid' => $pid))->getField('cookie');
        $t          = array(' ', '　', '', '', '');
        $p          = array("", "", "", "", "");
        $cookie     = str_replace($t, $p, $cookie);
        $cookie     = $cookie . ';';
        $token      = get_word($cookie, '_tb_token_=', ';');
        $temp_one   = get_word($cookie, 't=', ';');
        $temp_two   = get_word($cookie, 'cna=', ';');
        $temp_three = get_word($cookie, 'l=', ';');
        $temp_four  = get_word($cookie, 'isg=', ';');
        $temp_five  = get_word($cookie, 'mm-guidance3', ';');
        $temp_six   = get_word($cookie, '_umdata=', ';');
        $temp_seven = get_word($cookie, 'cookie2=', ';');
        $temp_eight = get_word($cookie, 'cookie32=', ';');
        $temp_nice  = get_word($cookie, 'cookie31=', ';');
        $temp_pass  = get_word($cookie, 'alimamapwag=', ';');
        $temp_login = get_word($cookie, 'login=', ';');
        $temp_pw    = get_word($cookie, 'alimamapw=', ';');
        $cookie     = 't=' . $temp_one . ';cna=' . $temp_two . ';l=' . $temp_three . ';isg=' . $temp_four . ';mm-guidance3=' . $temp_five . ';_umdata=' . $temp_six . ';cookie2=' . $temp_seven . ';_tb_token_=' . $token . ';v=0;cookie32=' . $temp_eight . ';cookie31=' . $temp_nice . ';alimamapwag=' . $temp_pass . ';login=' . $temp_login . ';alimamapw=' . $temp_pw;
        $data       = array(
            'token'  => $token,
            'cookie' => $cookie,
        );
        return $data;
    }

    /**
     * 查询顶级用户ID
     *
     * @param $id
     * @return array|false|mixed|\PDOStatement|string|\think\Model
     */
    protected function _getParentsInfo($id) {
        $parent = M('user')->where(array('id' => $id))->find();
        if ($parent['parentid'] > 0) {
            $parent = $this->_getParentsInfo($parent['parentid']);
        }
        return $parent;
    }

    /**
     * 获取用户pid
     */
    protected function _getUserPid() {
        return M('user')->getFieldById($this->uid, 'pid');
    }

    /**
     * 获取用户字段
     *
     * @param        $id
     * @param string $field
     * @return mixed
     */
    protected function _getUserField($id, $field = "username") {
        return M('user')->getFieldById($id, $field);
    }

    /**
     * 根据用户的uid获取用户的pid已经顶级pid
     *
     * @param $uid
     * @return array
     */
    protected function _getParentPid($uid) {
        $pid = M('user')->getFieldById($uid, 'pid');
        if (!$pid) {
            $parent_pid = $pid = C('PID');
        } else {
            $parent_data = explode('_', $pid);
            $where       = array(
                'proxy_type' => 1,
                'pid'        => array('like', $parent_data[0] . '_' . $parent_data[1] . "%")
            );
            $parent_pid  = M('user')->where($where)->getField('pid');
            if (!$parent_pid) {
                $parent_pid = C('PID');
            }
        }
        return array('pid' => $pid, 'parent_pid' => $parent_pid);
    }

    /**
     * 获取代理的推广二维码
     *
     * @param $pid
     * @return string
     */
    public function _getQrcodeUrl($pid, $store_type = 0) {
        $qrcode_url    = '';
        $time          = time();
        $wxqrcode_path = 'Uploads/' . date('Y') . '/' . date('md') . '/';

        if (1 == $store_type) {
            $options = array('appid' => C('STORE_WEIXIN_BASE.app_id'), 'appsecret' => C('STORE_WEIXIN_BASE.app_secret'));
            $token_key = 'store_weChat_access_token';
        } else {
            $options = array();
            $token_key = 'tdk_weChat_access_token';
        }

        $WeiXin        = new \Common\Org\WeiXin($options);
        $access_token  = $this->_getWeChatAccessToken($token_key);
        $wx_qrcode_url = $WeiXin->getQRImageUrl($pid, $access_token);

        $wx_qrcode_url = str_ireplace('https', 'http', $wx_qrcode_url);
        $uid           = M('user')->where(array('pid' => $pid))->getField('id');
        $this->_getImage($wx_qrcode_url, C('uploadPath') . '/', 'wx_qrcode_' . $uid . '.png', '');
        $image = new \Think\Image();
        $image->open(C('uploadPath') . '/wx_qrcode_' . $uid . '.png');
        $image->thumb(172, 172)->save(C('uploadPath') . '/wx_qrcode_' . $uid . '.png');
        $wxqrcode_bg = C('uploadPath') . 'wxqrcode_bg.jpg';
        $image->open($wxqrcode_bg);
        $locate = array('487', '985');
        $image->water(C('uploadPath') . '/wx_qrcode_' . $uid . '.png', $locate)->save(C('uploadPath') . '/tdk_qrcode_' . $uid . '.jpg');

        $result = $this->saveFile(C('uploadPath') . '/tdk_qrcode_' . $uid . '.jpg', $uid . '_' . $time . '.jpg');
        if ($result['code'] == 1) {
            // 删除本地服务器图片
            $fileName = ROOT_PATH . C('uploadPath') . '/wx_qrcode_' . $uid . '.png';
            if (file_exists($fileName)) {
                @unlink($fileName);
            }
            $fileName = ROOT_PATH . C('uploadPath') . '/tdk_qrcode_' . $uid . '.jpg';
            if (file_exists($fileName)) {
                @unlink($fileName);
            }
            return $result['url'];
        } else {
            return $qrcode_url;
        }
    }

    /**
     * 本地保存微信二维码
     *
     * @param        $url
     * @param string $save_dir
     * @param string $filename
     * @param int $type
     * @return array
     */
    public function _getImage($url, $save_dir = '', $filename = '', $type = 0) {
        if (trim($url) == '') {
            return array('file_name' => '', 'save_path' => '', 'error' => 1);
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (trim($filename) == '') {//保存文件名
            $ext = strrchr($url, '.');
            if ($ext != '.gif' && $ext != '.jpg' && $ext != '.png' && $ext != '.jpeg') {
                return array('file_name' => '', 'save_path' => '', 'error' => 3);
            }
            $filename = time() . rand(0, 10000) . $ext;
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir .= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return array('file_name' => '', 'save_path' => '', 'error' => 5);
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch      = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $img = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $img = ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $img);
        fclose($fp2);
        unset($img, $url);
        return array('file_name' => $filename, 'save_path' => $save_dir . $filename, 'error' => 0);
    }

    /**
     * 记录日志
     *
     * @param $file_name
     * @param $data
     */
    protected function _addLog($file_name, $data) {
        $path = "/home/order_log/{$file_name}" . date('Ymd');
        file_put_contents($path, var_export($data, true) . "\r\n", FILE_APPEND);
    }

    /*  获取用户的token和cookie
    *  根据用户pid的第二段获取不同的access_token,qtk_cookie
    *  @param  key   string
    *  @return data  array
    * */
    protected function _getProxyData($key) {
        $data = S('tdk_proxy_data');
        if (!$data) {
            $proxy = M('proxy')->field('pid,taobao_access_token as token, qtk_cookie')->select();
            foreach ($proxy as $k => $v) {
                list($_, $k, $_, $_) = explode('_', $v['pid']);
                $data[$k]['token']      = $v['token'];
                $data[$k]['qtk_cookie'] = $v['qtk_cookie'];
            }
            S('tdk_proxy_data', $data);
        }
        if (isset($data[$key]) && $data[$key]) {
            return $data[$key];
        } else {
            $key = '121610813';
            return $data[$key];
        }
    }

    /**
     * @param $money
     * @return string
     */
    protected function _getFeeRate() {
        $fee_rate  = 100;
        $parent_id = $this->_getUserField($this->uid, 'ParentID');
        if (!$parent_id) {
            return $fee_rate;
        }
        $dip = M('user_proxy_ratio')->where(array('uid' => $parent_id, 'cid' => $this->uid))->getField('dip');
        if ($dip > 0 && $dip < 100) {
            return (int)$dip;
        }
        return $fee_rate;
    }

    /**
     * 获取商品列表
     */
    public function _getItemsList($where, $order, $start_num = 0, $limit = 21, $type = 'api') {
        $fee_rate = 100;
        if ($type == 'api') {
            if ($this->version >= 2) {
                $user_ad_id = $this->_getUserField($this->uid, 'dwxk_adsense_id');
                if (!$user_ad_id) {
                    $where['shop_type'] = array('in', 'B,C');
                }
            } else {
                $where['shop_type'] = array('in', 'B,C');
            }
            $fee_rate = $this->_getFeeRate();
        }
        $field        = 'id,num_iid,title,uname,price,coupon_price,pic_url,shop_type,quan,snum,lnum,volume,commission_rate,commission';
        $data         = M('items')->field($field)->where($where)->limit($start_num, $limit)->order($order)->select();
        $mi_li_status = C('MILI.is_use');
        foreach ($data as $key => &$val) {
            if ('//' == substr($val['pic_url'], 0, 2)) {
                $val['pic_url'] = 'https:' . $val['pic_url'];
            }
            $val['commission_rate'] = $val['commission_rate'] / 100;
            $val['goods_type']      = 'tdk';
            if ($val['shop_type'] == 'J') {
                $val['type']       = '楚楚街';
                $val['goods_type'] = 'chuchujie';
            } else {
                $val['commission_rate'] = computedPrice($val['commission_rate'], 1);
                $val['commission']      = computedPrice($val['commission']);
                $val['type']            = '淘店客推荐';
            }
            $val['num'] = $val['snum'] + $val['lnum'];
            if ($type == 'api') {
                if ($mi_li_status == 'Y') {
                    $val['uname'] = 'tongyong';
                } else {
                    if ($val['uname'] == 'yingxiao') {
                        $val['uname'] = 'wfa';
                    }
                }
                if ($type == 'api') {
                    if ($fee_rate < 100) {
                        $val['yong_jin'] = sprintf("%.2f", $val['commission'] * $fee_rate / 100) . '元';
                    } else {
                        $val['yong_jin'] = $val['commission'] . '元';
                    }
                }
            }
        }
        unset($val);
        return $data;
    }

    /**
     * 获取搜索服务中的商品
     *
     * @param        $query_content
     * @param        $sort
     * @param null $filter
     * @param int $start_num
     * @param int $limit
     * @param string $type
     * @return array
     */
    protected function _getOpenSearchList($query_content, $sort, $filter = null, $start_num = 0, $limit = 21, $type = 'api') {
        if ($type === 'api') {
            if ($this->version >= 2) {
                $user_ad_id = $this->_getUserField($this->uid, 'dwxk_adsense_id');
                if (!$user_ad_id) {
                    $query = "(shop_type:'B' OR shop_type:'C')";
                }
            } else {
                $query = "(shop_type:'B' OR shop_type:'C')";
            }
        }
        if (isset($query) && $query) {
            if ($query_content) {
                $keyword = $query_content . ' AND ' . $query;
            } else {
                $keyword = $query;
            }
        } else {
            $keyword = $query_content;
        }
        $keyword = $keyword ? $keyword . " AND pass:'1'" : "pass:'1'";
        $obj     = new OpenSearch();
        $res     = $obj->search($keyword, $sort, $filter, $start_num, $limit);
        if ($res['status'] == 'OK') {
            $data         = $res['data'];
            $mi_li_status = C('MILI.is_use');
            $fee_rate     = 100;
            if ($type == 'api') {
                $fee_rate = $this->_getFeeRate();
            }
            foreach ($data as $key => &$val) {
                if ('//' == substr($val['pic_url'], 0, 2)) {
                    $val['pic_url'] = 'https:' . $val['pic_url'];
                }
                $val['commission_rate'] = $val['commission_rate'] / 100;
                $val['goods_type']      = 'tdk';
                if ($val['shop_type'] == 'J') {
                    $val['type']       = '楚楚街';
                    $val['goods_type'] = 'chuchujie';
                } else {
                    $val['commission_rate'] = computedPrice($val['commission_rate'], 1);
                    $val['commission']      = computedPrice($val['commission']);
                    $val['type']            = '淘店客推荐';
                }
                $val['num'] = $val['snum'] + $val['lnum'];
                if ($type == 'api') {
                    if ($mi_li_status == 'Y') {
                        $val['uname'] = 'tongyong';
                    } else {
                        if ($val['uname'] == 'yingxiao') {
                            $val['uname'] = 'wfa';
                        }
                    }
                    if ($fee_rate < 100) {
                        $val['yong_jin'] = sprintf("%.2f", $val['commission'] * $fee_rate / 100) . '元';
                    } else {
                        $val['yong_jin'] = $val['commission'] . '元';
                    }
                }
            }
            unset($val);
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 获取搜索服务中的商品
     *
     * @param        $query_content
     * @param null $filter
     * @param string $type
     * @return int
     */
    protected function _getOpenSearchCount($query_content, $filter = null, $type = 'api') {
        if ($type == 'api') {
            if ($this->version == 2) {
                $user_ad_id = $this->_getUserField($this->uid, 'dwxk_adsense_id');
                if (!$user_ad_id) {
                    $query = "(shop_type:'B' OR shop_type:'C')";
                }
            } else {
                $query = "(shop_type:'B' OR shop_type:'C')";
            }
        }
        if (isset($query) && $query) {
            if ($query_content) {
                $keyword = $query_content . ' AND ' . $query;
            } else {
                $keyword = $query;
            }
        } else {
            $keyword = $query_content;
        }
        $keyword = $keyword ? $keyword . " AND pass:'1'" : "pass:'1'";
        $obj     = new OpenSearch();
        return $obj->searchCount($keyword, $filter);
    }

    /**
     *  检测商品状态，如果下线删除商品
     *
     * @param  num_iid string
     */
    protected function _checkItemsStatus($dataoke_id) {
        $dtkObj   = new \Common\Org\DaTaoKe();
        $item_res = $dtkObj->isItemOnline($dataoke_id);
        if ($item_res['code'] == -1) {
            M('items')->where(array('dataoke_id' => $dataoke_id))->delete();
            return array('status' => -404, 'info' => '商品已没有高佣，已下线！');
        } else {
            if ($item_res['data']) {
                $save_data   = $item_res['data'];
                $coupon_type = M('items')->where(array('dataoke_id' => $dataoke_id))->getField('coupon_type');
                if ($coupon_type == 4 && $save_data['coupon_price'] > 9.9) {
                    $save_data['coupon_type'] = 1;
                }
                $save_data['last_query_dataoke_time'] = time();
                M('items')->where(array('dataoke_id' => $dataoke_id))->save($save_data);
            }
            return array('status' => 0, 'info' => 'ok');
        }
    }

    /**
     *  迷离团队获取高佣的接口
     *
     * @param  id    string
     * @param  pid   string
     * @param  item  array
     * @return array
     *
     */
    protected function _miLiHighApply($id, $pid, $item) {
        if ('' != $item['activity_id']) {
            $activity_id = $item['activity_id'];
        } else {
            if (strpos($item['click_url'], 'activityId')) {
                $activity      = explode('=', $item['click_url']);
                $activity_temp = explode('&', $activity[1]);
                $activity_id   = $activity_temp[0];
            } else {
                $activity_id = '';
            }
        }
        $httpObj    = new \Common\Org\Http();
        $user_info  = explode('_', $pid);
        $proxy_data = $this->_getProxyData($user_info[1]);
        $map        = sprintf($this->mili_param, $id, $user_info[3], $user_info[2], $proxy_data['token']);
        $mi_li_res  = $httpObj->post($this->mili_url, $map);
        $res = json_decode($mi_li_res, true);
        if ($res && $res['result']['data']['coupon_click_url']) {
            $mili_data = $res['result']['data'];
            if ($activity_id) {
                $click_url = $res['result']['data']['coupon_click_url'] . '&activityId=' . $activity_id . '&pid=' . $pid . '&itemId=' . $id;
            } else {
                $click_url = $res['result']['data']['coupon_click_url'];
            }
            $obj = new \Common\Org\TaoBaoApi();
            $res = $obj->getApiPass($click_url, $item['title'], $item['pic_url']);
            if ($res['code'] == -1) {
                if (1235 == $this->uid) { //董广启账号
                    return array('status' => -1, 'info' => $res['msg']);
                }
                return array('status' => -1, 'data' => '', 'info' => '获取优惠券失败！!!');
            }
            return array('status' => 1, 'data' => $res, 'info' => '优惠券获取成功', 'mili_data' => $mili_data);
        } else {
            $mi_li_log  = '时间：' . date('Y-m-d H:i:s') . ';用户id：' . $this->uid . '请求参数：' . $map . ';迷离返回结果：' . $mi_li_res;
            $this->_addLog('mi_li.log', $mi_li_log);

            return array('status' => 0, 'data' => '', 'info' => $res['sub_msg']);
        }
    }

    /**
     *     轻淘客获取高佣淘口令
     *
     * @param   array   item
     * @param   string  pid
     * @return  array   res
     */
    protected function _getQtkData($item, $pid) {
        $user_info  = explode('_', $pid);
        $proxy_data = $this->_getProxyData($user_info[1]);

        $cookie = $proxy_data['qtk_cookie'];
        // 根据轻淘客需要的参数进行拼接整理
        $quan        = $item['price'] - $item['coupon_price'];
        $tmp         = explode('&', $item['click_url']);
        $activity    = explode('=', $tmp[0]);
        $activity_id = $activity[1];
        $yongjin     = $item['commission_rate'] / 100;
        // 随机类型
        $tmp_type = array('qq', 'wx');
        $tmp_res  = array_rand($tmp_type, 1);
        $type     = $tmp_type[$tmp_res];
        // 组装轻淘客需要的参数
        $_param      = array(
            'goods_id'   => $item['num_iid'],  // 商品id
            'pid'        => $pid, // 用户的pid
            'activityId' => $activity_id, // 商品的活动id
            'sellerId'   => $item['sellerid'], // 卖家id
            'used_price' => $item['coupon_price'], // 券后价
            'sell_num'   => $item['volume'],
            'yongjin'    => $yongjin, //  佣金的比例值
            'quan_price' => $quan, // 券价格
            'type'       => $type,  // 所需淘口令的类型
            'title'      => $item['title'], // 商品标题
            'price'      => $item['price'], // 商品原价
            'imageSrc'   => $item['pic_url'], // 商品图片
        );
        $httpObj     = new \Common\Org\Http();
        $submit_data = http_build_query($_param);
        // 从轻淘客换取所需数据
        $res = json_decode($httpObj->postCookie(C('QTK.request_url'), $cookie, $submit_data, C('QTK.refresh_url')), true);

        $res['time'] = date('Y-m-d H:i:s');
        $res['id']   = $this->uid . "@" . $item['id'];
        if ($res['status'] == '1') {
            $this->_addLog('qtk_fail.log', $res);
            return array('status' => 0, 'data' => '', 'info' => '轻淘客调用失败！');
        } else {
            $this->_addLog('qtk_fail.log', $res);
            return array('status' => 1, 'data' => $res, 'info' => '轻淘客调用成功！');
        }
    }

    /**
     * 获取全网数据
     *
     * @param $keyword
     * @param $page
     * @param $sort
     * @return array
     */
    protected function _getTaoBaoSearch($keyword, $page, $sort, $page_size = 20) {
        $obj      = new \Common\Org\TaoBaoApi();
        $res      = $obj->search($keyword, $this->uid, $page, $page_size, $sort);
        $data     = $res['data'] ? $res['data'] : array();
        $fee_rate = $this->_getFeeRate();
        foreach ($data as &$val) {
            $val['yong_jin'] = sprintf("%.2f", $val['yong_jin'] * $fee_rate / 100) . '元';
        }

        return $data;
    }

    /**
     * 获取分类信息
     */
    public function _getItemCate() {
        $cate = S('tdk_items_cate');
        if (!$cate) {
            $cate = M('items_cate')->field('id as cate_id,name,cateimg as img')->where(array('status' => 1, 'pid' => 18))->index('cate_id')->order('ordid asc,id desc')->select();
            foreach ($cate as &$val) {
                $val['img'] = getImgUrl($val['img']);
            }
            S('tdk_items_cate', $cate);
        }
        return $cate;
    }

    /**
     * @param string $type_id
     */
    protected function _getAdImg($type_id = '15') {
        $data = S('tdk_ad_img');
        if (!$data) {
            $now   = time();
            $where = array('status' => 1, 'start_time' => array('elt', $now), 'end_time' => array('egt', $now));
            $img   = M('ad')->where($where)->field('content as img,url,desc,board_id')->order('ordid asc,id desc')->select();
            $data  = array();
            foreach ($img as $val) {
                $val['img']               = getImgUrl($val['img']);
                $val['desc']              = $val['desc'] ? $val['desc'] : '广告';
                $data[$val['board_id']][] = $val;
            }
            S('tdk_ad_img', $data, 86400);
        }
        return isset($data[$type_id]) && $data[$type_id] ? $data[$type_id] : array();
    }

    /**
     * 一键转链
     *
     *
     * @param $content
     * @param $pid
     * @return array
     */
    protected function _turnChain($content, $pid) {
        $return_data = array('code' => -1, 'msg' => 'error', 'data' => '');
        if (!$content) {
            $return_data['msg'] = '推广信息不能为空！';
            return $return_data;
        }
        if (!$pid) {
            $return_data['msg'] = '推广位ID不能为空！';
            return $return_data;
        }
        $str = preg_replace('/[￥|《|€][a-zA-z0-9]{11}[￥|《|€]/', '', $content);
        $str = preg_replace('/((http|https):[\/]{2}[a-zA-Z\d\.#\?\/\=\&\_]*)/', '', $str);
        $this->_getTagsStr($str);
        $kou_ling_reg = "/[￥|《|€][a-zA-z0-9]{11}[￥|《|€]/";
        if (preg_match($kou_ling_reg, $content)) {
            return $this->_kouLingTurnChain($content, $pid);
        } else {
            return $this->_urlTurnChain($content, $pid);
        }

    }

    /**
     * @param $content
     * @param $pid
     * @return array
     */
    protected function _urlTurnChain($content, $pid) {
        $return_data = array('code' => -1, 'msg' => 'error', 'data' => '');
        $exp_reg     = "/((http|https):[\/]{2}[a-zA-Z\d\.#\?\/\=\&\_]*)/";
        preg_match_all($exp_reg, $content, $res);
        if (!isset($res[0]) || count($res[0]) < 2) {
            $return_data['msg'] = '推广链接不合法！';
            return $return_data;
        }
        $click_url        = $res[0][0];
        $activity_exp_reg = "/(activityId|activity_id|activityid)=([a-z|\d]{20,})/";
        preg_match_all($activity_exp_reg, $click_url, $activity);
        if (!isset($activity[2]) || count($activity[2]) != 1) {
            $return_data['msg'] = '领券链接缺少活动ID，无法转链！';
            return $return_data;
        }
        $activity_id = $activity[2][0];
        $item_url    = $old_item_url = $res[0][1];
        if (strpos($item_url, 's.click.taobao.com')) {
            $temp_res = getTaobaoTrueUrl($item_url);
            if ($temp_res['res_url']) {
                $item_url = $temp_res['res_url'];
            } else {
                $return_data['msg'] = '商品链接不符合转链要求，无法转链！';
                return $return_data;
            }
        }

        $item_exp_reg = "/id=(\d{10,15})/";
        preg_match_all($item_exp_reg, $item_url, $item);
        if (!isset($item[1]) || count($item[1]) != 1) {
            $return_data['msg'] = '商品链接中不存在商品编号，无法转链！';
            return $return_data;
        }
        $item_id   = $item[1][0];
        $obj       = new \Common\Org\TaoBaoApi();
        $item_info = $obj->getTaoBaoItemInfo($item_id);
        if ($item_info['code'] == 1) {
            $info['title']   = $item_info['data']['title'];
            $info['pic_url'] = $item_info['data']['pict_url'];
        } else {
            $info['title']   = '点击下方打开获取淘宝优惠信息';
            $info['pic_url'] = '';
        }
        $info['activity_id'] = $activity_id;

        $coupon_money    = 0; //优惠券金额
        $commission      = 0; //可获得的佣金
        $commission_rate = 0; //可获得的佣金率

        //迷离获取淘口令
        $mi_li_status = C('MILI.is_use');
        if ($mi_li_status == 'Y') {
            $res = $this->_miLiHighApply($item_id, $pid, $info);
            if ($res['status'] != 1) {
                $return_data['msg'] = $res['info'];
                return $return_data;
            }
            $info['kou_ling'] = $res['data']['kou_ling'];
            $info['url']      = $res['data']['url'];

            if (isset($res['mili_data']['max_commission_rate'])) {
                $commission_rate = computedPrice($res['mili_data']['max_commission_rate'] * $this->_getFeeRate() / 100, 1);
            }

            if (isset($res['mili_data']['coupon_info'])) {
                preg_match_all('/满([0-9]*).*减([0-9]*)/i', $res['mili_data']['coupon_info'], $coupon_info);
                if (count($coupon_info) >= 3) {
                    $price        = $coupon_info[1][0];
                    $coupon_money = $coupon_info[2][0];
                    $coupon_price = $price - $coupon_money;
                    $commission   = round($coupon_price * $commission_rate / 100, 1);
                }
            }
        } else {
            if (strpos($info['click_url'], 'uland.taobao.com')) {
                $res = $obj->getApiPass($info['click_url'], $info['title'], $info['pic_url']);
            } else {
                $res = $obj->getUrlApiPass($info['click_url'], $info['title'], $info['pic_url']);
            }
            if ($res['code'] != 1) {
                $return_data['msg'] = '获取优惠券失败！';
                return $return_data;
            }
            $info['kou_ling'] = $res['kou_ling'];
            $info['url']      = $res['url'];
        }
        $short_url = $obj->getShortUrl($info['url']);
        if ($short_url['code'] == 1) {
            $info['url'] = $short_url['data'];
        }

        $content = str_replace($click_url, $info['url'], $content);
        $content = str_replace($old_item_url, "复制这条信息【{$info['kou_ling']}】，打开[手机淘宝]即可领券并下单。", $content);
        $content = str_replace("[图片]\n", '', $content);
        $data    = array(
            'turn_chain_item_info' => $content,
            'coupon_url'           => $info['url'],
            'kou_ling'             => $info['kou_ling'],
            'pic_url'              => $info['pic_url'],
            'coupon_money'         => $coupon_money,
            'commission'           => $commission,
            'commission_rate'      => $commission_rate,
        );

        if ('//' == substr($data['pic_url'], 0, 2)) {
            $data['pic_url'] = 'https:' . $data['pic_url'];
        }

        $jpg_pos = stripos($data['pic_url'], '.jpg_');
        if (false !== $jpg_pos) {
            $data['pic_url'] = substr($data['pic_url'], 0, $jpg_pos + 4);
        }

        $return_data = array('code' => 1, 'msg' => 'ok', 'data' => $data);
        return $return_data;
    }

    /**
     * @param $content
     * @param $pid
     * @return array
     */
    protected function _kouLingTurnChain($content, $pid) {
        $return_data  = array('code' => -1, 'msg' => 'error', 'data' => '');
        $kou_ling_reg = "/(￥|《|€)[a-zA-z0-9]{11}(￥|《|€)/";
        preg_match($kou_ling_reg, $content, $kou_ling_res);
        if (!$kou_ling_res || !$kou_ling_res[0]) {
            $return_data['msg'] = '淘口令格式不正确！';
            return $return_data;
        }
        $kou_ling  = $kou_ling_res[0];
        $obj       = new TaoBaoApi();
        $item_info = $obj->getKouLingUrl($kou_ling);
        if ($item_info['suc'] == false) {
            $return_data['msg'] = '淘口令转链失败！';
            return $return_data;
        }

        $info     = array(
            'title'   => $item_info['content'],
            'pic_url' => empty($item_info['thumb_pic_url']) ? 'http://www.taodianke.com/Public/Home/img/wx.png' : strval($item_info['thumb_pic_url']),
        );
        $item_url = $item_info['url'];
        if (strpos($item_url, 'uland.taobao.com')) {
            $coupon_url = str_replace('uland.taobao.com/coupon/edetail', 'uland.taobao.com/cp/coupon/', $item_url);
            $httpObj    = new Http();
            $coupon_res = json_decode($httpObj->get($coupon_url), true);
            if (!$coupon_res['result']['item']['itemId']) {
                $return_data['msg'] = '优惠券链接未找到商品信息，无法转链！';
                return $return_data;
            }
            $item_id          = $coupon_res['result']['item']['itemId'];
            $activity_exp_reg = "/(activityId|activity_id|activityid)=([a-z|\d]{20,})/";
            preg_match($activity_exp_reg, $item_url, $activity);
            if (isset($activity[2]) && $activity[2]) {
                $info['activity_id'] = $activity[2];
            }
        } else {
            $temp_res = getTaobaoTrueUrl($item_url);
            if (!$temp_res['res_url']) {
                $return_data['msg'] = '商品链接不符合转链要求，无法转链！';
                if (1235 == $this->uid) {
                    $return_data['msg'] .= var_export($temp_res, true);
                }
                return $return_data;
            }

            $item_exp_reg = "/id=(\d{10,15})/";
            preg_match($item_exp_reg, $temp_res['res_url'], $item);
            if (!isset($item[1]) || !$item[1]) {
                $return_data['msg'] = '商品链接中不存在商品编号，无法转链！';
                if (1235 == $this->uid) {
                    $return_data['msg'] .= $item_url . '|||' . $temp_res . var_export($item_info, true);
                }
                return $return_data;
            }
            $item_id = $item[1];
        }

        $coupon_money    = 0; //优惠券金额
        $commission      = 0; //可获得的佣金
        $commission_rate = 0; //可获得的佣金率

        //迷离获取淘口令
        $mi_li_status = C('MILI.is_use');
        if ($mi_li_status == 'Y') {
            $res = $this->_miLiHighApply($item_id, $pid, $info);
            if ($res['status'] != 1) {
                $return_data['msg'] = $res['info'];
                return $return_data;
            }
            $info['kou_ling'] = $res['data']['kou_ling'];
            $info['url']      = $res['data']['url'];

            if (isset($res['mili_data']['max_commission_rate'])) {
                $commission_rate = computedPrice($res['mili_data']['max_commission_rate'] * $this->_getFeeRate() / 100, 1);
            }

            if (isset($res['mili_data']['coupon_info'])) {
                preg_match_all('/满([0-9]*).*减([0-9]*)/i', $res['mili_data']['coupon_info'], $coupon_info);
                if (count($coupon_info) >= 3) {
                    $price        = $coupon_info[1][0];
                    $coupon_money = $coupon_info[2][0];

                    //优惠券占比过大的重新获取一下商品价格
                    if ($coupon_money / $price > 0.8) {
                        $item = $this->_getTaobaoItemDetail($item_id);
                        if (isset($item['min_price'])) {
                            $price = $item['min_price'];
                        }
                    }

                    $coupon_price = $price - $coupon_money;
                    $commission   = round($coupon_price * $commission_rate / 100, 1);
                }
            }
        } else {
            if (strpos($info['click_url'], 'uland.taobao.com')) {
                $res = $obj->getApiPass($info['click_url'], $info['title'], $info['pic_url']);
            } else {
                $res = $obj->getUrlApiPass($info['click_url'], $info['title'], $info['pic_url']);
            }
            if ($res['code'] != 1) {
                $return_data['msg'] = '获取优惠券失败！';
                return $return_data;
            }
            $info['kou_ling'] = $res['kou_ling'];
            $info['url']      = $res['url'];
        }
        $content = "{$info['title']}\n复制这条信息【{$info['kou_ling']}】，打开[手机淘宝]即可领券并下单。";
        $data    = array(
            'turn_chain_item_info' => $content,
            'coupon_url'           => $info['url'],
            'kou_ling'             => $info['kou_ling'],
            'pic_url'              => $info['pic_url'],
            'coupon_money'         => $coupon_money,
            'commission'           => $commission,
            'commission_rate'      => $commission_rate,
        );

        if ('//' == substr($data['pic_url'], 0, 2)) {
            $data['pic_url'] = 'https:' . $data['pic_url'];
        }

        $jpg_pos = stripos($data['pic_url'], '.jpg_');
        if (false !== $jpg_pos) {
            $data['pic_url'] = substr($data['pic_url'], 0, $jpg_pos + 4);
        }

        $return_data = array('code' => 1, 'msg' => 'ok', 'data' => $data);
        return $return_data;
    }

    //获取淘宝商品基本信息
    protected function _getTaobaoItemDetail($item_id) {
        $url      = 'http://hws.m.taobao.com/cache/wdetail/5.0/?id=' . $item_id;
        $item_res = file_get_contents($url);
        $res_data = json_decode($item_res, true);

        $item = array();
        if (isset($res_data['data']['itemInfoModel'])) {
            $item  = $res_data['data']['itemInfoModel'];
            $temp1 = array_shift($res_data['data']['apiStack']);
            $temp2 = json_decode($temp1['value'], true);
            $item  = array_merge($item, $temp2['data']['itemInfoModel']);
            $temp3 = array_shift($temp2['data']['itemInfoModel']['priceUnits']);
            $temp4 = explode('-', $temp3['rangePrice']);

            if (count($temp4) > 1) {
                $item['min_price'] = $temp4[0];
                $item['max_price'] = $temp4[1];
            } else {
                $item['min_price'] = $item['max_price'] = $temp4[0];
            }
        }

        return $item;
    }

    /**
     * 生成淘宝转链地址
     *
     * @param $id
     * @param $pid
     * @return string
     */
    protected function _createTaoBaoDetailUrl($id, $pid) {
        $temp         = microtime(true) * 1000;
        $temp         = explode('.', $temp);
        $time         = time();
        $num          = rand(100, 999);
        $end          = strtotime("+12 months");
        $ip           = get_client_ip();
        $ali_track_id = "2:{$pid}:{$time}_{$num}_{$end}";
        $pv_id        = "10_{$ip}_{$num}_{$temp[0]}";
        $click_url    = sprintf($this->tao_bao_detail_url, $id, $ali_track_id, $pv_id);
        return $click_url;
    }

    /**
     * 获取微信公众号token
     *
     * $type == 'tdk_weChat_access_token' 淘店客公众号获取access_token缓存key
     * $type == 'store_weChat_access_token' 宅喵生活公众号获取access_token缓存key
     *
     * @param string $key
     * @return mixed|string
     */
    protected function _getWeChatAccessToken($key = 'tdk_weChat_access_token') {
        $res = S($key);
        if (!$res) {
            if ($key == 'tdk_weChat_access_token') {
                $obj = new WeiXin();
            } else {
                $option = array(
                    'app_id'     => C('STORE_WEIXIN_BASE.app_id'),
                    'app_secret' => C('STORE_WEIXIN_BASE.app_secret'),
                );
                $obj    = new WeiXin($option);
            }
            $res = $obj->getAccessToken();
            if ($res) {
                S($key, $res, 7000);
            }
        }
        return $res;
    }


}
