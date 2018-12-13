<?php

namespace Guoguo\Controller;
/**
 * Class IndexController
 * @package DDHomeApi\Controller
 */
class IndexController extends CommonController {

    protected $checkUser = false;

    /**
     * @var bool 是否验证uid
     */

    public function index() {
       
        $this->display();
    }
    public function Datetaok(){
        //header("content-type:text/html;charset=utf-8");
        $Taobaoke = new \Common\Org\Taobaoke();
        $url=$Taobaoke->Getcookes();
        $url=iconv("GB2312", "UTF-8", $url);
        //$url=str_replace(array("\r\n"), "", $url);
        //$reg = '/<figure>\s.*<h2 .* _price="(\d.*?\.00)" .* _href="(.*)" .*>(.*)<\/h2>\s.*<figcaption>(.*)<\/figcaption>\s.*<img alt-src=\'(.*)\'>\s.*<\/figure>/';
        $reg = '/<tr .*? class=\"tr_bg\">((.|\n)*?)<\/tr>/i';//2017-7-3 18:36:19-[0-9]+-[0-9]+\s[0-9]+:[0-9]+:[0-9]+)
        $reg = '/<tr style\=\"font-size:13px;\" class\=\"tr_bg\">\s\n.*<td class\=\"zs_shuzi\" style\=\"line-height:22px;\">(.*)\s\n.*<\/td>';
        $reg.='\s\n.*<td height\=\"114\"><a href="(.*)" target=".*" title=".*"><img src="(.*)" width\=\"100\" height\=\"100\" \/><\/a>[\s|\n]*<\/td>'; //<a href="https://item.taobao.com/item.htm?id=548503086180" target="_blank" title="去淘宝查看"><img src="https://img.alicdn.com/imgextra/i3/759474002/TB2PAj2vR8lpuFjSspaXXXJKpXa_!!759474002.jpg_100x100.jpg" width="100" height="100" /></a>
            $reg.='\s\n.*<td class\=\"zs_shuzi\" style=".*">￥(.*)<\/td>';//券后价
            $reg.='\s\n.*<td>\s\n.*<div style=".*">[\s|\n]*((.|\n)*?)[\s|\n]*<\/div>\s*<\/td>';//佣金信息
            $reg.='\s\n.*<td style=".*">[\s|\n]*((.|\n)*?)\s*<\/td>';//优惠券信息
            $reg.='\s\n.*<td style=".*">[\s|\n]*((.|\n)*?)[\n|\n\s].*<\/td>';//导购文案
            $reg.='\s\n.*<td style=".*">((.|\n)*?)<\/td>';//营销素材
            $reg.='\s\n.*<td class\=\"zs_shuzi\" style=".*">(.*)\s*<\/td>';//开始时间
            $reg.='\s\n.*<td style=".*">[\s|\n]*((.|\n)*?)<\/td>';//状态
            $reg.='\s\n.*<td .* class\=\"zs_caozuo\">[\s|\n]*((.|\n)*?)<\/td>\s\n.*<\/tr>/i';
            //$reg.='\s\n.*<td .* class\=\"zs_caozuo\">\s\n.*<a id="(.*)" class\=\"edit_my_order\" title=".*">.*<\/a><a .* title=".*">.*<\/a><a id=".*" class\=\"zsbeizhu\" title=".*">.*<\/a>\s.*<\/td>\s\n.*<\/tr>/i';
         preg_match_all($reg,$url,$data);
        $arr = array('strtime'=>$data[1],'url'=>$data[2],'img'=>$data[3],'price'=>$data[4],'yongjin'=>$data[5],'quan'=>$data[7],'wenan'=>$data[9],'endtime'=>$data[13],'dateurl'=>$data[13],'id'=>$data[13]);
        //dump($data);
        $this->outPut($arr,0);
    }

    public function updatepass(){
        if(IS_POST){
            $uid= $this->uid;
            $password = I('post.password','','trim');
            $npassword= I('post.npassword','','trim');
            $qpassword= I('post.qpassword','','trim');
            if($npassword!=$qpassword){
                $this->redirect_message(U('Index/updatepass'),array('error'=>'两次输入密码不对！'));
            }

            $user=M('tmuser')->where(array('id'=>$uid))->find();
            $password=encryptPwd($password);
            $lpassword=$user['password'];
            if($password!=$lpassword){
                $this->redirect_message(U('Index/updatepass'),array('error'=>'旧密码输入不对！'));
            }

            $map = array(
                'id' => $uid
            );
            $res = M('tmuser')->where($map)->save(array('password'=>encryptPwd($npassword)));
            if ($res) {
                session_destroy();
                redirect(U('Public/login'));
            }else{
                $this->redirect_message(U('Index/updatepass'),array('error'=>'密码修改是不！'));
            }

            redirect(U('Index/index'));
            die();

        }
        $this->display();
    }

    /***
     * 更新果果订单
     */
    public function Updateorder1()
    {
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
        $username = I('post.username');//广告位名称
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
        if($img){
            $data['img']=$img;
        }
        $data['username']=$username;
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
    /**
     *
     * 更新失效订单
     */
    public function Updatesxorder()
    {
        $order_id = I('post.order_id');//订单编号
        $itemid=I('post.itemid');//商品id
        $payStatus = I('post.payStatus');//订单状态（订单支付 订单失效 订单结算）
        $model=M('order_data');
        $where['order_id']=$order_id;
        $where['itemid']=$itemid;
        $data['payStatus']=$payStatus;
        $res=$model->where($where)->save($data);
        if($res){
            $this->outPut(null, 0, null, '更新成功');
        }else{
            $this->outPut(null, 0, null, '状态未变化');
        }
    }
}
