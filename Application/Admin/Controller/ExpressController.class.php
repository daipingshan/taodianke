<?php

namespace Admin\Controller;

/**
 * 快递服务控制器 
 */
class ExpressController extends CommonController {    

    /**
     * 获取快递服务所有订单
     */
    public function index() {                   
        $where = array();
        $merchant = M('merchant');
        $count = $merchant->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $merchant->where($where)->limit($limit)->select();        
        foreach ($list as &$v) {            
            $map=array(
                'mid'=>$v['id'],
                'status'=>'1'
            );
            $arrivecount = M('express')->where($map)->count('id');
            $smap=array(
                'mid'=>$v['id'],
                'status'=>'2'
            );
            $signcount = M('express')->where($smap)->count('id');
            $v['arrive'] = $arrivecount;//已到站
            $v['sign'] = $signcount;//已签收
            $v['sumsms'] = $v['arrive'] + $v['sign'];//发送的短信数
        }        
        foreach ($list as $key => $value) {
            $arrive[$key] = $value['arrive'];                
            $id[$key] = $value['id'];                
        }
        array_multisort($arrive,SORT_DESC,$id,SORT_DESC, $list); 
        $data = array(
            'pages' => $page->show(),
            'list' => $list,            
        );
        $this->assign($data);
        $this->display();

    }    

    /**
     * 获取每个商家的快递单详情
     */
    public function info_view() {
        $mid = I('get.mid',0,'intval');        
        $where = array();        
        $where['mid'] = $mid;        
        $exp = M('express');
        $count = $exp->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $exp->where($where)->limit($limit)->order('in_time desc')->select();
        foreach ($list as  &$v) {            
            $map =array(
                'id'=>$v['mid'],
            );            
            $mer = M('merchant')->where($map)->field('id,username')->find();
            $v['m_name'] = $mer['username'];            
        }
        $data = array(
            'pages' => $page->show(),
            'list' => $list,
            'search' => $search,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 获取模版信息服务所有订单
     */
    public function sms() {        
        $search=array(
            'merid'=>I('get.merid',0,'intval'),
            'status'=>I('get.status',0,'intval'),
        );
        
        $where = array();
        if(trim($search['merid'])){
            $where['uid'] = $search['merid'];
        }
        if(trim($search['status'])){
            $where['status'] = $search['status'];
        }

        $merchant =M('merchant')->field('id,username')->select();
        
        $sms = M('sms_template');
        $count = $sms->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $sms->where($where)->order('create_time desc')->limit($limit)->select();        
        foreach ($list as &$v) {            
            $map=array(
                'id'=>$v['uid'],                
            );
            $mer = M('merchant')->where($map)->field('id,username')->find();
            $v['username'] = $mer['username'];//发送的短信数
        }     
        $data = array(
            'pages' => $page->show(),
            'list' => $list,            
            'merchant' => $merchant,            
        );
        $this->assign($data);
        $this->display();
    } 

    /**
     * 通过审核
     */
    public function passed() {
        $id = I('get.id', '', 'trim');
        if (!$id) {
            $this->ajaxReturn(array('code' => -1, 'error' => '修改的短信模版不存在！'));
        }

        $sms = M('sms_template');
        $where = array('id' => $id);
        $sms_count = $sms->where($where)->count();
        if (!$sms_count || $sms_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '你修改的短信不存在！'));
        }
        $data=array(
            'status'=>3
        );
        
        $res = $sms->where($where)->save($data);
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '通过审核失败！'));
        }
        $this->ajaxReturn(array('code' => 0, 'success' => '通过审核成功！'));
    }

    /**
     * 不通过审核
     */
    public function no_passed() {
        $id = I('get.id', '', 'trim');
        if (!$id) {
            $this->ajaxReturn(array('code' => -1, 'error' => '修改的短信模版不存在！'));
        }
        $sms = M('sms_template');
        $where = array('id' => $id);
        $sms_count = $sms->where($where)->count();
        if (!$sms_count || $sms_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '你修改的短信不存在！'));
        }
        $data=array(
            'status'=>1
        );        
        $res = $sms->where($where)->save($data);
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '不通过审核失败！'));
        }
        $this->ajaxReturn(array('code' => 0, 'success' => '不通过审核成功！'));
    }

    /**
     * 添加短信模版  2016.08.10
     */
    public function add() {
        if (IS_POST) {
            $sms_data = I('post.sms', array(), '');
            $sms_data['create_time']=time();
            $sms_data['uid']=0;
            $sms_data['status']=3;
            
            $res = M('sms_template')->add($sms_data);
            if (!$res) {
                $this->redirect_message(U('Express/add'), array('error' => '短信模版添加失败！'));
            }
            $this->redirect_message(U('Express/sms'), array('success' => '短信模版添加成功！'));
            die();
        }
        $f_cate_list = M('category')->where(array('fid' => 0))->field('id,name')->select();

        $data = array(
            'f_cate_list' => $f_cate_list,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 编辑短信模版2016.08.10
     */
    public function edit() {
        if (IS_POST) {
            $sms_data = I('post.sms', array(), '');
            $sms_id = I('post.id', array(), '');            
            $res = M('sms_template')->where(array('id' => $sms_id))->save($sms_data);
            if ($res === false) {
                $this->redirect_message(U('Express/edit', array('aid' => $agent_id)), array('error' => '短信模版编辑失败！'));
            }
            $this->redirect_message(U('Express/sms'), array('success' => '短信模版编辑成功！'));
            die();
        }
        $sms_id = I('get.id', '', '');
        $sms_info = M('sms_template')->where(array('id' => $sms_id))->find();
        if (!$sms_info) {
            $this->redirect_message(U('Express/sms'), array('error' => '短信模版不存在！'));
        }
        $data = array(            
            'data' => $sms_info
        );
        $this->assign($data);
        $this->display();
    }   

    /**
     * 删除短信模版2016.08.10
     */
    public function del() {
        $id = I('get.id', '', 'trim');
        if (!$id) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除的短信模版不存在！'));
        }
        $sms = M('sms_template');
        $where = array('id' => $id);
        $sms_count = $sms->where($where)->delete();
        if (!$sms_count) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除短信模版失败！'));
        }
        $this->ajaxReturn(array('code' => 0, 'success' => '删除短信模版成功！'));
    }

}
