<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/21 0021
 * Time: 上午 8:51
 */

namespace Home\Controller;

use Common\Controller\CommonBusinessController;

/**
 * 淘店客PC站公共控制器
 * Class CommonController
 *
 * @package Home\Controller
 */
class CommonController extends CommonBusinessController {

    /**
     * 用户pid
     *
     * @var null
     */
    protected $pid = null;

    /**
     * 三多网络微信推广id
     *
     * @var int
     */
    protected $wei_xin_pid = 63559;

    /**
     * @var int
     */
    protected $limit = 50;

    /**
     * 构造函数
     */
    public function __construct() {
        parent:: __construct();
        if (session('uid')) {
            $this->uid = session('uid');
            $this->pid = session('pid');
            if (session('wei_xin_pid')) {
                $this->wei_xin_pid = session('wei_xin_pid');
            }
        } else {
            $this->pid = C('PID');
        }
        $this->assign('menu', $this->_getMenu());
    }

    /**
     * 非ajax分页方法
     * totalRows  总数量
     * listRows   每页条数
     * map        查询条件
     *
     * @return string
     */
    protected function pages($totalRows, $listRows, $map = array(), $rollPage = 5) {
        $Page = new \Think\Page($totalRows, $listRows, '', ACTION_NAME);
        if ($map && IS_POST) {
            foreach ($map as $key => $val) {
                $val = urlencode($val);
                $Page->parameter .= "$key=" . urlencode($val) . '&';
            }
        }
        if ($rollPage > 0) {
            $Page->rollPage = $rollPage;
        }
        $Page->setConfig('prev', '<');
        $Page->setConfig('next', '>');
        $Page->setConfig(
            'theme', '<div class="pagination"><ul>%UP_PAGE% %LINK_PAGE% %DOWN_PAGE%</ul></div>'
        );
        return $Page;
    }

    /**
     * 获取首页菜单配置
     */
    protected function _getMenu() {
        $menu = array(
            array('name' => '首页', 'url' => U('Index/index'), 'class' => 'first', 'act' => 'index'),
            array('name' => '领券直播', 'url' => U('Item/index'), 'class' => '', 'act' => 'item'),
            array('name' => '9块9包邮', 'url' => U('Item/freeShipping'), 'class' => '', 'act' => 'free'),
            array('name' => '热销', 'url' => U('Item/hot'), 'class' => '', 'act' => 'hot'),
            array('name' => '精品推荐', 'url' => U('Item/handpick'), 'class' => '', 'act' => 'handpick'),
        );
        if (session('uid')) {
            $one_key_menu = array('name' => '一键转链', 'url' => U('Item/oneKeyTurnChain'), 'class' => '', 'act' => 'one_key');
            array_push($menu, $one_key_menu);
        }
        return $menu;
    }

    /**
     * 获取模板
     */
    protected function _getTemplate() {
        $data = S('tdk_template_' . $this->uid);
        if (!$data) {
            $data = $this->_getDefaultTemplate();
        }
        return $data;
    }

    /**
     * 获取默认模板
     */
    protected function _getDefaultTemplate() {
        $data = S('tdk_default_template');
        if (!$data) {
            $data = array(
                "【宅喵精选】#标题#\n【在售价】#原价#元\n【券后价】#券后价#元包邮\n【销量】 #销量#件\n  抢购链接 #领券链接#",
                "【宅喵今日推荐】#标题#\n【在售价】#原价#元\n【券后价】#券后价#元包邮\n【推荐理由】#文案#\n【销量】 #销量#件\n  抢购链接 #领券链接#",
                "【宅喵今日推荐】#标题#\n【在售价】#原价#元\n【券后价】#券后价#元包邮\n【推荐理由】#文案#\n【销量】 #销量#件\n  领券下单：复制此信息\n  #淘口令#☞前往淘宝"
            );
            S('default_template', $data);
        }
        return $data;
    }

}