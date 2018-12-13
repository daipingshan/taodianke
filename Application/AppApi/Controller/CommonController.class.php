<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2016/12/28
 * Time: 15:07
 */

namespace AppApi\Controller;

use Common\Controller\CommonBusinessController;

/**
 * Class CommonAction
 */
class CommonController extends CommonBusinessController {

    /**
     *接口缓存方式
     *
     * @var type
     */
    protected $api_cache_type = 'S';

    /**
     * 接口数据缓存时间
     */
    const CACHE_TIME_OUT = 300;

    /**
     * @var bool 是否验证uid
     */
    protected $checkUser = true;

    /**
     * @var bool 是否验证签名
     */
    protected $signCheck = false;

    /**
     * @var bool 是否验证token
     */
    protected $tokenCheck = false;

    /**
     * @var int 每页请求多少条数据
     */
    protected $reqnum = 20;

    /**
     * @var int 错误1级标志
     */
    private $msg = '';

    /**
     * @var int 错误标志
     */
    private $code = 0;

    /**
     * @var int 是否有下一页
     */
    private $hasNext = 0;

    /**
     * @var null 返回数据
     */
    private $data = '';

    /**
     * @var string 返回结果
     */
    private $result = '';

    /**
     * @var null 全局变量
     */
    private $G = null;

    /**
     * @var string
     */
    protected $pid = '';

    /**
     * @var string
     */
    protected $dwxk_adsense_id = '';

    /**
     * 线下门店用户类型 0非线下门店账号  1线下门店店主  2线下门店收银
     *
     * @var int
     */
    protected $store_type = 0;

    /**
     * 构造函数
     *
     * @access public
     */
    function __construct() {
        parent:: __construct();
        if ($this->checkUser) {
            $this->check();
        }
    }

    /**
     * @param        $model             模型
     * @param        $idkey             主键
     * @param        $dataArr           数据
     * @param        $whereArr          where条件
     * @param string $whereStr          where条件2
     */
    protected function getHasNext($model, $idkey, $dataArr, $whereArr, $whereStr = "1=1") {
        if (count($dataArr) < $this->reqnum) {
            $this->hasNext = 0;
        } else {
            if ($this->pageflag == 2) {
                $first = array_shift($dataArr);
                $whereStr .= " and " . $idkey . ">" . intval($first[$idkey]);
            } else {
                $end = array_pop($dataArr);
                $whereStr .= " and " . $idkey . "<" . intval($end[$idkey]);
            }
            $count         = $model->where($whereArr)->where($whereStr)->count();
            $this->hasNext = $count == 0 ? $count : 1;
        }
    }

    /**
     *  设置 hasnext
     *
     * @param bool $next
     * @param array $data
     * @param int $limitNum
     * @return bool|int|string
     */
    protected function setHasNext($next = false, &$data = array(), $limitNum = 20) {
        if ($next !== false) {
            $this->hasNext = $next;
            return $this->hasNext;
        }
        $this->hasNext = '0';
        if ($data && $limitNum) {
            if (count($data) > $limitNum) {
                $this->hasNext = '1';
                array_pop($data);
            }
        }

        return $this->hasNext;
    }

    /**
     * 验证函数
     *
     * @access private
     */
    protected function check() {
        $user_data             = $this->_checkToken();
        $this->uid             = $user_data['uid'];
        $this->pid             = $user_data['pid'];
        $this->dwxk_adsense_id = $user_data['dwxk_adsense_id'];
        $this->store_type      = $user_data['store_type'];
    }

    /**
     * 获取账户余额
     *
     * @param string $type
     * @return mixed
     */
    protected function _amount($type = 'select_settle') {
        $today      = date('d');
        $first_time = strtotime(date("Y-m-01", strtotime('-1 months')));
        $pro_time   = strtotime(date('Y-m-01'));
        $where      = array('user_id' => $this->uid, 'pay_status' => 'settle', 'withdraw_id' => 0);
        if ($type == 'select_settle') {
            if ($today < C('balance_time')) {
                $where['_string'] = "UNIX_TIMESTAMP(earning_time) < {$first_time} ";
            } else {
                $where['_string'] = "UNIX_TIMESTAMP(earning_time) < {$pro_time} ";
            }
        } else {
            $where['_string'] = "UNIX_TIMESTAMP(earning_time) < {$pro_time} ";
        }

        $amount = M('order_commission')->where($where)->sum('fee');
        return $amount;
    }

    /**
     * 输出函数，如果启用debug,输出日志
     *
     * @param array $result   输出数据
     * @param int $errCode    错误code
     * @param string $proType 处理类型
     */
    public function outPut($result = null, $errCode, $proType = null, $msg = '') {
        $this->setData(is_null($result) ? '' : $result);

        $this->setError($errCode, $proType, $msg);
        $this->setResult();
        ob_clean();
        echo json_encode($this->result);
        if (C('SER_LOG')) {
            $this->logOut($result);
        }
        exit;
    }

    /**
     * 设置输出结果
     *
     */
    private function setResult() {
        $this->result = array('code' => $this->code, 'msg' => $this->msg, 'hasnext' => $this->hasNext, 'data' => $this->data);
    }

    /**
     * 设置数据
     *
     */
    private function setData($data) {
        //新增数据过滤转换   daipingshan 2015-04-14
        $data       = $this->_checkData($data);
        $this->data = $data;
    }

    /**
     * 设置错误信息
     *
     * @param int $error      错误code
     * @param string $proType 处理类型
     */
    private function setError($error = 0, $proType = null, $msg = '') {
        $this->code = $error;
        $this->msg  = $this->ef->getErrMsg($error, $proType) . $msg;
    }

    /**
     *
     */
    public function setG() {
        foreach ($_GET as $key => $value) {
            $this->G[$key] = trim(I('get.' . $key));
        }
        foreach ($_POST as $key => $value) {
            $this->G[$key] = trim(I('post.' . $key));
        }
    }

    /**
     * 验证是否为空
     *
     * @param $array
     */
    public function _checkblank($array) {
        $errmsg = '';
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (I('param.' . $value) === "") {
                    $errmsg .= $value . ',';
                }
            }
        } else {
            if (I('param.' . $array) === "") {
                $errmsg .= $array . ',';
            }
        }
        if (!empty($errmsg)) {
            $strc = substr($errmsg, 0, strlen($errmsg) - 1);
            $this->outPut('', -1, null, ":缺少参数" . $strc);
        }
    }

    /**
     * 生成token
     *
     * @param $uid
     */
    protected function _createToken($uid) {
        $time  = time();
        $token = md5($time . $uid);
        return $token;
    }

    /**
     * 获取用户uid
     *
     * @return mixed
     */
    protected function _checkToken() {
        $token = I('request.token', '', 'trim');
        if (empty($token)) {
            return array('uid' => 0, 'pid' => '', 'dwxk_adsense_id' => 0, 'store_type' => 0);
        }
        $user_data = S('tdk_' . $token);
        if (!$user_data) {
            $user_data = M('user')->field('id as uid,pid,dwxk_adsense_id,store_type')->where(array('token' => $token))->find();
            if ($user_data) {
                S('tdk_' . $token, $user_data);
            }
        }
        if (!$user_data) {
            $this->outPut(null, -4, null, '用户在其他地方登录，请重新登录!');
        } else {
            return $user_data;
        }
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

}