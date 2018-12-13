<?php
/**
 * Created by PhpStorm.
 * User: superegoliu
 * Date: 2016/12/13
 * Time: 15:15
 */

namespace ApiTaoke\Controller;


class JuanController extends CommonController {
    protected $checkUser = false;
    public function index() {
        $id = I('id', '','trim');
        if($id) {
            if ($id == '425078') {
                $item['ckurl'] = 'https://uland.taobao.com/coupon/edetail?activityId=8968ceecdd724358a91d267661f458b3&pid=mm_29479672_14302447_56676061&itemId=547956047278&src=qhkj_dtkp&dx=1';
                header("USER-AGENT:: Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4");
                header("Location:" . $item['ckurl']);
                exit();
            }
        }
    }
} 