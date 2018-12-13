<?php
/**
 * Created by PhpStorm.
 * User: superegoliu
 * Date: 2016/7/12
 * Time: 9:50
 */
namespace Fxapi\Controller;
class TaobaokeApiController extends CommonController {
    protected $checkUser = false;
    public function index(){
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>异步查询接口 </div>','utf-8');
    }
    public function GetYongjinn($id,$uid){
        $model = D('User');
        $userRes = $model->where(array('id'=>$uid))->field('mid')->find();
        if($userRes['mid']){
            $mm=$userRes['mid'];
        }else{
            $mm=C('PIDMM');
        }
        $Taobaoke = new \Common\Org\Taobaoke();
        $url=$Taobaoke->fanliurl($mm,$id);
        return $url;
    }
    public function GetYongjin(){
        $this->check();
        $url = I('post.url', '', 'trim');
        // 验证
        $error = [];
        if ($url == '') {
            $error[] = '查询的商品地址不能为空';
        }
        $uid=$this->uid;
        if (!empty($error)) {
            $this->outPut(null, -1, null, implode('|', $error));
        }

        $Taobaoke = new \Common\Org\Taobaoke();
        $key=$url;
        $pchace=S($key);
        if($pchace){
            $jig= $pchace;
        }else{
            $jig=$Taobaoke->Yongjin($url);

        }
        if(is_array($jig)){
            $url="http://ff.win.taobao.com/promotion.htm?id=".$jig[0]['sellerId']."&cc=taoke&aid=0";
            $promotiont=$this->getCoupon($url);
            if($promotiont){
                $jig[0]['promotion']=$promotiont;
            }
            S($key, $jig,C('CAHCETIME'));
            $data['tkCommFee']=$jig[0]['tkCommFee'];//佣金
            $data['reservePrice']=$jig[0]['reservePrice'];//商品原价
            $data['zkPrice']=$jig[0]['zkPrice'];//商品单价
            $data['title']=$jig[0]['title'];//商品标题
            $data['pictUrl']=$jig[0]['pictUrl'];//商品图片
            $data['sellerId']=$jig[0]['sellerId'];//商家id
            $data['cout']="<table style=border: solid 1px #B4B4B4;height:25px;padding:4px; >".
                         "<tr><td width=100px  rowspan=3><img width=100px height=100px src=".$data['pictUrl']." /></td><td width=100px colspan=4>佣金</td><td width=100px colspan=4>".$data['tkCommFee']."</td></tr>".
                         "<tr><td width=120px colspan=4>商品原价</td><td width=100px colspan=4>".$data['reservePrice']."</td></tr>".
                        "<tr><td width=120px colspan=4>商品单价</td><td width=100px colspan=4>".$data['zkPrice']."</td></tr>".
                         "<tr><td width=80px colspan=4>标题</td><td width=100% colspan=10>".$data['title']."</td></tr></table>";

            $data['promotion']=$jig[0]['promotion'];
            //获取推广地址
            $id=$jig[0]['auctionId'];
            $urlkey=$id.$uid;
            $url=S($urlkey);
            if($url){
                $data['url']=$url;
            }else{

                $urll=self::GetYongjinn($id,$uid);
                if($urll){
                    $data['url']=$url=$urll[0]['ds_item_click'];
                    S($urlkey, $url,3600*10);
                }else{
                    $data['url']='';
                }
            }
            //获取推广地址结束
            $this->outPut($data, 0, null);
        }else{
            $this->outPut(null, -1, null, '此商品无返利');
        }
    }

    public function getInfo($key) {
        $model = M('option');
        $val = $model->where(['key'=>$key])->getField('val');
        $data = [$key=>$val];
        $this->outPut($data, 0, null);
    }

    public function setInfo($key, $val) {
        $model = M('option');
        $info = $model->where(['key'=>$key])->find();
        if ($info) {
            $model->where(['key'=>$key])->setField('val',$val);
        } else {
            $model->add(['key'=>$key,'val'=>$val]);
        }
        $this->outPut(null, 0, null);
    }

    public function getCoupon($filename = '') {
        if ($filename == '') {
            return false;
        }
        $content = file_get_contents($filename);
        if (!$content) {
            return false;
        } else {
            preg_match_all('/\<a class\=\"mod\-a col\-xs\-20 col\-l\-15\"([.\S\s]*?)\<\/a\>/i', $content, $matches);
            $htmls = $matches[0];
            if (empty($htmls)) {
                return false;
            } else {
                $data = [];
                foreach($htmls as $i => $html) {
                    preg_match_all('/\<span class\=\"mod\-a\-a\-b\"\>([.\S\s]*?)\<\/span\>/i', $html, $money);
                    preg_match_all('/\<span class\=\"num mod\-a\-c\-b\"\>([.\S\s]*?)\<\/span\>/i', $html, $date);
                    preg_match_all('/\<span href\=\"([.\S\s]*?)\" class\=\"get\-btn\"\>/i', $html, $link);
                    preg_match_all('/\<span class\=\"mod\-a\-b\"\>([.\S\s]*?)\<span class\=\"line\"\>/i', $html, $cond);
                    preg_match_all('/\<span class\=\"line\"\>([.\S\s]*?)\<\/span\>/i', $html, $yond);
                    $data = [
                        'money' => $money[1][0],
                        'date'  => $date[1][0],
                        'link'  => $link[1][0],
                        'cond'  => preg_replace('/[\s]*/', '', strip_tags($cond[1][0])),
                        'yond'  => ($yond[1][0])
                    ];

                   $trs[] = "<tr><td>{$data['money']}元优惠券</td><td>{$data['date']}</td><td>{$data['cond']}</td><td>{$data['yond']}</td><td><a target='_blank' href='http:{$data['link']}'>去领取</a></td></tr>";
                }
                $table = implode('', $trs);
                $html = '<table style=border: solid 1px #B4B4B4; width:100%;>'.$table.'</table>';
                return $html;
            }
        }
        
    }
    /*
     * 获取转换后的地址
     */
    public function ZhuangUrl(){
        $url = I('get.url', '', 'trim');
        // 验证
        $error = [];
        if ($url == '') {
            $error[] = '查询的商品地址不能为空';
        }
        if (!empty($error)) {
            $this->outPut(null, -1, null, implode('|', $error));
        }
        $Taobaoke = new \Common\Org\Taobaoke();
        $url=$Taobaoke->ZhuanhUrl($url);
        /*if(strstr($url,'taobao.com')||strstr($url,'tmall.com')){
            $url=explode('?',$url);
            $http=$url[0];
            $url=$url[1];
            $id=explode('id=',$url);
            $idd=explode('&',$id[1]);
            $idd=$idd[0];
            $urll=$http.'?id='.$idd;
            $url['id']=$idd;
            $url['url']=$urll;
        }else{
            $urll='';
        }*/
        echo $url;
        //return $url;
    }
    public function ZhuanFanli(){
        file_put_contents('/tmp/taobao.log',var_export($_POST, true).'||',FILE_APPEND);
        $url = I('post.url', '', 'trim');
        $id = I('post.id', '', 'trim');
        if(strstr($url,'taobao.com')||strstr($url,'tmall.com')){
            //https://item.taobao.com/item.htm?spm=a230r.1.14.31.L5pOgb&id=527464305878&ns=1&abbucket=5
            if($id){
                $url=explode('?',$url);
                $http=$url[0];
                $url=$url[1];
                $urll=$http.'?id='.$id;
                $url['id']=$id;
                $url['url']=$urll;
            }else{
                $urll=$url;
                $url=explode('=',$url);
                $id=$url[1];
            }

        }else{

        }

        //获取返利金额
        $Taobaoke = new \Common\Org\Taobaoke();
        $key=$id;
        $pchace=S($key);
        if($pchace){
            $jig= $pchace;
        }else{
            $jig=$Taobaoke->Yongjin($urll);
            S($key, $jig,C('CAHCETIME'));
        }
        if(!is_array($jig)){
            echo $urll.'没有返利';exit;
        }
        //获取返利结束
        //获取推广地址
        $id=$jig[0]['auctionId'];
        $urlkey=$urll;
        $url=S($urlkey);
        if(is_array($url)){
            $data['url']=$url['url'];
        }else{
            $urlyj=self::GetYongjinn($id,'12');
            if($urlyj){
                $data['url']=$urlyj[0]['ds_item_click'];
            }else{
                $data['url']='';
            }
        }
        //获取推广地址结束
        //转换为短地址
        if($url['dzz']){
            $dzz=$url['dzz'];
        }else{
            $Taobaokee = new \Common\Org\Taobaoke();
            $dzz=$Taobaokee->Zhauandurl($data['url']);
            $data['dzz']=$dzz;
            S($urlkey,$data,3600*10);
        }

        //转换短地址结束
        $jg= $urll."  ".$jig[0]['title']." \r\n 原价：".$jig[0]['reservePrice']." \r\n 现价：".$jig[0]['zkPrice']." \r\n 佣金：".$jig[0]['tkCommFee']." \r\n "."去下单".$dzz;
        echo $jg;
    }
} 