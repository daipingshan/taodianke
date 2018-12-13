<?php
namespace AppAdmin\Controller;
class FinanceController extends CommonController {
    private $PID = "mm_121610813_22448587_79916379";
    private $proxy_id = 1;
    public function __construct() {
        parent:: __construct();
        $this->user =  M('user');
        $this->proxy_ratio =  M('user_proxy_ratio');
        $this->order =  M('order_data');

    }
    public function do_payment(){
        $crsh_User = M('order_cash');
        $id = I('get.id',0,int);
        if( IS_GET && ( $id !=0 ) ){
            $data['status'] = 'Y';
            $data['cash_time'] = date("Y-m-d h:i:s");
            if($crsh_User->where('id='.$id)->save($data)){
                $this->success('操作成功',"/AppAdmin/Finance/index");
                exit;
            }
        }else{
            $this->success('非法操作',"/AppAdmin/Finance/index");
        }

    }
    public function index(){
        //旧表
        $crsh_User = M('order_cash');
        //新表
        $order_withdraw = M('order_withdraw');
        $proxy_list = S('proxy_list');
        if(!$proxy_list){
            $proxy_list = M('proxy')->index('id')->select();
            S('proxy_list',$proxy_list);
        }
        //显示代理申请
        $show_proxy_id = I('show_proxy_id',1,'int');
        if( 0 != $show_proxy_id ){
            $map['proxy_id'] = $show_proxy_id;
        }
        //分类选择
        if(I('get.status',"all") == 1){
            $map['status'] = 'Y';
            $cstaus = 1;
        }elseif(I('get.status',"all") == 2){
            $map['status'] = 'N';
            $cstaus = 2;
        }else{
            $cstaus = 2;
        }

        $count      = $crsh_User->where($map)->count();
        $Page       = new \Think\Page($count,50);
        $show       = $Page->show();
        //9月份删除$list 表

        $list = $crsh_User->where($map)->order('add_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $list_new = $order_withdraw->where($map)->order('add_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        $list = array_merge($list,$list_new);

        foreach ($list as $k => &$v){
            $v['proxy_name'] = $proxy_list[$v['proxy_id']]['name'];
            $v['proxy_id']   = $proxy_list[$v['proxy_id']]['id'];
        }
        unset($v);
        //dump($list);
        $this->assign('list',$list);
        $this->assign('cstaus',$cstaus);
        $this->assign('page',$show);
        $this->assign('proxy_list',$proxy_list);
        $this->assign('show_proxy_id',$show_proxy_id);
        $this->display();
    }
    public function ranking(){

        //用户查询
        $user = M('user');
        $order = M("order_data");
        $status = I('get.status',0,'int');
        /* 时间 */
        $yesterday = strtotime('yesterday');
        $now         = strtotime('now');
        $today       = strtotime(date("Y-m-d 23:59:59") );
        // 一周，一个月
        $week = strtotime('-1 week');
        $month = strtotime('-1 month');

        if($status == 6){
            $MinTime  = I('get.start',0,'htmlspecialchars');
            $MaxTime = I('get.end',0,'htmlspecialchars');
            $start = strtotime($MinTime);
            $end = (strtotime($MaxTime) + 86399);
        }else{
            $start = $yesterday;
            $end = $now;
        }
        //dump($yesterday);
        $where = array(
            'payStatus'=>'订单结算',
            'UNIX_TIMESTAMP(earningTime)' => array('between',array($start,$end))
        );
        $order = $this->order->where($where)->select();

        //所有PID
        foreach($order as $v){
            $pids[] = $v['pid'];
        }

        //统计
        foreach ($pids as $str) {
            @$NumPId[$str] = $NumPId[$str] + 1;
            $ordernum = $ordernum + 1;
        }
        $this->assign("ordernum",$ordernum);
        //去重
        $pids = implode("','",array_unique($pids));
        $pids =  "('".$pids."')";

        $countSQL= "SELECT count(id) as count FROM ytt_user WHERE pid in $pids";

        $Model = new \Think\Model();
        $count = $Model->query($countSQL);// 查询满足要求的总记录数
        $Page  = new \Think\Page($count[0]['count'],50);// 实例化分页类 传入总记录数和每页显示的记录数(25)


        $userSQL = "SELECT mobile,username,id,realname,pid,bank_account
							    FROM ytt_user
							    WHERE  pid in $pids
							    LIMIT $Page->firstRow,$Page->listRows";
        $show  = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $agent =  $Model->query($userSQL);

        foreach ($agent as $k => $v) {

            // 收益总额（order中的fee的总和）
            $fee = $this->Gettlement_data($v['pid'],$start,$end);
            $income = $this->proxy_income($v['id'],$fee);

            $agent[$k]['fee'] = $fee;
            $agent[$k]['proxy_income'] 	= $income['proxy_income'];
            $agent[$k]['my_income'] = $income['my_income'];
            $agent[$k]['my_chil_income'] = 0;
            $agent[$k]['dip']					= $income['dip'];
            // 分成金额（收益总额*分成比例）
            //子代理情况
            if($cUser = $this->proxy_child($v['id'])){
                foreach($cUser as $ck => $cv){
                    $cfee = $this->Gettlement_data($cv['pid'],$start,$end);
                    $child_income = $this->proxy_income($cv['id'],$cfee);
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
        $agent = $this->columnSort($agent,'my_income');
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
    //查询用户预估收入集合
    protected function Payment_data($pid,$Startime,$Endtime){
        $order = M('order_data');
        if(is_int($Startime) && is_int($Endtime)){
            $sql = "SELECT  sum(fee) as allfee
    					FROM ytt_order_data
						WHERE UNIX_TIMESTAMP(create_time) > ".$Startime."
						AND UNIX_TIMESTAMP(create_time) < ".$Endtime."
					    AND payStatus IN ('订单付款','订单结算')
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
    //查询用户结算收入集合
    protected function Gettlement_data($pid,$Startime,$Endtime){
        $order = M('order_data');
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
    //查询顶级用户ID
    private  function Get_myParent($id){
        $where['id'] = $id;
        $user = M('user')->where($where)->find();

        if($user['parentid'] != 0){
            $user = $this->Get_myParent($user['parentid']);
        }

        return $user;

    }
    //数组排序
    function columnSort($unsorted, $column) {
        $sorted = $unsorted;
        for ($i=0; $i < sizeof($sorted)-1; $i++) {
            for ($j=0; $j<sizeof($sorted)-1-$i; $j++)
                if ($sorted[$j][$column] < $sorted[$j+1][$column]) {
                    $tmp = $sorted[$j];
                    $sorted[$j] = $sorted[$j+1];
                    $sorted[$j+1] = $tmp;
                }
        }
        return $sorted;
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