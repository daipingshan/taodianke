<?php
namespace AppAdmin\Controller;

class GoodsController extends CommonController {

    const PAGE = 80;

    public function index(){
        $order = M('items');
        /* 订单值    */
        $pid = I('get.cate_id',NULL,"int");

        if(IS_GET && $pid != null){

            $user = $order->where('pid = "'.$pid.'"')->getField('username');
            // print_r($user);

            $where['pid'] = $pid;
            $Sment = 0;
            if($Sment == 1){
                $where['is_pay'] = 'Y';
            }elseif($Sment == 2){
                $where['is_pay'] = 'N';
            }
            //dump($where);
            $count      = $order->where($where)->count();// 查询满足要求的总记录数
            $Page       = new \Think\Page($count,50);// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show       = $Page->show();// 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $order->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        }else{

            $count      = $order->count();// 查询满足要求的总记录数
            $Page       = new \Think\Page($count,50);// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show       = $Page->show();// 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $order->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        }

        foreach ($list as $k => $v){
            $list[$k]['yongjin'] = round($v['coupon_price']*($v['commission_rate']/10000),1).'元';
        }
        $this->assign('list',$list);// 赋值数据集

        $this->assign('page',$show);// 赋值分页输出
        $this->assign('count',$count); //订单数量


        $this->display();
    }

    /*
     * 	设置9.9包邮分类
     * */
    public function set()
    {
        $items = M('items');
        $where = array(
            'coupon_price'=>array('between','0,9.9'),
            'pass'=>'1',
        );
        $res = $items->where($where)->save(array('coupon_type'=>'4'));
        if($res){
            $this->assign('res','ok');
            $this->success('新增成功', 'index',1);
        }else{
            $this->assign('res','fail');
            $this->error('新增失败', 'index',1);
        }
    }

    /*
    *	去除重复商品
    */
    public function del()
    {
        $items = M('items');
        $goods = $items->field('id,title,num_iid,pass,uname,endtime')->group('num_iid')->having('count(num_iid)>1')->select();
        $data = array();
        $count = 0;
        foreach ($goods as $k => $v) {
            $id = $items->where(array('num_iid'=>$v['num_iid']))->limit('1')->delete();
            $data[$k]['id'] = $v['id'];
            $data[$k]['title'] = $v['title'];
            $data[$k]['num_iid'] = $v['num_iid'];
            $count++;
        }
        echo $count;
        echo '<pre>';
        print_r($data);
        die;
    }

    /*
    *   定期删除
    */
    public function deleteGoods()
    {
        $items = M('items');
        $order = $items->field('id,title,endtime,pass,uname')->where(array('pass'=>'1'))->select();
        $data = array();
        $count = 0;
        $time = strtotime(date('Y-m-d 0:0:0',time()));
        foreach ($order as $k => $v) {
            $endtime = strtotime($v['endtime']);
            if($endtime < $time){
                $data[$k]['id']=$v['id'];
                $count++;
                $info = $items->where(array('id'=>$v['id']))->delete();
            }
        }
        echo $count;
        echo '<pre>';
        print_r($data);
        die;
    }

    //推荐商品
    public function tj_good($id){

    }
    //商品分类
    public function cate(){

        $res=M('items_cate')->order('ordid asc')->select();
        $res=Tree::tree($res);
        $this->res = $res;
        $this->display();

    }

    public function search(){
        $id = I('get.goodid',0,'int');
        $good_name = I('get.goodname',NULL,'string');
        $good = M('items');
        if($id != 0){
            $map['num_iid'] = array('eq',$id);
            $count      = $good->where($map)->count();
            $Page       = new \Think\Page($count,50);
            $show       = $Page->show();
            $list = $good->where($map)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        }elseif($good_name != NULL){

            $map['title'] = array('like',"%".$good_name."%");
            $count      = $good->where($map)->count();
            $Page       = new \Think\Page($count,50);
            $show       = $Page->show();
            $list = $good->where($map)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        }
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('count',$count);
        $this-> display();

    }

    /*
     *	http鏈接x`
     * */
    public function http($url, $cookie)
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_REFERER, 'http://pub.alimama.com/promo/search/index.htm');
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Cookie:{' . $cookie . '}',));
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($this->ch);
        return $result;
    }



}

class Tree{
    //定义一个空的数组
    static public $treeList = array();
    //接收$data二维数组,$pid默认为0，$level级别默认为1
    static public function tree($data,$pid=0,$level = 1){
        foreach($data as $v){
            if($v['pid']==$pid){
                $v['level']=$level;
                self::$treeList[]=$v;//将结果装到$treeList中
                self::tree($data,$v['id'],$level+1);
            }
        }
        return self::$treeList ;
    }
}