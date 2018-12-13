<?php
namespace Proxy\Controller;

class FinanceController extends CommonController {

    public function __construct() {
        parent:: __construct();
        $this->user =  M('user');
        $this->proxy_ratio =  M('user_proxy_ratio');
        $this->order =  M('order_data');
        
    }
//1.检查代理身份
//2.查询代理信息。
    private function check_user_proxy(){
        $map['pid'] = $_SESSION['pid'];
        $proxy_info = M('proxy')->where($map)->find();
        if(!$proxy_info) {
            return false;
        }else{
            return $proxy_info;
        }
    }
    //付款操作
    private function doPlayMoney($id){
        $order_cash = M('order_cash');
        $data['status'] = 'Y';
        $data['cash_time'] = date("Y-m-d h:i:s");
        $order_cash->where('id='.$id)->save($data);
    }
    public function index(){
        $order_cash = M('order_cash');
        if(!$proxy_info = $this->check_user_proxy()){
            $this->error('仅企业级代理，可以查看提现信息','/Proxy/Index',2);
        }
    	/* status change */
    	$sc = I('get.sc',0,int);
    	$id = I('get.id',0,int);
    	if(IS_GET && ($sc == 1 && $id !=0)){
    		$this->doPlayMoney($id);
    	}
    	///dump($proxy_info);
    	/* status change end*/
    	$map['proxy_id'] = $proxy_info['id'];
    	//分类选择
    	if(I('get.status',"all") == 1){
    		$map['status'] = 'Y';
    		$cstaus = 1;
    	}elseif(I('get.status',"all") == 2){
    		$map['status'] = 'N';
    		$cstaus = 2;
    	}else{
    		$cstaus = 3;
    	}
    	$count      = $order_cash->where($map)->count();
    	$Page       = new \Think\Page($count,50);
    	$show       = $Page->show();
    	$list       = $order_cash->where($map)->order('add_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
    	$this->assign('list',$list);
    	$this->assign('cstaus',$cstaus);
    	$this->assign('page',$show);
    	$this->display();
    }
    /*
    *   代理消费统计首页
    */
    public function ranking(){

        $ordernum = 0;
        $turnover =0;
        $Settlement =0;
        //用户查询
        //  时间筛选 0 本月  1今日  2昨日
        $status = I('get.status',0,'int');
        // 根据条件获取时间戳
        $today     = strtotime(date("Y-m-d"));
        $yesday    = strtotime('-1 day');       
        $month1st  = strtotime(date("Y-m-1")); 
        $thmonth   = strtotime(date("Y-m-1"));
        $last      = strtotime(date('Y-m-01',strtotime('-1 month')));     //  上月第一天
        //  根据status 获取数据条件 
        if($status ==  '1'){
            // 获取今日的数据   create_time 大于今日凌晨  小于现在
            $start = $today;
            $end   = time();                        
        }elseif ($status == '2') {
            //  获取昨日的数据数据
            $start = $yesday;
            $end   = $today;                        
        }else{
            // 获取本月的数据
            $start = $month1st;
            $end   = time();
        }
        //  获取代理的一些基本数据
        //  获取当前用户的pid
        $pid = $this->user->where(array('id'=>$_SESSION["Auth_user"]))->getField('pid');            

        // 计算当前代理本月的预估收入  时间，用户id，订单状态          
        $foreIncome   = $this->getData($thmonth, time(), $pid, 0);
      
        // 计算当前代理本月的结算收入  
        $settIncome   = $this->getData($month1st, time(), $pid, 1); 

        // 计算当前代理上月的结算收入
        $settIncLa   = $this->getData($last, $month1st, $pid, 1);

        //die;
        $list = array();

        // 获取当前用户的的id和子代理pid
        $child_ids = $this->getUserchild($_SESSION["Auth_user"]);
        if($child_ids){
            $child_pids = $this->getUserChildPid($child_ids); //string
        }
        //子代理数据
        if($child_ids){ //string
            $child_pids = $this->getUserChildPid($child_ids); //string
            if($child_pids){
                $where = array('id' => array('in', $child_ids) );
                $users = $this->user->where($where)->field('id,pid,mobile,username,realname')->select(); 
                foreach ($users as $k => $v) {
                	$list[$k]['mobile']   = $v['mobile'];
                	$list[$k]['username']   = $v['username'];
                    $list[$k]['realname']   = $v['realname'];
                    $list[$k]['id']         = $v['id'];
                    $list[$k]['pid']        = $v['pid'];                    
                    $list[$k]['ygsr']       = $this->getData($start, $end, $v['pid'], 0);      //本月预估
                    $list[$k]['jssr']       = $this->getData($start, $end, $v['pid'], 1);      //本月结算
                    $list[$k]['syjssr']     = $this->getData($last, $month1st, $v['pid'], 1);  //上月结算
                }
            }
        }             
                
        //今天的总订单 与 总金额数据
        $this->assign('turnover',$foreIncome);          //   当前代理本月的预估收入    
        $this->assign('ordernum',$settIncome);          //   当前代理本月的结算收入    
        $this->assign('Settlement',$settIncLa);         //   当前代理上月的结算收入     
        $this->assign('status',$status);                //   状态
        $this->assign('list',$list);                    //   子代理用户数据
        $this->display();
    }
    //查询顶级用户ID
    private  function Get_myParent($id){
    	$where['id'] = $id;
    	$user = M('user')->where($where)->find();
    
    	if($user['parentid'] != 0){
    		$user = $this->Get_myParent($user['parentid']);
    	}
    
    	return $user;
    
    }
    //获得用户PID
    protected   function getUserChildPid($cids){
        $where = array('id' => array('in',$cids));
        $result = $this->user->where($where)->getField("pid",true);
        //dump($result);
        $pids = implode(",",$result);
        return $pids;
    } 

    //获得用户ID
    protected function getUserchild($id){       
        $map['ParentID'] = $id;
        $result = $this->user->where($map)->getField("id",true);
        $ids = implode(",",$result);
        return $ids;
    }

    /*
    *   获取代理收入情况
    */
    public function getData($start, $end, $id, $js = 0)
    {       
        if($js == 0){
            $where = array(
                '_string'                     => "payStatus = '订单结算' OR payStatus = '订单付款'" ,
                'UNIX_TIMESTAMP(create_time)' => array('between',array($start,$end)),
                'pid'=>$id
            );        
        }else{
            $where = array(
                'payStatus'=>'订单结算',
                'UNIX_TIMESTAMP(earningTime)' => array('between',array($start,$end)),
                'pid'=>$id
            );            
        }

        $res = $this->order->where($where)->sum(fee);
        if(!$res){
            $res = 0;
        }
        return $res;
    }

}