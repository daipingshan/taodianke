<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2017/12/8
 * Time: 11:20
 */

namespace Admin\Controller;

use Common\Org\Http;
use Common\Org\OpenSearch;
use Common\Org\TranslateSDK;

/**
 * 今日头条相关
 *
 * Class TopLineController
 *
 * @package Admin\Controller
 */
class TopLineController extends CommonController {

    /**
     * 发文地址
     *
     * @var string
     */
    private $send_url = "https://mp.toutiao.com/core/article/edit_article_post/?source=mp&type=figure";

    /**
     * 获取商品详情地址
     *
     * @var string
     */
    private $item_info_url = "https://mp.toutiao.com/article/get_product_info/";

    /**
     * 上传图片地址
     *
     * @var string
     */
    private $upload_url = "https://mp.toutiao.com/tools/upload_picture/?type=ueditor&pgc_watermark=1&action=uploadimage&encode=utf-8";

    /**
     * 查询商品地址
     *
     * @var string
     */
    private $search_url = "http://www.51taojinge.com/api/temai_select.php?uid=%s&search=%s&count=%s&pingtai=%s&str_time=%s&end_time=%s&orderY=%s&page=%s&1=1";

    /**
     * 头条文章查询
     *
     * @var string
     */
    private $search_news_url = "http://rym.quwenge.com/temai_article.php??uid=%s&count=%s&str_time=%s&end_time=%s&orderY=%s&page=%s&1=1";

    /**
     * @var string
     */
    protected $sale_item_info_url = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityList?keyword_type=platform_sku_id&keyword_value=%s&keyword_type_shop=word&platform=3";

    /**
     * @var array
     */
    private $item_cate = array(
        array('uid' => '', 'name' => '全部'),
        array('uid' => '5569547953', 'name' => '每日穿衣之道'),
        array('uid' => '5568158065', 'name' => '辣妈潮宝'),
        array('uid' => '5572814229', 'name' => '美妆课堂'),
        array('uid' => '5573124268', 'name' => '潮男周刊'),
        array('uid' => '5573658957', 'name' => '会生活'),
        array('uid' => '5570589814', 'name' => '美食大搜罗'),
        array('uid' => '5571864339', 'name' => '户外行者'),
        array('uid' => '5573716916', 'name' => '文娱多宝阁'),
        array('uid' => '5571749564', 'name' => '数码极客'),
        array('uid' => '5565295982', 'name' => '爱车族'),
    );

    /**
     * 头条账号管理
     */
    public function index() {
        $data = M('top_line_account')->select();
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
        $res  = M('top_line_account')->add($data);
        if ($res) {
            S('top_line_account', null);
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
        $res  = M('top_line_account')->save($data);
        if ($res !== false) {
            S('top_line_account', null);
            if ($id == 1) {
                S('top_line_cookie', null);
            }
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
        $res = M('top_line_account')->delete($id);
        if ($res) {
            S('top_line_account', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 查看该账号的基本信息
     */
    public function openInfo() {
        $id = I('get.id', 0, 'int');
        if ($id == 1) {
            $cookie = $this->_getTopLineCookie();
            if (!$id || !$cookie) {
                $this->error('非法请求！');
            }
        } else {
            $data = $this->_getTopLineAccount();
            if (!$id || !isset($data[$id])) {
                $this->error('非法请求！');
            }
            $cookie = $data[$id]['cookie'];
        }
        $url = "https://mp.toutiao.com/core/article/new_article/?article_type=3&format=json";
        $res = $this->_get($url, array(), $cookie);
        if ($res === false) {
            $this->error('服务器请求失败！');
        }
        $res = json_decode($res, true);
        if (isset($res['user_name'])) {
            $this->success($res['user_name']);
        } else {
            $this->error('cookie信息已过期！');
        }
    }

    /**
     * 获取头条账号信息
     */
    protected function _getTopLineAccount() {
        $data = S('top_line_account');
        if (!$data) {
            $data = M('top_line_account')->where(array('id' => array('neq', 1)))->index('id')->select();
            if ($data) {
                S('top_line_account', $data);
            }
        }
        return $data;
    }

    /**
     * 获取头条默认账号信息
     */
    protected function _getTopLineCookie() {
        $data = S('top_line_cookie');
        if (!$data) {
            $data = M('top_line_account')->getFieldById(1, 'cookie');
            if ($data) {
                S('top_line_cookie', $data);
            }
        }
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
        $uid     = I('get.uid', '', 'trim');
        $keyword = I('get.keyword', '', 'trim');
        $source  = I('get.source', 0, 'int');
        $time    = I('get.time', '', 'trim');
        $where   = array('status' => 1);
        if ($keyword) {
            $where['description|name|top_line_article_title'] = array('like', "%{$keyword}%");
        }
        if ($source) {
            $where['shop_type'] = $source;
        }
        if ($uid) {
            $where['cate_id'] = $uid;
        }
        if ($time) {
            list($start_time, $end_time) = explode(' - ', $time);
            if ($start_time && $end_time) {
                if ($start_time && $end_time) {
                    $where['top_line_article_behot_time'] = array('between', array(strtotime($start_time), strtotime($end_time)));
                }
            }
        }
        $cache_data = $this->_getItemCache();
        $count      = M('tao_bao_items')->where($where)->count();
        $page       = $this->pages($count, $this->reqnum);
        $limit      = $page->firstRow . ',' . $page->listRows;
        $field      = array(
            'id',
            'top_line_article_id'         => 'temai_id',
            'shop_type'                   => 'type',
            'img',
            'price',
            'real_url'                    => 'url',
            'top_line_article_title'      => 'temai_title',
            'name'                        => 'title',
            'description'                 => 'describe_info',
            'shop_goods_id'               => 'taobao_id',
            'top_line_article_behot_time' => 'behot_time',
        );
        $data       = M('tao_bao_items')->field($field)->where($where)->limit($limit)->order('top_line_article_behot_time desc,id desc')->select();
        foreach ($data as &$item) {
            $item['id']              = $item['taobao_id'];
            $item['temai_id']        = "http://www.toutiao.com/a" . $item['temai_id'];
            $item['tmall_url']       = $item['url'];
            $item['go_detail_count'] = 0;
            if ($item['type'] == 1) {
                $item['type'] = "天猫";
            } else {
                $item['type'] = "淘宝";
            }
            $item['post_data'] = json_encode($item);
            if (isset($cache_data[$item['taobao_id']])) {
                $item['is_add'] = 1;
            } else {
                $item['is_add'] = 0;
            }
        }
        $url_param = array('uid' => $uid, 'keyword' => $keyword, 'source' => $source, 'time' => $time, 'page' => $page);
        $this->assign(array('cate' => $this->item_cate, 'data' => array_chunk($data, 4), 'page' => $page->show(), 'url_param' => $url_param, 'cart_count' => count($cache_data)));
        $this->display();
    }

    /**
     * 获取头条商品
     */
    public function newItemsList() {
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
            $db_data       = $open_data['data'];
        } else {
            $count   = M('hao_huo_items')->where($where)->count();
            $page    = $this->pages($count, $this->reqnum);
            $limit   = $page->firstRow . ',' . $page->listRows;
            $db_data = M('hao_huo_items')->where($where)->limit($limit)->order('top_line_article_behot_time desc')->select();
        }
        $data = array();
        foreach ($db_data as $key => $item) {
            $data[$key * 2 + 1]['id']            = $item['shop_goods_id'];
            $data[$key * 2 + 1]['temai_id']      = $item['top_line_article_id'];
            $data[$key * 2 + 1]['type']          = '放心购';
            $data[$key * 2 + 1]['img']           = $item['img'];
            $data[$key * 2 + 1]['price']         = $item['price'];
            $data[$key * 2 + 1]['url']           = $item['real_url'];
            $data[$key * 2 + 1]['temai_title']   = $item['top_line_article_title'];
            $data[$key * 2 + 1]['title']         = $item['name'];
            $data[$key * 2 + 1]['describe_info'] = $item['description'];
            $data[$key * 2 + 1]['taobao_id']     = $item['shop_goods_id'];
            $data[$key * 2 + 1]['behot_time']    = $item['top_line_article_behot_time'];
            $data[$key * 2 + 1]['temai_id']      = "http://www.toutiao.com/a" . $item['top_line_article_id'];
            $data[$key * 2 + 1]['tmall_url']     = $item['real_url'];
            $data[$key * 2 + 1]['post_data']     = json_encode($data[$key * 2 + 1]);
            $attached_img                        = json_decode($item['attached_imgs'], true);
            $data[$key * 2 + 2]['id']            = $item['shop_goods_id'];
            $data[$key * 2 + 2]['temai_id']      = $item['top_line_article_id'];
            $data[$key * 2 + 2]['type']          = '放心购';
            $data[$key * 2 + 2]['img']           = $attached_img[0]['img'];
            $data[$key * 2 + 2]['price']         = $item['price'];
            $data[$key * 2 + 2]['url']           = $item['real_url'];
            $data[$key * 2 + 2]['temai_title']   = $item['top_line_article_title'];
            $data[$key * 2 + 2]['title']         = $item['name'];
            $data[$key * 2 + 2]['describe_info'] = $attached_img[0]['description'];
            $data[$key * 2 + 2]['taobao_id']     = $item['shop_goods_id'];
            $data[$key * 2 + 2]['behot_time']    = $item['top_line_article_behot_time'];
            $data[$key * 2 + 2]['temai_id']      = "http://www.toutiao.com/a" . $item['top_line_article_id'];
            $data[$key * 2 + 2]['tmall_url']     = $item['real_url'];
            $data[$key * 2 + 2]['post_data']     = json_encode($data[$key * 2 + 2]);
            if (isset($cache_data[$item['shop_goods_id']])) {
                $data[$key * 2 + 1]['is_add'] = 1;
                $data[$key * 2 + 2]['is_add'] = 1;
            } else {
                $data[$key * 2 + 1]['is_add'] = 0;
                $data[$key * 2 + 2]['is_add'] = 0;
            }
        }
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'cart_count' => count($cache_data), 'time' => $time));
        $this->display();
    }

    /**
     * 获取头条商品
     */
    public function itemsMoreList() {
        $uid        = I('get.uid', '', 'trim');
        $keyword    = I('get.keyword', '', 'trim');
        $read_num   = I('get.read_num') < 0 ? 0 : I('get.read_num');
        $source     = I('get.source', '', 'int');
        $time       = I('get.time', '', 'trim');
        $sort       = I('get.sort', '', 'int');
        $page       = I('get.page', 1, 'int');
        $start_time = $end_time = '';
        if ($time) {
            list($start_time, $end_time) = explode(' - ', $time);
        }
        if ($keyword) {
            $uid = '';
        }
        $cache_data = $this->_getItemCache();
        $url        = sprintf($this->search_url, $uid, $keyword, $read_num, $source, $start_time, $end_time, $sort, $page);
        $data       = json_decode($this->_get($url), true);
        foreach ($data as &$item) {
            $item['id']        = $item['taobao_id'];
            $item['post_data'] = json_encode($item);
            if (isset($cache_data[$item['taobao_id']])) {
                $item['is_add'] = 1;
            } else {
                $item['is_add'] = 0;
            }
        }
        $is_last   = count($data) == 100 ? true : false;
        $url_param = array('uid' => $uid, 'keyword' => $keyword, 'read_num' => $read_num, 'source' => $source, 'time' => $time, 'sort' => $sort, 'page' => $page);
        $this->assign(array('cate' => $this->item_cate, 'data' => array_chunk($data, 4), 'is_last' => $is_last, 'page' => $page, 'url_param' => $url_param, 'cart_count' => count($cache_data)));
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
        $save_data = I('post.post_data', '', 'trim');
        if (!$item_id || $save_data == '') {
            $this->error('请求参数不合法！');
        }
        $data = $this->_getItemCache();
        if (count($data) >= 10) {
            $this->error('商品数量最多不能超过10件');
        }
        if (isset($data[$item_id]) && $data[$item_id]) {
            $this->error('该商品已在选品库，不能重复添加');
        }
        $cookie = $this->_getSaleCookie();
        $temp   = $this->_get(sprintf($this->sale_item_info_url, $item_id), array(), $cookie);
        if ($temp === false) {
            $this->error('特卖达人cookie信息已过期！');
        }
        $temp_data = json_decode($temp, true);
        if ($temp_data['errno'] != '0') {
            $this->error($temp_data['msg']);
        }
        if (!$temp_data['goods_infos']) {
            M('hao_huo_items')->where(array('shop_goods_id' => $item_id))->save(array('status' => 0));
            $this->error('商品已下架');
        }
        $cookie = $this->_getTopLineCookie();
        $temp   = $this->_get($this->item_info_url, array('gurl' => $save_data['tmall_url']), $cookie);
        if ($temp === false) {
            $this->error('请求服务器失败！');
        }
        $temp_data = json_decode($temp, true);
        if ($temp_data['message'] == 'error') {
            $this->error('该账号对应cookie信息已过期，请联系管理员！');
        }
        if (strpos($save_data['img'], 'ttcdn-tos')) {
            $upload_res = $this->_uploadSaleImg($save_data['img']);
            if ($upload_res['status'] == 0) {
                $this->error($upload_res['info']);
            }
            $save_data['img'] = $upload_res['url'];
            $img_key          = $upload_res['img_key'];
        }
        if (!isset($img_key)) {
            $img_key = explode('?', $save_data['img']);
            $img_key = explode('/', $img_key[0]);
            $img_key = $img_key[count($img_key) - 1];
        }
        $charge_url = $temp_data['data']['charge_url'];
        $pid        = get_word($charge_url, '\?pid=', '&');
        $sub_pid    = get_word($charge_url, '&subPid=', '&');
        if ($pid && $sub_pid) {
            $charge_url = str_replace($pid, $this->pid, $charge_url);
            $charge_url = str_replace($sub_pid, $this->pid, $charge_url);
        }
        if ($type > 0) {
            $obj = new TranslateSDK();
            $res = $obj->translate($save_data['describe_info'], 'zh', 'en');
            $res = $obj->translate($res['trans_result'][0]['dst'], 'en', 'zh');
            if (!isset($res['trans_result'][0]['dst'])) {
                $this->error('伪原创失败！');
            }
            $save_data['describe_info'] = $res['trans_result'][0]['dst'];

        }
        $url                    = str_replace('http:\/\/', '', $save_data['img']);
        $url                    = str_replace('https:\/\/', '', $url);
        $save_data['json_data'] = array(
            'url'         => $url,
            'uri'         => $img_key,
            'ic_uri'      => $save_data['type'] == '放心购' ? $img_key : '',
            'product'     => array(
                'product_url'        => $save_data['tmall_url'],
                'price_url'          => $charge_url,
                'corrdinate'         => "50%,50%",
                'price'              => $save_data['price'],
                'title'              => $save_data['title'],
                'recommend_reason'   => $save_data['describe_info'],
                'commodity_id'       => $temp_data['data']['commodity_id'],
                'slave_commodity_id' => $temp_data['data']['slave_commodity_id'],
                'goods_json'         => $temp_data['data']['goods_json'],
            ),
            'desc'        => $save_data['describe_info'],
            'web_uri'     => $img_key,
            'url_pattern' => "{{image_domain}}",
            'gallery_id'  => rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
        );
        $save_data['sort']      = count($data) + 1;
        $data[$item_id]         = $save_data;
        $user_id                = $this->user['id'];
        S('top_line_item_' . $user_id, $data);
        $this->success('添加成功');
    }

    /**
     * 编辑商品信息
     */
    public function updateItemCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id = I('post.id', 0, 'int');
        $content = I('post.content', '', 'trim');
        $img_key = I('post.img_key', '', 'trim');
        $img_url = I('post.img_url', '', 'trim');
        $sort    = I('post.sort', 0, 'int');
        if ($item_id == 0) {
            $this->error('请求参数不合法！');
        }
        if (!$img_key || !$img_url) {
            $this->error('商品图片不存在，请上传！');
        }
        if (mb_strlen($content) < 5 || mb_strlen($content) > 200) {
            $this->error('推荐理由必须在5-200个字符之间！');
        }

        $data = $this->_getItemCache();
        if (!isset($data[$item_id]) || !$data[$item_id]) {
            $this->error('该商品不在选品库，无法编辑');
        }
        if ($img_key != $data[$item_id]['json_data']['uri']) {
            $data[$item_id]['json_data']['url']     = $img_url;
            $data[$item_id]['json_data']['uri']     = $img_key;
            $data[$item_id]['json_data']['ic_uri']  = $data[$item_id]['json_data']['ic_uri'] ? $img_key : '';
            $data[$item_id]['json_data']['web_uri'] = $img_key;
        }
        $data[$item_id]['img']               = $img_url;
        $data[$item_id]['sort']              = $sort;
        $data[$item_id]['describe_info']     = $content;
        $data[$item_id]['json_data']['desc'] = $content;
        $user_id                             = $this->user['id'];
        S('top_line_item_' . $user_id, $data);
        $this->success('编辑成功');
    }

    /**
     * 商品上移或下移
     */
    public function moveItemCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id       = I('get.id', 0, 'int');
        $other_id = I('get.other_id', 0, 'int');
        $type     = I('get.type', '+', 'trim');
        if (!$id || !$other_id) {
            $this->error('请求参数不合法！');
        }
        $data = $this->_getItemCache();
        if (!isset($data[$id]) || !$data[$id]) {
            $this->error('商品不存在，无法移动！');
        }
        if ($type == '+') {
            $data[$id]['sort']       = $data[$id]['sort'] + 1;
            $data[$other_id]['sort'] = $data[$other_id]['sort'] - 1;
        } else {
            $data[$id]['sort']       = $data[$id]['sort'] - 1;
            $data[$other_id]['sort'] = $data[$other_id]['sort'] + 1;
        }
        $user_id = $this->user['id'];
        S('top_line_item_' . $user_id, $data);
        $this->success('移动成功');
    }

    /**
     * 删除选品
     */
    public function delItemCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id = I('post.id', 0, 'trim');
        if ($item_id == 0) {
            $this->error('请求参数不合法！');
        }
        $user_id = $this->user['id'];
        $data    = S('top_line_item_' . $user_id);
        if (!isset($data[$item_id])) {
            $this->error('该商品不在选品库，不能删除');
        }
        unset($data[$item_id]);
        S('top_line_item_' . $user_id, $data);
        $this->success('删除成功');
    }

    /**
     * 预览文章
     */
    public function cartList() {
        $data = $this->_getItemCache();
        $this->assign('data', $this->_arraySort(array_values($data), 'sort'));
        $this->display();
    }

    /**
     * 保存文章
     */
    public function saveCart() {
        $data = $this->_getItemCache();
        if ($this->uid == 0) {
            $account = $this->_getTopLineAccount();
        } else {
            $sale_account_ids = session(C('SAVE_USER_KEY'))['top_line_account_ids'];
            $account          = M('top_line_account')->where(array('id' => array('in', $sale_account_ids)))->select();
        }
        $this->assign('data', $this->_arraySort(array_values($data), 'sort'));
        $this->assign('account', $account);
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
        $img        = I('post.img', '', 'trim');
        $send_time  = I('post.send_time', '', 'trim');
        if (!$account_id) {
            $this->error('请选择文章的发布账号！');
        }
        if (mb_strlen($title) < 5 || mb_strlen($title) > 30) {
            $this->error('文章标题必须是5-30个字符！');
        }
        if (!$img) {
            $this->error('请选择封面图！');
        }
        $items = $this->_getItemCache();
        if (count($items) == 0) {
            $this->error('商品库暂无商品，请先添加商品！');
        }
        $content = $json_content = $json_covers_img = array();
        foreach ($img as $id) {
            $json_covers_img[] = array(
                'id'           => rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
                'url'          => $items[$id]['json_data']['url'],
                'uri'          => $items[$id]['json_data']['uri'],
                'origin_uri'   => $items[$id]['json_data']['uri'],
                'ic_uri'       => $items[$id]['json_data']['ic_uri'],
                'thumb_width'  => 700,
                'thumb_height' => 700,
            );
        }
        $items_data = $this->_arraySort(array_values($items), 'sort');
        foreach ($items_data as $item) {
            $json_content[] = $item['json_data'];
            unset($item['json_data']);
            $content[] = $item;
        }
        $data = array(
            'user_id'         => $this->user['id'],
            'account_id'      => $account_id,
            'username'        => $this->user['name'],
            'title'           => $title,
            'json_covers_img' => json_encode($json_covers_img),
            'content'         => json_encode($content),
            'json_content'    => json_encode(array('list' => $json_content)),
            'send_time'       => $send_time ? date('Y-m-d H:i', strtotime($send_time)) : 0,
            'add_time'        => time(),
        );
        $res  = M('top_line_news')->add($data);
        if ($res) {
            $this->_delItemCache();
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 获取缓存商品
     */
    protected function _getItemCache() {
        $user_id = $this->user['id'];
        return S('top_line_item_' . $user_id) ? : array();
    }

    /**
     * 删除缓存商品
     */
    protected function _delItemCache() {
        $user_id = $this->user['id'];
        S('top_line_item_' . $user_id, null);
    }

    /**
     * @param $file_url
     * @return array
     */
    protected function _uploadSaleImg($file_url) {
        $url_path = dirname(APP_PATH) . '/www/Uploads/temp.jpg';
        $file_url = str_replace('https', 'http', $file_url);
        $content  = file_get_contents($file_url);
        file_put_contents($url_path, $content);
        $obj   = new \CurlFile($url_path);
        $param = array('upfile' => $obj);
        $data  = json_decode($this->_file($this->upload_url, $param, $this->_getTopLineCookie()), true);
        unlink($url_path);
        if (strtolower($data['state']) == 'success') {
            $url = "https://p2.pstatp.com/large/{$data['original']}";
            return array('status' => 1, 'info' => 'ok', 'url' => $url, 'img_key' => $data['original']);
        } else {
            return array('status' => 0, 'info' => '上传失败！');
        }
    }


    /**
     * 文件上传
     */
    public function uploadTopImg() {
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
            $param    = array('upfile' => $obj);
            $data     = json_decode($this->_file($this->upload_url, $param, $this->_getTopLineCookie()), true);
            unlink($url_path);
            if (strtolower($data['state']) == 'success') {
                $url = "https://p2.pstatp.com/large/{$data['original']}";
                $this->success(array('url' => $url, 'img_key' => $data['original']));
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
        $count  = M('top_line_news')->where($where)->count();
        $page   = $this->pages($count, $this->reqnum);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = M('top_line_news')->where($where)->limit($limit)->order('id desc')->select();
        $assign = array(
            'pages'     => $page->show(),
            'data'      => $data,
            'account'   => $this->_getTopLineAccount(),
            'user_data' => $user_data,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 预览文章
     */
    public function newsInfo() {
        $id   = I('get.id', 0, 'int');
        $info = M('top_line_news')->find($id);
        $data = json_decode($info['content'], true);
        $this->assign('data', $data);
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
        $info  = M('top_line_news')->find($id);
        if (!$id || !$info) {
            $this->error('文章不存在');
        }
        if (mb_strlen($title) < 5 || mb_strlen($title) > 30) {
            $this->error('文章标题必须是5-30个字符！');
        }
        $res = M('top_line_news')->save(array('id' => $id, 'title' => $title));
        if ($res !== false) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
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
        $info = M('top_line_news')->find($id);
        if (!$id || !$info) {
            $this->error('文章不存在');
        }
        $res = $this->_sendTop($info);
        if ($res == false) {
            $this->error('请求服务器失败，请稍后重试！');
        }
        $res = json_decode($res, true);
        if ($res['code'] == 0) {
            $pgc_id = $res['data']['pgc_id'];
            M('top_line_news')->save(array('id' => $id, 'pgc_id' => $pgc_id, 'is_send' => 1));
            $this->success($res['message']);
        } else {
            $this->error($res['message']);
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
        $info = M('top_line_news')->find($id);
        if (!$id || !$info) {
            $this->error('文章不存在');
        }
        $res = $this->_sendTop($info, 0);
        if ($res == false) {
            $this->error('请求服务器失败，请稍后重试！');
        }
        $res = json_decode($res, true);
        if ($res['code'] == 0) {
            $pgc_id = $res['data']['pgc_id'];
            M('top_line_news')->save(array('id' => $id, 'pgc_id' => $pgc_id, 'is_save' => 1));
            $this->success($res['message']);
        } else {
            $this->error($res['message']);
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
        $is_send = M('top_line_news')->getFieldById($id, 'is_send');
        if ($is_send == 1) {
            $this->error('该文章已发布，不能删除！');
        }
        $res = M('top_line_news')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * @param int $type
     * @param $info
     * @return bool|mixed
     */
    protected function _sendTop($info, $type = 1) {
        $items        = json_decode($info['json_content'], true);
        $gallery_data = $gallery_info = array();
        $account      = $this->_getTopLineAccount();
        foreach ($items['list'] as $item) {
            $gallery_data[$item['gallery_id']] = $item;
            $gallery_info[]                    = $item;
        }
        $post_data = array(
            'abstract'               => '',
            'authors'                => '',
            'self_appoint'           => 0,
            'save'                   => $type,
            'pgc_id'                 => '',
            'urgent_push'            => true,
            'is_draft'               => '',
            'title'                  => $info['title'],
            'content'                => "{!-- PGC_GALLERY:{$info['json_content']} --}",
            'pgc_feed_covers'        => $info['json_covers_img'],
            'gallery_data'           => $gallery_data,
            'gallery_info'           => $gallery_info,
            'article_ad_type'        => 3,
            'recommend_auto_analyse' => 0,
            'ic_uri_list'            => json_decode($info['img_keys']) ? : '',
            'article_type'           => 3,
            'timer_status'           => $info['send_time'] > 0 ? 1 : 0,
            'timer_time'             => $info['send_time'] > 0 ? $info['send_time'] : date('Y-m-d H:i'),
            'from_diagnosis'         => 0,
            'pgc_debut'              => 0
        );
        $res       = $this->_post($this->send_url, $post_data, $account[$info['account_id']]['cookie']);
        return $res;
    }

    /**
     * 头条文章管理
     */
    public function topNewsList() {
        $news_cate   = $this->item_cate;
        $news_cate[] = array('uid' => '6768100064', 'name' => '放心购精选');
        $uid         = I('get.uid', '', 'trim');
        $read_num    = I('get.read_num') < 0 ? 0 : I('get.read_num');
        $time        = I('get.time', '', 'trim');
        $sort        = I('get.sort', '', 'int');
        $page        = I('get.page', 1, 'int');
        $start_time  = $end_time = '';
        if ($time) {
            list($start_time, $end_time) = explode(' - ', $time);
        }
        $url       = sprintf($this->search_news_url, $uid, $read_num, $start_time, $end_time, $sort, $page);
        $data      = json_decode($this->_get($url), true);
        $is_last   = count($data) == 100 ? true : false;
        $url_param = array('uid' => $uid, 'read_num' => $read_num, 'time' => $time, 'sort' => $sort, 'page' => $page);
        $this->assign(array('cate' => $news_cate, 'data' => $data, 'time' => $time, 'is_last' => $is_last, 'page' => $page, 'url_param' => $url_param));
        $this->display();

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
            $_data = array_values($data);
            $data  = array();
            foreach ($_data as $key => $item) {
                $data[$key * 2 + 1]['id']            = $item['shop_goods_id'];
                $data[$key * 2 + 1]['temai_id']      = $item['top_line_article_id'] . $item['shop_goods_id'];
                $data[$key * 2 + 1]['type']          = '放心购';
                $data[$key * 2 + 1]['img']           = $item['img'];
                $data[$key * 2 + 1]['price']         = $item['price'];
                $data[$key * 2 + 1]['url']           = $item['real_url'];
                $data[$key * 2 + 1]['title']         = $item['name'];
                $data[$key * 2 + 1]['describe_info'] = $item['description'];
                $data[$key * 2 + 1]['taobao_id']     = $item['shop_goods_id'];
                $data[$key * 2 + 1]['temai_id']      = "http://www.toutiao.com/a" . $item['top_line_article_id'];
                $data[$key * 2 + 1]['tmall_url']     = $item['real_url'];
                $data[$key * 2 + 1]['post_data']     = json_encode($data[$key * 2 + 1]);
                $attached_img                        = $item['attached_imgs'];
                $data[$key * 2 + 2]['id']            = $item['shop_goods_id'];
                $data[$key * 2 + 2]['temai_id']      = $item['top_line_article_id'] . $item['shop_goods_id'];
                $data[$key * 2 + 2]['type']          = '放心购';
                $data[$key * 2 + 2]['img']           = $attached_img[0]['img'];
                $data[$key * 2 + 2]['price']         = $item['price'];
                $data[$key * 2 + 2]['url']           = $item['real_url'];
                $data[$key * 2 + 2]['title']         = $item['name'];
                $data[$key * 2 + 2]['describe_info'] = $attached_img[0]['description'];
                $data[$key * 2 + 2]['taobao_id']     = $item['shop_goods_id'];
                $data[$key * 2 + 2]['temai_id']      = "http://www.toutiao.com/a" . $item['top_line_article_id'];
                $data[$key * 2 + 2]['tmall_url']     = $item['real_url'];
                $data[$key * 2 + 2]['post_data']     = json_encode($data[$key * 2 + 2]);
                if (isset($cache_data[$item['shop_goods_id']])) {
                    $data[$key * 2 + 1]['is_add'] = 1;
                    $data[$key * 2 + 2]['is_add'] = 1;
                } else {
                    $data[$key * 2 + 1]['is_add'] = 0;
                    $data[$key * 2 + 2]['is_add'] = 0;
                }
            }
            $this->assign('data', array_chunk($data, 4));
        }
        $this->assign('cart_count', count($cache_data));
        $this->display();
    }

    /**
     * 微头条商品数据
     */
    public function daTaoKeItemList() {
        $keyword = I('get.keyword', '', 'trim');
        $sort    = I('get.sort', 0, 'int');
        $where   = array('add_time' => date('Ymd'));
        if ($keyword) {
            $where['title|short_title|desc'] = array('like', "%$keyword%");
        }
        switch ($sort) {
            case 1 :
                $order = "is_send,coupon_price desc";
                break;
            case 2 :
                $order = "is_send,coupon_price asc";
                break;
            case 3 :
                $order = "is_send,commission_rate desc";
                break;
            case 4 :
                $order = "is_send,commission_rate asc";
                break;
            default :
                $order = 'is_send,id desc';
        }
        $data = M('dtk_goods')->where($where)->order($order)->select();
        $this->assign('data', array_chunk($data, 4));
        $this->display();
    }

    /**
     * 一键发送
     */
    public function sendGoods() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id    = I('post.id', 0, 'int');
        $goods = M('dtk_goods')->find($id);
        if (!$id || !$goods) {
            $this->error('请求参数不合法');
        }
        $img_res = $this->_uploadWeiTopImg($goods['pic']);
        if ($img_res['status'] == 0) {
            $this->error($img_res['info']);
        }
        $link_res = $this->_getShortLink($goods['goods_id']);
        if ($link_res['status'] == 0) {
            $this->error($link_res['info']);
        }
        $send_url  = "https://www.toutiao.com/c/ugc/content/publish/";
        $send_data = array(
            'content'    => "点击此链接{$link_res['url']}\r\n券后【{$goods['coupon_price']}】元 包邮秒杀\r\n{$goods['short_title']}\r\n{$goods['desc']}\r\n更多好货请关注【白菜好货分享】",
            'image_uris' => $img_res['img_key'],
        );
        $res       = $this->_post($send_url, $send_data, $this->_getWeiTopCookie());
        $res       = json_decode($res, true);
        if ($res['message'] == 'success') {
            M('dtk_goods')->where(array('id' => $id))->setField(array('is_send' => 'Y'));
            $this->success('发送成功');
        } else {
            $this->error('发送失败！');
        }
    }

    /**
     * @param $file_url
     * @return array
     */
    protected function _uploadWeiTopImg($file_url) {
        if (strpos($file_url, 'http') === false) {
            $file_url = "https:" . $file_url;
        }
        $url_path = dirname(APP_PATH) . '/www/Uploads/wei_top_temp.jpg';
        $file_url = str_replace('https', 'http', $file_url);
        $content  = file_get_contents($file_url);
        file_put_contents($url_path, $content);
        $obj   = new \CurlFile($url_path);
        $param = array('photo' => $obj);
        $data  = json_decode($this->_file("https://mp.toutiao.com/upload_photo/?type=json", $param, $this->_getTopLineCookie()), true);
        unlink($url_path);
        if (strtolower($data['message']) == 'success') {
            return array('status' => 1, 'info' => 'ok', 'url' => $data['web_url'], 'img_key' => $data['web_uri']);
        } else {
            return array('status' => 0, 'info' => '上传失败！');
        }
    }

    /**
     * 获取头条默认账号信息
     */
    protected function _getWeiTopCookie() {
        $data = S('wei_top_cookie');
        if (!$data) {
            $data = M('top_line_account')->getFieldById(8, 'cookie');
            if ($data) {
                S('wei_top_cookie', $data);
            }
        }
        return $data;
    }

    /**
     * @param $goods_id
     * @param null $pid
     * @param null $token
     * @return mixed
     */
    protected function _getShortLink($goods_id, $pid = null, $token = null) {
        // 迷离团队请求配置参数
        if (!$pid) {
            $pid = 'mm_121610813_42450934_228388649';
        }
        if (!$token) {
            $token = C('TB_ACCESS_TOKEN.default_token');
        }
        $pid_info  = explode('_', $pid);
        $url       = 'http://tbapi.00o.cn/highapi.php';
        $param     = 'item_id=%s&adzone_id=%s&platform=1&site_id=%s&token=%s';
        $param     = sprintf($param, $goods_id, $pid_info[3], $pid_info[2], $token);
        $Http      = new Http();
        $mi_li_res = $Http->post($url, $param);
        $res       = json_decode($mi_li_res, true);
        if ($res && $res['result']['data']['coupon_click_url']) {
            $click_url = $res['result']['data']['coupon_click_url'];
            return array('status' => 1, 'url' => $click_url, 'info' => 'ok');
        } else {
            return array('status' => 0, 'info' => $res['sub_msg']);
        }
    }

    /**
     * 打开文章详情
     */
    public function openArticleDetail() {
        $article_id    = I('get.id', '', 'trim');
        $location_url  = 'http://www.toutiao.com/a' . $article_id;
        $url           = 'http://www.toutiao.com/toutiao/group/' . $article_id;
        $Http          = new Http();
        $content       = $Http->get($url);
        $title_reg_exp = '%<title>(.*?)</title>%si';
        preg_match($title_reg_exp, $content, $title_match);
        $title = "";
        if (count($title_match) > 0) {
            $title = $title_match[1];
        }
        $reg_exp = '%<textarea id="gallery-data-textarea" .*?>(.*?)</textarea>%si';
        preg_match($reg_exp, $content, $match);
        $match_data = json_decode($match[1], true);
        $data       = array();
        if (count($match_data) > 0) {
            foreach ($match_data as $k => $item) {
                if (isset($item['price']) && strpos($item['real_url'], 'haohuo.snssdk.com')) {
                    $data[$k] = array(
                        'shop_type_name' => $item['shop_type_name'],
                        'img'            => $item['img'],
                        'title'          => $item['name'],
                        'price'          => $item['price'],
                        'desc'           => $item['description'],
                        'url'            => $item['real_url'],
                        'goods_url'      => 'https://haohuo.snssdk.com/product/detail?id=' . $item['shop_goods_id']
                    );
                }
                if (isset($item['commodity'])) {
                    $data[$k] = array(
                        'shop_type_name' => $match_data[$k - 1]['shop_type_name'],
                        'img'            => $item['location'],
                        'title'          => $match_data[$k - 1]['name'],
                        'price'          => $match_data[$k - 1]['price'],
                        'desc'           => $item['description'],
                        'url'            => $match_data[$k - 1]['real_url'],
                        'goods_url'      => 'https://haohuo.snssdk.com/product/detail?id=' . $match_data[$k - 1]['shop_goods_id']
                    );
                }
            }
        }
        $this->assign('location_url', $location_url);
        $this->assign('title', $title);
        $this->assign('data', $data);
        $this->display();
    }
}