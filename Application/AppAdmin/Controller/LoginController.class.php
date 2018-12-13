<?php
namespace AppAdmin\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
       $this->display();

    }
    public function verify(){
        $config =    array(
            'fontSize'    =>    18,    // 验证码字体大小
            'length'      =>    4,     // 验证码位数
            'useNoise'    =>    false, // 关闭验证码杂点
            'imageW'      =>    140,
            'imageH'      =>    35,

        );
        $Verify =     new \Think\Verify($config);
        $Verify->codeSet = '0123456789';
        $Verify->entry();

    }
}