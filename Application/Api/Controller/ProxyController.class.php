<?php
namespace Api\Controller;

/**
 * Class ItemController
 *
 * @package Api\Controller
 */
class ProxyController extends CommonController{
	protected $checkUser = false;
	
	/**
	 * 构造函数
	 *
	 * @access public
	 */
	function __construct() {
		parent:: __construct();
		$token = I('get.token','','trim');
		//token验证
		if ($token) {
			$this->check();
		}
	}
	public function set_proxy_cookie(){
		$proxy = M('proxy');
		$cookie = I('post.cookie',NULL,'htmlspecialchars');
		$pid = I('post.pid',NULL,'htmlspecialchars');
		
		if($pid != NULL AND $cookie != NULL){
			$data['cookie'] = $cookie;
			$where['pid'] = $pid;
			if($proxy->where($where)->save($data)){
				$this->outPut("OK",1);
			}
		}
		$this->outPut("错误：未成功写入COOKIE",-1);
	}
}