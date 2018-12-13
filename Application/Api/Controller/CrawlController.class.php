<?php

namespace Api\Controller;

/**
 *  Class CrawlController
 *  信息抓取入库
 *  @package Api\Controller
 */
class CrawlController extends CommonController{

	protected $checkUser = false;
	
	public function index(){
	   
	    $this->display();
	}
	public function test(){
	    
	    $page = I("get.page",0);
	    
	    $rdata['msg'] = "页面抓取开始";
	    $rdata['type'] = 10;
	    $rdata['page'] = $page;
	    
	    if($page == 1000){
	        $rdata['msg'] = "完成采集！";
	        $rdata['type'] = '5';
	    }
	  
	    $this->ajaxReturn($rdata);
	    
	}
	public function DWTaokeDATA(){
	    $page = I("get.page",0);
	    $dataArr = $this->GetTaokeDATA($page);
	    //最后一页
	    if($dataArr == NULL){
	        
	        $rdata['msg'] = "抓取完毕，已是最后一页";
	        $rdata['type'] = 5;
	        $this->ajaxReturn($rdata);
	    }
	    $num = 0;
	    foreach($dataArr['result'] as $k => $v){
	       
	        //成功就插入
	        if($this->GoodRepeatCheck($v['GoodsID'])){
	            $num++;
	            /*参数调整配置*/
	            $bili = round(($v['Quan_price'] / $v['Price']) * 10, 1);
	            $dephp_56 = C('ftx_coupon_add_time');
	            if ($dephp_56) {
	                $coupon_end_time = (int)(time() + $dephp_56 * 3600);
	            } else {
	                $coupon_end_time = (int)(time() + 72 * 86400);
	            }
	            $commission = number_format(($v['Price'] * $v['Commission'] / 100), 1);
	            /*参数调整配置 END*/
	            $dataList[] = array(
	                'quan_url'=>$v['Quan_link'],
	                'snum'=>$v['Quan_surplus'],    //剩余优惠券
	                'lnum'=>$v['Quan_receive'],   //已领取优惠卷
	                'quan'=>$v['Quan_price'],    //优惠券金额
	                'mprice'=>$this->get_word($v['Quan_condition'], '单笔满', '元可用'),
	                'starttime'=>date("Y-m-d",time()),//设置当前时间
	                'endtime'=>substr($v['Quan_time'],0,10),//结束时间
	                'quanurl'=>$v['Quan_link'],//优惠券地址
	                'qurl'=>$v['Quan_m_link'], //短链接
	                'tags'=>$v['D_title'], //标签
	                'price'=>$v['Price'], //价钱
	                'intro'=>$v['Introduce'], //文案
	                'coupon_rate'=>$bili * 1000,
	                'sellerId'=>$v['SellerID'], //卖家ID
	                'commission_rate'=>$v['Commission'] * 100, //？？
	                'commission'=>$commission,      // ？？
	                'title'=>$v['Title'],  //标题
	                'click_url'=>$v['Quan_m_link'], //短链接？
	                'num_iid'=>$v['GoodsID'],  //商品ID
	                'pic_url'=>$v['Pic'],
	                'coupon_price'=>$v['Quan_surplus'], //使用优惠卷后价格
	                //
	                 
	                //固顶信息
	                'uid'=>1, //插入信息用户ID
	                'uname'=>"GuoNan",
	    
	                'coupon_end_time'=>$coupon_end_time, //???啥时间
	    
	                /*
	    
	            //
	            二次补充！!!
	    
	            'inventory'=>$v['NULL'], //库存
	            'cu'=>$v['NULL'],//卖点
	            'shop_type'=>$v['IsTmall'], //是否是天猫
	            'desc'=>$v['NULL'], //描述
	            'nick'=>$v['NULL'], //卖家名称
	            'ems'=>$v['1'], //是否包邮
	            'volume'=>$v['NULL'], //销量
	            'cate_id'=>$v['NULL'], //分类
	            */
	    
	                'coupon_start_time'=>time()
	            );
	        }
	    
	    }//循环结束
	 
	    //	print_r($dataList);
	    // 	$x= $items->limit(0,100)->select();
	    //dump($x);
	    if(count($dataList) == 0){
	        
	        $rdata['msg'] = '本页均为重复商品!';
	        $rdata['type'] = 10;
	        $rdata['page'] = $page;
	      
	        $this->ajaxReturn($rdata);
	       
	    }else {
	        
	        $items = M('items');
    	    if($items->addAll($dataList)){
    	        $rdata['msg'] = '商品添加成功新增产品'.$num."条";
    	        $rdata['type'] = 10;
    	        $rdata['page'] = $page;
    	        $this->ajaxReturn($rdata);
    	    }  
	    }
	   
	}
	public function GetTaokeDATA($page = 1){
	    
			// 创建一个新cURL资源  
			$ch = curl_init();  
			  
			// 设置URL和相应的选项  
			curl_setopt($ch, CURLOPT_URL, "http://api.dataoke.com/index.php?r=Port/index&type=total&appkey=4frqyegyob&v=2&page=".$page);  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
			  
			// 抓取URL并把它传递给浏览器  
			if(!$Goodsdata = curl_exec($ch)){
				$rdata['msg'] = "页面抓取失败";
        	    $rdata['type'] = '0';
         	    $this->ajaxReturn($rdata);
				exit;
			}else{
			    
			    $rdata['msg'] = "抓取成功,开始处理数据！";
        	    
			}
				
			
			////关闭cURL资源，并且释放系统资源  
			curl_close($ch);  
			
			//dump($Goodsdata);
			return $dataArr = json_decode($Goodsdata,true);
	       
	
}
	//储存网站数据
	public function SetTaokeDATA(){
		
		//优惠券地址
		$data['quan_url'] = I('get.quan_url',0,'url');
		//优惠券剩余数量
		$data['snum'] =  I('get.snum',0,'int');
		//已领券数量
		$data['lnum'] =  I('get.lnum',0,'int');
		//优惠券金额
		$data['quan'] =  I('get.quan',0,'float');
		//优惠券使用条件
		$data['mprice'] =  I('get.mprice',NULL,'string');
		//优惠券开始时间
		$data['starttime'] = I('get.starttime','1700-0-0','string');
		//优惠券结束时间
		$data['endtime'] =  I('get.endtime','1700-0-0','string');
		//优惠券地址？
		$data['quanurl'] = I('get.quanurl',0,'url');
		//优惠券地址？
		$data['qurl'] =  I('get.qurl',0,'url');
		//销售类型？
		$data['shop_type'] =  I('get.shop_type',NULL,'string');
		//标签
		$data['tags'] =  I('get.tags',0.00,'string');
		//正常售价
		$data['price'] =  I('get.price',0,'float');
		//销量
		$data['volume'] =  I('get.volume',0,'int');
		//描述
		$data['desc'] =  I('get.desc',NULL,'string');
		//卖点
		$data['cu'] =  I('get.cu',NULL,'string');
		//库存
		$data['inventory'] =  I('get.inventory',0,'int');
		//商品文案
		$data['intro'] =  I('get.intro',NULL,'string');
		//优惠券比例
		$data['coupon_rate'] =  I('get.coupon_rate',0,'int');
		//卖家ID
		$data['sellerId'] =  I('get.sellerId',NULL,'string');
		//佣金？
		$data['commission_rate'] =  I('get.commission_rate',0.00,'float');
		//
		$data['commission'] = I('get.commission',0.00,'float');
		//商品短标题
		$data['title'] =  I('get.title',NULL,'string');
		//短链接
		$data['click_url'] =  I('get.click_url',NULL,'url');
		//商品淘宝id
		$data['num_iid'] = I('get.num_iid',NULL,'url');
		//商品主图
		$data['pic_url'] =  I('get.pic_url',NULL,'url');
		//券后价
		$data['coupon_price'] =  I('get.coupon_price',0,'float');
		//卖家
		$data['nick'] =  I('get.nick',0,'string');
		//分类ID
		$data['cate_id'] =  I('get.cate_id',0,'int');
		//是否包邮1 包邮 2不包邮
		$data['ems'] =  I('get.ems',0,'int');
		//优惠券结束时间
		$data['coupon_end_time'] =  I('get.coupon_end_time',0,'int');
		//优惠券入库时间
		$data['coupon_start_time'] =  I('get.coupon_start_time',0,'int');
					
		$good = M('items');
		/*http://atk.com/index.php/api/Crawl/GetTaokeDATA/quan_url/www.baidu.com/snum/10/lnum/10/quan/10.21/mprice/djksji/starttime/2018-10-11/endtime/2019-12-15/quanurl/ajdkajsiw.com/qurl/15151dasd/shop_type/151as1d515213/tags/sjkdjks/price/19.02/volume/150/desc/sdjkhansjkdnjqhibiqawbnijnadsid/cu/as,jdkj1j2kn3123/inventory/150/intro/sdaikojdlkajkdlasdasd/coupon_rate/15/sellerId/001/commission_rate/2.22/commission/1.22/title/testGoods/click_url/wqaekqjekl12341545/num_iid/001/pic_url/asdasd/coupon_price/52.11/nick/asdasd/cate_id/252545/ems/1/coupon_end_time/123123423/coupon_start_time/123123123*/
		
		if(!empty($data['num_iid']) && 
		   !empty($data['title']) &&
		   !empty($data['pic_url']) &&
		   !empty($data['cate_id']) &&
		   !empty($data['price']) &&
		   !empty($data['quan']) &&
		   !empty($data['lnum']) &&
		   !empty($data['snum']) &&
		   !empty($data['quan_url']) &&
		   !empty($data['nick'])){
			   
			//成功就插入
			if($this->GoodRepeatCheck($data['num_iid'])){
						
				$good->add($data);
						
			}else{
				
				$this->outPut(null,-1,null,'商品已存在');	
			}
			
			
			//重复的更新
			//暂不开发
		}else{
			//参数不全
			
			 $this->outPut(null,-1,null,'参数不全'.$data);
		}	
	}
	//商品重复查询
	public function GoodRepeatCheck($id = 0){
		
			$good = M('items');
			$num = $good->where('num_iid = '.$id)->count();

			if($num >= 1){
				
				return false;
			}else{
				
				return true;
			}
	}
	
	//数据入库
	
	//数据更新
	
	public function upd_item_desc($id){
		
		//是否是天猫
		$items = $this->getitems_info($id);
		if (!$items['nick']) {
			$items = $this -> get_item_info($id);
		} 
		$this->ajaxReturn($items);
	}
	//获取商品详情2
	public function get_item_info($dephp_68) {
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
		if (substr_count($dephp_7['price'], '-')) {
			$dephp_73 = explode('-', $dephp_7['price']);
			$dephp_7['price'] = min($dephp_73[0], $dephp_73[1]);
		} 
		$dephp_7['nick'] = $dephp_4['data']['seller']['nick'];
		$dephp_7['shop_type'] = $dephp_4['data']['seller']['type'];
		//$dephp_7['desc'] = $this->getdesc($dephp_68);
		return $dephp_7;
	} 
	//获取商品详情
	public function getitems_info($dephp_68) {
		$dephp_1 = 'http://s.m.taobao.com/search?nid=' . $dephp_68;
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
		$dephp_71 = $dephp_4['itemsArray'][0]['ordinaryPostFee'];
		if ($dephp_71 == '0.00') {
			$dephp_7['ems'] = 1;
		} else {
			$dephp_7['ems'] = 0;
		} 
		$dephp_7['inventory'] = $dephp_4['itemsArray'][0]['quantity'];
		$dephp_7['price'] = $dephp_4['itemsArray'][0]['price'];
		$dephp_7['nick'] = $dephp_4['itemsArray'][0]['nick'];
		$dephp_72 = $dephp_4['itemsArray'][0]['userType'];
		if ($dephp_72) {
			$dephp_7['shop_type'] = 'B';
		} else {
			$dephp_7['shop_type'] = 'C';
		} 
		$dephp_7['cu'] = $dephp_4['itemsArray'][0]['zkType'];
		$dephp_7['desc'] = $this->getdesc($dephp_68);
		return $dephp_7;
	} 
	//get_pic
	public function getdesc($iid){
		$infoUrl = "http://hws.m.taobao.com/cache/mtop.wdetail.getItemDescx/4.1/?data=%7B%22item_num_id%22%3A%22".$iid."%22%7D";
		$content = $this->execcurl($infoUrl);
        $dapi = $this->is_json($content);
		$imglist = $dapi['data']['images'];
	    $num   = count($imglist);
		for($i=0;$i<$num;$i++){
		$imgurl .= '<img class="lazy" src='.$imglist[$i].'>';
		}
		return $imgurl;
	}
	
	
	public function execcurl($url,$ispost=false,$data='',$in='utf8',$out='utf8',$cookie='')
    {
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
	function get_word($html,$star,$end){
	    $pat = '/'.$star.'(.*?)'.$end.'/s';
	    if(!preg_match_all($pat, $html, $mat)) {
	    }else{
	        $wd= $mat[1][0];
	    }
	    return $wd;
	}

}

?>