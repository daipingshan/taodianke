<?php
/**
 *  Class CrawlController
 *  信息抓取入库
 *  @package Api\Controller
 */
namespace AppAdmin\Controller;

class CrawlController extends CommonController{

    public  $checkUser = false;
    private $ch = null;
    private $hundred = 500;
    private $pid = "mm_121610813_22448587_79916379";

    private $dataokeKey = "4frqyegyob";
     /**
     * 全网搜索商品地址
     *
     * @var string
     */
    private $search_url = "http://pub.alimama.com/items/search.json?q=%s&_t=%s&auctionTag=&toPage=%s&perPageSize=%s&shopTag=yxjh&t=%s&_tb_token_=&pvid=10_49.221.62.102_4720_1496801283153";

    /*
     * 	构造函数
     * */
    public function __construct() {
        parent:: __construct();
        if (ACTION_NAME != 'Ajax_addGood') {
            if (!isset($_SESSION['auth_user'])) {
                redirect(U('/AppAdmin/login/'));
            }
        }
    }
    //自动检测商品
    public function check_items(){

        $check_mark = I('check_mark','Y','string');
        $items = M('items');
        $where['pass'] = 1;
        $where['check_mark'] = $check_mark;
        if($check_mark == 'Y'){
            $new_mark = 'N';
        }else{
            $new_mark = 'Y';
        }

        //if(!$item = $items->where($where)->order('endtime asc,starttime asc')->limit(2)->getField('num_iid',true)){

        if(!$item = $items->where($where)->order('endtime asc,starttime asc')->find()){
            $return_data['result'] = -1;
            $return_data['msg'] = "没有可查询到的数据。";
            $this->ajaxReturn($return_data);
        }
        //dump($item);
        //exit;
        $i=0;
        $x =0;

        //foreach($item as $v){
        $save_map['num_iid'] = $item['num_iid'];
        //大淘客检测商品
        if(!$itemsinfo = $this->Get_dataoke_item($item['num_iid'])){
            $items->where($save_map)->delete();
            $x++;
        }else{
            $itemsinfo= $itemsinfo['result'];
            $itemsinfo['ordid'] = 9999;
            $itemsinfo['pass'] = 1;
            $data = $this->Data_set($itemsinfo);
            $data['check_mark'] = $new_mark;
            $items->where($save_map)->save($data);
            $i++;
        }
        //}

        $return_data['result'] = 1;
        $return_data['check_mark'] = $check_mark;
        $return_data['msg'] = "删除".$x."件商品，更新".$i."件商品。";
        $this->ajaxReturn($return_data);

    }
    public function del_dtk_items(){
        $items = M('items');

        if($items->where("ordid = 9999")->delete()){
            $this->success('删除成功','/AppAdmin/Crawl/index');
        }else{
            $this->error('删除失败','/AppAdmin/Crawl/index');
        }
    }
    //添加单商品
    public function addGood(){
        $id = I('godids','int');
        $ordid = I('ordid',9999,'int');

        $items = M('items');
        $itemsinfo = $this->Get_dataoke_item($id);

        if($itemsinfo['result'] == NULL){
            $this->error('未在大淘客中寻找到此款产品,或访问受限','add');
            exit;
        }
        $itemsinfo= $itemsinfo['result'];
        //删除旧商品， 之前考虑过更新， 还不如直接删除重新增加。
        $items->where("num_iid = ".$itemsinfo['GoodsID'])->delete();
        //新增商品
        $itemsinfo['ordid'] = $ordid;

        $data = $this->Data_set($itemsinfo);

        if($items->add($data)){
            if($this->SetItemsDetails($itemsinfo['GoodsID'])){
                $this->success('添加成功.','add');
                exit;
            }
        }

        $this->error('添加失败.','add');
    }
    //AJAX获取商品详情
    public function Ajax_addGood(){
        $id = I('godids',0,'int');
        $ordid = I('ordid',9999,'int');
        if($id == 0){
            $data['msg'] = '???????????';
            $data['status'] = -1;
            $this->ajaxReturn($data);
            exit;
        }
        $items = M('items');
        $itemsinfo = $this->Get_dataoke_item($id);
        if($itemsinfo['result'] == NULL){
            $data['msg'] = '未在大淘客中寻找到此款产品,或访问受限';
            $data['status'] = -1;
            $this->ajaxReturn($data);
            exit;
        }
        $itemsinfo= $itemsinfo['result'];

        if($item = $items->where("num_iid = ".$itemsinfo['GoodsID'])->find()){
            if($item['ordid'] != 9999){
                $data['title'] = "重复提交 - ".$item['title'];
                $data['pic_url'] = $item['pic_url'];
                $data['msg'] = '您已经提交过此产品，产品排序为'.$item['ordid'];
                $data['status'] = -2;
                $this->ajaxReturn($data);
                exit;
            }else{
                //删除旧商品， 之前考虑过更新， 还不如直接删除重新增加。
                $items->where("num_iid = ".$itemsinfo['GoodsID'])->delete();
            }
        }else{
            //新增商品
            $itemsinfo['ordid'] = $ordid;
            dump($itemsinfo);
            exit;
            $data = $this->Data_set($itemsinfo);
            if($items->add($data)){
                if($this->SetItemsDetails($itemsinfo['GoodsID'])){
                    $data['msg'] = '添加成功，排序结果为第'.$ordid."位";
                    $data['ordid'] = $ordid;
                    $data['status'] = 1;
                    $this->ajaxReturn($data);
                    exit;
                }
            }
        }

        $this->error('添加失败.','add');
    }

    /*
    *	增加单个商品
    */
    public function add(){
        $this->display();
    }
    /*
     * 	数据采集  首页
     * */
    public function index(){
        $ItemCount = $this->GetItemCount();
        $cfsql = "SELECT count(*) AS count FROM ytt_items GROUP BY num_iid HAVING count>1";
        $Model = new \Think\Model();
        $result = $Model->query($cfsql);
        $cfdata = $result[0]['count'];
        $this->assign('cfdata',$cfdata);
        $this->assign('ItemCount',$ItemCount);// 用户
        $this->display();
    }
    public function top100(){
        $items = M('items');
        //恢复TOP100
        $c_where['coupon_type'] = 5;
        $items->where($c_where)->delete();
        // 创建一个新cURL资源
        $ch = curl_init();
        // 设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, "http://api.dataoke.com/index.php?r=Port/index&type=paoliang&appkey=".$this->dataokeKey."&v=2");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 抓取URL并把它传递给浏览器
        if(!$Goodsdata = curl_exec($ch)){
            die('获取页面信息错误');
        }
        ////关闭cURL资源，并且释放系统资源
        curl_close($ch);
        $dataArr = json_decode($Goodsdata,true);
        $i=1;
        foreach($dataArr['result'] as $k => $v){

            /*********************参数调整配置********************/
            $v['coupon_type'] = 5;
            $v['ordid'] = $i++;
            if(!$id= $items->where("num_iid = ".$v['GoodsID'])->getField('id')){
                $dataList[] = $this->Data_set($v);
            }else{
                //先删
                $items->where("id = ".$id)->delete();
                $dataList[] = $this->Data_set($v);
            }

        }
        if(!$items->addAll($dataList)){
            $this->error('Top100错误','index');
        }
        $this->success('Top100更新成功'.$i,'index');
    }

    //主采集程序
    public function DWTaokeDATA(){
        $page = I("get.page",1);

        //如果等于0 就随机选择一个页面进行抓取
        $dataArr = $this->GetTaokeDATA($page);
        $items = M('items');
        $num = 0;
        //最后一页

        if($dataArr == NULL){
            sleep(2);
            $rdata['page'] = $page;
            $rdata['msg'] = "发生抓取错误。";
            $rdata['type'] = 10;

            $this->ajaxReturn($rdata);
        }
        foreach($dataArr['result'] as $k => $v){
            $v['coupon_type'] = 0;
            $v['ordid'] = 9999;
            if(!$items->where('num_iid = '.$v['GoodsID'])->find()){
                //写入
                $num++;
                /*********************参数调整配置********************/
                $dataList[] = $this->Data_set($v);
            }
        }//循环结束
        $rdata['ItemCount'] =  $this->GetItemCount();
        if(count($dataList) == 0){

            $rdata['msg'] = '本页均为重复商品!';
            $rdata['type'] = 10;
            $rdata['page'] = $page;
            $this->ajaxReturn($rdata);

        }else {
            $items->addAll($dataList);

            $rdata['msg'] = '商品添加成功新增产品'.$num."条";
            $rdata['type'] = 10;
            $rdata['page'] = $page;
            $this->ajaxReturn($rdata);

        }

    }

    //数据更新
    /**
     * @param $id
     * @return int
     */
    public function SetItemsDetails($id){
        usleep(200000);
        $item = M('items');
        $data1 = $this->getitems_info($id);
        if (!$data1['nick']) {
            $data2 = $this -> get_item_info($id);
        }
        $data = array_merge($data1,$data2);

        $data['pass'] = 1;
        $res = $this->findMarketGoods($id);
        if ($res['taobao_res'] === true) {
            if ($res['taobao_item_status'] == 'market') {
                $data['uname'] = 'yingxiao';
            }

            $obj  = new \Common\Org\TaoBaoApi();
            $desc = $obj->getApiDesc($id);
            $data['desc'] = $desc;
            $item->where("num_iid = ".$id)->save($data);
            return true;
        } else {
            return false;
        }
    }

    //获得单个商品信息
    public  function Get_dataoke_item($id = 0){
        if($id == 0){
            $id = I('get.id',0,'int');
        }
        // 创建一个新cURL资源
        $ch = curl_init();

        // 设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, "http://api.dataoke.com/index.php?r=port/index&appkey=".$this->dataokeKey."&v=2&id=".$id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 抓取URL并把它传递给浏览器
        $Goodsdata = curl_exec($ch);
        ////关闭cURL资源，并且释放系统资源
        curl_close($ch);
        //dump($Goodsdata);
        $items = json_decode($Goodsdata,true);

        if($items['result'] == null){
            return false;
        }

        //echo 123;
        return $items;

    }

    public function GetItemCount(){
        $item = M('items');
        $count = $item->count();
        return $count;
    }
    //获取大淘客数据
    public function GetTaokeDATA($page = 1){
        // 创建一个新cURL资源
        $ch = curl_init();
        // 设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, "http://api.dataoke.com/index.php?r=Port/index&type=total&appkey=".$this->dataokeKey."&v=2&page=".$page);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 抓取URL并把它传递给浏览器
        if(!$Goodsdata = curl_exec($ch)){
            return false;
        }
        ////关闭cURL资源，并且释放系统资源
        curl_close($ch);
        //dump($dataArr = json_decode($Goodsdata,true));
        return $dataArr = json_decode($Goodsdata,true);
    }
    //查找未添加淘宝内容订单
    public function upd_item_desc(){
        $item = M('items');
        $where['pass'] = 0;
        $where['desc'] = 0;

        if(!$items= $item->where($where)->order('id desc')->limit(25)->select()){
            $data['status'] = -1;
            $data['title'] = '没有可补充商品';
            $this->ajaxReturn($data);
        }


        foreach($items as $v){
            $this->SetItemsDetails($v['num_iid']);
            $data['title'] .= '<br>'.$v['title'];
        }
        $data['status'] = 1;
        $data['success']= 0;
        $data['pass0'] = $item->where('pass = 0')->count();

        $this->ajaxReturn($data);
    }

    //获取商品详情2
    public  function get_item_info($dephp_68 ) {

        $dephp_1 = 'http://hws.m.taobao.com/cache/wdetail/5.0/?id=' . $dephp_68;
        $dephp_69 = curl_init();
        curl_setopt($dephp_69, CURLOPT_URL, $dephp_1);
        curl_setopt($dephp_69, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($dephp_69, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($dephp_69, CURLOPT_MAXREDIRS, 2);
        $dephp_70 = curl_exec($dephp_69);
        curl_close($dephp_69);
        if (!$dephp_70) {
            $dephp_70 = file_get_contents($dephp_1);
        }
        $dephp_4 = json_decode($dephp_70, true);
        $dephp_7 = array();
        $dephp_73 = json_decode($dephp_4['data']['apiStack'][0]['value'], true);
        $dephp_7['price'] = $dephp_73['data']['itemInfoModel']['priceUnits'][1]['price'];
        /*
         *
         if (substr_count($dephp_7['price'], '-')) {
            $dephp_73 = explode('-', $dephp_7['price']);
            $dephp_7['price'] = min($dephp_73[0], $dephp_73[1]);
        }
        */
        $dephp_7['nick'] = $dephp_4['data']['seller']['nick'];
        $dephp_7['shop_type'] = $dephp_4['data']['seller']['type'];
        //dump($dephp_7);
        return $dephp_7;
    }

    //获取商品详情
    public function getitems_info($dephp_68) {
        //$dephp_68 = 35109095131;
        $dephp_1 = 'https://s.m.taobao.com/search?nid=' . $dephp_68;
        $dephp_69 = curl_init();
        curl_setopt($dephp_69, CURLOPT_URL, $dephp_1);
        curl_setopt($dephp_69, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($dephp_69, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($dephp_69, CURLOPT_MAXREDIRS, 2);
        curl_setopt($dephp_69, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($dephp_69, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        $dephp_70 = curl_exec($dephp_69);
        curl_close($dephp_69);
        if (!$dephp_70) {
            $dephp_70 = file_get_contents($dephp_1);
        }
        $dephp_4 = json_decode($dephp_70, true);
        //dump($dephp_70);
        $dephp_7 = array();
        $dephp_71 = $dephp_4['itemsArray'][0]['ordinaryPostFee'];
        if ($dephp_71 == '0.00') {
            $dephp_7['ems'] = 1;
        } else {
            $dephp_7['ems'] = 0;
        }
        $dephp_7['inventory'] = $dephp_4['itemsArray'][0]['quantity'];
        //$dephp_7['price'] = $dephp_4['itemsArray'][0]['price'];
        $dephp_7['nick'] = $dephp_4['itemsArray'][0]['nick'];
        $dephp_72 = $dephp_4['itemsArray'][0]['userType'];
        if ($dephp_72 == 1) {
            $dephp_7['shop_type'] = 'B';
        } else {
            $dephp_7['shop_type'] = 'C';
        }
        $dephp_7['cu'] = $dephp_4['itemsArray'][0]['zkType'];


        return $dephp_7;
    }

    //get_pic
    public function getdesc($iid){
        $imgurl = '';
        $infoUrl = "http://hws.m.taobao.com/cache/mtop.wdetail.getItemDescx/4.1/?data=%7B%22item_num_id%22%3A%22".$iid."%22%7D";
        $content = $this->execcurl($infoUrl);
        if(!$dapi = $this->is_json($content)){
            return false;
        }
        $imglist = $dapi['data']['images'];
        $num   = count($imglist);
        for($i=0;$i<$num;$i++){
            $imgurl .= '<img src='.$imglist[$i].' style="display: inline-block;height: auto;max-width: 100%;">';
        }
        return $imgurl;
    }


    public function execcurl($url,$ispost=false,$data='',$in='utf8',$out='utf8',$cookie='')
    {
        $ch = '';
        $fn = curl_init();
        curl_setopt($fn, CURLOPT_URL, $url);
        curl_setopt($fn, CURLOPT_TIMEOUT, 30);
        curl_setopt($fn, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($fn, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($fn, CURLOPT_REFERER, $url);
        curl_setopt($fn, CURLOPT_HEADER, 0);
        if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if($cookie)
            curl_setopt($fn,CURLOPT_COOKIE,$cookie);
        if($ispost){
            curl_setopt($fn, CURLOPT_POST, TRUE);
            curl_setopt($fn, CURLOPT_POSTFIELDS, $data);
        }
        $fm = curl_exec($fn);
        curl_close($fn);
        if($in!=$out){
            $fm = $this->Newiconv($in,$out,$fm);
        }
        return $fm;
    }

    //jianceJSON
    public function is_json($string){
        try{
            $data = json_decode($string,true);
        }catch(Exception $e){
            return  $string;
        }
        return $data;
    }

    //GBKzhuanUTF-8
    public function Newiconv($_input_charset="GBK",$_output_charset="UTF-8",$input ) {
        $output = "";
        if(!isset($_output_charset) )$_output_charset = $this->parameter['_input_charset '];
        if($_input_charset == $_output_charset || $input ==null) { $output = $input;
        }
        elseif (function_exists("m\x62_\x63\x6fn\x76\145\x72\164_\145\x6e\x63\x6f\x64\x69\x6e\147")){
            $output = mb_convert_encoding($input,$_output_charset,$_input_charset);
        } elseif(function_exists("\x69\x63o\156\x76")) {
            $output = iconv($_input_charset,$_output_charset,$input);
        }
        else die("对不起，你的服务器系统无法进行字符转码.请联系空间商。");
        return $output;
    }
    //获取字符
    public function get_word($html,$star,$end){
        $pat = '/'.$star.'(.*?)'.$end.'/s';
        if(!preg_match_all($pat, $html, $mat)) {
        }else{
            $wd= $mat[1][0];
        }
        return $wd;
    }
    protected  function get_yjbl($dephp_68) {
        $dephp_74 = 'http://pub.alimama.com/items/search.json?q=http%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D' . $dephp_68 . '&_t=1469960754588&auctionTag=&perPageSize=40&shopTag=&t=1469960754677&_tb_token_=47FPtqXA1lp&pvid=10_27.27.101.157_6579_1469960754648';
        $dephp_70 = execcurl($dephp_74);
        $dephp_75 = is_json($dephp_70);
        $dephp_76 = $dephp_75['data']['pageList'][0]['tkRate'];
        $dephp_77 = $dephp_75['data']['pageList'][0]['eventRate'];
        if ($dephp_77) {
            $dephp_78 = $dephp_77;
        } else {
            $dephp_78 = $dephp_76;
        }
        return $dephp_78;
    }

    //数组排序
    public  function columnSort($unsorted, $column) {
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

    public function Data_set($v){

        $bili = round(($v['Quan_price'] / $v['Price']) * 10, 1);

        $commission = round(($v['Price'] * $v['Commission'] / 100), 2);

        if($v['pass'] != 1){
            $v['pass'] = 0;
        }
        //设置9.9 包邮
        if($v['Price'] <= 9.9  && $v['coupon_type'] !=5){
            $v['coupon_type'] = 4;
        }
        $quanurl = 'http://taoquan.taobao.com/coupon/unify_apply.htm?sellerId=' . $v['GoodsID'] . '&activityId=' .$v['Quan_id'];
        $qurl = 'http://h5.m.taobao.com/ump/coupon/detail/index.html?sellerId=' . $v['GoodsID'] . '&activityId=' . $v['Quan_id'];
        $quan_url = 'https://uland.taobao.com/coupon/edetail?activityId=' . $v['Quan_id'] . '&pid=' . $this->pid. '&itemId=' .$v['GoodsID']. '&src=cd_cdll';
        $click_url = 'https://uland.taobao.com/coupon/edetail?activityId=' . $v['Quan_id'] . '&pid=' . $this->pid . '&itemId=' . $v['GoodsID']  . '&src=cd_cdll';
        if ($v['Commission_queqiao'] > $v['Commission_jihua']){
            $uname = "queqiao";
            $commission = $commission * 0.85;
        }else{
            if ($v['Jihua_link'] === 0 || $v['Jihua_link'] ==""){
                $uname = "tongyong";
            }else{
                $uname = "wfa";
            }
        }
        switch ($v['Cid']){
            case 1:
                //女装
                $cat_id = 20;
                break;
            case 2:
                //母婴
                $cat_id = 21;
                break;
            case 3:
                //美妆
                $cat_id = 22;
                break;
            case 4:
                //家居
                $cat_id = 23;
                break;
            case 5:
                //鞋包
                $cat_id = 24;
                break;
            case 6:
                //美食
                $cat_id = 25;
                break;
            case 7:
                //文体车品
                $cat_id = 26;
                break;
            case 8:
                //数码家电
                $cat_id = 27;
                break;
            case 9:
                //男装合并至女装
                $cat_id = 20;
                break;
            case 10:
                //内衣
                $cat_id =29;
                break;
            default:
                $cat_id =  $v['Cid'] + 10000;
        }
        /************参数调整配置 END************************/
        $dataList = array(
            'quanurl'=>$quanurl,//优惠券地址
            'qurl'=>$qurl, //短链接
            'quan_url'=>$quan_url,
            'snum'=>$v['Quan_surplus'],    //剩余优惠券
            'lnum'=>$v['Quan_receive'],   //已领取优惠卷
            'quan'=>$v['Quan_price'],    //优惠券金额
            'coupon_type'=>$coupon_type,
            'starttime'=>date("Y-m-d",time()),//设置当前时间
            'endtime'=>substr($v['Quan_time'],0,10),//结束时间
            'price'=>$v['Org_Price'], //价钱
            'intro'=>$v['Introduce'], //文案
            'coupon_rate'=>(int)$v['Quan_price'] / $v['Org_Price'] * 100,
            'sellerId'=>$v['SellerID'], //卖家ID
            'volume'=>$v['Sales_num'], //销量
            'commission_rate'=>$v['Commission'] * 100, //佣金比例
            'commission'=>$commission,      //佣金
            'title'=>$v['Title'],  //标题
            'click_url'=>$click_url, //转链？
            'num_iid'=>$v['GoodsID'],  //淘宝商品ID
            'dataoke_id'=>$v['ID'],  //商品ID
            'pic_url'=>$v['Pic'],
            'coupon_price'=>$v['Price'], //使用优惠卷后价格
            'passname'=>'已通过',
            'coupon_type' => (isset($v['coupon_type']) ? $v['coupon_type'] : 1),
            'uid'=>1, //插入信息用户ID
            'uname'=>$uname,
            'desc'=>0,
            'isq' => 1,//单品卷
            'tags'=>0, //标签
            'pass'=>$v['pass'],   //是否上线
            'coupon_end_time'=>strtotime($v['Quan_time']),
            'cate_id'=>$cat_id, //分类
            'coupon_start_time'=>time(),
            'ordid' => $v['ordid'],   //商品排序
            'activity_id'=>$v['Quan_id']
        );
        return $dataList;
    }

    /**
     *   查询是否是营销计划的商品
     *   @param   num_iid  string
     *   @return  true  false  bool
     */
    protected function findMarketGoods($num_iid){
        $keyword = 'https://detail.tmall.com/item.htm?id='.$num_iid;
        $httpObj = new \Common\Org\Http();
        $key      = urlencode( $keyword );
        $temp     = microtime( true ) * 1000;
        $temp     = explode( '.' , $temp );
        $end      = $temp[0] + 8;
        $url      = sprintf( $this->search_url , $key , $temp[0] , 1 , 10 , $end );

        $ali_data = json_decode( $httpObj->get( $url ) , true );
        if (isset($ali_data['data'])) {
            if (isset($ali_data['data']['pageList'][0]['tkMktStatus']) && $ali_data['data']['pageList'][0]['tkMktStatus'] == '1') {
                return array('taobao_res' => true, 'taobao_item_status' => 'market');
            } else {
                return array('taobao_res' => true, 'taobao_item_status' => 'not_market');
            }
        } else {
            return array('taobao_res' => false, 'taobao_item_status' => 'unknown');
        }
    }
}

?>