<?php

namespace Api\Controller;

class PublicController extends CommonController {

    /**
     * @var bool
     */
    protected $checkUser = false;

    /**
     * 用户登录
     */
    public function login() {
        $username = I('post.username', '', 'trim');
        $password = I('post.password', '', 'trim');
        $user = D('Api/User');
        $this->_checkblank(array('username', 'password'));
        $res = $user->login($username, $password);
        if (isset($res['error'])) {
            $this->outPut(null, -1, null, ternary($res['error'], ''));
        }
        if (!$res) {
            $this->outPut(null, 1022);
        }
        $uid = $res['id'];
        $token = $this->_createToken($uid);
        $data = array(
            'token' => $token,
        );
        $this->outPut($data, 0, null);
    }
	 /**
     * 找回密码 - 验证账号 发送短信验证码
     */
	public function rtp_check_user(){
		$userinfo = M('user');
		$userMods = D('Api/User');
		$userPhone = I('get.userPhone', '', 'trim');
		 
		if(!$userMods->isMobile($userPhone)){
			//手机号码错误
			 $this->outPut(null,-1,NULL,"手机号码输入不正确");
		}

		if($userinfo->where("mobile='$userPhone'")->count() == 0){
			 $this->outPut(null,-1,NULL,"没有该用户");
		}
		//找回密码验证码
		$_SESSION['rpt_v_code'] = 4231;
		
		//找回密码用户
		$_SESSION['usePhone'] = $userPhone;
		
		//发送验证码
		//curl();
		
		
		$this->outPut(null,0,"success");
	}

	 /**
     * 找回密码 - 验证验证码
     */
	public function rtp_check_code(){
		
		$rpt_v_code = I('post.rpt_v_code', '', 'trim');
		 
		if(!isset($rpt_v_code) || strlen($rpt_v_code) != 4){
			//手机号码错误
			 $this->outPut(null,-1,NULL,"请输入正确4位验证码。");
		}

		//找回密码验证码
		if($_SESSION['rpt_v_code'] != $rpt_v_code){
			//手机号码错误
			 $this->outPut(null,-1,NULL,"验证码错误");
		}
		
		
		$_SESSION['rpt_v_code'] = true;
		$this->outPut(null,0,"success");
	}
	/**
     * 找回密码 - 重置密码
    */
	public function rtp_reset_Pwd(){
		
		if(!isset($_SESSION['usePhone']) && $_SESSION['rpt_v_code'] != true){
			
			$this->outPut(null,-1,NULL,"验证码错误，请返回上一步进行检查！");
			
		}
		$pwd = I('post.pwd', '', 'trim');
		$pwd2 = I('post.pwd2', '', 'trim');
			
		$userinfo = M('user');
			
		if(strlen($pwd) < 6  && strlen($pwd2) < 6){
			//注册密码少于6位
			 $this->outPut(null,-1,NULL,"密码不能少于6位");
				
		}
		if($pwd !== $pwd2){
			//两次输入密码不一致
			$this->outPut(null,-1,NULL,"两次输入密码不一致");
				
		}	
			
		$data['password'] = md5(trim($pwd));
			
		$userinfo->where("mobile =".$_SESSION['usePhone'])->save($data);
		$_SESSION['rpt_v_code'] == false;
		$this->outPut(null,0,"success");
	
		
	}
	
	 /**
     * 用户注册
     */
	public function reg(){
		$userMods = D('Api/User');
		$userPhone = I('post.userPhone', '', 'trim');
		$userPwd   = I('post.userPwd', '', 'trim');
		$userPwd2  = I('post.userPwd2', '', 'trim');
		
		if(empty($userPhone) || !isset($userPhone)){
			//未输入电话号码
			 $this->outPut(null,-1,NULL,"未输入电话号码");
			 
		}
		if(!$userMods->isMobile($userPhone)){
			//手机号码错误
			 $this->outPut(null,-1,NULL,"手机号码错误");
			
		}
		if(empty($userPwd) || !isset($userPwd)){
			//未正确输入密码
			 $this->outPut(null,-1,NULL,"未正确输入密码");
			
		}
		if(empty($userPwd2) || !isset($userPwd2)){
			//请再一次输入用户密码
			 $this->outPut(null,-1,NULL,"请再一次输入用户密码");
			
		}
		
		if(strlen($userPwd) < 6  && strlen($userPwd2) < 6){
			//注册密码少于6位
			 $this->outPut(null,-1,NULL,"注册密码少于6位");
			
		}
		
		if($userPwd !== $userPwd2){
			//两次输入密码不一致
			 $this->outPut(null,-1,NULL,"两次输入密码不一致");
			
		}
	
		$userinfo = M('user');

		
		if($userinfo->where("mobile = '$userPhone'")->count() >= 1){
			//手机号码已经被注册
			$this->outPut(null,-1,NULL,"手机号码:".$userPhone."已经被注册");
		}

		$data['mobile'] = $userPhone;
		$data['password'] = md5(trim($userPwd));
		$data['reg_time'] = time();
		
	
		if($userinfo->add($data)){
			//注册成功
			$this->outPut(null,0,"success");
			
		}else{
			//注册失败
			$this->outPut(null,-1,NULL,"严重：数据正确，但无法正常注册。请联系管理员！");
		}

	}
	//获取字符
	function get_word($html,$star,$end){
	$pat = '/'.$star.'(.*?)'.$end.'/s';
		if(!preg_match_all($pat, $html, $mat)) {
		}else{
			$wd= $mat[1][0];
		}
		return $wd;
	}

	

}
