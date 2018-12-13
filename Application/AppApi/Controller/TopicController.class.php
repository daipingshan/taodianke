<?php
/**
 * 历史专题相关功能
 *
 * User: dongguangqi
 * Date: 2018/01/09
 */

namespace AppApi\Controller;

/**
 * Class TopicController
 *
 * @package AppApi\Controller
 */
class TopicController extends CommonController {

    /**
     * 查询所有历史专题（限6日内创建的专题）
     */
    public function index() {
        $page = I('get.page', 1, 'intval');

        $page--;
        $start_num = $page * $this->reqnum;

        $where = array('add_time' => array('egt', time() - 6 * 86400));
        $field = 'ids,title,desc';
        $topics = M('topic')->where($where)->order('id desc')->field($field)->limit($start_num, $this->reqnum + 1)->select();
        foreach ($topics as $key => $topic) {
            $params = array(
                'uid' => $this->uid,
                'ids' => $topic['ids'],
                'title' => urlencode($topic['title']),
                'desc' => urlencode($topic['desc'])
            );
            $url = U('Topic/detail', $params);
            $topics[$key]['url'] = C('WEIXIN_BASE.public_number_url') . $url;
        }

        $this->setHasNext(false, $topics);
        $this->outPut($topics, 0);
    }

    /**
     * 新增专题
     */
    public function add() {
        $ids   = I('ids', '', 'trim');
        $title = I('title', '', 'trim');
        $desc  = I('desc', '', 'trim');

        if ('' == $ids || '' == $title || '' == $desc) {
            $this->outPut(null, -1, null, '参数异常！');
        }

        if (count(explode(',', $ids)) < 5) {
            $this->outPut(null, 0);
            //$this->outPut(null, -1, null, '商品太少，不予保存！');
        }

        $data = array(
            'uid'   => $this->uid,
            'ids'   => $ids,
            'title' => $title,
            'desc'  => $desc,
            'add_time' => time()
        );
        $res = M('topic')->add($data);

        if ($res) {
            $this->outPut(null, 0);
        } else {
            $model->rollback();
            $this->outPut(null, -1, null, '失败');
        }
    }
}