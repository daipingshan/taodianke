<?php
/**
 * Created by luowen.
 * User: Guoxiaonan
 * Date: 2017/4/7
 * Time: 9:09
 */

namespace AppApi\Controller;

/**
 * Class AuthController
 *
 * @package AppApi\Controller
 */
class UserController extends CommonController{

    /**
     * 用户信息
     */
    public function index(){
        $info = M('user')->field('username,pid,bank_name,bank_account,real_name')->find($this->uid);
        if(!$info['bank_name'] || $info['bank_account'] || !$info['real_name']){
            $info['is_bind_bank'] = 0;
        }else{
            $info['is_bind_bank'] = 1;
        }
        $info['image']  = getHeadImg($this->uid);
        $info['amount'] = $this->_amount();
        $this->outPut($info, 0);
    }

}

