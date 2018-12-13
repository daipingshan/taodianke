<?php

namespace Zhaoshang\Controller;
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
    /****
     * 大淘客相关接口
     *
     */
    public function NewUpdateorder(){
        $goods = I('post.josn');//标题
        $biaoshi= I('post.biaoshi');//标题
        file_put_contents('/tmp/dataoke.log',var_export($goods, true).'||',FILE_APPEND);
        //$goods = base64_decode(str_replace(array('%2b', ' '), '+', urldecode($goods)));
        $goods_arr = @json_decode($goods, true);
        file_put_contents('/tmp/dataoke.log',var_export($goods_arr, true).'||',FILE_APPEND);
        if(is_array($goods_arr)){
            $data=$goods_arr['dingdan'];
        }
        unset($data[0]);
        $data=array_values($data);
        file_put_contents('/tmp/dataoke.log',var_export($data, true).'||',FILE_APPEND);
        $jgg='';
        foreach($data as $ke=>$vad){

            $jgg.=self::NewUpdate($vad,$biaoshi);

        }
        $this->outPut(null, 0, null,$jgg);
    }
    public function NewUpdate($date,$biaoshi)
    {
        usleep(50000);
        $did = $date['did'];//大淘客编号
        $stime = $date['stime'];//提交时间
        $taourl=$date['taourl'];//淘宝商品地址
        $img= $date['img'];//图片地址
        $price = $date['price'];//券后价
        $yongjin = $date['yongjin'];//佣金
        $yongjinurl = $date['yongjinurl'];//佣金地址

        $quan = $date['quan'];//优惠券信息
        $quanendtime = $date['quanendtime'];//I('post.total_fee');//付款金额
        $quanstr = $date['quanstr'];//优惠券地址
        $wenan = $date['wenan'];//文案
        $ktime = $date['ktime'];//开始时间
        $dataourl = $date['dataourl'];//大淘客地址
        $type=$date['type'];//订单类型
        $zhuangtai=$date['zhuangtai'];//

        $model=M('zhaoshang');
        $where['did']=$did;
        $data['stime']=$stime;
        $data['taourl']=$taourl;
        $data['img']=$img;
        $data['price']=$price;
        $data['yongjin']=$yongjin;
        $data['yongjinurl']=$yongjinurl;

        $data['quanendtime']=$quanendtime;
        $data['quanstr']=$quanstr;
        $data['wenan']=$wenan;
        $data['type']=$type;
        if($type=='2' ||$type=='3' ){
            $data['zhuangtai']=$dataourl;
        }else{
            $data['dataourl']=$dataourl;
        }
        $data['biaoshi']=$biaoshi;
        //file_put_contents('/tmp/taobaoke.log',var_export($data, true).'||',FILE_APPEND);

        $res=$model->where($where)->save($data);
        if($res){
            return date("m-d H:i:s")."-".$did."更新成功 \r\n";
            //$this->outPut(null, 0, null, '更新成功');
        }else{
            $resd=$model->where($where)->find();
            if($resd){
                return date("m-d H:i:s")."-".$did."成功数据存在\r\n";
                //$this->outPut(null, 0, null, '成功数据存在');
            }else{
                $data['did']=$did;
                $ress=$model->add($data);
                if($ress){
                    return date("m-d H:i:s")."-".$did."插入成功\r\n";
                    //$this->outPut(null, 0, null, '插入成功');
                }
            }

        }
    }
}
