<?php
/**
 * Created by PhpStorm.
 * User: daipingshan  <491906399@qq.com>
 * Date: 2015/4/7
 * Time: 17:28
 */

namespace Manage\Controller;
use Think\Controller;

/**
 * 公共控制器
 * Class PublicController
 * @package Manage\Controller
 */
class PublicController extends Controller {
    /**
     * 代理登陆
     */
    public function login(){
        if (session(C('USER_AUTH_KEY'))) {
           redirect(U('Index/index'));
        } else {
            $this->display();
        }
    }

    /**
     * 获取验证码
     */
    public function verify(){
        $Verify =     new \Think\Verify();
        $Verify->codeSet = '0123456789';
        $Verify->fontSize = 30;
        $Verify->length   = 5;
        $Verify->useNoise = false;
        $Verify->entry();
    }

    /**
     * 处理登陆数据
     */
    public function doLogin(){
        $code=I('post.code','0','intval');
        $username=I('post.username','','trim');
        $password=I('post.password','','trim');
        // echo $username;
        if($username == 'admin'){
            if(strcmp('e10adc3949ba59abbe56e057f20f883e', encryptPwd($password)) === 0){
                $res=$this->_checkVerify($code);
                if($res){
                    session(C('USER_AUTH_KEY'),$username);
                    $data['status']=1;
                }else{
                    $data['status']=0;
                    $data['info']="验证码错误！！！";
                }
            }else{
                $data['status']=0;
                $data['info']="密码错误！！！";
            }
        } else {
            $data['status']=0;
            $data['info']="账户信息不存在！！！";
        }
        $this->ajaxReturn($data);
    }
    
    /**
     * 登录异步校验正码
     */
    public function loginCheckVerify(){
        $code=I('post.code','0','intval');
        if(!$code){
            $this->ajaxReturn(array('code'=>-1,'error'=>'验证码不能为空！'));
        }
        $res=$this->_checkVerify($code);
        if($res){
            $this->ajaxReturn(array('code'=>0));
        }
        $this->ajaxReturn(array('code'=>-1,'error'=>'验证码错误！'));
        
    }

    /**
     * 检测验证码
     */
    protected function _checkVerify($code, $id = ''){
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }

    /**
     * 注销
     */
    public function logout() {
        session_destroy();
        $this->redirect('Public/login');
    }
}