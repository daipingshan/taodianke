<?php

namespace Admin\Controller;
/**
 *用户管理
 */
class UserController extends CommonController {

    /**
     * @var bool 是否验证uid
     */

    public function index() {
        
        $query = urldecode(I('get.query','','trim'));
        
        $where = array();
        if($query){
            $where['_string'] = "`name` like '%{$query}%' or `mobile` like '%{$query}%' or telphone like '%{$query}%' or nickname like '%{$query}%'";
        }
        
        $user = M('user');
        $count = $user->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $user->where($where)->limit($limit)->select();
        
        $data = array(
            'query'=>$query,
            'pages'=>$page->show(),
            'list'=>$list,
        );
        $this->assign($data);
        $this->display();
    }
    public function shenqing(){
        $where = array();
        $user = M('feedback');
        $where['type']='申请开店';
        $count = $user->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $user->where($where)->limit($limit)->order('create_time desc,id desc ')->select();

        $data = array(
            'pages'=>$page->show(),
            'list'=>$list,
        );
        $this->assign($data);
        $this->display();
    }
    /**
        * 查看用户详情
        */
    public function info_view(){
        $uid = I('get.uid','','trim');
        if(!$uid){
            $this->redirect_message(U('User/index'),array('error'=>'用户id不能为空！'));
        }
        
        $user_info = M('user')->where(array('id'=>$uid))->find();
        $this->assign($user_info);
        $this->display();
    }
    
    /**
    * 查看优惠券
    */
    public function coupon(){
       
        $where = array();
        $search = array(
            'code'=>I('get.code','','trim'),            
            'consume'=>I('get.consume','','trim')
        );
        if(trim($search['code'])){
            $where['code'] = $search['code'];
        }
        if(trim($search['consume'])){
            $where['consume'] = $search['consume'];
        }

        $card = M('card');
        $count = $card->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;        
        $list =  $card->where($where)->limit($limit)->select();
        $data = array(            
            'pages' => $page->show(),
            'list' => $list,
        );        
        $this->assign($data);
        $this->display();
    }
    
    /**
     * 开启环信 账号密码
     */
    public function open_ease_mob_username(){
        $uid = I('get.uid','','trim');
        if(!$uid){
            $this->ajaxReturn(array('code'=>-1,'error'=>'开启的用户名id不能为空！'));
        }
        
        $user_info = M('user')->where(array('id'=>$uid))->field(array('id,ease_mob_id,ease_mob_password'))->find();
        if(!$user_info){
            $this->ajaxReturn(array('code'=>-1,'error'=>'开启的用户不存在！'));
        }
        
        if(isset($user_info['ease_mob_id']) && trim($user_info['ease_mob_id'])){
            $this->ajaxReturn(array('code'=>-1,'error'=>'已经开启环信账号，不要重复开启！'));
        }
        
        if(isset($user_info['ease_mob_password']) && trim($user_info['ease_mob_password'])){
            $this->ajaxReturn(array('code'=>-1,'error'=>'已经开启环信账号，不要重复开启！'));
        }
        
        $ease_mob = new \Common\Org\Easemob();
        $res = $ease_mob->register_user($uid);
        if(isset($res['error']) && trim($res['error'])){
            $this->ajaxReturn(array('code'=>-1,'error'=>'开启失败！'.$res['error']));
        }
        
        $data = array(
            'ease_mob_id'=>isset($res['username'])?$res['username']:'',
            'ease_mob_password'=>isset($res['password'])?$res['password']:'',
        );
        $res = M('user')->where(array('id'=>$uid))->save($data);
        if(!$res){
            $this->ajaxReturn(array('code'=>-1,'error'=>'开启失败！'));
        }
        
        $this->ajaxReturn(array('code'=>0,'success'=>'开启成功！'));
    }

}
