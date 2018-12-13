<?php
namespace AppManage\Controller;

class FinanceController extends CommonController {
    public function index(){
	   $crsh_User = M('order_cash');
	   /* status change */
	   $sc = I('get.sc',0,int);
	   $id = I('get.id',0,int);
	   if(IS_GET){
	   
	   
	       if($sc == 1 && $id !=0){
	   
	           $data['status'] = 'Y';
	           $data['cash_time'] = date("Y-m-d h:i:s");
	           // 	          dump($data);
	           // 	          exit;
	           $crsh_User->where('id='.$id)->save($data);
	   
	       }
	   }
	   /* status change  end*/
	   
	   //分类选择
	   if(I('get.status',"all") == 1){ 
	       $status = "status = 'Y'";
	       $cstaus = 1;
	   }elseif(I('get.status',"all") == 2){
	       $status = "status = 'N'";
	       $cstaus = 2;
	   }else{
	       $status = "status != '0'";
	       $cstaus = 3;
	   }
	   $count      = $crsh_User->where($status)->count();// 查询满足要求的总记录数
	   $Page       = new \Think\Page($count,50);// 实例化分页类 传入总记录数和每页显示的记录数(25)
	   $show       = $Page->show();// 分页显示输出
	   // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
	   $list = $crsh_User->where($status)->order('add_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
	   $this->assign('list',$list);// 赋值数据集
	   $this->assign('cstaus',$cstaus);// 复制输出状态
	   $this->assign('page',$show);// 赋值分页输出
	   
	
	  
	  
       $this->display();
    }
    public function ranking(){
        
         //
         //用户查询
         $user = M('user');
         $order = M("order_data");
   
         //有订单用户
         $map['pid'] = array('neq',""); 
         $userinfo =  $user->where($map)->limit(0,100)->select();
     
         foreach($userinfo as $k => $v){
             $where['pid'] = $v['pid'];
             //当天的
             //$where['TO_DAYS( create_time )'] = "TO_DAYS( NOW( ) )";
             $count = $order->where($where)->count();
          
             if($count != 0){
                $orderTotalSum = $order->where($where)->sum('total_fee');
                $newarr[$k] = $v;
                $newarr[$k]['orderCount'] = $count;
                $newarr[$k]['orderTotalSum'] = number_format($orderTotalSum,2);
             }
            
         }
         
        //dump($newarr);
//      $sql = "SELECT * FROM ytt_order_data WHERE
//                                 TO_DAYS( create_time ) = TO_DAYS( NOW( ) )
//                 ORDER BY  `ytt_order_data`.`create_time` ASC LIMIT 0 , 30";
        $this->assign('list',$newarr);// 赋值数据集
        $this->display();
    }
    public function statistical(){
        
        $this->display();
    }
}