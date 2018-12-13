<?php

/**
 * Created by JetBrains PhpStorm.
 * User: zhoujz
 * Date: 15-3-6
 * Time: 下午3:01
 *
 */

namespace Common\Controller;

use Think\Controller;

use Common\Org\AliYunOss as OSS;
use Common\Org\Http;

/**
 * Class CommonBaseController
 *
 * @package Common\Controller
 */
class CommonBaseController extends Controller {

    /**
     * 错误信息
     *
     * @var string
     */
    protected $error = '';

    /**
     * 日志信息
     *
     * @var array|string
     */
    private $logs = '';

    /**
     * 每页分页个数
     *
     * @var int
     */
    protected $reqnum = 20;

    /**
     * 弹出层分页个数
     *
     * @var int
     */
    protected $popup_reqnum = 10;

    /**
     * @var int 用户id
     */
    protected $uid = 0;

    /**
     * 是否启用openSearch服务
     *
     * @var bool
     */
    protected $openSearchStatus = true;

    /**
     * @var int
     */
    protected $version = 1;

    /**
     * 构造函数
     */
    public function __construct() {
        parent:: __construct();
        $this->logs = array(
            'from'    => CONTROLLER_NAME . '/' . ACTION_NAME,
            'message' => ''
        );
        $this->ef   = new \Common\Org\errorInfo;
        $version    = I('request.V', 0, 'int');
        if ($version > 0) {
            $this->version = $version;
        }
    }

    /**
     * 处理数组结构，将id作为一维的 key 值
     *
     * @param array : $arr
     *              return array : $newArr
     */
    protected function _createNewArr($arr = array()) {
        $newArr = '';
        if ($arr && is_array($arr)) {
            foreach ($arr as $key => $val) {
                $newArr[$val['id']] = $val;
            }
        }

        return $newArr;
    }

    /**
     * 模块首页面显示
     */
    public function index() {
        //$actionModel = M(CONTROLLER_NAME);
        //$this->assign('showParentName',$actionModel->showParentName);
        //$this->assign('showName', $actionModel->showName);
        $this->display();
    }

    /**
     * 获取错误
     */
    protected function getErrorMsg($ret, $error = 0, $proType = null, $msg = '') {
        return $this->ef->getErrMsg($ret, $error, $proType) . $msg;
    }

    /**
     * 非ajax分页方法
     * totalRows  总数量
     * listRows   每页条数
     * map        查询条件
     *
     * @return string
     */
    protected function pages($totalRows, $listRows, $map = array(), $rollPage = 5) {
        $Page = new \Think\Page($totalRows, $listRows, '', MODULE_NAME . '/' . ACTION_NAME);
        if ($map && IS_POST) {
            foreach ($map as $key => $val) {
                $val = urlencode($val);
                $Page->parameter .= "$key=" . urlencode($val) . '&';
            }
        }
        if ($rollPage > 0) {
            $Page->rollPage = $rollPage;
        }
        $Page->setConfig('header', '条信息');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('last', '末页');
        $Page->setConfig(
            'theme', '<div style="float: left"><span>当前页' . $listRows . '条数据 总%TOTAL_ROW% %HEADER%</span></div><div style="float: right"><ul class=pagination><li>%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE%</li></ul></div>'
        );
        return $Page;
    }

    protected function pagess($totalRows, $listRows, $map = array(), $rollPage = 0) {
        $page = self::pages($totalRows, $listRows, $map = array(), $rollPage = 0);

        $page->lastSuffix = false;
        $page->rollPage   = 7;
        $page->setConfig(
            'theme', '<ul class=pagination><li><span style="float:left;">当前页' . $listRows
            . '条数据 总%TOTAL_ROW% %HEADER%</span> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</li></ul> %FORM_PAGE%'
        );
        return $page;
    }

    /**
     * @param $id 分页主键
     * @param $id 分页主键
     *
     * @return array  where条件数组
     */
    protected function setPage($id, $param = array()) {

        $this->pageflag = I('get.pageflag', 0, 'intval');
        $this->lastid   = I('get.lastid', 0);
        if (!$this->lastid) {
            $this->lastid = I('param.last_id', 0);//$keyword =I('param.keyword','','trim');
        }
        if (!$this->lastid) {
            $this->lastid = I('get.lastId', 0);
        }
        $end_id   = I('get.end_id', 0, 'intval');
        $whereArr = array();
        switch ($this->pageflag) {
            case 1:
                if ($end_id) {
                    $whereArr['_string'] = "(`$id`<'{$this->lastid}' or (`$id`='{$this->lastid}' and id<$end_id))";
                } else {
                    $whereArr[$id] = array('LT', $this->lastid);
                }
                $this->sort = "$id DESC";

                break;
            case 2:
                if ($end_id) {
                    $whereArr['_string'] = "(`$id`>'{$this->lastid}' or (`$id`='{$this->lastid}' and id<$end_id))";
                } else {
                    $whereArr[$id] = array('GT', $this->lastid);
                }
                $this->sort = "$id ASC";
                break;
            default:
                if ($this->lastid != 0) {
                    if ($end_id) {
                        $whereArr['_string'] = "(`$id`<'{$this->lastid}' or (`$id`='{$this->lastid}' and id<$end_id))";
                    } else {
                        $whereArr[$id] = array('LT', $this->lastid);
                    }
                }
                $this->sort = "$id DESC";
                break;
        }
        return $whereArr;
    }

    /**
     * 验证是否为空
     *
     * @param $array
     */
    protected function _checkblank($array) {
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
            $this->error($errmsg);
            exit;
        }
    }

    /**
     * 返回最新的错误信息
     *
     * @return string
     */
    protected function _getLastErr() {
        return $this->error;
    }

    /**
     * 处理数据到搜索服务
     *
     * @param $method   处理方法
     *                  add：添加单条数据 addALL：添加多条数据
     *                  update:更新数据   delete：删除数据
     * @param $data
     * @param $table
     *
     * @return bool
     */
    private function __opDataToSearch($method, $data, $table) {
        if (C('ASYNC_SERVICE')) {
            //异步方式处理数据
            //加入存储的数据
            $pushData = array(
                'table'  => $table,
                'data'   => $data,
                'method' => $method
            );
            return $this->__pushChannel('search', 'search', $pushData);
        } else {
            //非异步方式处理
            return $this->_opDataToSearch($method, $data, $table);
        }
    }

    /**
     * 处理数据到搜索服务
     *
     * @param $method 方法
     * @param $data   处理数据
     * @param $table  表名
     *
     * @return bool
     */
    protected function _opDataToSearch($method, $data, $table) {
        if (!is_array($data)) {
            return false;
        }

        $search = new \Common\Org\search();
        $result = $search->{$method}($data, $table);
        if (!$result) {
            $this->error = $search->error;

            return false;
        } else {
            return true;
        }
    }

    /**
     * 推送服务处理
     *
     * @param $type     要通知的异步处理服务
     * @param $key      redis通道名称
     * @param $pushData 推送数据
     *
     * @return bool
     */
    private function __pushChannel($type, $key, $pushData) {
        //生成redis的Key值
        $redis = new \Common\Org\phpRedis('pconnect');

        $publishData = array(
            'type' => $type, //服务类型
            'key'  => $key //redis数据存储key
        );

        $result = $redis::$redis->sAdd($key, json_encode($pushData));
        if ($result === 0) {
            //TODO 异常处理
            //return false;
        }

        //向redis频道发送异步处理消息
        $redis::$redis->publish($type, json_encode($publishData));
        $redis->close();

        return true;
    }

    /**
     * 搜索数据
     *
     * @param $queryString  搜索字符串
     * @param $startHit     起始点
     * @param $hits         每页数量
     *
     * @return bool
     */
    protected function _Search($queryString, $filter, $sort = array(), $startHit = 0, $hits = 20) {
        try {
            $search = new \Common\Org\search();
            $result = $search->search($queryString, $filter, $sort, $startHit, $hits);
            if (isset($result['status']) && strtolower(trim($result['status'])) == 'ok') {
                if (isset($result['result']['items']) && $result['result']['items']) {
                    return $result['result']['items'];
                }
                return array();
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * 搜索下拉提示
     *
     * @param type $query
     * @param type $limit
     * @return boolean
     */
    protected function _searchKeyDropTip($query = '', $limit = 10) {

        try {
            $search = new \Common\Org\search();
            $result = $search->searchKeyDropTip($query, $limit);
            if ($result) {
                return $result;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * 搜索数据
     *
     * @param $queryString  搜索字符串
     * @param $startHit     起始点
     * @param $hits         每页数量
     *
     * @return bool
     */
    protected function _SearchCount($queryString, $filter) {
        $search = new \Common\Org\search();
        $result = $search->search($queryString, $filter);
        if (isset($result['status']) && strtolower(trim($result['status'])) == 'ok') {
            if (isset($result['result']['total']) && $result['result']['total']) {
                return $result['result']['total'];
            }
        }
        return 0;
    }

    /**
     * 向搜索服务添加单条数据
     *
     * @param $data  添加的数据，一维数组形式
     * @param $table 表名
     *
     * @return bool
     */
    protected function _addToSearch($data, $table) {
        return $this->__opDataToSearch('add', $data, $table);
    }

    /**
     * 向搜索服务添加多条数据
     *
     * @param $data  添加的数据，二维数组形式
     * @param $table 表名
     *
     * @return bool
     */
    protected function _addAllToSearch($data, $table) {
        return $this->__opDataToSearch('addAll', $data, $table);
    }

    /**
     * 向搜索服务更新数据
     *
     * @param $data  更新的数据
     * @param $table 表名
     *
     * @return bool
     */
    protected function _updateToSearch($data, $table) {
        return $this->__opDataToSearch('update', $data, $table);
    }

    /**
     * 删除搜索服务数据
     *
     * @param $data  删除的数据（必须包含主键）
     * @param $table 表名
     *
     * @return bool
     */
    protected function _delToSearch($data, $table) {
        return $this->__opDataToSearch('delete', $data, $table);
    }

    /**
     * 日志写入
     *
     * @param $contents  内容，数组或字符串
     * @param $level     层级
     *                   EMERG   严重错误
     *                   ALERT   提示或警告
     *                   INFO   信息
     *                   DEBUG   调试
     * @param $source    日志来源 API
     *
     * @return bool
     */
    protected function _writeLog($contents, $level, $source) {
        if (!C('LOG')) {
            return true;
        }

        if (is_string($contents)) {
            $this->logs['message'] = $contents;
        } else {
            if (is_array($contents)) {
                $this->logs = array_merge($this->logs, $contents);
            }
        }

        if (C('ASYNC_SERVICE')) {
            //异步处理方式
            //加入存储的数据
            $pushData = array(
                'contents' => $this->logs,
                'level'    => $level,
                'source'   => $source
            );

            return $this->__pushChannel('LOG', 'LOG', $pushData);
        } else {
            return $this->_writeTOSLS($level, $source, $this->logs);
        }
    }

    /**
     * 数据库错误日志写入
     *
     * @param $var   数据库操作返回值
     * @param $param D实例化模型
     */
    protected function _writeDBErrorLog($var, $model, $source = 'manage') {
        if ($var === false) {
            if (method_exists($model, 'getErrorInfo')) {
                $error            = $model->getErrorInfo();
                $error['message'] = $error['info'];
                unset($error['info']);
                $this->_writeLog($error, 'EMERG', $source);
            } else if (method_exists($model, 'getDbError')) {
                $error['sql']     = $model->_sql();
                $error['message'] = $model->getDbError();
                $this->_writeLog($error, 'EMERG', $source);
            }
        }
    }

    /**
     * 上传项目图片
     */
    public function uploadImg() {
        $Object = new OSS();
        $data   = $Object->uploadObject();
        if ($data['code'] == 1) {
            $res = array(
                "state"   => "SUCCESS",
                "db_url"  => $data['url'],
                "url"     => getImgUrl($data['url']),
                "message" => $data[0]['savename'],
                "error"   => 0,
            );
        } else {
            $res = array(
                "state"   => 'ERROR',
                "error"   => 1,
                "url"     => '',
                'db_url'  => '',
                "message" => $data['info'],
            );
        }
        ob_clean();
        $this->ajaxReturn($res);
    }

    /**
     * 保存文件
     */
    public function saveFile($path, $filename) {
        $Object = new OSS();
        return $Object->saveObject($path, $filename);
    }

    /**
     * 日志写入
     *
     * @param $type     sql,debug,param,process 类型
     * @param $topic    API,boss,manmange,operate,web,wap,weichat,seller 标记
     * @param $contents 内容
     *
     * @return bool
     */
    protected function _writeTOSLS($type, $topic, $contents) {
        $project = 'youngtlogs';
        $log     = new \Common\Org\log();
        $result  = $log->putLogs($contents, $type, $topic, $project, $topic);
        if ($result !== false) {
            return true;
        } else {
            //日志写入错误
            $log->error = "日志写入失败！";
            return false;
        }
    }

    /**
     * 动态调用ots方法
     *
     * @param type $method 方法名
     * @param type $data   所传参数 array(param1,param2,...)
     * @return boolean
     */
    protected function _opDataToOts($method, $data) {

        if (!C('OPEN_OTS_SERVICE')) {
            return false;
        }

        $ots = new \Common\Org\OTS();
        if (method_exists($ots, $method)) {
            $res = @call_user_func_array(array($ots, $method), $data);
            if (is_object($res) || !isset($res['error'])) {
                // 如果返回结果正常，则处理异常数据
                $ots->saveTmpDataToOts();
            }
            return $res;
        }
        return false;
    }

    /**
     * 异步向数据库下单
     *
     * @param type $data
     */
    protected function _opDataToMysqlOrder($data, $async = false) {

        // 异步处理
        if (C('ASYNC_SERVICE') && $async) {
            return $this->__pushChannel('MySql', 'mysqlOrderAdd', array('params' => $data));
        }

        $uid           = isset($data['uid']) ? $data['uid'] : '';
        $id            = isset($data['id']) ? $data['id'] : '';
        $num           = isset($data['num']) ? $data['num'] : '';
        $mobile        = isset($data['mobile']) ? $data['mobile'] : '';
        $plat          = isset($data['plat']) ? $data['plat'] : '';
        $uniq_identify = isset($data['uniq_identify']) ? $data['uniq_identify'] : '';
        $team          = new \Common\Model\TeamModel();
        if (method_exists($team, 'addOrder')) {
            $res = $team->addOrder($uid, $id, $num, $mobile, $plat, 0, $uniq_identify);
            if (!$res || isset($res['error'])) {
                // 写错误日志
            }
        }
    }

    /**
     * ots 操作 向表中添加一行数据
     *
     * @param type $tableName       string 表名称
     * @param type $primaryKey      array 主键数据 array('uid'=>'2','gid'=>'123')
     * @param type $attributeColumn array 其他属性 array('name'=>'zhangsan','age'=>1)
     * @return bool
     */
    protected function _putRowDataToOTS($table, $primaryKey, $attributeColumn = array()) {

        if (C('ASYNC_SERVICE')) {
            //异步方式处理数据
            //加入存储的数据
            $pushData = array(
                'method' => 'putRow',
                'params' => array($table, $primaryKey, $attributeColumn),
            );
            return $this->__pushChannel('OTS', 'OTS', $pushData);
        } else {
            //非异步方式处理
            $res = $this->_opDataToOts('putRow', array($table, $primaryKey, $attributeColumn));
            if (!$res || isset($res['error'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * ots 更新一行数据
     *
     * @param type $table           string 表名
     * @param type $primaryKey      array 修改数据的主键条件 array('uid'=>'2','gid'=>'123')
     * @param type $attributeColumn array 需要修改的属性 array('name'=>'zhangsan','age'=>1)
     * @return type
     */
    protected function _updateRowDataToOTS($table, $primaryKey, $attributeColumn = array()) {

        if (C('ASYNC_SERVICE')) {
            //异步方式处理数据
            //加入存储的数据
            $pushData = array(
                'method' => 'updateRow',
                'params' => array($table, $primaryKey, $attributeColumn),
            );
            return $this->__pushChannel('OTS', 'OTS', $pushData);
        } else {
            //非异步方式处理
            $res = $this->_opDataToOts('updateRow', array($table, $primaryKey, $attributeColumn));
            if (!$res || isset($res['error'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * ots 根据主键删除数据
     *
     * @param type $table      string 表名
     * @param type $primaryKey array 主键条件 array('uid'=>'2','gid'=>'123')
     * @return type
     */
    protected function _deleteRowDataToOTS($table, $primaryKey) {

        if (C('ASYNC_SERVICE')) {
            //异步方式处理数据
            //加入存储的数据
            $pushData = array(
                'method' => 'deleteRow',
                'params' => array($table, $primaryKey),
            );
            return $this->__pushChannel('OTS', 'OTS', $pushData);
        } else {
            //非异步方式处理
            $res = $this->_opDataToOts('deleteRow', array($table, $primaryKey));
            if (!$res || isset($res['error'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * ots 获取单行数据
     *
     * @param type $tableName  string 表名
     * @param type $primaryKey array 主键条件 array('uid'=>'2','gid'=>'123')
     * @param type $select     array 显示返回的字段  array('name','age') 不穿参数全部返回
     * @return type
     */
    protected function _getRowDataToOTS($table, $primaryKey, $select = array()) {

        $res = $this->_opDataToOts('getRow', array($table, $primaryKey, $select));
        if (!$res || isset($res['error'])) {
            return false;
        }
        return $res;
    }

    /**
     * ots 读取指定主键范围内的多行数据。
     *
     * @param type $table           string 表名
     * @param type $select          array 显示的字段 array('name','age')
     * @param type $startPrimaryKey array 主键范围最小值 array('uid'=>1,'gid'=>1)
     * @param type $endPrimaryKey   array 主键范围最大值 array('uid'=>10,'gid'=>10)
     * @param type $limit           string 显示的条数，
     * @return type ots\protocolbuffer\GetRangeResponse 返回查询结果
     */
    protected function _getRangeDataToOTS($table, $startPrimaryKey, $endPrimaryKey, $select = array(), $limit = '15') {

        $res = $this->_opDataToOts('getRange', array($table, $select, $startPrimaryKey, $endPrimaryKey, $limit));
        if (!$res || isset($res['error'])) {
            return false;
        }
        return $res;
    }

    /**
     * 推送消息
     *
     * @param type $method
     * @param type $data
     * @return boolean
     */
    protected function _opDataPushMessage($method = '', $data = array()) {
        $pushAppMessage = new \Common\Org\PushAppMessage();
        if (method_exists($pushAppMessage, $method)) {
            $res = @call_user_func_array(array($pushAppMessage, $method), array($data));
            return $res;
        }
        return false;
    }

    /**
     * 推送给指定账号
     *
     * @param type $data
     *  array(
     *  title=>'标题', 不可空
     *  content=>'内容', 不可空
     *  account=>array('uid'), 不可空
     *  message_type=> 0, 可空 0：代表透传消息，1：通知 不传默认为0
     *  custom=>array(), 其他参数 可空
     *  custom:
     *  团单推送
     *  {
     *  type: "team_detail",
     *  data:{
     *  id: "团单id"
     *  }
     *  }
     *  购买成功推送
     *  {
     *  type: "pay_success"
     *  data:{
     *  }
     *  }
     *  团券消费成功
     *  {
     *  type: "coupon_consume_success"
     *  data:{
     *  }
     *  }
     *  plat=>'平台', android ios 默认不传为android
     *  send_time=>'发送的时间', 格式Y-m-d H:i:s  默认为当前时间
     *  )
     * @return  code==0 成功，其他失败； data array 记录推送结果；msg 错误信息
     */
    protected function _pushMessageToAccess($data, $async = true) {
        if (C('ASYNC_SERVICE') && $async) {
            //异步方式处理数据
            //加入存储的数据
            $pushData = array(
                'method' => 'pushMessageToAccess',
                'params' => $data,
            );
            return $this->__pushChannel('PUSH_MESSAGE', 'PUSH_MESSAGE', $pushData);
        } else {
            //非异步方式处理
            return $this->_opDataPushMessage('pushMessageToAccess', $data);
        }
    }

    /**
     * 推送给指定标签
     *
     * @param type $data
     *  array(
     *  title=>'标题', 不可空
     *  content=>'内容', 不可空
     *  tags=>array('city_id'), 不可空
     *  message_type=> 0, 可空 0：代表透传消息，1：通知 不传默认为0
     *  custom=>array(), 其他参数 可空
     *  custom:
     *  团单推送
     *  {
     *  type: "team_detail",
     *  data:{
     *  id: "团单id"
     *  }
     *  }
     *  购买成功推送
     *  {
     *  type: "pay_success"
     *  data:{
     *  }
     *  }
     *  团券消费成功
     *  {
     *  type: "coupon_ consume_success"
     *  data:{
     *  }
     *  }
     *  plat=>'平台', android ios 默认不传为android
     *  send_time=>'发送的时间', 格式Y-m-d H:i:s  默认为当前时间
     *  )
     * @return  code==0 成功，其他失败； data array 记录推送结果；msg 错误信息
     */
    protected function _pushMessageToTags($data, $async = true) {
        if (C('ASYNC_SERVICE') && $async) {
            //异步方式处理数据
            //加入存储的数据
            $pushData = array(
                'method' => 'pushMessageToTags',
                'params' => $data,
            );
            return $this->__pushChannel('PUSH_MESSAGE', 'PUSH_MESSAGE', $pushData);
        } else {
            //非异步方式处理
            return $this->_opDataPushMessage('pushMessageToTags', $data);
        }
    }

    /**
     * 推送给所有设备
     *
     * @param type $data
     *  array(
     *  title=>'标题', 不可空
     *  content=>'内容', 不可空
     *  message_type=> 0, 可空 0：代表透传消息，1：通知 不传默认为0
     *  custom=>array(), 其他参数 可空
     *  custom:
     *  团单推送
     *  {
     *  type: "team_detail",
     *  data:{
     *  id: "团单id"
     *  }
     *  }
     *  购买成功推送
     *  {
     *  type: "pay_success"
     *  data:{
     *  }
     *  }
     *  团券消费成功
     *  {
     *  type: "coupon_ consume_success"
     *  data:{
     *  }
     *  }
     *  升级信息
     *  {
     *  type: "app_upgrade_info",
     *  data:{
     *  upgrade:{
     *  version:'1.0.1',
     *  is_force:'Y',
     *  description:'升级描述',
     *  download_url:'http://ytfile.oss-cn-hangzhou.aliyuncs.com/youngt.apk',
     *  }
     *  }
     *  }
     *  plat=>'平台', android ios 默认不传为android
     *  send_time=>'发送的时间', 格式Y-m-d H:i:s  默认为当前时间
     *  )
     * @return  code==0 成功，其他失败； data array 记录推送结果；msg 错误信息
     */
    protected function _pushMessageToAll($data, $async = true) {
        if (C('ASYNC_SERVICE') && $async) {
            //异步方式处理数据
            //加入存储的数据
            $pushData = array(
                'method' => 'pushMessageToAll',
                'params' => $data,
            );
            return $this->__pushChannel('PUSH_MESSAGE', 'PUSH_MESSAGE', $pushData);
        } else {
            //非异步方式处理
            return $this->_opDataPushMessage('pushMessageToAll', $data);
        }
    }

    /**
     * 检测字段值是否为NULL，如果为NULL将其转换为''
     *
     * @param  string | array ：需要被检测的数据
     * @return string | array ：处理过后的数据
     */
    protected function _checkData($data) {
        if (is_array($data)) {
            foreach ($data as &$val) {
                if (is_array($val)) {
                    $val = $this->_checkData($val);
                } else {
                    $val = isset($val) ? $val : '';
                }
            }
        } else {
            $data = isset($data) ? $data : '';
        }
        return $data;
    }

    /**
     * @param        $arr
     * @param        $keys
     * @param string $type
     * @return array
     */
    protected function _arraySort($arr, $keys, $type = 'asc') {
        //数组重新排序
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[] = $arr[$k];
        }
        return $new_array;
    }

    /**
     * @param $param
     * @param $table
     * @return string
     */
    protected function _addPrefix($param, $table) {
        $new_param = '';
        if ($param) {
            if (is_array($param)) {
                foreach ($param as $key => $val) {
                    $new_param[$table . '.' . $key] = $val;
                }
            } else {
                if (strpos($param, ',')) {
                    foreach (explode(',', $param) as $key => $val) {
                        $new_param .= $table . '.' . $val . ',';
                    }
                    $new_param = substr($new_param, 0, -1);
                } else {
                    $new_param = $table . '.' . $new_param;
                }
            }
        }
        return $new_param;
    }

    /**
     * @param $str
     * @param string $source
     * @param bool $is_save 是否保存历史记录到数据库
     * @return array
     */
    protected function _getTagsStr($str, $source = 'dataoke', $user_id = 0, $is_save = true) {
        if (empty($str)) {
            return $str;
        }

        if (mb_strlen($str) > 5) { //大于5个字才做分词
            $Obj  = new \Common\Org\Participle();
            $path = APP_PATH . "Common/Org/Participle";
            $Obj->set_dict($path . '/dict.utf8.xdb');
            $Obj->set_rule($path . '/rules.utf8.ini');
            $Obj->set_ignore(true);
            $Obj->send_text($str);
            $words = $Obj->get_tops(5);
            $Obj->close();
            $tags  = array();
            foreach ($words as $val) {
                $tags[] = $val['word'];
            }
        } else {
            $tags  = array($str);
        }

        //部分测试白名单人员的搜索记录不做保存
        $user_id_config = explode(',', C('SearchUserIds'));
        if (in_array($user_id, $user_id_config)) {
            $is_save = false;
        }

        if (count($tags) > 0 && true === $is_save) {
            $this->_saveDBTags($str, $tags, $source, $user_id);
            $this->_saveCacheTags($tags);
        }

        return implode('', $tags);
    }

    /**
     * @param $keyword
     * @param $tags
     * @param $source
     * @param $user_id
     */
    protected function _saveDBTags($keyword, $tags, $source, $user_id) {
        $data = array(
            'source'             => $source,
            'user_id'            => $user_id,
            'search_keyword'     => $keyword,
            'search_keyword_md5' => md5($keyword),
            'segment_words'      => implode(',', $tags),
            'add_time'           => time(),
        );
        M('search_list')->add($data);
    }

    /**
     * 记录搜索关键词
     *
     * @param $arr
     */
    protected function _saveCacheTags($arr) {
        $redis = \Common\Org\Redis::getInstance();
        foreach ($arr as $v) {
            $redis->hIncrBy('tags', $v, 1);
        }
    }

    /**
     * @return mixed
     */
    protected function _getCacheTags() {
        $redis = \Common\Org\Redis::getInstance();
        return $redis->hGetAll('tags');
    }

    /**
     * @return mixed
     */
    protected function _delCacheTags() {
        $redis = \Common\Org\Redis::getInstance();
        $redis->del('tags');
    }


}
