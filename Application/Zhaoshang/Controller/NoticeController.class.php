<?php

namespace Admin\Controller;
/**
 *公告管理
 */
class NoticeController extends CommonController {

    /**
     * @var bool 是否验证uid
     */

    public function index() {
       
        $search = array(
            'query' => urldecode(I('get.query', '', 'trim')),
        );

        $where = array(
        
        );

        if (trim($search['query'])) {
            $where['title'] = array('like', "%{$search['query']}%");
        }
        
        $notice = M('notice');
        $count = $notice->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $notice->where($where)->limit($limit)->order('id desc')->select();
        
        $data = array(
            'pages' => $page->show(),
            'list' => $list,
            'search' => $search,
        );
        $this->assign($data);
        $this->display();
        
    }
    
    /**
        * 删除公告
        */
    public function delete(){
        $nid = I('get.nid',0,'intval');
        if(!$nid){
            $this->ajaxReturn(array('code'=>-1,'error'=>'删除的公告id不能为空！'));
        }
        
        $notice = M('notice');
        $where = array(
            'id'=>$nid
        );
        $notice_count = $notice->where($where)->count();
        if(!$notice_count || $notice_count<=0){
            $this->ajaxReturn(array('code'=>-1,'error'=>'删除的公告不存在！'));
        }
        $res = $notice->where($where)->delete();
        if(!$res){
            $this->ajaxReturn(array('code'=>-1,'error'=>'公告删除失败！'));
        }
        $this->ajaxReturn(array('code'=>0,'success'=>'公告删除成功！'));
    }
    
    /**
     * 公告 添加
     */
    public function add(){
         if (IS_POST) {
            $notice_data = I('post.notice', array(), '');
            $notice_data['create_time'] = time();
            $res = M('notice')->add($notice_data);
            if (!$res) {
                $this->ajaxReturn(array('code' => -1, 'error' => '公告添加失败！'));
            }
            $this->ajaxReturn(array('code' => 0, 'success' => '公告添加成功！'));
            die();
        }

        $data = array(

        );
        $this->assign($data);
        $this->display();
    }
    
     /**
     * 公告 编辑
     */
    public function edit(){
         if (IS_POST) {
            $notice_data = I('post.notice', array(), '');
            $notice_id = I('post.nid', 0, 'intval');
            
            if(!$notice_id){
                $this->ajaxReturn(array('code' => -1, 'error' => '公告id不能为空！'));
            }
            
            $res = M('notice')->where(array('id'=>$notice_id))->save($notice_data);
            if ($res === false) {
                $this->ajaxReturn(array('code' => -1, 'error' => '公告编辑失败！'));
            }
            $this->ajaxReturn(array('code' => 0, 'success' => '公告编辑成功！'));
            die();
        }
        
        $notice_id = I('get.nid', 0, 'intval');
        $notice_info = array();
        if($notice_id){
            $notice_info = M('notice')->where(array('id'=>$notice_id))->find();
        }
        
        $data = array(
           'data'=>$notice_info
        );
        $this->assign($data);
        $this->display();
    }
    
    /**
     * 添加编辑数据 检测
     */
    public function check_add_edit_data(){
        $notice_data = I('post.notice', array(), '');
        if(!isset($notice_data['title']) || !trim($notice_data['title'])){
            $this->ajaxReturn(array('code' => -1, 'error' => '公告标题不能为空！'));
        }
        if(!isset($notice_data['content']) || !trim($notice_data['content'])){
            $this->ajaxReturn(array('code' => -1, 'error' => '公告内容不能为空！'));
        }
        
        $this->ajaxReturn(array('code' => 0));
    }

}
