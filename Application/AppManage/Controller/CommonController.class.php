<?php 
namespace AppManage\Controller;
use Think\Controller;

class CommonController extends Controller {
     // 自动登录
     public function _initialize(){
         
  
        //判断用户是否已经登录
        if (!isset($_SESSION['Auth_user'])) {
             redirect(U('/Assets/login/'));
        }
        
    }
}
?>