<?php
/**
 * Created by PhpStorm.
 * User: superegoliu
 * Date: 2016/12/13
 * Time: 15:15
 */

namespace ApiTaoke\Controller;


class TaoApiController extends CommonController {
    protected $checkUser = false;
   
    /***
     *
     *
     *
     */
    public function NewUpdateorder(){
        $goods = I('post.josn');//标题
        $type = I('post.type');//标题
        file_put_contents('/tmp/taobaoke.log',var_export($type, true).'||',FILE_APPEND);
        $goods = base64_decode(str_replace(array('%2b', ' '), '+', urldecode($goods)));
        $goods_arr = @json_decode($goods, true);
        file_put_contents('/tmp/taobaoke.log',var_export($goods_arr, true).'||',FILE_APPEND);
        if(is_array($goods_arr)){
            $data=$goods_arr['dingdan'];
        }
        unset($data[0]);
        $data=array_values($data);
        $jgg='';
        foreach($data as $ke=>$vad){

            $jgg.=self::NewUpdate($vad,$type);

        }
        $this->outPut(null, 0, null,$jgg);
    }
    public function NewUpdate($date,$type)
    {
        usleep(50000);
        $order_id = $date['F25'];//I('post.order_id');//订单编号
        $title = $date['F3'];//I('post.title');//标题
        $itemid=$date['F4'];//I('post.itemid');//商品id
        $discount_rate= $date['F11'];//I('post.discount_rate');//收入比率
        $share_rate = $date['F12'];//I('post.share_rate');//分成比率
        $fee = $date['F14'];//I('post.fee');//效果评估
        $price = $date['F8'];//I('post.price');//商品单价
        $number = $date['F7'];//I('post.number');//数量
        $total_fee = $date['F13'];//I('post.total_fee');//付款金额
        $create_time = $date['F1'];//I('post.create_time');//订单创建时间
        $click_time = $date['F2'];//I('post.click_time');//订单单击时间
        $payStatus = $date['F9'];//I('post.payStatus');//订单状态（订单支付 订单失效 订单结算）
        $order_type = $date['F10'];//I('post.order_type');//商品类型 （天猫 淘宝）
        if($order_type=='天猫'){
            $auctionUrl ='https://detail.tmall.com/item.htm?id'.$itemid;//I('post.auctionUrl');//商品地址
        }else{
            $auctionUrl ='https://item.taobao.com/item.htm?id'.$itemid;//I('post.auctionUrl');//商品地址
        }

        $earningTime = $date['F17'];//I('post.earningTime');//结算时间
        $img = '';//I('post.img');//图片地址
        $pid = $date['F27'].'_'.$date['F29'];//I('post.pid');//结算时间
        $model=M('order_data');
        $where['order_id']=$order_id;
        $where['itemid']=$itemid;
        $data['title']=$title;
        $data['itemid']=$itemid;
        $data['discount_rate']=$discount_rate;
        $data['share_rate']=$share_rate;
        $data['fee']=$fee;
        $data['price']=$price;
        $data['number']=$number;
        $data['total_fee']=$total_fee;
        $data['create_time']=$create_time;
        $data['click_time']=$click_time;
        $data['payStatus']=$payStatus;
        $data['order_type']=$order_type;
        $data['auctionUrl']=$auctionUrl;
        $data['earningTime']=$earningTime;
        $data['type']=$type;

        // 增加代理pid
        $pidd=M('user')->where(array('type'=>$type))->getField('pid');
        $array=explode('_', $pidd);
        $dai = $array[0].'_'.$array[1].'_';
        if($type == 'sanduo'){            
            $data['pid']=C('PID').$pid;
        }else {
            $data['pid'] = $dai.$pid;
        }
        
        if($img){
            $data['img']=$img;
        }
        //file_put_contents('/tmp/taobaoke.log',var_export($data, true).'||',FILE_APPEND);

        $res=$model->where($where)->save($data);
        if($res){
            return date("m-d H:i:s")."-".$order_id."更新成功 \r\n";
            //$this->outPut(null, 0, null, '更新成功');
        }else{
            $resd=$model->where($where)->find();
            if($resd){
                return date("m-d H:i:s")."-".$order_id."成功数据存在\r\n";
                //$this->outPut(null, 0, null, '成功数据存在');
            }else{
                $data['order_id']=$order_id;
                $ress=$model->add($data);
                if($ress){
                    return date("m-d H:i:s")."-".$order_id."插入成功\r\n";
                    //$this->outPut(null, 0, null, '插入成功');
                }
            }

        }
    }

    /****
     *
     *
     *
     *
     */
    public function Updateorder()
    {
        $type = I('post.type');//标题
        $order_id = I('post.order_id');//订单编号
        $title = I('post.title');//标题
        $itemid=I('post.itemid');//商品id
        $discount_rate= I('post.discount_rate');//收入比率
        $share_rate = I('post.share_rate');//分成比率
        $fee = I('post.fee');//效果评估
        $price = I('post.price');//商品单价
        $number = I('post.number');//数量
        $total_fee = I('post.total_fee');//付款金额
        $create_time = I('post.create_time');//订单创建时间
        $click_time = I('post.click_time');//订单单击时间
        $payStatus = I('post.payStatus');//订单状态（订单支付 订单失效 订单结算）
        $order_type = I('post.order_type');//商品类型 （天猫 淘宝）
        $auctionUrl = I('post.auctionUrl');//商品地址
        $earningTime = I('post.earningTime');//结算时间
        $img = I('post.img');//图片地址
        $pid = I('post.pid');//结算时间
        $model=M('order_data');
        $where['order_id']=$order_id;
        $where['itemid']=$itemid;
        $data['title']=$title;
        $data['itemid']=$itemid;
        $data['discount_rate']=$discount_rate;
        $data['share_rate']=$share_rate;
        $data['fee']=$fee;
        $data['price']=$price;
        $data['number']=$number;
        $data['total_fee']=$total_fee;
        $data['create_time']=$create_time;
        $data['click_time']=$click_time;
        $data['payStatus']=$payStatus;
        $data['order_type']=$order_type;
        $data['auctionUrl']=$auctionUrl;
        $data['earningTime']=$earningTime;
        $data['pid']=C('PID').$pid;
        $data['type']=$type;
        if($img){
            $data['img']=$img;
        }
        $res=$model->where($where)->save($data);
        if($res){
            $this->outPut(null, 0, null, '更新成功');
        }else{
            $resd=$model->where($where)->find();
            if($resd){
                $this->outPut(null, 0, null, '成功数据存在');
            }else{
                $data['order_id']=$order_id;
                $ress=$model->add($data);
                if($ress){
                    $this->outPut(null, 0, null, '插入成功');
                }
            }

        }
    }

    
} 