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
class ItemController extends CommonController{

    /**
     * 获取数据
     */
    public function getItem(){
        $status = I('get.status',0,'int');
        if($status == 0){
            $data = $this->_getData();
        }else{
            $data = $this->_getUserData();
        }
        if($data['error']){
            $this->outPut(null,-1,null,$data['error']);
        }else{
            $this->outPut($data,0);
        }

    }

    /**
     * 获取默认情况下的数据
     */
    protected function _getData(){
        $where = array('pass'=>1,'isshow'=>1);
       /* $where['coupon_start_time'] = array('elt',time());
        $where['coupon_end_time'] = array('egt',time());*/
        $field = "id,num_iid,title,intro as long_title,price,coupon_price,endtime,pic_url";
        $data = M('items')->field($field)->where($where)->limit(0,200)->select();
        foreach ( $data as &$item ) {
            $item['content'] = $item['title']."\n".'原价：'.$item['price'].' 抢购价：'.$item['coupon_price']."\n".'下单地址:复制这条信息，打开→手机淘宝→即可看到【'.$item['title'].'】￥2031TIDqZa￥';
        }
        return $data;
    }

    /**
     * 获取我的推广下的产品数据
     */
    protected function _getUserData(){
        $uid = $this->uid;
        $item_like = M('items_like')->field('id,num_iid')->index('num_iid')->where(array('uid'=>$uid))->order('id desc')->select();
        if(!$item_like){
            $this->outPut(null,-1,null,'您目前没有推广商品！');
        }
        $num_iid = array_keys($item_like);
        $where['num_iid'] = array('in',$num_iid);
       /* $where['coupon_start_time'] = array('elt',time());
        $where['coupon_end_time'] = array('egt',time());*/
        $field = "id,num_iid,title,intro as long_title,price,coupon_price,endtime,pic_url";
        $data = M('items')->field($field)->where($where)->index('num_iid')->select();
        foreach ( $data as &$item ) {
            $item['content'] = $item['title']."\n".'原价：'.$item['price'].' 抢购价：'.$item['coupon_price']."\n".'下单地址:复制这条信息，打开→手机淘宝→即可看到【'.$item['title'].'】￥2031TIDqZa￥';
        }
        return $data;
    }

}