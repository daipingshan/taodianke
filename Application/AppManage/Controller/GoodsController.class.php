<?php
namespace AppManage\Controller;

class GoodsController extends CommonController {
    public function index(){
       $order = M('items');
       /* 订单值    */
       $pid = I('get.cate_id',NULL,"int");
      
       if(IS_GET && $pid != null){
           
          $user = $user_info->where('pid = "'.$pid.'"')->getField('username');
         // print_r($user);
      
           $where['pid'] = $pid;
           if($Sment == 1){
               $where['is_pay'] = 'Y';
           }elseif($Sment == 2){
               $where['is_pay'] = 'N';
           }
           //dump($where);
           $count      = $order->where($where)->count();// 查询满足要求的总记录数
           $Page       = new \Think\Page($count,50);// 实例化分页类 传入总记录数和每页显示的记录数(25)
           $show       = $Page->show();// 分页显示输出
           // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
           $list = $order->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
          
       }else{
           
           $count      = $order->count();// 查询满足要求的总记录数
           $Page       = new \Think\Page($count,50);// 实例化分页类 传入总记录数和每页显示的记录数(25)
           $show       = $Page->show();// 分页显示输出
           // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
           $list = $order->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
          
       }
     
       
	    $this->assign('list',$list);// 赋值数据集
	   
	    $this->assign('page',$show);// 赋值分页输出
	    $this->assign('count',$count); //订单数量
	 

		$this->display();
	}
	
}