<?php

/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2015/4/22
 * Time: 11:19
 */

namespace Common\Org;

/**
 * 发送短息接口
 * Class sendSms
 * @package Common\Org
 */
class Taobaoke {

    /**
     * @var string 亿美序列号
     */
    private $YmUsername = '6SDK-EMY-6688-KGXTS';

    /**
     * @var string  亿美短信接口密码
     */
    private $YmPwd = '741044';

    /**
     * @var string  亿美短信接口url
     */
    private $YmUrl = 'http://sdk4report.eucp.b2m.cn:8080/sdkproxy/sendsms.action';

    /**
     * @var string  亿美获取余额接口url
     */
    private $YmMoneyUrl = 'http://sdk4report.eucp.b2m.cn:8080/sdkproxy/querybalance.action';

    /**
     * @var string  诚意通短信接口用户名
     */
    private $CytUsername = 'yllw';

    /**
     * @var string  诚意通短信接口密码
     */
    private $CytPwd = 'superego@123';

    /**
     * @var string  诚意通短信接口url
     */
    private $CytUrl = 'http://yl.mobsms.net/send/gsend.aspx';

    /**
     * @var string  诚意通获取余额接口url
     */
    private $CytMoneyUrl = 'http://yl.mobsms.net/send/getfee.aspx';

    /**
     * @var string  诚意通短信接口用户名
     */
    private $JcUsername = 'qtw';

    /**
     * @var string  君成短信接口密码
     */
    private $JcPwd = '17c6e912734720ac744ab69112d4e79a';

    /**
     * @var string  君成短信接口url
     */
    private $JcUrl = 'http://www.jc-chn.cn/smsSend.do';

    /**
     * @var string  君成获取余额接口url
     */
    private $JcMoneyUrl = 'http://www.jc-chn.cn/balanceQuery.do';

    /**
     * @var string  免费短信appkey
     */
    private $appkey = 'd5246ec75a5d';

    /**
     * @var string 免费短信验证接口url
     */
    // private $freeUrl = 'https://api.sms.mob.com/sms/verify';
    private $freeUrl = 'https://web.sms.mob.com/sms/verify';
    private $freeUrl2 = 'https://webapi.sms.mob.com/sms/verify';

    /**`
     * @var string  发送短信电话号
     */
    private $phone = '';

    /**
     * @var string  发送内容
     */
    private $content = '';

    /**
     * @var string  发送时间
     */
    private $time = '';

    /**
     * @var string  输入内容
     */
    private $output = '';
    private $errInfo = array(
        '-1'    => '系统异常',
        '-2'    => '必填选项为空',
        '-3'    => '短信内容0个字节',
        '-4'    => '0个有效号码',
        '-5'    => '余额不够',
        '-6'    => '含有一级敏感词',
        '-7'    => '含有二级敏感词，人工审核',
        '-8'    => '提交频率太快，退避重发',
        '-9'    => '数据格式错误',
        '-10'   => '用户被禁用',
        '-11'   => '短信内容过长',
        '-12'   => '失败',
        '-13'   => '用户名或者密码不正确',
        '-101'  => '命令不被支持',
        '-102'  => 'RegistryTransInfo删除信息失败',
        '-103'  => 'RegistryInfo更新信息失败',
        '-104'  => '请求超过限制',
        '-110'  => '号码注册激活失败',
        '-111'  => '企业注册失败',
        '-113'  => '充值失败',
        '-117'  => '发送短信失败',
        '-118'  => '接收MO失败',
        '-119'  => '接收Report失败',
        '-120'  => '修改密码失败',
        '-122'  => '号码注销激活失败',
        '-123'  => '查询单价失败',
        '-124'  => '查询余额失败',
        '-125'  => '设置MO转发失败',
        '-126'  => '路由信息失败',
        '-127'  => '计费失败0余额',
        '-128'  => '计费失败余额不足',
        '-190'  => '数据操作失败',
        '-1102' => '序列号密码错误',
        '-1103' => '序列号Key错误',
        '-1104' => '路由失败，请联系系统管理员',
        '-1105' => '注册号状态异常, 未用 1',
        '-1107' => '注册号状态异常, 停用 3',
        '-1108' => '注册号状态异常, 停止 5',
        '-1110' => '序列号错误,序列号不存在内存中,或尝试攻击的用户',
        '-1131' => '充值卡无效',
        '-1132' => '充值密码无效',
        '-1133' => '充值卡绑定异常',
        '-1134' => '充值状态无效',
        '-1135' => '充值金额无效',
        '-1901' => '数据库插入操作失败',
        '-1902' => '数据库更新操作失败',
        '-1903' => '数据库删除操作失败',
        '512'   => '服务器拒绝访问，或者拒绝操作',
        '513'   => '求Appkey不存在或被禁用',
        '514'   => '权限不足',
        '515'   => '服务器内部错误',
        '517'   => '缺少必要的请求参数',
        '518'   => '请求中用户的手机号格式不正确（包括手机的区号）',
        '519'   => '请求发送验证码次数超出限制',
        '520'   => '无效验证码',
        '526'   => '余额不足',

        '405'   => 'AppKey为空',
        '406'   => 'AppKey无效',
        '456'   => '国家代码或手机号码为空',
        '457'   => '手机号码格式错误',
        '466'   => '请求校验的验证码为空',
        '467'   => '请求校验验证码频繁（5分钟内同一个appkey的同一个号码最多只能校验三次）',
        '468'   => '验证码错误',
        '474'   => '没有打开服务端验证开关',

        '6002'  => '用户账号不正确',
        '6008'  => '无效的手机号码',
        '6009'  => '手机号码是黑名单',
        '6010'  => '用户密码不正确',
        '6011'  => '短信内容超过了最大长度限制',
        '6012'  => '该企业用户设置了ip限制',
        '6013'  => '该企业用户余额不足',
        '6014'  => '发送短信内容不能为空',
        '6015'  => '发送内容中含非法字符',
        '6019'  => '账户已停机，请联系客服',
        '6021'  => '扩展号码未备案',
        '6023'  => '发送手机号码超过太长，已超过300个号码',
        '6024'  => '定制时间不正确',
        '6025'  => '扩展号码太长（总长度超过20位）',
        '6080'  => '提交异常，请联系服务商解决',
        '6085'  => '短信内容为空',
    );

    /**
     * 构造函数
     */
    public function __construct() {
        $this->handle = curl_init();
        $header       = array(
            'Content-Type: application/x-www-form-urlencoded;charset=gb2312',
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest'
        );
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handle, CURLOPT_HEADER, 0);
        curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $header);
        curl_setopt($this->handle, CURLOPT_TIMEOUT, 30);
    }

    /**
     * @param $url  设置提交url
     */
    private function setUrl($url) {
        curl_setopt($this->handle, CURLOPT_URL, $url);
    }

    /***
     * @param $cookies
     * 设置cookies
     */
    private function setCookies($cookies) {
        curl_setopt($this->handle, CURLOPT_COOKIE, $cookies);
        //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    }
    /**
     * 设置curl参数
     */
    private function setOptions($flag, $value) {
        curl_setopt($this->handle, $flag, $value);
    }

    /**
     * 设置发送数据
     * @param $arr  数据数组
     */
    private function setData($data, $type) {
        $tempArr = '';
        switch ($type) {
            case 'Ymsms':
                $arr['cdkey']    = $this->YmUsername;
                $arr['password'] = $this->YmPwd;
                $arr['phone']    = $data['mobile'];
                $arr['message']  = $data['content'];
                break;
            case 'Jcsms' :
                $arr['username'] = $this->JcUsername;
                $arr['password'] = $this->JcPwd;
                $arr['mobile']   = $data['mobile'];
                $arr['content']  = '【青团网】' . $data['content'];
                break;
            case 'verify' :
                $arr = $data;
                break;
            case 'verify2' :
                $arr = $data;
                break;
        }
        $this->setOptions(CURLOPT_POST, 1);
        $this->setOptions(CURLOPT_POSTFIELDS, http_build_query($arr));
    }

    /**
     * @param $method  执行的方法
     * @param $data    发送的数据
     * @return string
     */
    private function op_curl($method, $data = "", $type) {
        $url = '';
        if (!empty($data) && is_array($data)) {
            $this->phone   = isset($data['mobile']) ? $data['mobile'] : '';
            $this->content = isset($data['content']) ? $data['content'] : '';
            $this->time    = date("Y/m/d H:i:s");
        }
        switch ($method) {
            case 'sendMsg':
                $url = $this->sendUrl($type);
                break;
            case 'balance':
                $url = $this->balanceUrl($type);
                break;
            case 'verify':
                $url = $this->freeUrl;
                $this->setData($data, $method);
                break;
            case 'verify2':
                $url = $this->freeUrl2;
                $this->setData($data, $method);
                break;
        }
        $this->setUrl($url);
        if ($method == 'sendMsg' && $type != 'Cytsms') {
            $this->setData($data, $type);
        }
        $this->exec_curl();
        $this->colse();
        return $this->returnResult($method, $type);
    }

    /**
     * $param $type 根据短信类型进行获取url地址
     */
    private function balanceUrl($type) {
        switch ($type) {
            case 'Ymsms':
                return $this->YmMoneyUrl . '?cdkey=' . $this->YmUsername . '&password=' . $this->YmPwd;
                break;
            case 'Jcsms':
                return $this->JcMoneyUrl . '?username=' . $this->JcUsername . '&password=' . $this->JcPwd;
                break;
            case 'Cytsms':
                return $this->CytMoneyUrl . '?name=' . $this->CytUsername . '&pwd=' . $this->CytPwd;
                break;
        }
    }

    /**
     * $param $type 根据短信类型进行获取url地址
     */
    private function sendUrl($type) {
        $url = '';
        switch ($type) {
            case 'Ymsms':
                $url = $this->YmUrl;
                break;
            case 'Jcsms':
                $url = $this->JcUrl;
                break;
            case 'Cytsms':
                $url = $this->CytUrl . '?name=' . $this->CytUsername . '&pwd=' . $this->CytPwd . '&dst=' . $this->phone . '&msg=' . iconv('UTF-8', 'gb2312', $this->content);
                break;
        }
        return $url;
    }

    /**
     * 打开句柄
     */
    private function exec_curl() {
        $this->output = curl_exec($this->handle);
    }

    /**
     * 关闭句柄
     */
    private function colse() {
        curl_close($this->handle);
    }

    /**
     * 错误信息展示
     * @param $key
     * @return mixed
     */
    private function errorInfo($key) {
        return $this->errInfo[$key];
    }

    /**
     * 日志输出
     */
    private function logOut($flag) {
        $destination = C('LOG_PATH') . 'sms/' . date('y_m_d') . '.log';
        if (!is_dir(C('LOG_PATH') . 'sms')) {
            @mkdir(C('LOG_PATH') . 'sms');
        }
        \Think\Log::write("$flag:$this->time-$this->phone-$this->content-$this->output", \Think\Log::INFO, '', $destination);
    }

    /**
     * 返回错误信息
     * @param $flag   操作标记
     * @return string  返回结果
     */
    private function returnResult($flag = "sendMsg", $type) {
        if ($flag == 'sendMsg') {
            $this->parsingRes($type);
            if (array_key_exists($this->output, $this->errInfo)) {
                $this->logOut($flag);
                return array('status' => -1, 'data' => $this->errorInfo($this->output));
            } else {
                return array('status' => 1, 'data' => '发送成功');
            }
        } elseif ($flag == 'balance') {
            return array('status' => 1, 'data' => $this->output);
        } elseif ($flag == 'verify') {
            $this->output = @json_decode($this->output, true);
            if (!isset($this->output['status']) || array_key_exists($this->output['status'], $this->errInfo)) {
                $this->logOut($flag);
                return array('status' => -1, 'data' => $this->errorInfo($this->output['status']));
            } else {
                return array('status' => 1, 'data' => '验证成功');
            }
        }elseif ($flag == 'verify2') {
            $this->output = @json_decode($this->output, true);
            if (!isset($this->output['status']) || array_key_exists($this->output['status'], $this->errInfo)) {
                $this->logOut($flag);
                return array('status' => -1, 'data' => $this->errorInfo($this->output['status']));
            } else {
                return array('status' => 1, 'data' => '验证成功');
            }
        }
    }

    /**
     * 解析返回结果
     */
    private function parsingRes($type) {
        $arr = array();
        $res = $this->output;
        switch ($type) {
            case 'Ymsms':
                $this->output = $res;
                break;
            case 'Jcsms':
                if ($res == 0)
                    $res = -12;
                if ($res == -1)
                    $res = -13;
                $this->output = $res;
                break;
            case 'Cytsms':
                $arr = explode('&', $res);
                $tmp = array_pop($arr);
                list($error_num, $err_info) = explode('=', $tmp);
                $this->output = $err_info;
                break;
        }
    }


    /**
     * 发送短信
     * @param $phone            电话必填(支持10000个手机号,建议<=5000)多个英文逗号隔开
     * @param $content          短信内容
     * @param $type             发送短信平台类型 （Ymsms 亿美软通 Jcsms 君成 Cytsms 诚意通）
     * @return string
     */
    public function sendMsg($phone, $content, $type = 'Jcsms') {
        $type = 'Jcsms';
        $data = array(
            'mobile'  => $phone,
            'content' => sms_trim($content),
        );
        return $this->op_curl('sendMsg', $data, $type);
    }

    /**
     * @param $type 查询余额类型
     * @return string
     */
    public function balance($type = 'Ymsms') {
        return $this->op_curl('balance', '', $type);
    }

    /**
     * @param $phone 手机号码
     * @param $code  验证码
     */
    public function checkVerify($phone, $code,$appkey) {
        $data = array(
            'appkey' => $appkey,
            'phone'  => $phone,
            'zone'   => '86',
            'code'   => $code,
        );
        return $this->op_curl('verify', $data, '');
    }
    /**
     * @param $phone 手机号码
     * @param $code  验证码
     */
    public function checkVerify2($phone, $code,$appkey) {
        $data = array(
            'appkey' => $appkey,
            'phone'  => $phone,
            'zone'   => '86',
            'code'   => $code,
        );
        return $this->op_curl('verify2', $data, '');
    }
    /**
     * 联合登录
     */
    public function unionlogin($username,$password,$toekn,$sig){
        if($toekn){
            return $this->tokenlogin($toekn,$sig);
        }else{
            return $this->namelogin($username,$password);
        }
    }

    /**
     * @param $token
     * @param $sig
     * @return array
     * token验证登陆
     */
    private function tokenlogin($token,$sig){
        $data = array(
            'token' => $token,
            'sig'=>$sig
        );
        $url = 'http://api.ree9.com/member/getuserinfo?token='.$token.'&sig='.$sig;
        $http  = new \Common\Org\Http();
        $data=json_decode($http->get($url,$data), true);
        if(isset($data['code']) && intval(trim($data['code'])) == 0 && isset($data['data']['mobile'])){
            $data=array(
                'status'=>1,
                'phone'=>$data['data']['mobile']
            );
        }else{
            $data=array(
                'status'=>0,
            );
        }
        return $this->output =$data;
    }
    /*
     * 账户密码登录
     */
    private function namelogin($username,$password){
        $data = array(
            'account' => $username,
            'password'  => $password
        );
        $url = 'http://m.youngt.com/Public/doLogin.html';
        $this->setOptions(CURLOPT_POST, 1);
        $this->setOptions(CURLOPT_POSTFIELDS, http_build_query($data));
        $this->setUrl($url);
        $this->exec_curl();
        $this->colse();
        $data=@json_decode($this->output, true);
        // file_put_contents('/tmp/ddhome_paycallback.log',var_export($this->output, true).'||',FILE_APPEND);
        $data['phone']=$username;
        return $this->output =$data;
    }
    public function cookes()
    {
        $url = 'http://pub.alimama.com/pubauc/searchAuctionList.json?spm=a2320.7388781.a214tr8.d006.Vhz59a&q=https://item.taobao.com/item.htm?id=524513679327&spm=a1z09.2.0.0.9NoAZa&_u=4736kls2b17&toPage=1&perPagesize=40&t=1465019018302&pvid=50_101.107.157.234_11397_1465018728240&_tb_token_=aJpEXzbMEap&_input_charset=utf-8';
        $cookies='t=e422f833d7ab6a9948856c64e55881de; l=AkJCOOaJwVw1xCCxdktJ3UvH0kakE0Yt; cna=Ek/pDx9oJkcCAXUjzbJRUhHk; isg=AiAgnw78YxuNdd8fkaltxquy9i7BNATzbP2Sd5ox7DvOlcC_QjnUg_YmeYXq; cookie2=26789abed3cec4cc6942080e11c64767; _tb_token_=vvCaSAYalfp; v=0; cookie32=04b9fb8f9ebb707127504e3688ad77b8; cookie31=Mjk0Nzk2NzIsc3VwZXJlZ29saXUsMTI1Nzg5NzMwQHFxLmNvbSxUQg%3D%3D; alimamapwag=TW96aWxsYS80LjAgKGNvbXBhdGlibGU7IE1TSUUgNy4wOyBXaW5kb3dzIE5UIDYuMDsgVHJpZGVudC80LjA7IFNMQ0MxOyAuTkVUIENMUiAyLjAuNTA3Mjc7IC5ORVQgQ0xSIDMuNS4zMDcyOTsgLk5FVCBDTFIgMy4wLjMwNzI5OyAuTkVUNC4wQzsgLk5FVDQuMEUp; login=V32FPkk%2Fw0dUvg%3D%3D; alimamapw=QxQTURNUVF9eXBNvAVFTVgRVB1ZTVlQGCgJRU1YFAlMEUVAFB1AFAFBVVlI%3D';
        $this->setOptions(CURLOPT_GET, 1);

        $this->setCookies($cookies);
        $this->setUrl($url);
        $this->exec_curl();
        $this->colse();
        $data=@json_decode($this->output, true);
        return $data['data']['pagelist'];
    }
    public function Getcookes()
    {
        $header       = array(
            'Content-Type: application/x-www-form-urlencoded;charset=gb2312',
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest'
        );
        $url ='http://www.dataoke.com/zs_tk/quan_order.asp?type=sh_2';// 'http://www.dataoke.com/zs_tk/quan_order.asp';
        $cookies='CNZZDATA1257179126=673246782-1480582009-%7C1499056755; UM_distinctid=15c254a17379-0e7a5185cd18f5-46524130-100200-15c254a173855; ASPSESSIONIDCADRBRSC=HIHKNMOAKNOBPICGLIJPMPPD; dtk_web=7nphncs2dreep2lfrn2sd3ugj3; browserCode=4511aea5a8c89c5078137a5d0129717a; random=3808; userid=420951; user_email=125789730%40qq.com; user%5Femail=125789730%40qq.com; upe=2e0d3a40; e88a8013345a8f05461081898691958c=15c4bf946ac5fa72ffdc92787d3a5e614798712ca%3A4%3A%7Bi%3A0%3Bs%3A6%3A%22420951%22%3Bi%3A1%3Bs%3A16%3A%22125789730%40qq.com%22%3Bi%3A2%3Bi%3A2592000%3Bi%3A3%3Ba%3A0%3A%7B%7D%7D; ASPSESSIONIDQSRCBSTR=EMFJJNOAGKDJCHHGJBIKDPNO; ASPSESSIONIDSSRCASSR=JCGBGNOAOLOONCAFJMBDEBDL; ASPSESSIONIDAADQDQTD=HPGBHNOABMMOPODLIAPPMLAH; ASPSESSIONIDCABSAQSD=DNFJKNOAMCLMIILHIPKOEIBI; ASPSESSIONIDAACRDQSC=FEIFBNOADFHPJMGEJNHOPOMA; ASPSESSIONIDSSTDBTSR=EBIFANOADCGIOLFDOEBKJIBJ; ASPSESSIONIDSQRADSTR=FIIMMMOAAHADPDPPCNAMJPJK; ASPSESSIONIDSQRCBTSR=LGDOIOOAEHGDIGHDMAINAGNA; ASPSESSIONIDCCDSARSC=OBDGNOOAOFNCDGJGOOBBBJGL; ASPSESSIONIDQQSCBTTR=HADIMOOAKDPPJKOCPDEMKNEE; ASPSESSIONIDAAAQARTC=CDECEOOAJFPDMFJKJNKFKOOA';
        $this->setOptions(CURLOPT_GET, 1);
        //curl_setopt($this->handle, CURLOPT_HTTPHEADER, $header);
        //$this->setOptions(CURLOPT_HTTPHEADER, $header);
        $this->setCookies($cookies);
        $this->setUrl($url);
        $this->exec_curl();
        $this->colse();
        $data=$this->output;
        return $data;
    }
    public function Yongjin($url)
    {
        $url = 'http://pub.alimama.com/items/search.json?q='.$url;
        //var_dump($url);exit;
        $this->setOptions(CURLOPT_GET, 1);
        $this->setUrl($url);
        $this->exec_curl();
        $this->colse();
        $data=@json_decode($this->output, true);
        if($data['data']['head']['status']=='OK'){
            //var_dump($data['data']['pageList']);exit;
            return $data['data']['pageList'];
        }else{
            return false;
        }
    }
    public function fanliurl($mm,$id)
    {
        $url = "http://g.click.taobao.com/display?cb=jsonp_callback_06476987702772021&pid=".$mm."&wt=0&ti=5&tl=290x380&rd=2&ct=itemid=".$id."&st=2&rf=http://fanli.youngt.com&et=64443062&pgid=5941834c29e932ecb90a7656f22aa618&ttype=1&v=1.2&cm=&ck=&cw=0&unid=0";
        $this->setOptions(CURLOPT_GET, 1);
        $this->setUrl($url);
        $this->exec_curl();
        $this->colse();
        $data=$this->output;
        $data=str_replace('jsonp_callback_06476987702772021(','',$data);
        $data=str_replace(')','',$data);
        $data=@json_decode($data, true);
        if($data['code']==200){
            return $data['data']['items'];
        }else{
            return false;
        }

    }
    public function fanliurl2($mm,$id)
    {
        //$url = "http://g.click.taobao.com/display?cb=jsonp_callback_06476987702772021&pid=mm_29479672_4308028_56672643&wt=0&ti=5&tl=290x380&rd=2&ct=itemid=".$id."&st=2&rf=http://www.taodianke.com&et=64443062&pgid=5941834c29e932ecb90a7656f22aa618&ttype=1&v=1.2&cm=&ck=&cw=0&unid=0";
        $url = "http://g.click.taobao.com/display?cb=jsonp_callback_06476987702772021&pid=".$mm."&wt=0&ti=5&tl=290x380&rd=2&ct=itemid=".$id."&st=2&rf=http://fanli.youngt.com&et=64443062&pgid=5941834c29e932ecb90a7656f22aa618&ttype=1&v=1.2&cm=&ck=&cw=0&unid=0";
        $this->setOptions(CURLOPT_GET, 1);
        $this->setUrl($url);
        $this->exec_curl();
        $this->colse();
        $data=$this->output;
        $data=str_replace('jsonp_callback_06476987702772021(','',$data);
        $data=str_replace(')','',$data);
        $data=@json_decode($data, true);
        if($data['code']==200){
            return $data['data']['items'];
        }else{
            return false;
        }
    }
    //转换地址方法开始
    public function ZhuanhUrl($clickurl){
        $headers = get_headers($clickurl, TRUE);
        $tu = $headers['Location'];
        $eturl = self::unescape($tu[1]);   //获取完整连接

        $u = parse_url($eturl);        //得到et的连接
        $param = $u['query'];
        $ref = str_replace('tu=', '', $param);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ref);

        curl_setopt($ch, CURLOPT_REFERER, $tu[1]);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch, CURLOPT_MAXREDIRS,2);
        $out = curl_exec($ch);
        $dd =  curl_getinfo($ch);
        curl_close($ch);
        $item_url = $dd['url'];
        return $item_url;
    }
    public function ZhuangtaoUrl($url){
        $eturl = self::unescape($url);   //获取完整连接
        var_dump($eturl);
        $u = parse_url($eturl);        //得到et的连接
        var_dump($u);exit;
        $param = $u['query'];
        $ref = str_replace('tu=', '', $param);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ref);

        curl_setopt($ch, CURLOPT_REFERER, $tu[1]);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch, CURLOPT_MAXREDIRS,2);
        $out = curl_exec($ch);
        $dd =  curl_getinfo($ch);
        curl_close($ch);
        $item_url = $dd['url'];
        return $item_url;
    }
    function unescape($str) {
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i ++) {
            if ($str[$i] == '%' && $str[$i + 1] == 'u')
            {
                $val = hexdec(substr($str, $i + 2, 4));
                if ($val < 0x7f)
                    $ret .= chr($val);
                else if ($val < 0x800)
                    $ret .= chr(0xc0 | ($val >> 6)).chr(0x80 | ($val & 0x3f));
                else
                    $ret .= chr(0xe0 | ($val >> 12)).chr(0x80 | (($val >> 6) & 0x3f)).chr(0x80 | ($val & 0x3f));             $i += 5;
            } else  if ($str[$i] == '%'){
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            } else
                $ret .= $str[$i];
        }
        return $ret;
    }
    //转换地址方法结束
    //转换短地址
    public  function Zhauandurl($url){
        $url=urlencode($url);
        $url = "http://50r.cn/urls/add.json?url=".$url;
        $this->setOptions(CURLOPT_GET, 1);
        $this->setUrl($url);
        $this->exec_curl();
        $this->colse();
        $data=$this->output;
        //{"error":null,"url":"http://50r.cn/2JUifN"}
        $data=@json_decode($data, true);
        if($data['error']==null){
            return $data['url'];
        }else{
            return false;
        }
    }
    public  function taobaoke($url){
        return self::curlGet($url);

    }

    public  function curlGet($url){
        /*$ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);*/

        $ua = "Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_USERAGENT, $ua);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $sources = curl_exec($ch);

        curl_close($ch);
        return $sources;
    }
    //转换短地址结束
}
