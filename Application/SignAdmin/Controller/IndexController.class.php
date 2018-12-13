<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2015/10/9
 * Time: 17:44
 */

namespace SignAdmin\Controller;

class IndexController extends CommonController {

    /**
     * 默认访问页面
     */
    public function index() {
        $Model = M('sign');
        $count = $Model->count('id');
        $page  = $this->pages($count, 10);
        $data = $Model->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign(array('page'=>$page->show(),'data'=>$data,'current'=>'index'));
        $this->display();
    }

     /**
     * 用户管理
     */
    public function user() {
        $Model = M('user');
        $count = $Model->count('id');
        $page  = $this->pages($count, 10);
        $data = $Model->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign(array('page'=>$page->show(),'data'=>$data,'current'=>'user'));
        $this->display();
    }

    /**
     * 管理员登录页面
     */
    public function payData() {
        $Model = M('pay_log');
        $count = $Model->count('id');
        $page  = $this->pages($count, 10);
        $data = $Model->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($data as &$val) {
            $info = M('sign')->find($val['sign_id']);
            $val = array_merge($val,$info);
        }
        $this->assign(array('page'=>$page->show(),'data'=>$data,'current'=>'pay_data'));
        $this->display();
    }

}