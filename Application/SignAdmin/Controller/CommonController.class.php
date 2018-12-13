<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2016/12/28
 * Time: 15:07
 */

namespace SignAdmin\Controller;
use Common\Controller\CommonBusinessController;
/**
 * Class CommonAction
 */
class CommonController extends CommonBusinessController {

    /**
     * 配置用户是否登录
     * @var bool
     */
    protected $checkUser = true;

    /**
     * 用户id
     * @var
     */
    protected $adminId;


    /**
     * 构造函数
     *
     * @access public
     */
    function __construct() {
        parent:: __construct();
        if ($this->checkUser) {
            $this->_checkUser();
        }
        //错误提示
        if (session('?error')) {
            $this->assign('error', session('error'));
            session('error', null);
        }
        //成功提示
        if (session('?success')) {
            $this->assign('success', session('success'));
            session('success', null);
        }
    }

    /**
     * 检测用户是否登录
     */
    protected function _checkUser() {
        $admin_id = session('admin_id');
        if ($admin_id) {
            $this->adminId = $admin_id;
        } else {
            redirect(U('Public/login'));
        }
    }

}