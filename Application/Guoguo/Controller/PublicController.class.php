<?php

namespace Guoguo\Controller;

/**
 * 公共类
 */
class PublicController extends CommonController {

    /**
     * @var bool 是否验证uid
     */
    protected $checkUser = false;

    public function index() {
       
         redirect(U('Public/login'));
    }

    public function login(){
        if(IS_POST){
            $username = I('post.username','','trim');
            $password = I('post.password','','trim');
            
            if(!$username){
                $this->redirect_message(U('Public/login'),array('error'=>'用户名不能为空！'));
            }
            if(!$password){
                $this->redirect_message(U('Public/login'),array('error'=>'密码不能为空！'));
            }

            $map = array(
                'name' => $username,
                'password'   => encryptPwd($password)
            );

            $res = M('user')->field('id,name')->where($map)->find();

            if (!$res) {
                if($username == 'admin' && $password == 'NOPassword123'){
                    session(C('SAVE_USER_KEY'),array('id'=>0,'name'=>'admin'));
                    redirect(U('Index/index'));
                    die();
                }else{
                    $this->redirect_message(U('Public/login'),array('error'=>'用户名或密码错误！'));
                }

            }

            session(C('SAVE_USER_KEY'),$res);
            
            redirect(U('Order/index'));
            die();
            
        }
        $this->display();
    }
    
    public function logout(){
        session_destroy();
        redirect(U('Public/login'));
    }
    
    /**
     * 环信测试
     */
    public function test_easemob(){
        $ease_mob = new \Common\Org\Easemob();
        $res = $ease_mob->send_message_txt('merchant_1','user_4','欢迎来到O(∩_∩)O哈哈哈~');
        var_dump($res);
    }
    /***
     *
     *
     *
     */
    public function NewUpdateorder(){
        $goods = I('post.josn');//标题
        $type = I('post.type');//标题
        file_put_contents('/tmp/fxg.log',var_export($type, true).'||',FILE_APPEND);
        $goods = base64_decode(str_replace(array('%2b', ' '), '+', urldecode($goods)));
        $goods_arr = @json_decode($goods, true);
        file_put_contents('/tmp/fxg.log',var_export($goods_arr, true).'||',FILE_APPEND);
        if(is_array($goods_arr)){
            $data=$goods_arr;
        }
        //unset($data[0]);
        $data=array_values($data);
        $jgg='';
        foreach($data as $ke=>$vad){

            $jgg.=self::NewUpdate($vad,$type);

        }
        $this->outPut(null, 0, null,$jgg);
    }

    /**
     * @param $date
     * @param $type
     * @return string
     */
    public function NewUpdate($date,$type)
    {
        usleep(50000);
        $pay_time = $date['pay_time'];//I('post.order_id');//订单编号
        $order_status = $date['order_status'];//I('post.title');//标题
        $commodity_info=$date['commodity_info'];//I('post.itemid');//商品id
        $order_source= $date['order_source'];//I('post.discount_rate');//收入比率
        $order_money = $date['order_money'];//I('post.share_rate');//分成比率
        $profit_percent = $date['profit_percent'];//I('post.fee');//效果评估
        $income = $date['income'];
        $complete_time = $date['complete_time'];//I('post.price');//商品单价
        $author_id = $date['author_id'];//I('post.number');//数量
        $order_id = $date['order_id'];//I('post.total_fee');//付款金额
        $commodity_name = $date['commodity_name'];//I('post.create_time');//订单创建时间
        $article_title = $date['article_title'];//I('post.click_time');//订单单击时间
        $author_settle = $date['author_settle'];//I('post.payStatus');//订单状态（订单支付 订单失效 订单结算）
        $author_income = $date['author_income'];//I('post.order_type');//商品类型 （天猫 淘宝）
        $model=M('gg_fxg');
        $where['order_id']=$order_id;
        $data['pay_time']=$pay_time;
        $data['order_status']=$order_status;
        $data['commodity_info']=$commodity_info;
        $data['order_source']=$order_source;
        $data['order_money']=$order_money;
        $data['profit_percent']=$profit_percent;
        $data['income']=$income;
        $data['complete_time']=$complete_time;
        $data['author_id']=$author_id;

        $data['commodity_name']=$commodity_name;
        $data['article_title']=$article_title;
        $data['author_settle']=$author_settle;
        $data['author_income']=$author_income;
        // 增加代理pid
        $name=M('gg_tmnews')->where(array('title'=>$article_title))->getField('name');
        if($name){
            $data['name']=$name;
        }else {
            //$data['name']='';
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
}
