<?php

namespace Admin\Controller;

/**
 * 财务管理
 */
class FinanceController extends CommonController {

    private $order_status = array(
        'alipay'=>'支付宝支付',
        'wapalipay'=>'网页版支付宝支付',  
        'wapwechatp'=>'微信公众号支付',
        'unionpay'=>'银联支付',
        'wxpay'=>'微信支付',
    );
    /**
     * @var bool 是否验证uid
     */
    public function index() {

        $search = array(
            'query' => urldecode(I('get.query', '', 'trim')),
            'mid' => I('get.mid', '', 'trim'),
        );

        $where = array();
        if (trim($search['mid'])) {
            $where['settlement.mid'] = $search['mid'];
        }
        
        if (trim($search['query'])) {
            $where['_string'] = "merchant.username like '%{$search['query']}%' or merchant.mobile like '%{$search['query']}%'";
        }


        $settlement = M('settlement');
        $count = $settlement->where($where)->join('inner join merchant on merchant.id=settlement.mid')->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $field = array(
            'settlement.id'=>'id',
            'settlement.origin'=>'origin',
            'settlement.mid'=>'mid',
            'settlement.create_time'=>'create_time',
            'settlement.set_time'=>'set_time',
            'settlement.status'=>'status',
            'merchant.username'=>'m_name',
            'merchant.cid'=>'cid',
            'merchant.lirun'=>'lirun',
        );
        $list = $settlement->where($where)->field($field)->limit($limit)->order('settlement.create_time desc ')
                ->join('inner join merchant on merchant.id=settlement.mid')
                ->select();

        foreach ($list as &$v) {
            $shen=self::getAllCitie($v['cid']);
            $v['fencheng'] = $v['origin'] - $v['origin']*$v['lirun']/100;
            $v['yongjin'] =   $v['origin']*$v['lirun']/100;
            $v['city']=self::SetCityName($shen[0]['fid'],$shen[0]['name']);
        }
        $data = array(
            'pages' => $page->show(),
            'list' => $list,
            'search' => $search,
        );
        $this->assign($data);

        $this->display();
    }
    public function SetCityName($id,$name) {
        $data = self::getAllCitie($id); //取值到市
        if ($data) {
            foreach ($data as $row) {
                $data1 = self::getAllCitie($row['fid']); //取值到区
                if ($data1) {
                    foreach ($data1 as $val) {
                        $data2 = self::getAllCitie($val['fid']); //取值到小区
                        foreach ($data2 as $vl) {
                            $shen=$vl['name'] . $val['name'] . $row['name'].$name;
                        }
                    }
                }
            }
        }
        return $shen;

    }
    /**
     * 获取城市列表
     */
    private  function getAllCities($id = 0) {
        $where['fid'] = $id;
        $data = M('category')->field('id,name,fid')->where($where)->order('sort desc')->select();
        return $data;
    }
    private  function getAllCitie($id = 0) {
        $where['id'] = $id;
        $data = M('category')->field('id,name,fid')->where($where)->order('sort desc')->select();
        return $data;
    }
    /**
        *  结算操作
        */
    public function settlement(){
        $sid = I('get.sid',0,'intval');
        
        if(!$sid){
            $this->ajaxReturn(array('code'=>-1,'error'=>'结算id不能为空！'));
        }
        
        $where = array(
            'id'=>$sid
        );
        $settlement = M('settlement');
        $settlement_info = $settlement->where($where)->field('origin,status')->find();
        if(!$settlement_info){
            $this->ajaxReturn(array('code'=>-1,'error'=>'结算信息不存在！'));
        }
        if(isset($settlement_info['origin']) && $settlement_info['origin']<=0){
            $this->ajaxReturn(array('code'=>-1,'error'=>'结算金额大于0才能结算！'));
        }
        if(isset($settlement_info['status']) && intval($settlement_info['status']) == 1){
            $this->ajaxReturn(array('code'=>-1,'error'=>'该记录已经结算，不能重新结算，建议刷新下当前页面！'));
        }
        
        $where = array(
            'id'=>$sid
        );
        $res = $settlement->where($where)->save(array('status'=>1,'set_time'=>time()));
        if(!$res){
             $this->ajaxReturn(array('code'=>-1,'error'=>'结算失败！'));
        }
        echo "<script>parent.location.reload();</script>"; 
        $this->ajaxReturn(array('code'=>0,'success'=>'结算成功！'));
    }
    
    /**
    *  结算操作
    */
    public function settlements(){
        $sid = I('get.sid',0,'intval');
        
        $settlement = M('settlement');
        if(IS_POST){
            $sid = I('post.sid',0,'intval');
            $where = array(
                'id'=>$sid
            );
            $settlement = M('settlement');
            $settlement_info = $settlement->where($where)->field('origin,status')->find();
            if(!$settlement_info){                              
                 echo "<script>alert('结算信息不存在！');parent.location.reload();</script>"; 
                 die;
            }
            if(isset($settlement_info['origin']) && $settlement_info['origin']<=0){              
                 echo "<script>alert('结算金额大于0才能结算！');parent.location.reload();</script>"; 
                 die;
            }
            if(isset($settlement_info['status']) && intval($settlement_info['status']) == 1){
                echo "<script>alert('该记录已经结算，不能重新结算，建议刷新下当前页面！');parent.location.reload();</script>";
                die;
            }
            $res = $settlement->where($where)->save(array('status'=>1,'set_time'=>time()));
            if(!$res){
              
                 echo "<script>alert('结算失败！');parent.location.reload();</script>"; 
                 die;
            }              
            echo "<script>alert('操作成功');parent.location.reload();</script>";            
            die();
        }
        if(!$sid){
            $this->redirect_message(U('Finance/index'), array('error' => '结算id不能为空！'));           
        }
        
        $where = array(
            'id'=>$sid
        );
    
        $set_info = $settlement->where($where)->find();  

        $merchant = M('merchant');
        $mwhere = array(
            'id' => $set_info['mid']
        );
        $merchant_info = $merchant->where($mwhere)->field('username,agent_id,bank_no,bank_name,bank_user,alipay,lirun')->find();
        
        if(isset($set_info['details']) && trim($set_info['details'])){
            $set_info['details'] = json_decode($set_info['details'],true);
        }
        $details = $set_info['details'];
       if($details){
            $okorder = array();          
            foreach ($details as &$v) {
                $id=M('orders')->where(array('order_no'=>$v['order_no'],'origin'=>$v['origin']))->getField('id');

                $ogwhere = array(
                    'order_id' =>$id,
                );
                $order_goods=M('orders_goods')->where($ogwhere)->select();
                
                foreach ($order_goods as  $m) {
                    $goodswhere = array(
                        'id' => $m['goods_id'],
                    );                    
                    $goods=M('goods')->where($goodswhere)->select();
                    $okorder = array(); 
                    foreach ($goods as &$n) {  
                        $ticheng = $n['sell_price']*$m['num'] - $n['purchase_price']*$m['num'];
                        $zong = $n['ticheng'] = $ticheng*$merchant_info['lirun']/100; 
                        $sum += $zong; 
                        if($sum < 0){
                            $sum_fencheng = 0;
                        }else{
                            $sum_fencheng = $sum;
                        }
                    }
                }  

            }                     
        }
                
        $agent_id = $merchant_info['agent_id'];
        $agent = M('agent');
        $awhere = array(
            'id'=>$agent_id
        );
        $agent_info = $agent->where($awhere)->field('username,bank_no,bank_name,bank_user')->find();
        
        $sumticheng=M('settlement')->where($where)->setField('ticheng',$sum);
        $data = array(
            'sid' => $sid,
            'm_origin' => $set_info['origin']-$sum_fencheng,
            'status' => $set_info['remark'],
            'm_user' => $merchant_info['username'],
            'm_bank_no' => $merchant_info['bank_no'],
            'm_bank_name' => $merchant_info['bank_name'],
            'm_alipay' => $merchant_info['alipay'],
            'm_bank_user' => $merchant_info['bank_user'],
            'a_user' => $agent_info['username'],
            'a_bank_no' => $agent_info['bank_no'],
            'a_bank_name' => $agent_info['bank_name'],
            'a_bank_user' => $agent_info['bank_user'],
            'a_ticheng' => $sum_fencheng,
        );
        $this->assign($data);
        $this->display();
    }

    public function info_view(){
         $sid = I('get.sid',0,'intval');
         
         $settlement_info = array();
         if($sid){
             $settlement = M('settlement');
             $settlement_info = $settlement->where(array('id'=>$sid))->find();
             if(isset($settlement_info['mid']) && trim($settlement_info['mid'])){
                 $merchant_info= M('merchant')->where(array('id'=>$settlement_info['mid']))->field('username')->find();
                 if(isset($merchant_info['username']) && trim($merchant_info['username'])){
                     $settlement_info['m_name'] = $merchant_info['username'];
                 }
             }
             if(isset($settlement_info['details']) && trim($settlement_info['details'])){
                 $settlement_info['details'] = json_decode($settlement_info['details'],true);
             }
         }
        $merchant_info= M('merchant')->where(array('id'=>$settlement_info['mid']))->find();
        if($settlement_info['details']){
            foreach ($settlement_info['details'] as &$v) {
                 $id=M('orders')->where(array('order_no'=>$v['order_no'],'origin'=>$v['origin']))->getField('id');

                $ogwhere = array(
                    'order_id' =>$id,
                );
                $order_goods=M('orders_goods')->where($ogwhere)->select();
                
                
                foreach ($order_goods as  $m) {
                    $goodswhere = array(
                        'id' => $m['goods_id'],
                    );                    
                    $goods=M('goods')->where($goodswhere)->select();
                    
                    $ticheng = 0;
                    $okorder = array();
                    foreach ($goods as $k=> &$n) {  
                        
                        $ticheng = ($n['sell_price']-$n['purchase_price'])*$m['num'];
                        $zong = $n['ticheng'] = $ticheng*$merchant_info['lirun']/100; 
                        $sum += $ticheng;
                        if($sum < 0){
                            $fencheng = 0;
                        }else{
                            $fencheng = $sum*$merchant_info['lirun']/100;
                        }
                    }
                    
                }

                $type=M('orders')->where(array('order_no'=>$v['order_no'],'origin'=>$v['origin']))->field('pay_type')->find();
                $v['type']=$this->order_status[$type['pay_type']];
                $v['ding']= $v['origin']*100;
            }

        }
        $data = array(
            'data' => $settlement_info,
            'sum' => $sum,
            'bili' => $merchant_info['lirun'],
            'fencheng' => $fencheng,
        );

        $this->assign($data);
        $this->display();
    }

}
