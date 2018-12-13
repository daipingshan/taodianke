<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2016/12/28
 * Time: 15:42
 */

namespace Api\Controller;

/**
 * Class ItemController
 *
 * @package Api\Controller
 */
class WechatgroupController extends CommonController
{

	const POINT = 1000;
    const NEW_POINT = 100;
    

    public function __construct() {
        parent:: __construct();        
        $this->order = M('order_data');
        $this->wx_user = M('wx_user');
        $this->cash = M('wx_cash');
        $this->user = M('user');
        $this->item = M('items');
        $this->wx_group = M('wechat_group');
        $this->user_sign = M('user_sign');
    }  

    /*
	*	微信群里面用户发送关键字功能
	*   @param  zoneid    微信群的唯一标识
	*   @param  openid    用户的唯一标识
	*   @param  key       用户发送的指定的字符
    */
	public function wechatSendMsg()
	{
		//  签到时间，发送关键字，
		$_data = array(			
			'zoneid' => I('get.zoneid', 0, 'trim'),
			'openid' => I('get.openid', 0, 'trim'),
			'key'    => I('get.key', 0, 'trim'),
		);

		if ($_data['key'] == '签到' || $_data['key'] == '签到排行') {
			$data['msg'] = $this->userSign($_data);
			$data['zoneid'] = $_data['zoneid'];
			$data['openid'] = $_data['openid'];
		}
		$this->outPut($data, 0);	
	}


	/*
	*   用户签到功能，查看签到排行榜
	*   @param  zoneid    微信群的唯一标识
	*   @param  openid    用户的唯一标识
	*   @param  key       用户发送的指定的字符
	*/
	public function userSign($_data)
	{		
		$data = '1';
		if ($_data['key'] == '签到') {						
			$user = $this->user_sign->where(array('openid'=>$_data['openid']))->find();

			if (!$user) {
				$res = array(
					'zoneid'=>$_data['zoneid'],
					'openid'=>$_data['openid'],
					'add_time'=>time(),
					'create_time'=>time(),
					'point'=>'1',
					'day_num'=>'1',
					'status'=>'1',
				);
				$result = $this->user_sign->add($res);
			}elseif ($user['create_time'] > strtotime(date('Y-m-d'))) {
				 $data = '-1';
			}else{
				$point = $user['point'] + 1;
				$res = array(
					'create_time'=>time(),
					'point'=>$point,
					'day_num'=>$point,
				);
				$result = $this->user_sign->where(array('openid'=>$_data['openid']))->save($res);
			}
			if(!$result){
				$data = '-1';
			}
		}elseif ($_data['key'] == '签到排行') {
			$data = $this->user_sign->field('id,openid,day_num')->order('day_num')->select();
		}


		return $data;		
	}

	/*
    *   自动找单接口(群调用)
    */
    public function searchGoods()
    {
        $param = array(
            'zoneid'=>I('get.zoneid',0,'trim'),            
            'keyword'=>I('get.keyword',0,'trim'),            
        );
        $where = array('zoneid'=>$param['zoneid']);
        //$zpid = $this->wx_group->where($where)->getField('zpid');
        $zpid = 'mm_121610813_23840080_86378481';
        if(!$zpid){
            $result = '您的群号不正确，请重新输入！';
            $this->outPut(null, -1, null, $result);
        }

        $id = $this->user->where(array('pid'=>$zpid))->getField('id');

        if (strpos($param['keyword'], 'http://') !== false || strpos($param['keyword'], 'https://') !== false) {
            $key = urlencode($param['keyword']);
            $datas = getCookie();
            $dephp_97 = microtime(true) * 1000;     
            $dephp_97 = explode('.', $dephp_97);
            $end = $dephp_97[0] + 8;

            $dephp_1 = 'http://pub.alimama.com/items/search.json?q='.$key.'&_t='.$dephp_97[0].'&auctionTag=&perPageSize=40&shopTag=yxjh&t='.$end.'&_tb_token_='.$datas['token'].'&pvid=10_49.221.62.102_4720_1496801283153';
          
            $dephp_3 = json_decode(https($dephp_1,$datas['cookie']),true);
      
            $quanjia = $dephp_3['data']['pageList'][0]['zkPrice'] - $dephp_3['data']['pageList'][0]['couponAmount'];
            $info = array(
                'num_iid'=>$dephp_3['data']['pageList'][0]['auctionId'],
                'pvid'=>$dephp_3['data']['head']['pvid'],
                'title'=>$dephp_3['data']['pageList'][0]['title'],
                'price'=>$dephp_3['data']['pageList'][0]['zkPrice'],
                'pic'=>'http:'.$dephp_3['data']['pageList'][0]['pictUrl'],                
                'coupon_price'=>$quanjia,
            );
            if(!$dephp_3){
                $data = '抱歉，没有找到相关商品，换个搜索词，或者减少筛选条件试试吧。';
            }else{
                $reset = explode('_', $zpid);
                $adzoneid = $reset[3];
                $siteid = $reset[2];
                $dephp_1 ='http://pub.alimama.com/common/code/getAuctionCode.json?auctionid='.$info['num_iid'].'&adzoneid='.$adzoneid.'&siteid='.$siteid.'&scenes=1&t='.$dephp_97[0].'&_tb_token_='.$datas['token'].'&pvid='.$info['pvid'];

                $dephp_900 = json_decode(http($dephp_1,$datas['cookie']),true);

                if(!$dephp_900['data']['couponLink']){
                    $keykou = $dephp_900['data']['taoToken'];
                }else{
                    $keykou = $dephp_900['data']['couponLinkTaoToken'];
                }                
                $url = 'http://m.taodianke.com/index?uid='.$id;        
                $info['tao_kou_ling'] = $info['title']."&#10;".'原价：'.$info['price'].' 抢购价：'.$info['coupon_price']."&#10;".'下单地址:复制这条信息，打开→手机淘宝→即可「领取优惠券」并购买{'.$keykou.'}。更多商品，详见：{'.$url.'}';
                $data = array(
                    'pic'=>$info['pic'],
                    'info'=>$info,
                );
            }
        }else{
            $key = urlencode($param['keyword']);
            $url = 'http://m.taodianke.com/?m=search&a=index&k='.$key.'&uid='.$id;
            $data = '已为您找到'.$param['keyword'].'相关商品优惠券，点击查看：'.$url;
        }
        $this->outPut($data, 0);
    }

	/**
	 * 获取数据
	 */
	public function getItem()
	{
		$status = I('get.status', 0, 'int');
		if ($status == 0) {
			$data = $this->_getData();
		} else {
			$data = $this->_getUserData();
		}
		if ($data['error']) {
			$this->outPut(null, -1, null, $data['error']);
		} else {
			$this->outPut($data, 0);
		}

	}

	/**
	 * 获取默认情况下的数据
	 */
	protected function _getData()
	{
		$where = array('pass' => 1, 'isshow' => 1);
		/* $where['coupon_start_time'] = array('elt',time());
		 $where['coupon_end_time'] = array('egt',time());*/
		$field = "id,num_iid,title,intro as long_title,price,coupon_price,endtime,pic_url";
		$data = M('items')->field($field)->where($where)->limit(0, 200)->select();
		foreach ($data as &$item) {
			$item['content'] = $item['title'] . "\n" . '原价：' . $item['price'] . ' 抢购价：' . $item['coupon_price'] . "\n" . '下单地址:复制这条信息，打开→手机淘宝→即可看到【' . $item['title'] . '】￥2031TIDqZa￥';
		}
		return $data;
	}

	/**
	 * 获取我的推广下的产品数据
	 */
	protected function _getUserData()
	{
		$uid = $this->uid;
		$item_like = M('items_like')->field('id,num_iid')->index('num_iid')->where(array('uid' => $uid))->order('id desc')->select();
		if (!$item_like) {
			$this->outPut(null, -1, null, '您目前没有推广商品！');
		}
		$num_iid = array_keys($item_like);
		$where['num_iid'] = array('in', $num_iid);
		/* $where['coupon_start_time'] = array('elt',time());
		 $where['coupon_end_time'] = array('egt',time());*/
		$field = "id,num_iid,title,intro as long_title,price,coupon_price,endtime,pic_url";
		$data = M('items')->field($field)->where($where)->index('num_iid')->select();
		foreach ($data as &$item) {
			$item['content'] = $item['title'] . "\n" . '原价：' . $item['price'] . ' 抢购价：' . $item['coupon_price'] . "\n" . '下单地址:复制这条信息，打开→手机淘宝→即可看到【' . $item['title'] . '】￥2031TIDqZa￥';
		}
		return $data;
	}

}