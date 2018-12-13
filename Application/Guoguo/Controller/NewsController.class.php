<?php

namespace Guoguo\Controller;

/**
 * 代理商管理
 */
class NewsController extends CommonController {

    public function index() {
        $query = urldecode(I('get.query','','trim'));

        $where = array();
        if($query){
            $where['_string'] = "`title` like '%{$query}%'";
        }
        $uid= $this->uid;
        $user=M('user')->where(array('id'=>$uid))->find();
        if($uid!=0){
            switch ($user['name'])
            {

                case '刘飞':
                    $where['name']=array('in',array('孙玉','王丹','王需','林萍','姚进','刘飞'));
                    break;
                case '王思敏':
                    $where['name']=array('in',array('秦佩琪','王思敏','王蒙','徐庆'));
                    break;
                case '仰宗虎':
                    break;
                default:
                    $where['name']=$user['name'];
            }

        }
        $article = M('ytt_gg_tmnews');
        $count = $article->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $article->where($where)->limit($limit)->order('id desc')->select();
        //var_dump($article->getLastSql());exit;
        $data = array(
            'query'=>$query,
            'pages' => $page->show(),
            'list' => $list,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 添加代理商
     */
    public function add() {

        if (IS_POST) {
            $uid= $this->uid;
            $user=M('user')->where(array('id'=>$uid))->find();
            $article_data = I('post.article', array(), '');
            $article_data['time'] = time();
            $article_data['name']=$user['name'];
            $res = M('ytt_gg_tmnews')->add($article_data);
            if (!$res) {
                $this->redirect_message(U('News/add'), array('error' => '文章添加失败！'));
            }
            $this->redirect_message(U('News/index'), array('success' => '文章添加成功！'));
            die();
        }

        $this->display();
    }

    /**
     * 编辑新闻
     */
    public function edit() {
        if (IS_POST) {
            $article_data = I('post.article', array(), '');
            $article_id = I('post.id', array(), '');
            $res = M('ytt_gg_tmnews')->where(array('id' => $article_id))->save($article_data);
            if ($res === false) {
                $this->redirect_message(U('News/edit', array('aid' => $article_id)), array('error' => '文章编辑失败！'));
            }
            $this->redirect_message(U('News/index'), array('success' => '文章编辑成功！'));
            die();
        }

        $article_id = I('get.id', '', '');

        $article_data = M('ytt_gg_tmnews')->where(array('id' => $article_id))->find();

        $data = array(
            'data' => $article_data
        );
        $this->assign($data);
        $this->display();
    }

    /**
     *  添加 编辑  数据监测
     */
    public function check_add_edit_data() {
        $article_data = I('post.article', array(), '');        

        if (!isset($article_data['title']) || !trim($article_data['title'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '文章标题不能为空！'));
        }

        $this->ajaxReturn(array('code' => 0));
    }

    /**
     * 删除新闻
     */
    public function delete() {
        $aid = I('get.aid', '', 'trim');        
        if (!$aid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除新闻的id不能为空！'));
        }
        $article = M('article');
        $where = array('id' => $aid);
        $article_count = $article->where($where)->count();
        if (!$article_count || $article_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '你删除的新闻不存在！'));
        }
        $where = array('id' => $aid);
        $res = $article->where($where)->delete();
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除新闻失败！'));
        }
        $this->ajaxReturn(array('code' => 0, 'success' => '删除新闻成功！'));
    }

    /**
     * 意见反馈
     */
    public function feedback() {
        $query = urldecode(I('get.query','','trim'));

        $where = array();
        if($query){
            $where['_string'] = "`name` like '%{$query}%'";
        }

        $feedback = M('feedback');
        $count = $feedback->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $feedback->where($where)->limit($limit)->order('id desc')->select();

        $data = array(
            'query'=>$query,
            'pages' => $page->show(),
            'list' => $list,
        );
        $this->assign($data);
        $this->display();
    }

    /*****
     *
     *
     * 搜索文章
     */
   public function searsch(){
       $time =I('get.time');// $this->_get('time');
       $times =explode(' - ', $time);
       $begin_time=strtotime($times[0]);
       $end_time=strtotime($times[1]);
       //var_dump($begin_time);var_dump($end_time);exit;
       $query =urldecode(I('get.query','','trim'));
       $newid =I('get.newstype','','trim');
       $uid= $this->uid;
       $where = array();
       if($newid){
           if($begin_time&&$end_time){
               $join="ytt_news g on m.news_id=g.id and g.type_id={$newid} and (g.behot_time >= {$begin_time} and g.behot_time <= {$end_time})";
           }else{
               $join='ytt_news g on m.news_id=g.id and g.type_id='.$newid.'';
           }

       }else{
           if($begin_time&&$end_time){
               $join="ytt_news g on m.news_id=g.id and (g.behot_time >= {$begin_time} and g.behot_time <= {$end_time})";
           }else{
               $join='ytt_news g on m.news_id=g.id';
           }

       }
       if($query){
           $where['_string'] = "`content` like '%{$query}%'";
       }
       $article = M('news_item');
       //M('orders_goods')->alias('m')->field('g.id,g.pic,m.num,m.sell_price')->join('goods g on m.goods_id=g.id')->where('m.order_id=' . $row['id'])->select();
       $count = $article->alias('m')->join($join)->where($where)->count();
       $page = $this->pages($count, $this->reqnum);
       //var_dump($article->getLastSql());exit;
       $limit = $page->firstRow . ',' . $page->listRows;
       $list = $article->alias('m')->join($join)->where($where)->limit($limit)->order('m.id desc')->select();
       //var_dump($list);exit;
       $data = array(
           'query'=>$query,
           'pages' => $page->show(),
           'list' => $list,
           'uid' => $uid,
       );
       $this->assign('time',$time);
       $this->assign('newid',$newid);
       $this->assign($data);
       $this->display();
   }
}
