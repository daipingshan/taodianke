<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/4/27
 * Time: 16:28
 */

namespace Admin\Controller;

/**
 * Class SaleArticleController
 *
 * @package Admin\Controller
 */
class SaleArticleController extends CommonController {

    /**
     * @var array
     */
    protected $article_type = array(
        '5500950648' => '户外推荐',
        '5500838410' => '生活美食会',
        '5500803015' => '数码周刊',
        '5500367585' => '美妆丽人',
        '5501814762' => '酷奇潮玩',
        '5501679303' => '宝妈推荐',
        '5500311915' => '懂车品',
        '5500358214' => '每日搭配之道',
        '5500903267' => '放心家居',
        '5501832587' => '潮男指南',
        '6768458493' => '放心购精选'
    );

    public function index() {
        $time     = I('get.time', '', 'trim,urldecode');
        $media_id = I('get.media_id', '', 'trim');
        $title    = I('get.title', '', 'trim');
        $read_num = I('get.read_num', '', 'int');
        $sort     = I('get.sort', '', 'int');
        $where    = array();
        $order    = 'behot_time desc';
        if ($media_id) {
            $where['media_id'] = $media_id;
        }
        if ($time) {
            list($start_time, $end_time) = explode('~', $time);
            if ($start_time && $end_time) {
                $start_time          = strtotime($start_time);
                $end_time            = strtotime($end_time);
                $where['behot_time'] = array('between', array($start_time, $end_time));
            }
        }
        if ($title) {
            $where['title'] = array('like', "%{$title}%");
        }
        if ($read_num) {
            $where['go_detail_count'] = array('egt', $read_num);
        }
        if ($sort) {
            if ($sort == 1) {
                $order = 'go_detail_count desc,behot_time desc';
            } elseif ($sort == 2) {
                $order = 'comments_count desc,behot_time desc';
            }
        }
        $count     = M('temai_article')->where($where)->count('id');
        $page      = $this->pages($count, $this->reqnum);
        $limit     = $page->firstRow . ',' . $page->listRows;
        $data      = M('temai_article')->where($where)->order($order)->limit($limit)->select();
        $url_param = array('read_num' => $read_num, 'time' => $time, 'title' => $title);
        $assign    = array(
            'pages'        => $page->show(),
            'data'         => $data,
            'article_type' => $this->article_type,
            'url_param'    => $url_param,
        );
        $this->assign($assign);
        $this->display();
    }
}