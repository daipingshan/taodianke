<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2017/12/8
 * Time: 11:47
 */

namespace Admin\Controller;

use Common\Org\Http;
use Common\Org\OpenSearch;
use Common\Org\TranslateSDK;
use Think\Exception;

/**
 * 特卖管理
 * Class SaleController
 *
 * @package Admin\Controller
 */
class SaleController extends CommonController {

    /**
     * @var string
     */
    protected $upload_url = "http://kolplatform.jinritemai.com/index/image/singleUpload";

    /**
     * @var string
     */
    protected $item_info_url = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityList?keyword_type=platform_sku_id&keyword_value=%s&keyword_type_shop=word&platform=3";

    /**
     * @var string
     */
    public $http_referer = "http://kolplatform.jinritemai.com";

    /**
     * @var string
     */
    protected $ajax_validate_smart_article_url = "http://kolplatform.jinritemai.com/index/article/ajaxValidateSmartArticle";

    /**
     * @var string
     */
    protected $ajax_validate_commodity_user_url = "http://kolplatform.jinritemai.com/index/article/ajaxValidateCommodityUserUrl";

    /**
     * @var string
     */
    protected $publish_url = "http://kolplatform.jinritemai.com/index/article/doAddArticle?article_type=2";

    /**
     * @var string
     */
    protected $figure_url = "http://kolplatform.jinritemai.com/index/article/doSaveDraft?article_type=2";

    /**
     * 文章所属领域
     *
     * @var array
     */
    protected $classify_cate = array(
        1  => '懂车品',
        2  => '每日搭配之道',
        3  => '放心家居',
        4  => '潮男指南',
        5  => '户外推荐',
        6  => '生活美食会',
        7  => '数码周刊',
        8  => '美妆丽人',
        9  => '酷奇潮玩',
        10 => '宝妈推荐'
    );
    protected $Categoryname  = array(
        1  => '手机类',
        2  => '数码',
        3  => '电脑、办公',
        4  => '家用电器',
        5  => '服饰内衣',
        6  => '个护化妆',
        7  => '运动户外',
        8  => '母婴',
        9  => '食品饮料',
        10 => '家居家装',
        11 => '礼品箱包',
        12 => '钟表类',
        13 => '珠宝首饰',
        14 => '厨具',
        15 => '玩具乐器',
        16 => '汽车用品',
        17 => '宠物生活',
        18 => '医药保健',
        19 => '家具',
        20 => '家具',
        21 => '家装建材',
        22 => '鞋靴',
        23 => '酒类'
    );
    /**
     * @var array
     */
    protected $Category = array(
        1  => array(27, 28),
        2  => array(29, 30, 31, 32, 33),
        3  => array(34, 35, 36, 37, 38, 39, 40),
        4  => array(41, 42, 43, 44, 46),
        5  => array(47, 48, 49, 50),
        6  => array(51, 52, 53, 54, 55, 56, 57),
        7  => array(58, 59, 60, 61, 62, 63, 64, 65, 66),
        8  => array(67, 68, 69, 70, 71, 72, 73, 74, 75, 76),
        9  => array(77, 78, 79, 80),
        10 => array(81, 82, 83, 84, 85),
        11 => array(86, 87, 88, 89),
        12 => array(90),
        13 => array(91, 92, 93, 94, 95, 96, 97, 98, 99, 101, 102),
        14 => array(103, 104, 105, 106, 107, 108, 109),
        15 => array(110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120),
        16 => array(121, 122, 123, 124, 125),
        17 => array(126, 127, 128, 129, 130, 131, 132),
        18 => array(133, 134),
        19 => array(135),
        20 => array(136, 137, 138, 139, 140, 141, 142),
        21 => array(143, 144, 145, 146, 147, 148),
        22 => array(149, 150),
        23 => array(151)
    );
    /**
     * @var array
     * 二级分配
     *
     */
    protected $Category_sub = array(
        27  => '手机通讯',
        28  => '手机配件',
        29  => '摄影摄像',
        30  => '影音娱乐',
        31  => '数码配件',
        32  => '智能设备',
        33  => '电子教育',
        34  => '电脑整机',
        35  => '电脑配件',
        36  => '外设产品',
        37  => '网络产品',
        38  => '办公设备',
        39  => '文具/耗材',
        40  => '游戏设备',
        41  => '生活电器',
        42  => '厨房小电',
        43  => '大家电',
        44  => '个护健康',
        45  => '',
        46  => '厨卫大电',
        47  => '男装',
        48  => '女装',
        49  => '内衣',
        50  => '服饰配件',
        51  => '面部护肤',
        52  => '身体护理',
        53  => '口腔护理',
        54  => '女性护理',
        55  => '洗发护发',
        56  => '香水彩妆',
        57  => '清洁用品',
        58  => '户外装备',
        59  => '健身训练',
        60  => '体育用品',
        61  => '户外鞋服',
        62  => '运动鞋包',
        63  => '运动服饰',
        64  => '骑行运动',
        65  => '垂钓用品',
        66  => '游泳用品',
        67  => '奶粉',
        68  => '营养辅食',
        69  => '尿裤湿巾',
        70  => '喂养用品',
        71  => '洗护用品',
        72  => '童车童床',
        73  => '妈妈专区',
        74  => '寝居服饰',
        75  => '童装童鞋',
        76  => '安全座椅',
        77  => '休闲食品',
        78  => '粮油调味',
        79  => '饮料冲调',
        80  => '茗茶',
        81  => '家纺',
        82  => '灯具',
        83  => '生活日用',
        84  => '家装软饰',
        85  => '收纳用品',
        86  => '潮流女包',
        87  => '精品男包',
        88  => '功能箱包',
        89  => '礼品',
        90  => '钟表',
        91  => '黄金',
        92  => '金银投资',
        93  => '银饰',
        94  => '钻石',
        95  => '翡翠玉石',
        96  => '水晶玛瑙',
        97  => '彩宝',
        98  => '时尚饰品',
        99  => '铂金',
        100 => '木手串/把件',
        101 => '珍珠',
        102 => 'K金饰品',
        103 => '烹饪锅具',
        104 => '刀剪菜板',
        105 => '厨房配件',
        106 => '水具酒具',
        107 => '餐具',
        108 => '酒店用品',
        109 => '茶具/咖啡具',
        110 => '遥控/电动',
        111 => '毛绒布艺',
        112 => '娃娃玩具',
        113 => '模型玩具',
        114 => '健身玩具',
        115 => '动漫玩具',
        116 => '益智玩具',
        117 => '积木拼插',
        118 => 'DIY玩具',
        119 => '创意减压',
        120 => '乐器',
        121 => '车载电器',
        122 => '维修保养',
        123 => '美容清洗',
        124 => '汽车装饰',
        125 => '安全自驾',
        126 => '宠物主粮',
        127 => '宠物零食',
        128 => '医疗保健',
        129 => '家居日用',
        130 => '宠物玩具',
        131 => '出行装备',
        132 => '洗护美容',
        133 => '传统滋补',
        134 => '护理护具',
        135 => '卧室家具',
        136 => '客厅家具',
        137 => '餐厅家具',
        138 => '书房家具',
        139 => '储物家具',
        140 => '阳台/户外',
        141 => '商业办公',
        142 => '儿童家具',
        143 => '灯饰照明',
        144 => '厨房卫浴',
        145 => '五金工具',
        146 => '电工电料',
        147 => '墙地面材料',
        148 => '装饰材料',
        149 => '流行男鞋',
        150 => '时尚女鞋',
        151 => '中外名酒'
    );
    /**
     * 性别分类
     *
     * @var array
     */
    protected $gender_cate = array(
        'male'      => '男',
        'female'    => '女',
        'unlimited' => '不限'
    );

    /**
     * @var int
     */
    protected $reqnum = 100;

    /**
     * 特卖账号管理
     */
    public function index() {
        $data = M('sale_account')->select();
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 添加账号
     */
    public function addAccount() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $username = I('post.username', '', 'trim');
        if (!$username) {
            $this->error('账号名称不能为空！');
        }
        $cookie = I('post.cookie', '', 'trim');
        if (!$cookie) {
            $this->error('cookie信息不能为空！');
        }
        $data = array('username' => $username, 'cookie' => $cookie, 'add_time' => time(), 'update_time' => time());
        $res  = M('sale_account')->add($data);
        if ($res) {
            S('sale_account', null);
            $this->success('添加成功');
        } else {
            $this->error('添加失败！');
        }
    }

    /**
     * 修改账号
     */
    public function updateAccount() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('post.id', 0, 'int');
        if (!$id) {
            $this->error('账号不存在！');
        }
        $username = I('post.username', '', 'trim');
        if (!$username) {
            $this->error('账号名称不能为空！');
        }
        $cookie = I('post.cookie', '', 'trim');
        if (!$cookie) {
            $this->error('cookie信息不能为空！');
        }
        $data = array('username' => $username, 'cookie' => $cookie, 'update_time' => time(), 'id' => $id);
        $res  = M('sale_account')->save($data);
        if ($res !== false) {
            if ($id == 1) {
                S('sale_cookie', null);
            }
            S('sale_account', null);
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
        }
    }

    /**
     * 删除账号
     */
    public function deleteAccount() {
        $this->error('该账号不能删除！');
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('post.id', 0, 'int');
        if (!$id) {
            $this->error('账号不存在！');
        }
        $res = M('sale_account')->delete($id);
        if ($res) {
            S('sale_account', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 查看该账号的基本信息
     */
    public function openInfo() {
        $id   = I('get.id', 0, 'int');
        $data = $this->_getSaleAccount();
        if (!$id || !isset($data[$id])) {
            $this->error('非法请求！');
        }
        $cookie = $data[$id]['cookie'];
        $url    = "http://kolplatform.jinritemai.com/index/benefit/ajaxGetAuthoeIncome";
        $res    = $this->_get($url, array(), $cookie);
        if ($res === false) {
            $this->error('cookie信息已过期！');
        } else {
            $res = json_decode($res, true);
            $this->success('本月业绩：' . $res['this_month_income'] . '-----今日业绩：' . $res['today_income']);
        }

    }

    /***
     * 获取指定第三方订单
     */
    public function GetFxgOrder() {
        //set_time_limit(600);
        $id   = I('get.id', 0, 'int');
        $data = $this->_getSaleAccount();
        if (!$id || !isset($data[$id])) {
            $this->error('非法请求！');
        }
        $cookie = $data[$id]['cookie'];
        $type   = $data[$id]['username'];
        if ($type == '52时尚' || $type == '叮当运动派' || $type == '白浅上神' || $type == '果不其然') {
            $t         = time();
            $startTime = date("Y-m-d", $t);
            $endTime   = date("Y-m-d", strtotime("-2 day"));
            //http://kolplatform.jinritemai.com/index/benefit/ajaxGetOrderDetail?start_time=" + startTime + "&end_time=" + endTime + "&order_status=&time_type=0&pageNumber=1&pageSize=800
            $url = "http://kolplatform.jinritemai.com/index/benefit/ajaxGetOrderDetail?start_time=" . $endTime . "&end_time=" . $startTime . "&order_status=&time_type=0&pageNumber=1&pageSize=800";
            $res = $this->_get($url, array(), $cookie);
            if ($res === false) {
                $this->error('cookie信息已过期！');
            } else {
                $res = json_decode($res, true);
                if ($res['total']) {
                    self::NewUpdateorder($res['rows'], $type);
                    $this->success('订单数量：' . $res['total']);
                } else {
                    $this->success('无订单返回');
                }

            }
        } else {
            $this->error('不支持插入！');
        }


    }

    /***
     * @param $data
     * 更新第三方订单
     */
    public function NewUpdateorder($data, $type) {
        $jgg = '';
        foreach ($data as $ke => $vad) {

            $jgg .= self::NewUpdate($vad, $type);

        }
    }

    /*
     *
     * 更新第三方订单
     */
    public function NewUpdate($date, $type) {
        usleep(50000);
        $pay_time               = $date['pay_time'];//I('post.order_id');//订单编号
        $order_status           = $date['order_status'];//I('post.title');//标题
        $commodity_info         = $date['commodity_info'];//I('post.itemid');//商品id
        $goods_id               = get_word($commodity_info, 'id=', '&');
        $order_source           = $date['order_source'];//I('post.discount_rate');//收入比率
        $order_money            = $date['order_money'];//I('post.share_rate');//分成比率
        $profit_percent         = $date['profit_percent'];//I('post.fee');//效果评估
        $income                 = $date['income'];
        $complete_time          = $date['complete_time'];//I('post.price');//商品单价
        $author_id              = $date['author_id'];//I('post.number');//数量
        $order_id               = $date['order_id'];//I('post.total_fee');//付款金额
        $commodity_name         = $date['commodity_name'];//I('post.create_time');//订单创建时间
        $article_title          = $date['article_title'];//I('post.click_time');//订单单击时间
        $author_settle          = $date['author_settle'];//I('post.payStatus');//订单状态（订单支付 订单失效 订单结算）
        $author_income          = $date['author_income'];//I('post.order_type');//商品类型 （天猫 淘宝）
        $model                  = M('gg_fxg');
        $where['order_id']      = $order_id;
        $data['pay_time']       = $pay_time;
        $data['order_status']   = $order_status;
        $data['commodity_info'] = $commodity_info;
        $data['order_source']   = $order_source;
        $data['order_money']    = $order_money;
        $data['profit_percent'] = $profit_percent;
        $data['income']         = $income;
        $data['complete_time']  = $complete_time;
        $data['author_id']      = $author_id;
        if($type=='52时尚'){
            $wherecar['title']  = $article_title;
            $articlew =M('gg_tmnews');
            $list = $articlew->where($wherecar)->find();
            if($list){
                $type= $list['name'];
            }
            $data['name']           = $type;
        }else{
            $data['name']           = $type;
        }

        $data['commodity_name'] = $commodity_name;
        $data['article_title']  = $article_title;
        $data['author_settle']  = $author_settle;
        $data['author_income']  = $author_income;
        $data['goods_id']       = $goods_id;

        $res = $model->where($where)->save($data);
        if ($res) {
            return date("m-d H:i:s") . "-" . $order_id . "更新成功 \r\n";
            //$this->outPut(null, 0, null, '更新成功');
        } else {
            $resd = $model->where($where)->find();
            if ($resd) {
                return date("m-d H:i:s") . "-" . $order_id . "成功数据存在\r\n";
                //$this->outPut(null, 0, null, '成功数据存在');
            } else {
                $data['order_id'] = $order_id;
                $ress             = $model->add($data);
                if ($ress) {
                    return date("m-d H:i:s") . "-" . $order_id . "插入成功\r\n";
                    //$this->outPut(null, 0, null, '插入成功');
                }
            }

        }
    }

    /**
     * 获取特卖账号信息
     */
    protected function _getSaleAccount() {
        $data = M('sale_account')->index('id')->select();
        return $data;
    }

    /**
     * 获取特卖默认账号信息
     */
    protected function _getSaleCookie() {
        $cookie = S('sale_cookie');
        if (!$cookie) {
            $cookie = M('sale_account')->getFieldById(1, 'cookie');
            if ($cookie) {
                S('sale_cookie', $cookie);
            }
        }
        return $cookie;
    }

    /**
     * 获取头条商品
     */
    public function itemsList() {
        $shop_goods_id = I('get.shop_goods_id', '', 'trim');
        $keyword       = I('get.keyword', '', 'trim');
        $time          = I('get.time', '', 'trim');
        $where         = array('status' => 1);
        $query         = "status:'1'";
        $filter        = null;
        if ($shop_goods_id) {
            $query .= "AND shop_goods_id:'{$shop_goods_id}'";
            $where['shop_goods_id'] = $shop_goods_id;
        }
        if ($keyword) {
            $query .= " AND keyword:'{$keyword}'";
            $where['top_line_article_title|description'] = array('like', "%{$keyword}%");
        }
        if ($time) {
            list($start_time, $end_time) = explode(' - ', $time);
            if ($start_time && $end_time) {
                $start_time                           = strtotime($start_time);
                $end_time                             = strtotime($end_time);
                $filter                               = "top_line_article_behot_time>{$start_time} AND top_line_article_behot_time<{$end_time}";
                $where['top_line_article_behot_time'] = array('between', array($start_time, $end_time));
            }
        }
        $cache_data = $this->_getItemCache();
        if ($this->openSearchStatus === true) {
            $obj           = new OpenSearch();
            $obj->app_name = 'sale';
            $count         = $obj->searchCount($query, $filter);
            $p             = I('get.p', 1, 'int');
            $page          = $this->pages($count, $this->reqnum);
            $start_num     = ($p - 1) * $this->reqnum;
            $open_data     = $obj->search($query, array(array('key' => 'top_line_article_behot_time', 'val' => 0)), $filter, $start_num, $this->reqnum);
            $data          = $open_data['data'];
        } else {
            $count = M('hao_huo_items')->where($where)->count();
            $page  = $this->pages($count, $this->reqnum);
            $limit = $page->firstRow . ',' . $page->listRows;
            $data  = M('hao_huo_items')->where($where)->limit($limit)->order('top_line_article_behot_time desc')->select();
        }
        foreach ($data as &$item) {
            $item['post_data'] = json_encode($item);
            if (isset($cache_data[$item['shop_goods_id']])) {
                $item['is_add'] = 1;
            } else {
                $item['is_add'] = 0;
            }
        }
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'cart_count' => count($cache_data), 'time' => $time));
        $this->display();
    }

    /**
     * 获取头条商品
     */
    public function highReadItemsList() {

        $shop_goods_id = I('get.shop_goods_id', '', 'trim');
        $article_id    = I('get.article_id', '', 'trim');
        $keyword       = I('get.keyword', '', 'trim');
        $time          = I('get.time', '', 'trim');
        $where         = array('status' => 1);
        if ($shop_goods_id) {
            $where['shop_goods_id'] = $shop_goods_id;
        }
        if ($article_id) {
            $where['top_line_article_id'] = $article_id;
        }
        if ($keyword) {
            $where['top_line_article_title|description'] = array('like', "%{$keyword}%");
        }
        if ($time) {
            list($start_time, $end_time) = explode(' - ', $time);
            if ($start_time && $end_time) {
                $start_time                           = strtotime($start_time);
                $end_time                             = strtotime($end_time);
                $where['top_line_article_behot_time'] = array('between', array($start_time, $end_time));
            }
        }
        $cache_data = $this->_getItemCache();
        $count      = M('high_read_items')->where($where)->count();
        $page       = $this->pages($count, $this->reqnum);
        $limit      = $page->firstRow . ',' . $page->listRows;
        $data       = M('high_read_items')->where($where)->limit($limit)->order('top_line_article_behot_time desc,article_id desc')->select();
        foreach ($data as &$item) {
            $item['post_data'] = json_encode($item);
            if (isset($cache_data[$item['shop_goods_id']])) {
                $item['is_add'] = 1;
            } else {
                $item['is_add'] = 0;
            }
        }
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'cart_count' => count($cache_data), 'time' => $time));
        $this->display();
    }

    /**
     * 特卖达人后台商品库
     */
    public function selectItems() {
        $get_url                     = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityList";
        $cookie                      = $this->_getSaleCookie();
        $clear_param                 = S('sale_param_' . $this->uid);
        $param                       = array('platform' => 3, 'flag' => 0, 'task_pool_flag' => true);
        $param['category1_id']       = I('get.category1_id', '', 'int');
        $param['keyword_type']       = I('get.keyword_type', '', 'trim');
        $param['keyword_value']      = I('get.keyword_value', '', 'trim');
        $param['keyword_type_shop']  = I('get.keyword_type_shop', '', 'trim');
        $param['keyword_value_shop'] = I('get.keyword_value_shop', '', 'trim');
        $param['page']               = I('get.p', '', 'trim');
        $param['sort']               = I('get.sort', '', 'trim');
        $param['order']              = I('get.order', '', 'trim');
        $param['cos_ratio_down']     = I('get.cos_ratio_down', '', 'trim');
        $param['cos_ratio_up']       = I('get.cos_ratio_up', '', 'trim');
        $param['sku_price_down']     = I('get.sku_price_down', '', 'trim');
        $param['sku_price_up']       = I('get.sku_price_up', '', 'trim');
        if ($clear_param) {
            $param = array_merge($param, $clear_param);
            S('sale_param_' . $this->uid, null);
        }
        $res = $this->_get($get_url, $param, $cookie);
        if ($res === false) {
            $this->assign('error_info', '您的登录状态已失效，请联系管理员！');
        } else {
            $res = json_decode($res, true);
            if ($res['errno'] == 0) {
                $data   = $res['goods_infos'];
                $count  = $res['total_count'];
                $page   = $this->pages($count, 40);
                $assign = array('data' => $data ? array_chunk($data, 4) : array(), 'page' => $page->show(), 'param' => $param);
                $this->assign($assign);
            } else {
                $this->assign('error_info', $res['msg']);
            }
        }
        $this->assign('type', I('get.type', 'sale', 'trim'));
        $this->display();
    }

    /**
     * 保存筛选条件
     */
    public function saveParam() {
        $data = I('get.', '', 'trim');
        S('sale_param_' . $this->uid, $data);
    }

    /**
     * 获取头条商品
     */
    public function itemsUrlList() {
        $url        = I('get.url', '', 'trim');
        $cache_data = $this->_getItemCache();
        if ($url) {
            $content = $this->_get($url);
            $reg_exp = '%<textarea id="gallery-data-textarea" .*?>(.*?)</textarea>%si';
            preg_match($reg_exp, $content, $match);
            $match_data = json_decode($match[1], true);
            if (count($match_data) == 0) {
                $this->redirect_message(U('itemsUrlList'), array('error' => '链接地址不合法或文章不符合规则！'));
            }
            $temp_time = microtime(true) * 1000;
            $temp_time = explode('.', $temp_time);
            $data      = array();
            foreach ($match_data as $k => $item) {
                if (isset($item['price']) && strpos($item['real_url'], 'haohuo.snssdk.com')) {
                    $data[$item['id']] = array(
                        'shop_type'          => $item['shop_type'],
                        'shop_goods_id'      => $item['shop_goods_id'],
                        'price_tag_position' => $item['price_tag_position'],
                        'img'                => $item['img'],
                        'goods_json'         => $item['goods_json'],
                        'name'               => $item['name'],
                        'price'              => $item['price'],
                        'description'        => $item['description'],
                        'real_url'           => $item['real_url'],
                        'user_url'           => $item['user_url'],
                        'self_charging_url'  => $item['self_charging_url'],
                    );
                }
                if (isset($item['commodity']) && isset($data[$item['commodity']['id']])) {
                    $data[$item['commodity']['id']]['attached_imgs'][] = array(
                        'img'             => $item['location'],
                        'origin_location' => $item['origin_location'],
                        'description'     => $item['description'],
                        'id'              => 'commodity_' . $temp_time[0],
                    );
                }
            }
            if (!$data) {
                $this->redirect_message(U('itemsUrlList'), array('error' => '未找到商品！'));
            }
            foreach ($data as &$item) {
                $item['attached_imgs'] = json_encode($item['attached_imgs']);
                $item['post_data']     = json_encode($item);
                if (isset($cache_data[$item['shop_goods_id']])) {
                    $item['is_add'] = 1;
                } else {
                    $item['is_add'] = 0;
                }
            }
            $this->assign('data', array_chunk($data, 4));
        }
        $this->assign('cart_count', count($cache_data));
        $this->display();
    }

    /**
     * 添加商品
     */
    public function addItemCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id   = I('post.id', '', 'trim');
        $type      = I('post.type', 0, 'int');
        $post_data = I('post.post_data', '', 'trim');
        if (!$item_id || !$post_data) {
            $this->error('请求参数不合法！');
        }
        $data = $this->_getItemCache();
        if (count($data) >= 20) {
            $this->error('商品数量最多不能超过20件');
        }
        if (isset($data[$item_id]) && $data[$item_id]) {
            $this->error('该商品已在选品库，不能重复添加');
        }
        $cookie = $this->_getSaleCookie();
        $temp   = $this->_get(sprintf($this->item_info_url, $item_id), array(), $cookie);
        if ($temp === false) {
            $this->error('cookie信息已过期！');
        }
        $temp_data = json_decode($temp, true);
        if ($temp_data['errno'] != '0') {
            $this->error($temp_data['msg']);
        }
        if (!$temp_data['goods_infos']) {
            M('hao_huo_items')->where(array('shop_goods_id' => $item_id))->save(array('status' => 0));
            $this->error('商品已下架');
        }
        $goods_info = json_decode($post_data['goods_json'], true);
        $sec_cid    = '';
        if (empty($goods_info)) {
            $sku_id                = $temp_data['goods_infos'][0]['sku_id'];
            $sku_url               = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityDetail?sku_id={$sku_id}";
            $sku_info              = json_decode($this->_get($sku_url, array(), $cookie), true);
            $sec_cid               = $sku_info['commodity']['sec_cid'];
            $post_data['real_url'] = $sku_info['commodity']['real_url'];
            $post_data['user_url'] = $sku_info['commodity']['real_url'] . '&conver=1';
            if ($sku_info['errno'] != '0') {
                $this->error($temp_data['msg']);
            }
            $post_data['goods_json'] = $sku_info['commodity']['goods_json'];
            $goods_info              = json_decode($sku_info['commodity']['goods_json'], true);
            M('hao_huo_items')->where(array('shop_goods_id' => $item_id))->save(array('goods_json' => $sku_info['commodity']['goods_json']));
        }
        if (!$sec_cid) {
            $sku_id                = $temp_data['goods_infos'][0]['sku_id'];
            $sku_url               = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityDetail?sku_id={$sku_id}";
            $sku_info              = json_decode($this->_get($sku_url, array(), $cookie), true);
            $sec_cid               = $sku_info['commodity']['sec_cid'];
            $post_data['real_url'] = $sku_info['commodity']['real_url'];
            $post_data['user_url'] = $sku_info['commodity']['real_url'] . '&conver=1';
        }
        $shop_name                  = $temp_data['goods_infos'][0]['shop_name'];
        $shop_goods                 = array('fee' => $temp_data['goods_infos'][0]['cos_info']['cos_fee'], 'ratio' => ($temp_data['goods_infos'][0]['cos_info']['cos_ratio'] * 100) . "%");
        $temp_time                  = microtime(true) * 1000;
        $temp_time                  = explode('.', $temp_time);
        $post_data['attached_imgs'] = json_decode($post_data['attached_imgs'], true);
        list($post_data['attached_imgs'][0]['img'], $_) = explode('?', $post_data['attached_imgs'][0]['img']);
        $post_data['attached_imgs'][0]['origin_location'] = '';
        $post_data['attached_imgs'][0]['id']              = 'commodity_' . $temp_time[0];


        if ($type > 0) {
            $obj = new TranslateSDK();
            $res = $obj->translate($post_data['attached_imgs'][0]['description'], 'zh', 'en');
            $res = $obj->translate($res['trans_result'][0]['dst'], 'en', 'zh');
            if (!isset($res['trans_result'][0]['dst'])) {
                $this->error('伪原创失败！');
            }
            $post_data['attached_imgs'][0]['description'] = $res['trans_result'][0]['dst'];
            $post_res                                     = $obj->translate($post_data['description'], 'zh', 'en');
            $post_res                                     = $obj->translate($post_res['trans_result'][0]['dst'], 'en', 'zh');
            if (!isset($post_res['trans_result'][0]['dst'])) {
                $this->error('伪原创失败！');
            }
            $post_data['description'] = $post_res['trans_result'][0]['dst'];

        }
        $temp_time[0] = $temp_time[0] + 1;
        list($post_data['img'], $_) = explode('?', $post_data['img']);
        $save_data      = array(
            "shop_type"          => $post_data['shop_type'],
            "shop_goods_id"      => $post_data['shop_goods_id'],
            "tags"               => "",
            "oritags"            => "",
            "price_tag_position" => $post_data['price_tag_position'],
            "img"                => $post_data['img'],
            "img_url"            => $post_data['img'],
            'goods_json'         => $post_data['goods_json'],
            "shop_id"            => $goods_info['shop_id'],
            "shop_name"          => $shop_name,
            "name"               => $post_data['name'],
            "price"              => $post_data['price'],
            "description"        => $post_data['description'],
            "attached_imgs"      => $post_data['attached_imgs'],
            "charging_id"        => $this->pid,
            "real_url"           => $post_data['real_url'],
            "user_url"           => $post_data['user_url'],
            "isAutoAdded"        => true,
            "fromLibrary"        => $goods_info['fromLibrary'],
            "id"                 => "commodity_" . $temp_time[0],
            "sec_cid"            => $sec_cid,
            "self_charging_url"  => $post_data['self_charging_url'],
            "user_url_status"    => "success",
            "type"               => 'goods',
            "sort"               => count($data) + 1,
        );
        $data[$item_id] = $save_data;
        $user_id        = $this->user['id'];
        S('sale_item_' . $user_id, $data);
        $shop_goods['msg'] = "添加成功";
        $this->success($shop_goods);
    }

    /**
     * 添加图集
     */
    public function addImgCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id   = I('post.id', '', 'trim');
        $post_data = I('post.post_data', '', 'trim');
        $type      = I('post.type', 0, 'int');
        if (!$item_id || !$post_data['img'] || !$post_data['description']) {
            $this->error('请求参数不合法！');
        }
        $data = $this->_getItemCache();
        if (count($data) >= 20) {
            $this->error('商品数量最多不能超过20件');
        }
        if (isset($data[$item_id]) && $data[$item_id]) {
            $this->error('该商品已在选品库，不能重复添加');
        }
        if ($type > 0) {
            $obj = new TranslateSDK();
            $res = $obj->translate($post_data['description'], 'zh', 'en');
            $res = $obj->translate($res['trans_result'][0]['dst'], 'en', 'zh');
            if (!isset($res['trans_result'][0]['dst'])) {
                $this->error('伪原创失败！');
            }
            $post_data['description'] = $res['trans_result'][0]['dst'];
        }
        $temp_time      = microtime(true) * 1000;
        $temp_time      = explode('.', $temp_time);
        $save_data      = array(
            'shop_goods_id'   => $post_data['id'],
            'img'             => $post_data['img'],
            'origin_location' => '',
            'description'     => $post_data['description'],
            'id'              => "commodity_{$temp_time[0]}",
            'type'            => 'img',
            'sort'            => count($data) + 1
        );
        $data[$item_id] = $save_data;
        $user_id        = $this->user['id'];
        S('sale_item_' . $user_id, $data);
        $this->success('添加成功');
    }

    /**
     * 编辑商品信息
     */
    public function updateItemCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id  = I('post.id', '', 'trim');
        $name     = I('post.name', '', 'trim');
        $img      = I('post.img', '', 'trim');
        $son_img  = I('post.son_img', '', 'trim');
        $info     = I('post.info', '', 'trim');
        $son_info = I('post.son_info', '', 'trim');
        $sort     = I('post.sort', 0, 'int');
        if (!$item_id) {
            $this->error('请求参数不合法！');
        }
        if (mb_strlen($name) < 6 || mb_strlen($name) > 20) {
            $this->error('商品标题必须在6-20个字符！');
        }
        if (!$img) {
            $this->error('主图未上传，请上传！');
        }
        if (!$son_img) {
            $this->error('副图未上传，请上传！');
        }
        if (mb_strlen($info) < 5 || mb_strlen($info) > 100) {
            $this->error('主图文案必须在5-100个字符！');
        }
        if (mb_strlen($son_info) < 5 || mb_strlen($son_info) > 100) {
            $this->error('副图文案必须在5-100个字符！');
        }
        $data = $this->_getItemCache();
        if (!isset($data[$item_id]) || !$data[$item_id]) {
            $this->error('该商品不在选品库，无法编辑');
        }
        $img_data                        = $data[$item_id]['attached_imgs'];
        $img_data[0]['img']              = $son_img;
        $img_data[0]['description']      = $son_info;
        $data[$item_id]['name']          = $name;
        $data[$item_id]['img']           = $img;
        $data[$item_id]['img_url']       = $img;
        $data[$item_id]['sort']          = $sort;
        $data[$item_id]['description']   = $info;
        $data[$item_id]['attached_imgs'] = $img_data;
        $user_id                         = $this->user['id'];
        S('sale_item_' . $user_id, $data);
        $this->success('编辑成功');
    }

    /**
     * 编辑商品信息
     */
    public function updateImgCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id = I('post.id', '', 'trim');
        $img     = I('post.img', '', 'trim');
        $info    = I('post.info', '', 'trim');
        $sort    = I('post.sort', 0, 'int');
        if (!$item_id) {
            $this->error('请求参数不合法！');
        }
        if (!$img) {
            $this->error('图片未上传，请上传！');
        }
        if (mb_strlen($info) < 5 || mb_strlen($info) > 100) {
            $this->error('主图文案必须在5-100个字符！');
        }
        $data = $this->_getItemCache();
        if (!isset($data[$item_id]) || !$data[$item_id]) {
            $this->error('该商品不在选品库，无法编辑');
        }
        $data[$item_id]['img']         = $img;
        $data[$item_id]['sort']        = $sort;
        $data[$item_id]['description'] = $info;
        $user_id                       = $this->user['id'];
        S('sale_item_' . $user_id, $data);
        $this->success('编辑成功');
    }

    /**
     * 删除选品
     */
    public function delItemCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id = I('post.id', '', 'trim');
        if (!$item_id) {
            $this->error('请求参数不合法！');
        }
        $user_id = $this->user['id'];
        $data    = $this->_getItemCache();
        if (!isset($data[$item_id])) {
            $this->error('该商品不在选品库，不能删除');
        }
        unset($data[$item_id]);
        S('sale_item_' . $user_id, $data);
        $this->success('删除成功');
    }

    /**
     * 获取缓存商品
     */
    protected function _getItemCache() {
        $user_id = $this->user['id'];
        return S('sale_item_' . $user_id) ? : array();
    }

    /**
     * 删除缓存商品
     */
    protected function _delItemCache() {
        $user_id = $this->user['id'];
        S('sale_item_' . $user_id, null);
    }

    /**
     * 预览文章
     */
    public function cartList() {
        $data      = $this->_getItemCache();
        $cate_data = array();
        foreach ($data as &$cart) {
            $cate_name = $this->Category_sub[$cart['sec_cid']];
            if (isset($cate_data[$cart['sec_cid']])) {
                $cate_data[$cart['sec_cid']] = array('name' => $this->Category_sub[$cart['sec_cid']], 'num' => $cate_data[$cart['sec_cid']]['num'] + 1);
            } else {
                $cate_data[$cart['sec_cid']] = array('name' => $this->Category_sub[$cart['sec_cid']], 'num' => 1);
            }
            foreach ($this->Category as $key => $cate) {
                if (in_array($cart['sec_cid'], $cate)) {
                    $cate_name .= '||' . $this->Categoryname[$key];
                    break;
                }
            }
            $cart['sec_cid'] = $cate_name;
        };
        $this->assign('data', $this->_arraySort(array_values($data), 'sort'));
        $this->assign('cate_data', array_reverse($this->_arraySort(array_values($cate_data), 'num')));
        $this->display();
    }

    /**
     * 保存文章
     */
    public function saveCart() {
        $data = $this->_getItemCache();
        $this->assign('data', $this->_arraySort(array_values($data), 'sort'));
        if ($this->uid == 0) {
            $account = $this->_getSaleAccount();
        } else {
            $sale_account_ids = session(C('SAVE_USER_KEY'))['sale_account_ids'];
            $account          = M('sale_account')->where(array('id' => array('in', $sale_account_ids)))->select();
        }
        $this->assign('account', $account);
        $this->assign('classify_data', $this->classify_cate);
        $this->assign('gender_data', $this->gender_cate);
        $this->display();
    }

    /**
     * 保存文章
     */
    public function saveNews() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $title      = I('post.title', '', 'trim');
        $account_id = I('post.account_id', 0, 'int');
        $classify   = I('post.classify', 0, 'int');
        $gender     = I('post.gender', '', 'trim');
        $send_time  = I('post.send_time', '', 'trim');
        if (!$account_id) {
            $this->error('请选择文章的发布账号！');
        }
        if (mb_strlen($title) < 6 || mb_strlen($title) > 30) {
            $this->error('文章标题必须是6-30个字符！');
        }
        if (!$classify) {
            $this->error('请选择文章所属领域！');
        }
        if (!$gender) {
            $this->error('请选择文章适合性别！');
        }
        $items = $this->_getItemCache();
        if (count($items) == 0) {
            $this->error('商品库暂无商品，请先添加商品！');
        }
        $items_data = $this->_arraySort(array_values($items), 'sort');
        foreach ($items_data as &$val) {
            if ($val['type'] == 'img') {
                unset($val['shop_goods_id']);
            }
            unset($val['sort']);
            unset($val['type']);
        }
        $data = array(
            'user_id'      => $this->user['id'],
            'account_id'   => $account_id,
            'username'     => $this->user['name'],
            'title'        => $title,
            'json_content' => json_encode($items_data),
            'classify'     => $classify,
            'gender'       => $gender,
            'appoint'      => 25,
            'send_time'    => $send_time,
            'add_time'     => time(),
        );
        $res  = M('sale_news')->add($data);
        if ($res) {
            $this->_delItemCache();
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 文件上传
     */
    public function uploadSaleImg() {
        $upload           = new \Think\Upload();// 实例化上传类
        $upload->maxSize  = 5 * 1024 * 1024;// 设置附件上传大小
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = ''; // 设置附件上传（子）目录
        $info             = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            $url_path = dirname(APP_PATH) . '/www/Uploads/' . $info['file']['savepath'] . $info['file']['savename'];
            $url_path = str_replace('\\', '/', $url_path);
            $obj      = new \CurlFile($url_path);
            $param    = array('file_data' => $obj);
            $data     = json_decode($this->_file($this->upload_url, $param, $this->_getTopLineCookie()), true);
            unlink($url_path);
            if ($data['url']) {
                $this->success(array('url' => $data['url']));
            } else {
                $this->error('上传失败');
            }
        }
    }

    /**
     * 头条文章列表
     */
    public function newsList() {
        $user_id = $this->user['id'];
        $where   = $user_data = array();
        if ($user_id > 0) {
            $where = array('user_id' => $user_id);
        } else {
            $user_data = $this->_getUserData();
            $user_id   = I('get.user_id', 0, 'int');
            if ($user_id) {
                $where = array('user_id' => $user_id);
            }
        }
        $count  = M('sale_news')->where($where)->count();
        $page   = $this->pages($count, 20);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = M('sale_news')->where($where)->limit($limit)->order('id desc')->select();
        $assign = array(
            'pages'         => $page->show(),
            'data'          => $data,
            'account'       => $this->_getSaleAccount(),
            'classify_data' => $this->classify_cate,
            'gender_data'   => $this->gender_cate,
            'user_data'     => $user_data,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 预览文章
     */
    public function newsInfo() {
        $id        = I('get.id', 0, 'int');
        $info      = M('sale_news')->find($id);
        $data      = json_decode($info['json_content'], true);
        $cate_data = array();
        foreach ($data as &$cart) {
            $cate_name = $this->Category_sub[$cart['sec_cid']];
            if (isset($cate_data[$cart['sec_cid']])) {
                $cate_data[$cart['sec_cid']] = array('name' => $this->Category_sub[$cart['sec_cid']], 'num' => $cate_data[$cart['sec_cid']]['num'] + 1);
            } else {
                $cate_data[$cart['sec_cid']] = array('name' => $this->Category_sub[$cart['sec_cid']], 'num' => 1);
            }
            foreach ($this->Category as $key => $cate) {
                if (in_array($cart['sec_cid'], $cate)) {
                    $cate_name .= '||' . $this->Categoryname[$key];
                    break;
                }
            }
            $cart['sec_cid'] = $cate_name;
        }
        $this->assign('data', $data);
        $this->assign('cate_data', array_reverse($this->_arraySort(array_values($cate_data), 'num')));
        $this->display();
    }

    /**
     * 修改文章
     */
    public function updateNews() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id    = I('post.id', 0, 'int');
        $title = I('post.title', '', 'trim');
        $info  = M('sale_news')->find($id);
        if (!$id || !$info) {
            $this->error('文章不存在');
        }
        if (mb_strlen($title) < 6 || mb_strlen($title) > 30) {
            $this->error('文章标题必须是5-30个字符！');
        }
        $res = M('sale_news')->save(array('id' => $id, 'title' => $title));
        if ($res !== false) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
        }
    }

    /**
     * 非商品图集
     */
    public function imgList() {
        $keyword = I('get.keyword', '', 'trim');
        $cate_id = I('get.cate_id', 0, 'int');
        $user_id = $this->user['id'];
        $where   = $user_data = array();
        if ($user_id == 0) {
            $user_data = $this->_getUserData();
            $user_id   = I('get.user_id', 0, 'int');
            if ($user_id) {
                $where = array('user_id' => $user_id);
            }
        }
        if ($keyword) {
            $where['description'] = array('like', "%{$keyword}%");
        }
        if ($cate_id) {
            $where['cate_id'] = $cate_id;
        }
        $count      = M('top_line_img')->where($where)->count();
        $page       = $this->pages($count, $this->reqnum);
        $limit      = $page->firstRow . ',' . $page->listRows;
        $data       = M('top_line_img')->field('id,img,description')->where($where)->limit($limit)->order('id desc')->select();
        $cache_data = $this->_getItemCache();
        foreach ($data as $key => $val) {
            $data[$key]['post_data'] = json_encode($val);
            if (isset($cache_data[$val['id']])) {
                $data[$key]['is_add'] = 1;
            } else {
                $data[$key]['is_add'] = 0;
            }
        }
        $assign = array(
            'pages'      => $page->show(),
            'data'       => array_chunk($data, 4),
            'user_data'  => $user_data,
            'cart_count' => count($cache_data),
            'img_cate'   => $this->classify_cate,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 添加图集
     */
    public function addImg() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $cate_id = I('post.cate_id', 0, 'int');
        $img     = I('post.img', '', 'trim');
        $info    = I('post.info', '', 'trim');
        if (!$cate_id) {
            $this->error('请选择图集分类！');
        }
        if (!$img) {
            $this->error('请上传图集封面！');
        }
        if (mb_strlen($info) < 5 || mb_strlen($info) > 100) {
            $this->error('文章标题必须是6-100个字符！');
        }
        $data = array('cate_id' => $cate_id, 'img' => $img, 'description' => $info, 'user_id' => $this->uid, 'add_time' => time());
        $res  = M('top_line_img')->add($data);
        if ($res) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败！');
        }
    }

    /**
     * 删除图集
     */
    public function delImg() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('get.id', 0, 'int');
        if (!$id) {
            $this->error('请求参数不合法！');
        }
        if ($this->uid != 0) {
            $this->error('你没有删除权限！');
        }
        $res = M('top_line_img')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 发布文章
     */
    public function publish() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('sale_news')->find($id);
        if (!$id || !$info) {
            $this->error('文章不存在');
        }
        $account = $this->_getSaleAccount();
        $res     = $this->_post($this->ajax_validate_smart_article_url, array('commoditys_json' => $info['json_content']), $account[$info['account_id']]['cookie']);
        if ($res === false) {
            $this->error('验证文章信息，请求服务器失败！');
        }
        $res = json_decode($res, true);
        if ($res['code'] != 0) {
            $this->error($res['message']);
        }
        $res = $this->_post($this->ajax_validate_commodity_user_url, array('commoditys_json' => $info['json_content']), $account[$info['account_id']]['cookie']);
        if ($res === false) {
            $this->error('验证用户信息，请求服务器失败！');
        }
        $res = json_decode($res, true);
        if ($res['code'] != 0) {
            $this->error($res['message']);
        }
        $res = $this->_sendSale($info, $account[$info['account_id']]['cookie']);
        if ($res['status'] == 1) {
            M('sale_news')->save(array('id' => $id, 'is_send' => 1));
            $this->success('发布成功');
        } else {
            $this->error($res['info']);
        }
    }

    /**
     * 存草稿
     */
    public function figure() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('sale_news')->find($id);
        if (!$id || !$info) {
            $this->error('文章不存在');
        }
        $account = $this->_getSaleAccount();
        $res     = $this->_sendSale($info, $account[$info['account_id']]['cookie'], 'figure_url');
        if ($res['status'] == 1) {
            M('sale_news')->save(array('id' => $id, 'is_save' => 1));
            $this->success('保存成功');
        } else {
            $this->error($res['info']);
        }
    }

    /**
     * 删除文章
     */
    public function newsDel() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('get.id', 0, 'int');
        if (!$id) {
            $this->error('账号不存在！');
        }
        $is_send = M('sale_news')->getFieldById($id, 'is_send');
        if ($is_send == 1) {
            $this->error('该文章已发布，不能删除！');
        }
        $res = M('sale_news')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * @param $info
     * @param $cookie
     * @param string $type
     * @return bool|mixed
     */
    protected function _sendSale($info, $cookie, $type = 'publish_url') {
        $post_data = array(
            'start_time'       => '',
            'content_type'     => 2,
            'can_publish'      => '0',
            'atlas_attach_num' => 3,
            'll-hk-uuid'       => 'df38257es651',
            'content'          => '',
            'commoditys_json'  => $info['json_content'],
            'title'            => $info['title'],
            //'gender'           => $info['gender'],
            //'classify'         => $info['classify'],
        );
        if ($type == 'publish_url') {
            $post_data['can_publish'] = 0;
            if ($info['send_time']) {
                $post_data['start_time'] = $info['send_time'];
            }
        }
        if ($info['appoint'] > 0) {
            $post_data['appoint'] = $info['appoint'];
        }

        $temp = microtime(true) * 1000;
        $temp = explode('.', $temp);
        $num  = rand(1000, 9999);
        $url  = $this->$type . '&sig=' . $temp[0] . '.' . $num;
        $res  = $this->_doPost($url, $post_data, $cookie);
        return $res;
    }

    /**
     * @param string $url
     * @param array $params
     * @param $cookie
     * @return bool|mixed
     */
    protected function _doPost($url = '', $params = array(), $cookie = '') {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        $header = array(
            'Accept:application/json, text/javascript, */*; q=0.01',
            'Content-Type:application/x-www-form-urlencoded; charset=UTF-8',
            'Referer: ' . $this->http_referer,
            'X-Requested-With:XMLHttpRequest',
        );
        if ($cookie) {
            $header[] = "cookie:{$cookie}";
        }
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->http_time_out);
        curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 302) {
            if (strpos($aStatus['redirect_url'], 'kolplatform.jinritemai.com/index/article/myDraftList')) {
                return array('status' => 1, 'info' => 'ok');
            } else if (strpos($aStatus['redirect_url'], 'kolplatform.jinritemai.com/index/article/addArticleSuccess')) {
                return array('status' => 1, 'info' => 'ok');
            } else {
                return array('status' => 0, 'info' => '该发布账号cookie已过期，请联系管理员更新');
            }
        } else {
            return array('status' => 0, 'info' => '请求服务器失败，请稍后重试！');
        }
    }

    /**
     * 头条文章管理
     */
    public function highReadNewsList() {
        $create_user_id = I('get.create_user_id', 0, 'int');
        $read_num       = I('get.read_num') < 0 ? 0 : I('get.read_num');
        $keyword        = I('get.keyword', '', 'trim');
        $time           = I('get.time', '', 'trim');
        $where          = array();
        if ($create_user_id) {
            $where['create_user_id'] = $create_user_id;
        }
        if ($read_num) {
            $where['go_detail_count'] = array('gt', $read_num);
        }
        if ($keyword) {
            $where['title|tag'] = array('like', "%{$keyword}%");
        }
        if ($time) {
            list($start_time, $end_time) = explode(' - ', $time);
            if ($start_time && $end_time) {
                $start_time          = strtotime($start_time);
                $end_time            = strtotime($end_time) + 86399;
                $where['behot_time'] = array('between', array($start_time, $end_time));
            }
        }
        $count = M('high_read_article')->where($where)->count();
        $page  = $this->pages($count, 20);
        $limit = $page->firstRow . ',' . $page->listRows;
        $data  = M('high_read_article')->where($where)->limit($limit)->order('go_detail_count desc,id desc')->select();
        foreach ($data as &$val) {
            $val['num'] = M('high_read_article')->where($where)->where(array('create_user_id' => $val['create_user_id']))->count('id');
        }
        $this->assign(array('data' => $data, 'page' => $page->show(), 'time' => $time));
        $this->display();
    }

    /**
     * 放心购商品
     */
    public function newItems() {
        $title          = I('get.title', '', 'trim');
        $shop_goods_id  = I('get.shop_goods_id', '', 'trim');
        $shop_name      = I('get.shop_name', '', 'trim');
        $sort           = I('get.sort', 0, 'trim');
        $cos_ratio_up   = I('get.cos_ratio_up', '', 'trim');
        $cos_ratio_down = I('get.cos_ratio_down', '', 'trim');
        $sku_price_up   = I('get.sku_price_up', '', 'trim');
        $sku_price_down = I('get.sku_price_down', '', 'trim');
        $time           = I('get.time', '', 'trim');
        $where          = array();
        if ($title) {
            $where['sku_title'] = array('like', "%{$title}%");
        }
        if ($shop_goods_id) {
            $where['platform_sku_id'] = $shop_goods_id;
        }
        if ($shop_name) {
            $where['shop_name'] = $shop_name;
        }
        if ($cos_ratio_up && $cos_ratio_down) {
            $where['cos_ratio'] = array('between', array($cos_ratio_up / 100, $cos_ratio_down / 100));
        }
        if ($sku_price_up && $sku_price_down) {
            $where['sku_price'] = array('between', array($sku_price_up, $sku_price_down));
        }
        if ($time) {
            list($start_time, $end_time) = explode(' - ', $time);
            if ($start_time && $end_time) {
                $start_time           = date("Ymd", strtotime($start_time));
                $end_time             = date("Ymd", strtotime($end_time));
                $where['create_time'] = array('between', array($start_time, $end_time));
            }
        }
        switch ($sort) {
            case 1:
                $order = "hotrank desc,create_time desc";
                break;
            case 2:
                $order = "hotrank asc,create_time desc";
                break;
            case 3:
                $order = "month_sell_num desc,create_time desc";
                break;
            case 4:
                $order = "month_sell_num asc,create_time desc";
                break;
            case 5:
                $order = "cos_fee desc,create_time desc";
                break;
            case 6:
                $order = "cos_fee asc,create_time desc";
                break;
            case 7:
                $order = " sku_price desc,create_time desc";
                break;
            case 8:
                $order = "sku_price asc,create_time desc";
                break;
            default:
                $order = 'create_time desc,id desc';
                break;
        }
        $count     = M('product')->where($where)->count();
        $page      = $this->pages($count, 40);
        $limit     = $page->firstRow . ',' . $page->listRows;
        $data      = M('product')->where($where)->limit($limit)->order($order)->select();
        $start_num = date('Ymd', strtotime('-3 days'));
        $end_num   = date('Ymd');
        foreach ($data as &$v) {
            if ($v['create_time'] >= $start_num && $v['create_time'] <= $end_num) {
                $v['is_new'] = 1;
            } else {
                $v['is_new'] = 0;
            }
        }
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'time' => $time));
        $this->display();
    }

    /**
     * 异步采集商品
     */
    public function ajaxCollectionItem() {
        if (!IS_AJAX) {
            $this->error(array('code' => -4, 'info' => '非法请求!'));
        }
        $get_url = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityList";
        $cookie  = $this->_getSaleCookie();
        $page    = I('get.page', 1, 'int');
        $param   = array('platform' => 3, 'flag' => 0, 'task_pool_flag' => true, 'page' => $page);
        $res     = $this->_get($get_url, $param, $cookie);
        if ($res === false) {
            $this->error(array('code' => -3, 'info' => '您的登录状态已失效，请联系管理员!'));
        } else {
            $res = json_decode($res, true);
            if ($res['errno'] === 0) {
                $item_data = $res['goods_infos'];
                if (count($item_data) == 0) {
                    $this->error(array('code' => 0, 'info' => '数据已加载完成!'));
                }
                $model = M();
                $model->startTrans();
                $data = array();
                foreach ($item_data as $item) {
                    $data[$item['platform_sku_id']] = array(
                        'platform_sku_id' => $item['platform_sku_id'],
                        'sku_url'         => $item['sku_url'],
                        'sku_title'       => $item['sku_title'],
                        'sku_price'       => $item['sku_price'],
                        'figure'          => $item['figure'],
                        'shop_name'       => $item['shop_name'],
                        'shop_url'        => $item['shop_url'],
                        'hotrank'         => $item['hotrank'],
                        'month_sell_num'  => $item['month_sell_num'],
                        'cos_fee'         => $item['cos_info']['cos_fee'],
                        'cos_ratio'       => $item['cos_info']['cos_ratio'],
                        'create_time'     => date('Ymd'),
                    );
                }
                $have_data = M('product')->field('id,platform_sku_id')->where(array('platform_sku_id' => array('in', array_keys($data))))->select();
                try {
                    foreach ($have_data as $val) {
                        unset($data[$val['platform_sku_id']]['create_time']);
                        M('product')->where(array('id' => $val['id']))->save($val);
                        unset($data[$val['platform_sku_id']]);
                    }
                    if ($data) {
                        M('product')->addAll(array_values($data));
                    }
                    $model->commit();
                    if ($data) {
                        $this->success(array('code' => 1, 'info' => "第{$page}页商品数据采集成功！"));
                    } else {
                        $this->success(array('code' => 1, 'info' => "第{$page}页商品数据已采集，无新商品！"));
                    }
                } catch (\Exception $e) {
                    $model->rollback();
                    $this->error(array('code' => -2, 'info' => $e->getMessage()));
                }
            } else {
                $this->error(array('code' => -1, 'info' => $res['msg']));
            }
        }
    }

    /**
     * 复制文章商品加入选品库
     */
    public function copyCart() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('sale_news')->find($id);
        if (!$id || !$info) {
            $this->error('文章不存在');
        }
        $this->_delItemCache();
        $content = json_decode($info['json_content'], true);
        $data    = array();
        foreach ($content as $key => $val) {
            if (isset($val['shop_goods_id']) && $val['shop_goods_id']) {
                $data[$val['shop_goods_id']] = $val;
            } else {
                $data[$key] = $val;
            }
        }
        $user_id = $this->user['id'];
        S('sale_item_' . $user_id, $data);
        $this->success('加入成功！');
    }

    /**
     * 商品详情
     */
    public function goodsInfo() {
        $time = I('get.time', '', 'trim,urldecode');
        $id   = I('get.id', 0, 'int');
        $type = I('get.type', 0, 'int');
        if ($type == 1) {
            $fxgd = M('gg_fxg');
        } else {
            $fxgd = M('fxg');

        }
        $fxg        = $fxgd->field('order_source,article_title')->find($id);
        $url        = $fxg['order_source'];
        $content    = $this->_get($url);
        $cache_data = $this->_getItemCache();
        $reg_exp    = '%<textarea id="gallery-data-textarea" .*?>(.*?)</textarea>%si';
        preg_match($reg_exp, $content, $match);
        $match_data = json_decode($match[1], true);
        $temp_time  = microtime(true) * 1000;
        $temp_time  = explode('.', $temp_time);
        $data       = array();
        if ($time) {
            list($start_time, $end_time) = explode(' - ', $time);
            if ($start_time && $end_time) {
                $start_time = strtotime($start_time);
                $end_time   = strtotime($end_time) + 86399;
            }
        } else {
            $end_time   = time();
            $start_time = strtotime(date('Y-m-d'));
        }
        $goods_where = array('UNIX_TIMESTAMP(pay_time)' => array('between', array($start_time, $end_time)));
        foreach ($match_data as $k => $item) {
            if (isset($item['price']) && strpos($item['real_url'], 'haohuo.snssdk.com')) {
                $data[$item['id']] = array(
                    'shop_type'          => $item['shop_type'],
                    'shop_goods_id'      => $item['shop_goods_id'],
                    'price_tag_position' => $item['price_tag_position'],
                    'img'                => $item['img'],
                    'goods_json'         => $item['goods_json'],
                    'name'               => $item['name'],
                    'price'              => $item['price'],
                    'description'        => $item['description'],
                    'real_url'           => $item['real_url'],
                    'user_url'           => $item['user_url'],
                    'self_charging_url'  => $item['self_charging_url'],
                );
            }
            if (isset($item['commodity']) && isset($data[$item['commodity']['id']])) {
                $data[$item['commodity']['id']]['attached_imgs'][] = array(
                    'img'             => $item['location'],
                    'origin_location' => $item['origin_location'],
                    'description'     => $item['description'],
                    'id'              => 'commodity_' . $temp_time[0],
                );
            }
        }
        foreach ($data as &$item) {
            $num                   = $fee = 0;
            $content               = '';
            $item['attached_imgs'] = json_encode($item['attached_imgs']);
            $item['post_data']     = json_encode($item);
            $goods                 = $fxgd->where($goods_where)->where(array('goods_id' => $item['shop_goods_id'], 'article_title' => $fxg['article_title']))->field('count(id) as num,sum(income) as fee,right(left(pay_time,10),5) as time')->group('time')->select();
            foreach ($goods as $row) {
                $num += $row['num'];
                $fee += $row['fee'];
                $content .= "{$row['time']}：总订单数->{$row['num']};总收益：{$row['fee']}<br>";
            }
            $item['goods_num']    = $num;
            $item['goods_fee']    = $fee;
            $item['data_content'] = $content ? $content : '暂无订单信息！';
            if (isset($cache_data[$item['shop_goods_id']])) {
                $item['is_add'] = 1;
            } else {
                $item['is_add'] = 0;
            }
        }

        $this->assign('article_title', $fxg['article_title']);
        $this->assign('data', array_chunk($data, 4));
        $this->assign('cart_count', count($cache_data));
        $this->display();
    }


}