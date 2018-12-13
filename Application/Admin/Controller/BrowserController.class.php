<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/4/10
 * Time: 15:16
 */

namespace Admin\Controller;

/**
 * Class BrowserController
 *
 * @package Admin\Controller
 */
class BrowserController extends CommonController {

    /**
     * @var bool
     */
    protected $checkUser = false;

    /**
     * 获取账号和密码
     */
    public function getAccount() {
        $data = array(
            array('username' => 'daipingshan', 'password' => 123456),
            array('username' => 'zhangsan', 'password' => 123456),
            array('username' => 'lisi', 'password' => 123456),
            array('username' => 'wangwu', 'password' => 123456),
        );
        ob_clean();
        echo json_encode(array('code' => 1, 'data' => $data));
        exit;
    }
}