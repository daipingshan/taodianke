<?php

/**
 * User 用户表模型
 * Created by JetBrains PhpStorm.
 * User: daipingshan  <491906399@qq.com>
 * Date: 15-3-18
 * Time: 下午15:39
 * To change this template use File | Settings | File Templates.
 */

namespace AppApi\Model;

class UserModel extends CommonModel {


    /**
     * 默认登录类型
     *
     * @param type $username
     * @param type $password
     */
    public function login($username = '' , $password = '') {
        $where   = array(
            'mobile' => trim( $username ) ,
        );
        $userRes = $this->where( $where )->field( 'id,password,pid,bank_account,real_name,token,status' )->find();
        if ( !$userRes ) {
            return array( 'error' => '账户不存在，请重新输入' );
        } else if (0 == $userRes['status']) {
            return array( 'error' => '您的账号已被禁用' );
        } else if ( !isset($userRes['password']) || strcmp( $userRes['password'] , md5( trim( $password ) ) ) !== 0 ) {
            return array( 'error' => '账号或密码错误，请重新输入' );
        }
        return array( 'id' => $userRes['id'] , 'pid' => $userRes['pid'],'bank_account'=>$userRes['bank_account'],'real_name'=>$userRes['real_name'],'token'=>$userRes['token'] );
    }

    /**
     * 判断用户是否存在
     *
     * @param $uid 用户id
     *
     * @return bool
     */
    public function isExist($uid) {
        $mapping = array(
            'id' => $uid
        );

        if ( $this->getTotal( $mapping ) == 0 ) {
            return false;
        }
        return true;
    }


}
