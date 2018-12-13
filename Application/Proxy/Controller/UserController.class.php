<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/20 0020
 * Time: 上午 10:00
 */

namespace Proxy\Controller;

/**
 * Class UserController
 *
 * @package Proxy\Controller
 */
class UserController extends CommonController{

    /**
     * 我的代理
     */
    public function index(){
        $mobile = I('get.mobile', 0, 'int');
        if(session('proxy_type') == 1){
            $pid = session('pid');
            list($one, $two, $_, $_) = explode('_', $pid);
            $map['_string'] = "pid like '{$one}_{$two}%' AND pid <> '{$pid}'";
        }else{
            $map['ParentID'] = session('Auth_user');
        }
        if($mobile){
            $map['mobile'] = $mobile;
        }
        $model = M('user');
        $count = $model->where($map)->count('id');
        $page  = $this->pages($count, 20);
        $list  = $model
            ->alias('u')
            ->join('left join ytt_user_proxy_ratio p ON p.uid = '.session('Auth_user').' and p.cid = u.id')
            ->where($map)
            ->field('u.*,p.dip')
            ->order('u.id desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($list as $key => $val) {
            $list[$key]['dip']  = isset($val['dip']) ? intval($val['dip']) : 100;
        }
        $this->assign(array('list' => $list, 'page' => $page->show(), 'count' => $count));
        $this->display();
    }

    /**
     * 编辑我的信息
     */
    public function myInfo(){
        $map['id'] = $_SESSION['Auth_user'];
        $info      = M('user')->where($map)->find();
        $this->assign('userinfo', $info);
        $this->display();
    }

    /**
     * 保存我的信息
     */
    public function editInfo(){
        if(IS_POST){
            $data['email']    = I('post.email', '', 'trim');
            $data['username'] = I('post.username', '', 'trim');
            $data['realname'] = I('post.realname', '', 'trim');
            $data['qq']       = I('post.qq', '', 'trim');
            $pwd              = trim(I('post.pwd', null, 'string'));
            if($pwd != '' || $pwd != null){
                $data['password'] = md5($pwd);
            }
            $map['id'] = $_SESSION['Auth_user'];
            $res       = M('user')->where($map)->save($data);
            if($res !== false){
                $this->success('操作成功',U('User/index'));
            }else{
                $this->error('操作失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }

    /**
     * 设置分成比率
     */
    public function setRatio(){
        $user_id   = I('get.user_id', 0, 'int');
        $parent_id = M('user')->getFieldById($user_id, 'ParentID');
        if($parent_id != session('Auth_user')){
            $this->error('对不起，该用户不属于你的下线，无法设置分成比率，请联系管理员！');
        }
        $where = array('uid' => session('Auth_user'), 'cid' => $user_id);
        $info  = M('user_proxy_ratio')->where($where)->find();
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 保存分成比率
     */
    public function saveRatio(){
        if(IS_POST){
            $user_id = I('post.user_id',0,'int');
            $dip     = I('post.dip','','trim');
            $remarks = I('post.remarks','','trim');
            if(!$user_id){
                $this->error('请求参数不合法！');
            }
            $parent_id = M('user')->getFieldById($user_id, 'ParentID');
            if($parent_id != session('Auth_user')){
                $this->error('对不起，该用户不属于你的下线，无法设置分成比率，请联系管理员！');
            }
            $dip = (int)$dip;
            if($dip <= 10){
                $this->error('分成比率必须大于10！');
            }
            if($dip > 100){
                $this->error('分成比率不能超过100！');
            }
            $id = M('user_proxy_ratio')->where(array('uid'=>session('Auth_user'),'cid'=>$user_id))->getField('id');
            if($id){
                $data = array('uid'=>session('Auth_user'),'cid'=>$user_id,'id'=>$id,'dip'=>$dip,'remarks'=>$remarks);
                $res  = M('user_proxy_ratio')->save($data);
            }else{
                $data = array('uid'=>session('Auth_user'),'cid'=>$user_id,'dip'=>$dip,'remarks'=>$remarks);
                $res  = M('user_proxy_ratio')->add($data);
            }
            if($res !== false){
                $this->success('操作成功',U('User/index'));
            }else{
                $this->error('操作失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }

    /**
     * 我的顾客
     */
    public function customer(){
        $where = array('proxy_pid' => $this->userinfo['pid']);
        $count  = M('wxuser')->where($where)->count();
        $page   = $this->pages($count, $this->limit);
        $customer = M('wxuser')->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('add_time desc')->select();
        $user =  $user_ids = array();
        foreach ($customer as $k => $v) {
            $user_ids[$v['proxy_pid']] = $v['proxy_pid'];
        }
        if ($user_ids) {
            $user = M('user')->where(array( 'pid' => array( 'in', array_keys($user_ids) )))->field('pid, real_name')->index('pid')->select();
        }
        $this->assign('customer', $customer);
        $this->assign('user', $user);
        $this->assign('page', $page->show());
        $this->display();
    }
}