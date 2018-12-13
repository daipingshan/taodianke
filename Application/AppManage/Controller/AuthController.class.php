<?php
namespace AppManage\Controller;
use Think\Controller;
class AuthController extends Controller {
    public function index() {
         
        redirect(U('Public/login'));
    }
    
    public function login(){
        if(IS_POST){
            $username = I('post.username','','trim');
            $password = I('post.password','','trim');
    
            if(!$username){
                
                $data['status']  = false;
                $data['content'] = '用户名不能为空！';
                $this->ajaxReturn($data);
                
              
            }
            if(!$password){
                
                $data['status']  = false;
                $data['content'] = '密码不能为空！';
                $this->ajaxReturn($data);
              
            }
    
            $map = array(
                'name' => $username,
                'password'   => md5($password)
            );
            $admin = M('admin');
           
           
            if(!$AuthUser = $admin->where($map)->find()){
                
                $data['status']  = false;
                $data['content'] = '用户名密码错误！';
                $this->ajaxReturn($data);
                exit;   
            }else{
            
                $_SESSION['Auth_user'] = $AuthUser['id'];
                $data['status']  = true;
                $data['content'] = "success";
                $this->ajaxReturn($data);
                
            }
            $data['status']  = false;
            $data['content'] = "错误的查寻！";
            $this->ajaxReturn($data);
    
        }
       
       
    }
    
    public function logout(){
        session_destroy();
        redirect(U('/Assets/login/'));
    }
    
}