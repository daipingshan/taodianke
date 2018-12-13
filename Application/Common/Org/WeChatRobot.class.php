<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2017/11/15
 * Time: 17:20
 */

namespace Common\Org;


class WeChatRobot {

    protected $pid              = 0;
    protected $pushObj          = null;
    protected $redirect_uri     = 'https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxnewloginpage';
    protected $base_uri         = 'https://wx.qq.com/cgi-bin/mmwebwx-bin';
    protected $token            = '';
    protected $app_id           = 'wx782c26e4c19acffb';
    protected $lang             = 'zh_CN';
    protected $cookie           = '';
    protected $deviceId         = '';
    protected $user_agent       = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36';
    protected $skey             = '';
    protected $sid              = '';
    protected $uin              = '';
    protected $pass_ticket      = '';
    protected $BaseRequest      = array();
    protected $sync_key         = '';
    protected $SyncKey          = array();
    protected $User             = array();
    protected $MemberCount      = 0;
    protected $MemberList       = array();
    protected $ContactList      = array();  # 好友
    protected $GroupList        = array();  # 群
    protected $PublicUsersList  = array();  # 公众号／服务号
    protected $SpecialUsersList = array();  # 特殊账号
    protected $autoReplyMode    = false;
    protected $syncHost         = '';
    protected $save_path        = '';
    protected $media_count      = -1;
    protected $SpecialUsers     = array( 'newsapp', 'fmessage', 'filehelper', 'weibo', 'qqmail',
                                         'fmessage', 'tmessage', 'qmessage', 'qqsync', 'floatbottle', 'lbsapp', 'shakeapp',
                                         'medianote', 'qqfriend', 'readerapp', 'blogapp', 'facebookapp', 'masssendapp',
                                         'meishiapp', 'feedsapp', 'voip', 'blogappweixin', 'weixin', 'brandsessionholder',
                                         'weixinreminder', 'wxid_novlwrv3lqwv11', 'gh_22b87fa7cb3c', 'officialaccounts',
                                         'notification_messages', 'wxid_novlwrv3lqwv11', 'gh_22b87fa7cb3c', 'wxitil',
                                         'userexperience_alarm', 'notification_messages'
    );

    public function __construct($pid = 0) {
        $this->pid       = $pid;
        $this->token     = I('request.token', '', 'trim');
        $this->deviceId  = 'e'.substr(md5(uniqid()), 2, 15);
        $this->save_path = '/data/wechat_robot';
        $this->cookie    = $this->save_path.'/cookie/cookie'.$this->token;
        $this->pushObj   = new JPush();
    }


    /**
     * 获取uuid
     *
     * @return bool
     */
    public function getUUid() {
        $res = $this->_checkLogin();
        if ($res['status'] == 0) {
            $this->_sendPush('已登录，直接发送消息即可！');
            return array( 'status' => 1, 'info' => '已登录，直接发送消息即可！' );
        }
        $url    = 'https://login.wx.qq.com/jslogin';
        $params = array(
            'appid' => $this->app_id,
            'fun'   => 'new',
            'lang'  => $this->lang,
            '_'     => time(),
        );
        $res    = $this->_get($url, $params);
        $reg    = '/window.QRLogin.code = (\d+); window.QRLogin.uuid = "(\S+?)"/';
        if (preg_match($reg, $res, $pm)) {
            $code       = $pm[1];
            $cache_data = array(
                'uuid' => $pm[2]
            );
            S('weChat_'.$this->token, $cache_data, 300);
            if ($code == '200') {
                return array( 'status' => 0, 'info' => 'ok' );
            }
        }
        return array( 'status' => -1, 'info' => 'error' );
    }

    /**
     * @param $url
     * @param array $params
     * @param bool $api
     * @return bool|mixed
     */
    private function _get($url, $params = array(), $api = false) {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        $header = array(
            'User-Agent: '.$this->user_agent,
            'Referer: https://wx.qq.com/'
        );
        if ($api == 'webwxgetvoice')
            $header[] = 'Range: bytes=0-';
        if ($api == 'webwxgetvideo')
            $header[] = 'Range: bytes=0-';
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        if (!empty($params)) {
            if (strpos($url, '?') !== false) {
                $url .= "&".http_build_query($params);
            } else {
                $url .= "?".http_build_query($params);
            }
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 36);
        curl_setopt($oCurl, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($oCurl, CURLOPT_COOKIEJAR, $this->cookie);
        $sContent = curl_exec($oCurl);
        $aStatus  = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * POST 请求
     *
     * @param $url
     * @param $param
     * @param bool $json_fmt
     * @param bool $post_file
     * @return mixed
     */
    private function _post($url, $param, $json_fmt = true, $post_file = false) {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        if (PHP_VERSION_ID >= 50500 && class_exists('\CURLFile')) {
            $is_curlFile = true;
        } else {
            $is_curlFile = false;
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, false);
            }
        }
        $header = array(
            'User-Agent: '.$this->user_agent
        );
        if ($json_fmt) {
            $param    = self::json_encode($param);
            $header[] = 'Content-Type: application/json; charset=UTF-8';
        }
        if (is_string($param)) {
            $strPOST = $param;
        } elseif ($post_file) {
            if ($is_curlFile) {
                foreach ($param as $key => $val) {
                    if (substr($val, 0, 1) == '@') {
                        $param[$key] = new \CURLFile(realpath(substr($val, 1)));
                    }
                }
            }
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key."=".urlencode($val);
            }
            $strPOST = implode("&", $aPOST);
        }

        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        curl_setopt($oCurl, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($oCurl, CURLOPT_COOKIEJAR, $this->cookie);
        $sContent = curl_exec($oCurl);
        $aStatus  = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            if ($json_fmt)
                return json_decode($sContent, true);
            return $sContent;
        } else {
            return $sContent;
        }
    }

    /**
     * @param $json
     * @return string
     */
    public static function json_encode($json) {
        return json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * 获取二维码地址
     *
     * @return array
     */
    public function getQRUrl() {
        $res = $this->getUUid();
        if ($res['status'] == 0) {
            $uuid = $this->_getCache('uuid');
            $url  = 'https://login.weixin.qq.com/l/'.$uuid;
            return array( 'status' => 0, 'url' => $url );
        } else if ($res['status'] == 1) {
            return array( 'status' => -5, 'info' => $res['info'] );
        } else {
            return array( 'status' => -1, 'info' => '获取二维码失败' );
        }
    }


    /**
     * $login_satus
     * @return array
     */
    public function waitForLogin() {
        $uuid = $this->_getCache('uuid');
        if ($uuid) {
            $url  = sprintf('https://login.wx.qq.com/cgi-bin/mmwebwx-bin/login?tip=%s&uuid=%s&_=%s', 1, $uuid, time());
            $data = $this->_get($url);
            preg_match('/window.code=(\d+);/', $data, $pm);
            $code = $pm[1];
            if ($code == '201') {
                return array( 'status' => -1, 'info' => '已扫描成功，请确认登录' );
            } elseif ($code == '200') {
                preg_match('/window.redirect_uri="(\S+?)";/', $data, $pm);
                $r_uri              = $pm[1].'&fun=new';
                $this->redirect_uri = $r_uri;
                $this->base_uri     = substr($r_uri, 0, strrpos($r_uri, '/'));
                $this->_setCache('base_uri', $this->base_uri);
                return array( 'status' => 0 );
            } elseif ($code == '408') {
                return array( 'status' => -1, 'info' => '登录超时' );
            } else {
                return array( 'status' => -1, 'info' => '登录异常' );
            }
        } else {
            return array( 'status' => -1, 'info' => '登录失效' );
        }
    }

    /**
     * 微信登录
     *
     * @return bool
     */
    public function login() {
        $data  = $this->_get($this->redirect_uri."&version=v2&lang=zh_CN");
        $array = (array)simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!isset($array['skey']) || !isset($array['wxsid']) || !isset($array['wxuin']) || !isset($array['pass_ticket'])) {
            return array( 'status' => -1, 'info' => $array['message'] );
        }
        $this->skey        = $array['skey'];
        $this->sid         = $array['wxsid'];
        $this->uin         = $array['wxuin'];
        $this->pass_ticket = $array['pass_ticket'];
        $this->BaseRequest = array(
            'Uin'      => intval($this->uin),
            'Sid'      => $this->sid,
            'Skey'     => $this->skey,
            'DeviceID' => $this->deviceId
        );
        $this->_setCache('key', $this->BaseRequest);
        $this->_setCache('pass_ticket', $this->pass_ticket);
        return array( 'status' => 0, 'info' => 'ok' );
    }

    /**
     * @param bool $first
     * @return bool
     */
    public function webWeChatInit($first = true) {
        $url    = sprintf($this->base_uri.'/webwxinit?pass_ticket=%s&skey=%s&r=%s', $this->pass_ticket, $this->skey, time());
        $params = array(
            'BaseRequest' => $this->BaseRequest
        );
        $dic    = $this->_post($url, $params);

        $this->SyncKey = $dic['SyncKey'];
        $this->User    = $dic['User'];
        $this->_setCache('user', $dic['User']);
        $tempArr = array();
        if (is_array($this->SyncKey['List'])) {
            foreach ($this->SyncKey['List'] as $val) {
                $tempArr[] = "{$val['Key']}_{$val['Val']}";
            }
        } else if ($first) {
            return $this->webWeChatInit(false); //不知道为什么要执行2次，暂时注释 by dongguangqi
        }
        $this->sync_key   = implode('|', $tempArr);
        $this->MemberList = $this->ContactList;
        $this->_setCache('sync_key', $this->sync_key);
        return $dic['BaseResponse']['Ret'] == 0;
    }

    /**
     * 开启通知
     */
    public function openStatus() {
        $url    = sprintf($this->base_uri.'/webwxstatusnotify?lang=zh_CN&pass_ticket=%s', $this->pass_ticket);
        $params = array(
            'BaseRequest'  => $this->BaseRequest,
            "Code"         => 3,
            "FromUserName" => $this->User['UserName'],
            "ToUserName"   => $this->User['UserName'],
            "ClientMsgId"  => time()
        );
        $dic    = $this->_post($url, $params);
        return $dic['BaseResponse']['Ret'] == 0;
    }


    /**
     * @return bool
     */
    public function getUser() {
        $this->_getUserList();
        if (!is_array($this->MemberList)) {
            return false;
        }
        $this->MemberList = $this->_setMember($this->MemberList);
        $this->GroupList  = $this->_getGroupList();
        $this->_setCache('memberList', $this->MemberList);
        $this->_setCache('memberCount', $this->MemberCount);
        $this->_setCache('groupList', $this->GroupList);
        return true;
    }


    /**
     * 获取群列表
     *
     * @return array
     */
    protected function _getGroupList() {
        $data = array();
        foreach ($this->MemberList as $val) {
            if (strpos($val['UserName'], '@@') !== false) {  # 群聊
                $val['NickName'] = strip_tags($val['NickName']);
                $data[]          = $val;
            }
        }
        return $data;
    }

    /**
     * 获取用户列表
     *
     * @param int $seq
     */
    protected function _getUserList($seq = 0) {
        $url               = sprintf($this->base_uri.'/webwxgetcontact?pass_ticket=%s&skey=%s&r=%s&seq=%s', $this->pass_ticket, $this->skey, time(), $seq);
        $dic               = $this->_post($url, array());
        $this->MemberList  = array_merge($this->MemberList, $dic['MemberList']);
        $this->MemberCount = $this->MemberCount + $dic['MemberCount'];
        if ($dic['Seq'] > 0) {
            $this->_getUserList($dic['Seq']);
        }
    }

    /**
     * 重新设置数据
     *
     * @param $data
     * @return array
     */
    public function _setMember($data) {
        $return_data = array();
        foreach ($data as $val) {
            $return_data[$val['UserName']] = array(
                'UserName'   => $val['UserName'],
                'NickName'   => $val['NickName'],
                'RemarkName' => $val['RemarkName'],
            );
        }
        return array_values($return_data);
    }

    /**
     * 发送消息
     *
     * @param $name
     * @param $word
     * @param bool $is_username
     * @return array
     */
    public function sendMsg($name, $word, $is_username = false) {
        if ($is_username === true) {
            $id = $name;
        } else {
            $id = $this->getUSerID($name);
        }
        if ($id) {
            if ($this->weChatSendMsg($word, $id)) {
                $this->_sendPush("【文本】->发送成功");
                return array( 'status' => 0, 'info' => '消息发送成功' );
            } else {
                $this->_sendPush("【文本】-> 发送失败");
                return array( 'status' => -1, 'info' => '消息发送失败' );
            }
        } else {
            $this->_sendPush("【文本】-> 群名称不存在或未保存通讯录");
            return array( 'status' => -1, 'info' => '群名称不存在或未保存通讯录' );
        }
    }

    /**
     * 发送图片
     *
     * @param $name
     * @param $file_name
     * @param bool $is_username
     * @return array
     */
    public function sendImg($name, $file_name, $is_username = false) {
        if ($is_username === true) {
            $user_id = $name;
        } else {
            $user_id = $this->getUSerID($name);
        }
        if ($user_id) {
            $file_data = explode('/', $file_name);
            $filename  = $file_data[count($file_data) - 1];
            $up_name   = substr($filename, 2, 2);
            $dir       = $this->save_path.'/img/'.$up_name;
            if (!is_dir($dir)) {
                @mkdir($dir);
                @chmod($dir, 0777);
            }
            $file = $dir.'/'.$filename;
            if (!is_file($file)) {
                $img_content = file_get_contents($file_name);
                @file_put_contents($file, $img_content);
            }
            $response = $this->_uploadImg($user_id, $file);
            if ($response['status'] == -1) {
                return $response;
            }
            $media_id = $response['result']['MediaId'];
            $res      = $this->weChatSendImg($user_id, $media_id);
            if ($res) {
                $this->_sendPush("【图片】-> 发送成功");
                return array( 'status' => 0, 'info' => '发送成功' );
            } else {
                $this->_sendPush("【图片】-> 发送失败");
                return array( 'status' => -1, 'info' => '发送失败！' );
            }
        } else {
            $this->_sendPush("【图片】-> 群名称不存在或未保存通讯录");
            return array( 'status' => -1, 'info' => '群名称不存在或未保存通讯录' );
        }

    }

    /**
     * @param $word
     * @param $to
     * @return bool
     */
    public function weChatSendMsg($word, $to) {
        $url         = sprintf($this->base_uri.'/webwxsendmsg?pass_ticket=%s', $this->pass_ticket);
        $clientMsgId = (time() * 1000).substr(uniqid(), 0, 5);
        $data        = array(
            'BaseRequest' => $this->BaseRequest,
            'Msg'         => array(
                "Type"         => 1,
                "Content"      => $this->_transCoding($word),
                "FromUserName" => $this->User['UserName'],
                "ToUserName"   => $to,
                "LocalID"      => $clientMsgId,
                "ClientMsgId"  => $clientMsgId
            )
        );
        $dic         = $this->_post($url, $data);
        return $dic['BaseResponse']['Ret'] == 0;
    }

    public function weChatSendImg($user_id, $media_id) {
        $url         = sprintf($this->base_uri.'/webwxsendmsgimg?fun=async&f=json&pass_ticket=%s', $this->pass_ticket);
        $clientMsgId = (time() * 1000).substr(uniqid(), 0, 5);
        $data        = array(
            "BaseRequest" => $this->BaseRequest,
            "Msg"         => array(
                "Type"         => 3,
                "MediaId"      => $media_id,
                "FromUserName" => $this->User['UserName'],
                "ToUserName"   => $user_id,
                "LocalID"      => $clientMsgId,
                "ClientMsgId"  => $clientMsgId
            )
        );
        $dic         = $this->_post($url, $data);
        return $dic['BaseResponse']['Ret'] == 0;
    }

    protected function _uploadImg($ToUserName, $image_name) {
        $url = str_ireplace('https://', 'https://file.', $this->base_uri).'/webwxuploadmedia?f=json';
        # 计数器
        $this->media_count = $this->media_count + 1;
        # 文件名
        $file_name = $image_name;
        # MIME格式
        # mime_type = application/pdf, image/jpeg, image/png, etc.
        //$mime_type = mime_content_type($image_name);
        $fi        = new \finfo(FILEINFO_MIME_TYPE);
        $mime_type = $fi->file($image_name);
        # 微信识别的文档格式，微信服务器应该只支持两种类型的格式。pic和doc
        # pic格式，直接显示。doc格式则显示为文件。
        $media_type = explode('/', $mime_type)[0] == 'image' ? 'pic' : 'doc';
        $fTime      = filemtime($image_name);
        # 上一次修改日期
        $lastModifieDate = gmdate('D M d Y H:i:s TO', $fTime).' (CST)';//'Thu Mar 17 2016 00:55:10 GMT+0800 (CST)';
        # 文件大小
        $file_size = filesize($file_name);
        # PassTicket
        $pass_ticket = $this->pass_ticket;
        # clientMediaId
        $client_media_id = (time() * 1000).mt_rand(10000, 99999);
        # webwx_data_ticket
        $web_wx_data_ticket = '';
        $fp                 = fopen($this->cookie, 'r');
        while ($line = fgets($fp)) {
            if (strpos($line, 'webwx_data_ticket') !== false) {
                $arr = explode("\t", trim($line));
                //var_dump($arr);
                $web_wx_data_ticket = $arr[6];
                break;
            }
        }
        fclose($fp);

        if ($web_wx_data_ticket == '') {
            return array( 'status' => -1, 'info' => 'None Fuck Cookie' );
        }
        $upload_media_request = self::json_encode(array(
            "BaseRequest"   => $this->BaseRequest,
            "ClientMediaId" => $client_media_id,
            "TotalLen"      => $file_size,
            "StartPos"      => 0,
            "DataLen"       => $file_size,
            "MediaType"     => 4,
            "UploadType"    => 2,
            "FromUserName"  => $this->User["UserName"],
            "ToUserName"    => $ToUserName,
            "FileMd5"       => md5_file($image_name)
        ));

        $multipart_encoder = array(
            'id'                 => 'WU_FILE_'.$this->media_count,
            'name'               => $file_name,
            'type'               => $mime_type,
            'lastModifieDate'    => $lastModifieDate,
            'size'               => $file_size,
            'mediatype'          => $media_type,
            'uploadmediarequest' => $upload_media_request,
            'webwx_data_ticket'  => $web_wx_data_ticket,
            'pass_ticket'        => $pass_ticket,
            'filename'           => '@'.$file_name
        );
        $response_json     = json_decode($this->_post($url, $multipart_encoder, false, true), true);
        if ($response_json['BaseResponse']['Ret'] == 0) {
            return array( 'status' => 0, 'result' => $response_json );
        }
        return array( 'status' => -1, 'info' => '保存失败' );
    }

    /**
     * @param $data
     * @return null
     */
    public function _transCoding($data) {
        if (!$data) {
            return $data;
        }
        $result = null;
        if (gettype($data) == 'unicode') {
            $result = $data;
        } elseif (gettype($data) == 'string') {
            $result = $data;
        }
        return $result;
    }

    /**
     * 获取用户id
     *
     * @param $name
     * @return null
     */
    public function getUSerID($name) {
        foreach ($this->MemberList as $member) {
            if ($name == $member['RemarkName'] || $name == $member['NickName']) {
                return $member['UserName'];
            }
        }
        return null;
    }

    /**
     * 设置缓存
     *
     * @param $key
     * @param $data
     */
    protected function _setCache($key, $data) {
        $cache_data       = S('weChat_'.$this->token);
        $cache_data[$key] = $data;
        S('weChat_'.$this->token, $cache_data);
    }

    /**
     * 得到缓存
     *
     * @param $key
     * @return bool
     */
    protected function _getCache($key) {
        $cache_data = S('weChat_'.$this->token);
        if (isset($cache_data[$key]) && $cache_data[$key]) {
            return $cache_data[$key];
        } else {
            return false;
        }
    }

    /**
     * 清理缓存
     */
    protected function _clearCache() {
        S('weChat_'.$this->token, null);
    }

    /**
     * @param $name
     * @param $content
     * @param string $type
     * @return array
     */
    public function send($name, $content, $type = "text") {
        $res = $this->_checkLogin('send');
        if ($res['status'] == -1) {
            return array( 'status' => -1, 'info' => $res['info'] );
        }
        if ($type == 'text') {
            return $this->sendMsg($name, $content);
        } else {
            return $this->sendImg($name, $content);
        }
    }

    /**
     * @param $name
     * @param $content
     * @param string $type
     * @return array
     */
    public function sendMass($name, $content, $type = "text") {
        $res = $this->_checkLogin('send');
        if ($res['status'] == -1) {
            return array( 'status' => -1, 'info' => $res['info'] );
        }
        if ($type == 'text') {
            return $this->sendMsg($name, $content, true);
        } else {
            return $this->sendImg($name, $content, true);
        }
    }

    /**
     * 获取登录
     *
     * @return array
     */
    public function isLogin() {
        $wait_status = $this->waitForLogin();
        if ($wait_status['status'] == -1) {
            $this->_sendPush($wait_status['info']);
            return array( 'status' => -1, 'info' => $wait_status['info'] );
        }
        $login_status = $this->login();
        if ($login_status['status'] == -1) {
            $this->_sendPush($login_status['info']);
            return array( 'status' => -1, 'info' => $login_status['info'] );
        } else {
            $this->_sendPush('登录成功，微信初始化中 ...', 'window_text');
        }
        if (!$this->webWeChatInit()) {
            $this->_sendPush('微信初始化失败 ...');
            return array( 'status' => -1, 'info' => '微信初始化失败！' );
        } else {
            $this->_sendPush('微信初始化成功，正在获取联系人', 'window_text');
        }
        if (!$this->openStatus()) {
            $this->_sendPush('开启状态通知失败 ...');
        }
        if (!$this->getUser()) {
            $this->_sendPush('获取联系人失败 ...');
            return array( 'status' => -1, 'info' => '获取联系人失败！' );
        } else {
            $this->_sendPush(json_encode($this->GroupList), 'wechat_group');
        }
        return array( 'status' => 0, 'info' => 'ok' );
    }

    /**
     * 检测登录
     *
     * @param null $type
     * @return array
     */
    protected function _checkLogin($type = null) {
        $data = S('weChat_'.$this->token);
        if ($data && $data['key'] && $data['pass_ticket'] && $data['sync_key'] && $data['memberList'] && $data['user'] && $data['base_uri']) {
            $this->pass_ticket = $data['pass_ticket'];
            $this->MemberList  = $data['memberList'];
            $this->User        = $data['user'];
            $this->sync_key    = $data['sync_key'];
            $this->BaseRequest = $data['key'];
            $key               = $data['key'];
            $this->skey        = $key['Skey'];
            $this->sid         = $key['Sid'];
            $this->uin         = $key['Uin'];
            $this->deviceId    = $key['DeviceID'];
            $this->base_uri    = $data['base_uri'];
            $this->syncHost    = $data['base_uri'];
            $ret_code          = $this->_syncCheck();
            if ($ret_code == '0') {
                return array( 'status' => 0, 'info' => 'ok' );
            }
            $this->_clearCache();
            if ($type == 'send') {
                $this->_sendPush('登录状态已失效，请重新登录！');
            }
            return array( 'status' => -1, 'info' => '登录状态已失效！' );
        } else {
            return array( 'status' => -1, 'info' => '请登录后发送消息！' );
        }
    }

    /**
     * @return int
     */
    protected function _syncCheck() {
        $params = array(
            'r'        => time(),
            'sid'      => $this->sid,
            'uin'      => $this->uin,
            'skey'     => $this->skey,
            'deviceid' => $this->deviceId,
            'synckey'  => $this->sync_key,
            '_'        => time(),
        );
        $url    = $this->syncHost.'/synccheck?'.http_build_query($params);
        $data   = $this->_get($url);
        if (preg_match('/window.synccheck={retcode:"(\d+)",selector:"(\d+)"}/', $data, $pm)) {
            $ret_code = $pm[1];
        } else {
            $ret_code = -1;
        }
        return $ret_code;
    }

    /**
     * 推送消息
     *
     * @param $content
     * @param string $type
     */
    protected function _sendPush($content, $type = "text") {
        $data = array( 'type' => $type );
        $this->pushObj->push($content, $data, strval($this->pid), 'all', 'msg', 300);
    }
}
