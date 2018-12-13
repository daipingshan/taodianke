<?php
namespace Proxy\Controller;

class UserController extends CommonController {


	public function __construct() {
        parent:: __construct();
        $this->user =  M('user');
        $this->proxy_ratio =  M('user_proxy_ratio');
        $this->order =  M('order_data');
        
    }
    public function myInfo(){
	    $map['id'] = $_SESSION['Auth_user'];
	    $info = $this->user->where($map)->find();
	    $this->assign('userinfo',$info);
	    $this->display();
	    if(IS_POST){
                $data['email'] =        I('post.email',null,'email');
                $data['username'] =     I('post.usernamex',null,'string');
                $data['real_name'] =    I('post.real_name',null,'string');
                $data['qq'] =           I('post.qq',null,'int');

                $pwd  =trim(I('post.pwd',null,'string'));
                if($pwd != '' || $pwd != NULL){
                    $data['password'] =  md5($pwd);
                }
                if($this->user->where($map)->save($data)){
                    redirect(U('/Proxy/User/index'));
                }
        }
    }

    /*
	*	代理后台用戶管理首頁
	*
    */
    public function index(){

       	$map['ParentID'] = $_SESSION['Auth_user'];       	
	 	//echo $_SESSION['Auth_user'];
        $map['pid'] = array('NEQ','');
       	$count      = $this->user->where($map)->count();
       	$Page       = new \Think\Page($count,50);
       	$show       = $Page->show();
        $agent = $this->user->where($map)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$now = strtotime('now');
        $start = strtotime(I('get.start',0,'htmlspecialchars'));
        $end  = I('get.end',0,'htmlspecialchars');
       
		if($end == 0){
			$end = $now;
			$start = strtotime('-1 day');
		}else{
			$end =  strtotime(date("Y-m-d 23:59:59",strtotime($end)));
		}


        foreach ($agent as $k => $v) {

        	// 收益总额（order中的fee的总和）
        	$fee = $this->Gettlement_data($v['pid'],$start,$end);
        	$income = $this->proxy_income($v['id'],$fee);
       
        	$agent[$k]['fee']           = $fee;
        	$agent[$k]['proxy_income'] 	= $income['proxy_income'];
        	$agent[$k]['my_income']     = $income['my_income'];
        	$agent[$k]['my_chil_income']= 0;
  			$agent[$k]['dip']			= $income['dip'];
        	// 分成金额（收益总额*分成比例）
        	//子代理情况
        	if($cUser = $this->proxy_child($v['id'])){
        			foreach($cUser as $ck => $cv){
        				$cfee                   = $this->Gettlement_data($cv['pid'],$start,$end);
        				$child_income           = $this->proxy_income($cv['id'],$cfee);
        				$agent[$k]['child'][$ck] = $cv;
        				$agent[$k]['child'][$ck]['fee'] = $cfee;
        				$agent[$k]['child'][$ck]['proxy_income'] = $child_income['proxy_income'];
        				
        				if($income['dip'] ==0){
        					$agent[$k]['child'][$ck]['my_income'] = 0;
        				}else{
        					$agent[$k]['my_chil_income']  += round($child_income['my_income']*$income['dip']/100, 2);
        				}
        				$agent[$k]['child'][$ck]['dip']		=  $child_income['dip'];
        			}
        	}
        	//计算下级代理带来的收入总和，乘以分红比例
        	$cres = $this->proxy_income($v['id'],$agent[$k]['my_chil_income']);
        	if($cres['my_income'] !=0 ){
        		$agent[$k]['my_income'] 	= $income['my_income'] +$cres['my_income'];
        	}
        }
 
		//dump($agent);
        // 編輯用戶信息
		if(IS_POST && $_GET['action'] == 1){

		   	$uid = I('post.uid',0,'int');	//	總代理id
		   	$cid = I('post.cid',0,'int');	//	子代理id

		   	$data['dip'] = round(I('post.dip',0,'floatval'), 2);	//	分成比例
		   	$data['remarks'] = I('post.remarks',NULL,'trim');	//	備註
		 
		   	$where = array('uid' => $uid, 'cid'=> $cid);
		   	$res = $this->proxy_ratio->where($where)->find();
	  		if($res){
	  			$edit = $this->proxy_ratio->where($where)->save($data);	  			
	  		}else{
	  			$data['uid'] =  $uid;
	  			$data['cid'] =  $cid;
	  			$edit = $this->proxy_ratio->add($data);
	  		}
	  		if(!$edit){
	  			$this->display('/Public/error');
	  		}
	   	}

        $this->assign('uid',$_SESSION['Auth_user']);// 赋值数据集
	    $this->assign('list',$agent);// 赋值数据集
	    $this->assign('count',$count);// 赋值数据集
	    $this->assign('page',$show);// 赋值分页输出	   		   
	    $this->assign('start_time',date("Y-m-d H:i:s",$start));	   		   
	    $this->assign('end_time',date("Y-m-d H:i:s",$end));	
	    $this->assign('start',$start);
	    $this->assign('end',$end);
		$this->display();
	}
	/*
	 * 设置验证码。*/
	public function set_xcode(){
	    $msg  = '您正在修改支付宝收款信息，您的验证码为【';
	    $code = rand(1000,9999);
	    $msg .= $code."】,如果本次操作";
        $sms  = new \Common\Org\sendSms();
        $mobile = "15991606450";
        $data = $sms->sendMsg( $mobile , "$msg" );
        if( 1 == $data['status'] ){
            $time = 30 * 30;
            $_SESSION['xcode'] = $code;
            $_SESSION['xcode_time'];
        }
	    //没写完  功能暂时取消
    }
    private function doAlipayInfo($user_id,$alipay_acc){
//	    $map['id'] = $user_id;
//
//	    $this->user->where($map)->save($save_data);
    }
	/*
	 * 支付宝信息修改*/
	public function alipayInfo(){

	    $this->display();
    }
	/*
		我的下线
	*/
	public function proxy_child($id){
			$map['ParentID'] = $id;
			if(!$rel = $this->user->where($map)->select()){
				return false;
			}
			return $rel;
	}
	/*
	 * 	下线盈利、我的提成、分成比例
	 * */
	public function proxy_income($id,$allfee){

		$map['id'] = $id;
	
		$user = $this->user->where($map)->getField('id,ParentID,pid',true);
		// 查子代理的分成比例
		$where = array('uid' => $user[$id]['parentid'], 'cid' =>$id);
		$dip = $this->proxy_ratio->where($where)->getField('dip');

		if(!$dip || $dip == 0){
				$agent['proxy_income'] = $allfee;
				$agent['my_income'] = 0;
				$agent['dip'] = 100;
		}else {
				//上级利润
				$agent['proxy_income'] = round($allfee*$dip/100, 2);
				//用户利润
				$agent['my_income'] = ($allfee - $agent['proxy_income']);
				$agent['dip'] = $dip;
		}
		return $agent;
	}
    //查询用户结算收入集合
    protected function Gettlement_data($pid,$Startime,$Endtime){
    	$pid= trim($pid);
    	$order = $this->order;
    	if(is_int($Startime) && is_int($Endtime)){
    		$sql = "SELECT  sum(fee) as allfee
    					FROM ytt_order_data
						WHERE UNIX_TIMESTAMP(earningTime) > ".$Startime."
						AND UNIX_TIMESTAMP(earningTime) < ".$Endtime."
						AND earningTime != ''
					    AND payStatus = '订单结算' 
						AND pid = '".$pid."'";
    		//echo $sql;
    		$Model = new \Think\Model();
    		$result = $Model->query($sql);
    		if($result[0]['allfee'] == NULL){
    			return 0;
    		}
    		return $result[0]['allfee'];
    		 
    	}else{
    		return false;
    	}
    
    }
	/*
	*	编辑代理信息
	*/
	public function proxy_ratio(){
		$this->uid = I('get.uid',0,'int');
		$this->cid = I('get.cid',0,'int');
		$this->tel = I('get.tel',0,'string');
		$this->name =I('get.real_name',0,'string');
	 	$where = array('uid' => $this->uid, 'cid' => $this->cid);
		$this->ratiodata = $this->proxy_ratio->where($where)->find();
		$this->display();
	}
	
}