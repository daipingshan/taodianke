<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/6 0006
 * Time: 下午 6:07
 */

namespace AppApi\Controller;

/**
 * 爱分享
 *
 * Class ShareController
 *
 * @package AppApi\Controller
 */
class ShareController extends CommonController {

    /**
     * @var int
     */
    protected $limit = 10;

    /**
     * @var bool
     */
    protected $checkUser = false;

    /**
     * 爱分享首页
     */
    public function index() {
        $list = M('article_cate')->where('status = 1')->order('ordid desc,id desc')->select();
        foreach ($list as &$v) {
            $v['item_url'] = U('/Share/item', array('cid' => $v['id']));
        }
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 爱分享分类列表
     */
    public function item() {
        $page  = I('get.page', 0, 'int');
        $cid   = I('get.cid', 0, 'int');
        $next  = 0;
        $where = array();
        if ($cid) {
            $where['cate_id'] = $cid;
        }
        if (1 == $cid) {
            $order_by = 'id desc';
        } else {
            $order_by = 'ordid asc,id asc';
        }
        $start_num = $page * $this->limit;
        $list      = M('article')->where($where)->order($order_by)->limit($start_num, $this->limit + 1)->select();
        if (count($list) > $this->limit) {
            $next = 1;
            array_pop($list);
        }
        foreach ($list as &$v) {
            $v['item_url'] = U('/Share/article', array('id' => $v['id']));
            $v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
        }
        $this->assign('list', $list);
        $this->assign('next', $next);
        if (IS_AJAX) {
            $this->ajaxReturn(array('list' => $list, 'next' => $next));
        } else {
            $this->assign('cate_id', $cid);
            $this->display();
        }
    }

    /**
     * 爱分享详情
     */
    public function article() {
        $id            = I('get.id', 0, 'int');
        $row           = M('article')->find($id);
        $manage_domain = C('manage_domain_url');
        $row['info']   = str_ireplace('<img src="/Uploads/', '<img src="' . $manage_domain . '/Uploads/', html_entity_decode($row['info']));
        $this->assign('article', $row);
        $this->assign('cate_id', $row['cate_id']);
        $this->display();
    }

    /**
     * 广告详情
     */
    public function info() {
        $id           = I('get.id', 0, 'int');
        $info         = M('article')->find($id);
        $info['info'] = html_entity_decode($info['info']);
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 快速上单
     */
    public function add() {
        echo '该功能已迁移到管理后台，请去后台操作';exit;
        $this->display();
    }

    /**
     * 上传新单
     */
    public function ajaxAddGoods() {
        return false;
        $id       = I('get.goods_id', 0, 'trim');
        $order_id = I('get.order_id', 9999, 'trim');
        if (!$id) {
            $this->ajaxReturn(array('code' => -1, 'msg' => '请求参数不合法'));
        }
        $tao_obj = new \Common\Org\DaTaoKe();
        $res     = $tao_obj->getItem($id);
        if (!$res['result']) {
            $this->ajaxReturn(array('code' => -1, 'msg' => '未在大淘客中寻找到此款产品,或访问受限'));
        }
        $info = $res['result'];
        $item = M('items')->where(array('num_iid' => $info['GoodsID']))->find();
        if ($item) {
            if ($item['ordid'] != 9999) {
                $this->ajaxReturn(array('code' => -1, 'msg' => '您已经提交过此产品，产品排序为' . $item['ordid'], 'title' => "重复提交 - {$item['title']}", 'pic_url' => $item['pic_url']));
            } else {
                M('items')->where(array('num_iid' => $info['GoodsID']))->delete();
            }
        }
        M('items')->where(array('ordid' => array('lt', 1000), 'coupon_type' => array('neq', 5)))->setInc('ordid', 3);
        //新增商品
        $info['ordid'] = $order_id;
        $data          = $tao_obj->getItemData($info, true);
        $add_res       = M('items')->add($data);
        if ($add_res) {
            $this->ajaxReturn(array('code' => 1, 'msg' => "添加成功，排序结果为第{$order_id}位", 'title' => $item['title'], 'pic_url' => $item['pic_url']));
        } else {
            $this->ajaxReturn(array('code' => -1, 'msg' => "添加失败！"));
        }
    }

    /**
     *  全网商品的商品详情
     */
    public function qwdetail() {
        $param = array(
            'title'        => I('get.title', '', 'trim'),
            'price'        => I('get.price', '', 'trim'),
            'quan'         => I('get.quan', '', 'trim'),
            'coupon_price' => I('get.coupon_price', '', 'trim'),
            'volume'       => I('get.volume', '', 'trim'),
            'pic_url'      => I('get.pic_url', '', 'trim'),
            'kou_ling'     => I('get.kou_ling', '', 'trim'),
            'url'          => I('get.url', '', 'trim'),
        );

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
            redirect($param['url']);
        } else {
            $this->assign('data', $param);
            $this->display();
        }
    }

}