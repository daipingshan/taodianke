<?php

namespace Guoguo\Controller;
/**
 * 订单管理
 */
class OrderController extends CommonController {

    private $order_status = array(
        '0'=>'未支付',
        '1'=>'待发货',
        '2'=>'配送中',
        '3'=>'已配送',
        '4'=>'已完成',
        '5'=>'已拒绝',
        '6'=>'已取消',  
        '7'=>'退款（退货）中',
        '8'=>'客户端不显示',  
        '9'=>'待评价',  
    );
    protected $reqnum = 52;
    public function index() {
        self::GetOrder();
    }
    public function Fxg() {
        self::Getfxg();
    }
    /***
     * 放心购订单情况
     */
    public function Getfxg(){
        $current_month = date('Y-m-d');
        $ntime=strtotime($current_month);
        $etime=strtotime($current_month)+86399;
        $model=M('ytt_gg_fxg');
        $where=array(
            // 'UNIX_TIMESTAMP('')'=>array(),
        );
        $uid= $this->uid;
        $user=M('user')->where(array('id'=>$uid))->find();
        $activities_where="UNIX_TIMESTAMP(pay_time)>=".$ntime." and UNIX_TIMESTAMP(pay_time)<=".$etime." AND (order_status='订单完成' or order_status='订单付款' or order_status='结算完成') ";
        $data=$model->where($activities_where)->order('pay_time desc')->select();
        foreach($data as $ke=>$vad){
            if($vad['name']=='刘红婵' || $vad['name']=='孙玉'){
                unset($data[$ke]);
            }
        }
        $data=array_values($data);
        $tadaycout=$model->where($activities_where)->field('name,sum(income) as cout')->group('name')->order('cout desc')->select();
        $tadaycoutsum=0;
        foreach($tadaycout as $key=>$vad){
            if($user['name']==$vad['name']){
                $user['money']=$tadaycout[$key]['cout'];
            }
            if($vad['name']=='刘红婵' || $vad['name']=='孙玉'){
                unset($tadaycout[$key]);
            }
            $tadaycoutsum=$tadaycoutsum+$tadaycout[$key]['cout'];
        }
        $tadaycout=array_values($tadaycout);
        //
        // var_dump($data);exit;
        //取昨天时间
        $sdate=date("Y-m-d",strtotime("-1 day"));
        $ntime=strtotime($sdate);
        $etime=strtotime($sdate)+86399;
        $where="UNIX_TIMESTAMP(pay_time)>=".$ntime." and UNIX_TIMESTAMP(pay_time)<=".$etime."   AND (order_status='订单完成' or order_status='订单付款' or order_status='结算完成')";
        $cout=$model->where($where)->field('name,sum(income) as cout')->group('name')->order('cout desc')->select();
        foreach($cout as $ke=>&$vcad){
            if($vcad['name']=='刘红婵' || $vcad['name']=='孙玉'){
                unset($cout[$ke]);
            }
        }
        $cout=array_values($cout);
        //
        $currentt_month = date('Y-m-01');
        $mtime=strtotime($currentt_month);

        $monthwhere="UNIX_TIMESTAMP(pay_time)>=".$mtime." and UNIX_TIMESTAMP(pay_time)<=".time()."   AND (order_status='订单完成' or order_status='订单付款' or order_status='结算完成')";
        $monthcout=$model->where($monthwhere)->field('name,sum(income) as cout')->group('name')->order('cout desc')->select();
        $monthcoutsum=0;
        foreach($monthcout as $key=>$vad){
            if($user['name']==$vad['name']){
                $user['mmoney']=$monthcout[$key]['cout'];
                $user['num']=$key+1;
            }
            $monthcoutsum=$monthcoutsum+$monthcout[$key]['cout'];
        }
        if($user['num']==1){
            $user['mmoney2']=round($user['mmoney']-$monthcout[$user['num']]['cout'],2);
        }else{
            $user['mmoney2']=round($user['mmoney']-$monthcout[$user['num']]['cout'],2);
            $user['mmoney1']=round($monthcout[$user['num']-2]['cout']-$user['mmoney'],2);

        }
        //var_dump($user);
        //var_dump($monthcout);
        // exit;
        //
        $this->assign('monthcout', $monthcout);
        $this->assign('data', $data);
        $this->assign('uid', $uid);
        $this->assign('cout', $cout);
        $this->assign('user', $user);
        $this->assign('tadaycou', $tadaycout);
        $this->assign('monthcoutsum', $monthcoutsum);
        $this->assign('tadaycoutsum', $tadaycoutsum);
        $this->assign('sdate', $sdate);
        $this->assign('nwtime', $current_month);
        $this->display();
    }
    public function GetOrder(){
        $current_month = date('Y-m-d');
        $ntime=strtotime($current_month);
        $etime=strtotime($current_month)+86399;
        $model=M('order_data');
        $where=array(
            // 'UNIX_TIMESTAMP('')'=>array(),
        );
        $uid= $this->uid;
        $user=M('user')->where(array('id'=>$uid))->find();
        if($uid==0){
            $activities_where="UNIX_TIMESTAMP(create_time)>=".$ntime." and UNIX_TIMESTAMP(create_time)<=".$etime." AND payStatus='订单付款'";
        }else{
            $activities_where="UNIX_TIMESTAMP(create_time)>=".$ntime." and UNIX_TIMESTAMP(create_time)<=".$etime." AND payStatus='订单付款' AND (username='何思敏' OR username='赖伟健')";
        }

        $data=$model->where($activities_where)->order('create_time desc')->select();
        foreach($data as $ke=>$vad){
            if($vad['username']=='刘红婵' || $vad['username']=='孙玉'){
                unset($data[$ke]);
            }
        }
        $data=array_values($data);
        $tadaycout=$model->where($activities_where)->field('username,sum(fee) as cout')->group('username')->order('cout desc')->select();
        $tadaycoutsum=0;
        foreach($tadaycout as $key=>$vad){
            if($user['name']==$vad['username']){
                $user['money']=$tadaycout[$key]['cout'];
            }
            if($vad['username']=='刘红婵' || $vad['username']=='孙玉'){
                unset($tadaycout[$key]);
            }
            $tadaycoutsum=$tadaycoutsum+$tadaycout[$key]['cout'];
        }
        $tadaycout=array_values($tadaycout);
        //
        // var_dump($data);exit;
        //取昨天时间
        $sdate=date("Y-m-d",strtotime("-1 day"));
        $ntime=strtotime($sdate);
        $etime=strtotime($sdate)+86399;
        if($uid==0){
            $where="UNIX_TIMESTAMP(create_time)>=".$ntime." and UNIX_TIMESTAMP(create_time)<=".$etime."  AND payStatus='订单付款'";
        }else{
            $where="UNIX_TIMESTAMP(create_time)>=".$ntime." and UNIX_TIMESTAMP(create_time)<=".$etime."  AND payStatus='订单付款' AND (username='何思敏' OR username='赖伟健')";
        }

        $cout=$model->where($where)->field('username,sum(fee) as cout')->group('username')->order('cout desc')->select();
        foreach($cout as $ke=>&$vcad){
            if($vcad['username']=='刘红婵' || $vcad['username']=='孙玉'){
                unset($cout[$ke]);
            }
        }
        $cout=array_values($cout);
        //
        $currentt_month = date('Y-m-01');
        $mtime=strtotime($currentt_month);

        $monthwhere="UNIX_TIMESTAMP(create_time)>=".$mtime." and UNIX_TIMESTAMP(create_time)<=".time()."  AND payStatus='订单付款'";
        $monthcout=$model->where($monthwhere)->field('username,sum(fee) as cout')->group('username')->order('cout desc')->select();
        $monthcoutsum=0;
        foreach($monthcout as $key=>$vad){
            if($user['name']==$vad['username']){
                $user['mmoney']=$monthcout[$key]['cout'];
                $user['num']=$key+1;
            }
           $monthcoutsum=$monthcoutsum+$monthcout[$key]['cout'];
        }
         if($user['num']==1){
            $user['mmoney2']=round($user['mmoney']-$monthcout[$user['num']]['cout'],2);
        }else{
            $user['mmoney2']=round($user['mmoney']-$monthcout[$user['num']]['cout'],2);
            $user['mmoney1']=round($monthcout[$user['num']-2]['cout']-$user['mmoney'],2);

        }
        //var_dump($user);
        //var_dump($monthcout);
       // exit;
        //
        $this->assign('monthcout', $monthcout);
        $this->assign('data', $data);
        $this->assign('uid', $uid);
        $this->assign('cout', $cout);
        $this->assign('user', $user);
        $this->assign('tadaycou', $tadaycout);
        $this->assign('monthcoutsum', $monthcoutsum);
        $this->assign('tadaycoutsum', $tadaycoutsum);
        $this->assign('sdate', $sdate);
        $this->assign('nwtime', $current_month);
        $this->display();
    }
    public function GetYsdOrder(){
        $uid= $this->uid;
        $user=M('user')->where(array('id'=>$uid))->find();
        $model=M('order_data');
        // var_dump($data);exit;
        //取昨天时间
        $sdate=date("Y-m-d",strtotime("-1 day"));
        $ntime=strtotime($sdate);
        $etime=strtotime($sdate)+86399;
        //$where="UNIX_TIMESTAMP(create_time)>=".$ntime." and UNIX_TIMESTAMP(create_time)<=".$etime."  AND payStatus='订单付款'";
        if($uid==0){
            $where="UNIX_TIMESTAMP(create_time)>=".$ntime." and UNIX_TIMESTAMP(create_time)<=".$etime."  AND payStatus='订单付款'";
        }else{
            $where="UNIX_TIMESTAMP(create_time)>=".$ntime." and UNIX_TIMESTAMP(create_time)<=".$etime."  AND payStatus='订单付款' AND (username='何思敏' OR username='赖伟健')";
        }
        $cout=$model->where($where)->field('username,sum(fee) as cout')->group('username')->order('cout desc')->select();
        foreach($cout as $key=>$vad){
            if($user['name']==$vad['username']){
                $user['money']=$cout[$key]['cout'];
            }
        }
        //
        $data=$model->where($where)->order('create_time desc')->select();
        $this->assign('data', $data);
        $this->assign('cout', $cout);
        $this->assign('user', $user);
        $this->assign('sdate', $sdate);
        $this->display();
    }
    /***
     *
     *
     * 放心购
     */
    public function GetYsdOrder1(){
        $uid= $this->uid;
        $user=M('user')->where(array('id'=>$uid))->find();
        $model=M('gg_fxg');
        // var_dump($data);exit;
        //取昨天时间
        $sdate=date("Y-m-d",strtotime("-1 day"));
        $ntime=strtotime($sdate);
        $etime=strtotime($sdate)+86399;
        $where="UNIX_TIMESTAMP(pay_time)>=".$ntime." and UNIX_TIMESTAMP(pay_time)<=".$etime."  AND (order_status='订单完成' or order_status='订单付款')";
        $cout=$model->where($where)->field('name,sum(income) as cout')->group('name')->order('cout desc')->select();
        foreach($cout as $key=>$vad){
            if($user['name']==$vad['name']){
                $user['money']=$cout[$key]['cout'];
            }
        }
        //
        $data=$model->where($where)->order('pay_time desc')->select();
        $this->assign('data', $data);
        $this->assign('cout', $cout);
        $this->assign('user', $user);
        $this->assign('sdate', $sdate);
        $this->display();
    }
    public function GetmyOrder(){
        $uid= $this->uid;
        $user=M('user')->where(array('id'=>$uid))->find();
        $model=M('order_data');
        // var_dump($data);exit;
        //取昨天时间
        $sdate=date("Y-m-d");
        $ntime=strtotime($sdate);
        $etime=strtotime($sdate)+86399;
        if($uid==0){
            $where="UNIX_TIMESTAMP(create_time)>=".$ntime." and UNIX_TIMESTAMP(create_time)<=".$etime."  AND payStatus='订单付款'";
        }else{
            $where="UNIX_TIMESTAMP(create_time)>=".$ntime." and UNIX_TIMESTAMP(create_time)<=".$etime."  AND payStatus='订单付款' AND (username='何思敏' OR username='赖伟健')";
        }

        $cout=$model->where($where)->field('username,sum(fee) as cout')->group('username')->order('cout desc')->select();
        foreach($cout as $key=>$vad){
            if($user['name']==$vad['username']){
                $user['money']=$cout[$key]['cout'];
            }
        }
        //
        if($uid==0){
            $wwhere="UNIX_TIMESTAMP(create_time)>=".$ntime." and UNIX_TIMESTAMP(create_time)<=".$etime." and username='".$user['name']."'  AND payStatus='订单付款'";
        }else{
            $wwhere="UNIX_TIMESTAMP(create_time)>=".$ntime." and UNIX_TIMESTAMP(create_time)<=".$etime." and username='".$user['name']."'  AND payStatus='订单付款' AND (username='何思敏' OR username='赖伟健')";
        }

        $data=$model->where($wwhere)->order('create_time desc')->select();
        $this->assign('data', $data);
        $this->assign('cout', $cout);
        $this->assign('user', $user);
        $this->assign('sdate', $sdate);
        $this->display();
    }
    /***
     *
     *
     * 当天自己订单
     */
    public function GetmyOrder1(){
        $uid= $this->uid;
        $user=M('user')->where(array('id'=>$uid))->find();
        $model=M('gg_fxg');
        // var_dump($data);exit;
        //取昨天时间
        $sdate=date("Y-m-d");
        $ntime=strtotime($sdate);
        $etime=strtotime($sdate)+86399;
        $where="UNIX_TIMESTAMP(pay_time)>=".$ntime." and UNIX_TIMESTAMP(pay_time)<=".$etime."  AND (order_status='订单完成' or order_status='订单付款')";
        $cout=$model->where($where)->field('name,sum(income) as cout')->group('name')->order('cout desc')->select();
        foreach($cout as $key=>$vad){
            if($user['name']==$vad['name']){
                $user['money']=$cout[$key]['cout'];
            }
        }
        //
        $wwhere="UNIX_TIMESTAMP(pay_time)>=".$ntime." and UNIX_TIMESTAMP(pay_time)<=".$etime." and name='".$user['name']."'  AND (order_status='订单完成' or order_status='订单付款')";
        $data=$model->where($wwhere)->order('pay_time desc')->select();
        $this->assign('data', $data);
        $this->assign('cout', $cout);
        $this->assign('user', $user);
        $this->assign('sdate', $sdate);
        $this->display();
    }
    public function Toutiao(){
        if(IS_POST){
            $time =I('post.time');// $this->_get('time');
            $newid =I('post.newstype');
            $times =explode(' - ', $time);
            $begin_time=strtotime($times[0]);
            $end_time=strtotime($times[1]);
            $key=$time.'-'.$newid;
            if($begin_time>=$end_time){
                //$this->ajaxReturn(array('code' => -1, 'error' => '结束时间不能大于小于开始时间！'));
                $this->redirect_message(U('Order/Toutiao'), array('error' => '结束时间不能大于小于开始时间！'));
            }else{
                $pchace=S($key);
                if($pchace){
                    $data=$pchace;
                }else{
                    ini_set('default_socket_timeout',150);
                    $client = new \soapclient("http://j.fei-shou.com:801/Toutiao.asmx?WSDL");
                    $params = array(
                        'commzd' => $newid,
                        'stm'   => $end_time,
                        'setm'   => $begin_time
                    );
                    $data = array(
                        'parameters'   => $params
                    );
                    $result=$client->__soapCall('GetNews',$data);//$client->GetNews(array('parameters'=>array('commzd'=>$newid,'stm'=>$end_time,'setm'=>$begin_time)));//查询中国郑州的天气，返回的是一个结构体
                    $jg=$result->GetNewsResult;//显示结果
                    $data=@json_decode($jg, true);
                    S($key, $data,3600*2);
                }
                $this->assign('list', $data);
                $this->assign('dataa',$newid);
            }

            $this->assign('time',$time);
            $this->display();
        }else{
            $sdate=$sdate=date("Y-m-d H:i:s");
            $stime=date("Y-m-d H:i:s",strtotime("-2 hour"));
            $time=$stime.'-'.$sdate;

            $client = new \soapclient("http://j.fei-shou.com:801/Toutiao.asmx?WSDL");
            $result=$client->GetNewss();
            $jg=$result->GetNewssResult;//显示结果
            $data=@json_decode($jg, true);
            $this->assign('newid',$time);
            $this->assign('list', $data);
            $this->assign('time',$time);
            $this->display();
        }

    }
    public function cs(){
        try{
            //$url='http://j.fei-shou.com:801/Toutiao.asmx/GetNewss';
            //$var=file_get_contents($url);
            //var_dump($var);
          $client = new \SoapClient("http://j.fei-shou.com:801/Toutiao.asmx?WSDL");

            $params = array(
                'param1' => 1,
                'param2'   => 2
            );
            $data = array(
                'parameters'   => $params
            );
            $result = $client->__soapCall('HelloWorld',$data);
            var_dump($result->HelloWorldResult);
        }catch(Exception $e){

        }
    }
    public function news($url){
        //header("Content-type: text/html; charset=utf-8");
        if($url){

        }else{
            $url = I('get.type');
            $url = 'https://temai.snssdk.com/article/feed/index?id=2142501';
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);//若PHP编译时不带openssl则需要此行
        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        // 4. 释放curl句柄
        curl_close($ch);

        //<textarea id="gallery-data-textarea" style="display: none;"></textarea>

        $regg='/\<textarea id\=\"gallery\-data\-textarea\" style\=\"(.*)\">([.\S\s]*?)\<\/textarea\>/i';
        preg_match_all($regg, $output, $datacout);
        $dataa=json_decode($datacout[2][0], true);
        //<title>邻居告诉我，买灯就买3、7款，好打理不说，便宜又高档</title>
        if(is_array($dataa)){
            $this->assign('data',$dataa);
            $this->display();
            exit;
        }else{
            $reg = '/<figure>\s.*<h2 .* _price="(\d.*?\.00)" .* _href="(.*)" .*>(.*)<\/h2>\s.*<figcaption>(.*)<\/figcaption>\s.*<img alt-src=\'(.*)\'>\s.*<\/figure>/';
            preg_match_all($reg,$output,$data);

            $arr = array('price'=>$data[1],'href'=>$data[2],'title'=>$data[3],'product'=>$data[4],'img'=>$data[5]);
            return $arr;
        }
    }
    public function GetNews(){
        if(IS_POST){
            $query =I('post.query');
            $data=self::news($query);
        }else{
            $url =I('get.url');
            //var_dump($url);exit;
            $data=self::news($url);
            //var_dump($data['title'][0]);exit;
        }
            if($data){
                $count=count($data['title']);
                for($i=0; $i<$count;$i++){
                    $fdate[$i]['name']=$data['title'][$i];
                    $fdate[$i]['img']=$data['img'][$i];
                    $fdate[$i]['product']=$data['product'][$i];
                    $fdate[$i]['price']=$data['price'][$i];
                    $fdate[$i]['real_url']=$data['href'][$i];
                }
                if($fdate){
                    //var_dump($fdate);exit;
                }
            }
            $this->assign('data',$fdate);
            $this->display();

    }
    public function Renlin(){
        $model=M('gg_fxg');
        $where="`name` IS NULL or `name`='待认领'";
        $data=$model->where($where)->order('pay_time desc')->select();
        $data=array_values($data);
        $this->assign('data', $data);
        $this->display();
    }
    public function renl() {
        $gid = I('get.gid', '', 'trim');
        if (!$gid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除商品库的id不能为空！'));
        }
        $uid= $this->uid;
        $user=M('user')->where(array('id'=>$uid))->find();

        $goods_set = M('gg_fxg');
        $where = array('id' => $gid);
        $fxg=M('gg_fxg')->where($where)->find();
        $wherew['order_source']=$fxg['order_source'];
        $date['name']=$user['name'];
        $goods_count = $goods_set->where($wherew)->save($date);
        if (!$goods_count || $goods_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '认领失败！'));
        }

        $this->ajaxReturn(array('code' => 0, 'success' => '认领成功！'));
    }
    public function product(){
    $query =urldecode(I('get.query','','trim'));
    $xiaoliang =urldecode(I('get.xiaoliang','','trim'));
    $jiage =urldecode(I('get.jiage','','trim'));
    $where = array();
    if($query){
        $where['_string'] = "`sku_title` like '%{$query}%'";
    }
    if($jiage && $xiaoliang){
        $order='month_sell_num,sku_price desc';
        $xiaoliang='xiaoliang';
        $jiage="jiage";
    }elseif($xiaoliang){
        $order='month_sell_num desc';
        $xiaoliang='xiaoliang';
    }elseif($jiage){
        $order='sku_price desc';
        $jiage="jiage";
    }else{
        $jiage="";
        $xiaoliang="";
    }

    $article = M('ytt_product');
    //M('orders_goods')->alias('m')->field('g.id,g.pic,m.num,m.sell_price')->join('goods g on m.goods_id=g.id')->where('m.order_id=' . $row['id'])->select();
    $count = $article->where($where)->count();
    $page = $this->pages($count, $this->reqnum);
    //var_dump($article->getLastSql());exit;
    $limit = $page->firstRow . ',' . $page->listRows;
    $list = $article->where($where)->limit($limit)->order($order)->select();

    //var_dump($list);exit;
    $data = array(
        'query'=>$query,
        'xiaoliang'=>$xiaoliang,
        'jiage'=>$jiage,
        'pages' => $page->show(),
        'list' => $list,
    );
    $this->assign($data);
    $this->display();
}
}
