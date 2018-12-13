<?php

/**
 * User 用户表模型
 * Created by JetBrains PhpStorm.
 * User: daipingshan  <491906399@qq.com>
 * Date: 15-3-18
 * Time: 下午15:39
 * To change this template use File | Settings | File Templates.
 */

namespace Api\Model;

class UserModel extends CommonModel {

    /**
     * 默认登陆类型
     * @param type $username
     * @param type $password
     */
    public function login($username = '', $password = '') {

        $where = array(
            'username|mobile' => trim($username),
        );
        $userRes = $this->where($where)->field('id,password')->find();
        if (!$userRes) {
            return array('error' => '账户不存在，请重新输入');
        }
        if (!isset($userRes['password']) || strcmp($userRes['password'], md5(trim($password))) !== 0) {
            return array('error' => '账号或密码错误，请重新输入');
        }
        return array('id' => $userRes['id']);
    }

    /**
     * 判断用户是否存在
     * @param $uid 用户id
     *
     * @return bool
     */
    public function isExist($uid) {
        $mapping = array(
            'id' => $uid
        );
        if ($this->getTotal($mapping) == 0) {
            return false;
        }
        return true;
    }
	/**
	* 验证手机号是否正确
	
	*/
	function isMobile($mobile) {
		if (!is_numeric($mobile)) {
			return false;
		}
		return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
	}

}
