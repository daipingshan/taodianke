<?php

namespace Proxy\Controller;

/**
 * Class CommonAction
 */
class OrderController extends CommonController{

    /**
     * 订单管理列表
     */
    public function index(){
        $title         = I('get.title', '', 'trim');
        $id            = I('get.id', '', 'trim');
        $item_id       = I('get.item_id', '', 'trim');
        $user_id       = I('get.user_id', 0, 'int');
        $settle_status = I('get.settle_status', '', 'trim');
        $withdraw_id   = I('get.withdraw_id', 0, 'int');
        if(session('proxy_type') == 1){
            list($one,$two,$_,$_) = explode('_',session('pid'));
            $user = M('user')->field('id,real_name as username')->where(array('pid'=>array('like',"{$one}_{$two}%")))->index('id')->select();
            if($user){
                $user_id_arr = array_keys($user);
                $map['c.user_id'] = array('in',$user_id_arr);
            }else{
                $map['c.user_id'] = session('Auth_user');
            }
        }else{
            $user = M('user')->field('id,real_name as username')->where(array('ParentID'=>session('Auth_user')))->index('id')->select();
            if($user){
                $user_id_arr = array_keys($user);
                array_push($user_id_arr,session('Auth_user'));
                $map['c.user_id'] = array('in',$user_id_arr);
            }else{
                $map['c.user_id'] = session('Auth_user');
            }
        }
        if($user_id){
            unset($map['c.user_id']);
            $map['c.user_id'] = $user_id;
        }
        if($title){
            $map['c.title'] = array('LIKE', '%' . $title . '%');
        }
        if($id){
            $map['c.order_id'] = $id;
        }
        if($item_id){
            $map['c.item_id'] = $item_id;
        }
        if($settle_status){
            if($settle_status == 'Y'){
                $map['c.withdraw_id'] = array('gt', 0);
            }else{
                $map['c.withdraw_id'] = 0;
            }
        }
        if($withdraw_id){
            $map['c.withdraw_id'] = $withdraw_id;
        }
        $model  = M('order_commission');
        $count  = $model->alias('c')->join('left join ytt_order o ON o.order_id = c.order_id and o.order_num = c.order_num')->where($map)->count('c.id');
        $amount = $model->alias('c')->join('left join ytt_order o ON o.order_id = c.order_id and o.order_num = c.order_num')->where($map)->sum('c.fee');
        $page   = $this->pages($count, 20);
        $list   = $model->alias('c')->join('left join ytt_order o ON o.order_id = c.order_id and o.order_num = c.order_num')->join('left join ytt_user u on u.id = c.user_id')->field('c.*,o.title,o.number,o.order_type,u.username')->where($map)->order('c.id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign(array('list' => $list, 'page' => $page->show(), 'count' => $count, 'amount' => $amount ? $amount : 0,'user'=>$user));
        $this->display();
    }
}