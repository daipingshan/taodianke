<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2017/12/9
 * Time: 16:11
 */

namespace Admin\Controller;

use Common\Org\Http;

/**
 * 头条数据抓取
 * Class TopItemsController
 *
 * @package Admin\Controller
 */
class TopItemsController extends CommonController {

    /**
     * @var bool
     */
    protected $checkUser = false;

    /**
     * 抓取文章地址
     *
     * @var string
     */
    protected $news_url = "http://rym.quwenge.com/temai_article.php?uid=%s&str_time=%s&end_time=%s&count=%s&orderY=%s&page=%s&1=1";

    /**
     * @var array
     */
    private $cate = array(
        '5569547953',
        '5568158065',
        '5572814229',
        '5573124268',
        '5573658957',
        '5570589814',
        '5571864339',
        '5573716916',
        '5571749564',
        '5565295982'
    );

    /**
     * 头条文章抓取
     */
    public function getNews() {
        $is_get_data = true;
        $repeat_num  = 0;
        $fail_num    = 0;
        $page        = I('get.page', 1, 'int');
        //$start_hour  = (int)date('H') - 2;
        //$end_hour    = (int)date('H');
        //$start_hour  = $start_hour > 9 ? $start_hour : '0' . $start_hour;
        //$end_hour    = $end_hour > 9 ? $end_hour : '0' . $end_hour;
        while ($is_get_data) {
            $str_time            = date('Y-m-d', strtotime('-1 days')) . "00:00:00";
            $end_time            = date('Y-m-d', strtotime('-1 days')) . "23:59:59";
            $this->http_time_out = 5;
            $uid                 = "6768100064";
            $url                 = sprintf($this->news_url, $uid, $str_time, $end_time, '', '', $page);
            $res                 = $this->_get($url);
            $res                 = json_decode($res, true);
            if (count($res) == 0) {
                $fail_num++;
                if ($fail_num == 5) {
                    $is_get_data = false;
                }
                echo "第{$page}页文章抓取失败\r\n";
                sleep(3);
                continue;
            }
            $data = array();
            foreach ($res as $article) {
                $create_user_id               = get_word($article['url'], 'create_user_id=', '&');
                $data[$article['article_id']] = array(
                    'news_id'         => $article['id'],
                    'article_id'      => $article['article_id'],
                    'title'           => $article['title'],
                    'tag'             => $article['tag'],
                    'comments_count'  => $article['comments_count'],
                    'go_detail_count' => $article['go_detail_count'],
                    'behot_time'      => $article['behot_time'],
                    'url'             => $article['url'],
                    'article_genre'   => $article['article_genre'],
                    'type'            => $article['type'],
                    'check_str'       => $article['check_str'],
                    'create_user_id'  => $create_user_id,
                );
            }
            $key_news_id = array_keys($data);
            $have_data   = M('hao_huo_article')->field('article_id')->where(array('article_id' => array('in', $key_news_id)))->index('article_id')->select();
            foreach ($have_data as $key => $val) {
                unset($data[$key]);
            }
            if (count($data) == 0) {
                $repeat_num++;
                if ($repeat_num == 3) {
                    $is_get_data = false;
                }
                echo "第{$page}页文章抓取成功，但所有数据均已全部添加至文章库\r\n";
                sleep(3);
                $page = $page + 1;
                continue;
            }
            $add_data = array_values($data);
            $add_res  = M('hao_huo_article')->addAll($add_data);
            if ($add_res) {
                echo "第{$page}页文章抓取成功并添加成功\r\n";
                sleep(3);
                $page = $page + 1;
                continue;
            } else {
                echo "第{$page}页文章抓取成功,但添加失败\r\n";
                sleep(3);
                continue;
            }
        }
    }

    /**
     * 抓取商品数据
     */
    public function getItems() {
        $is_have = true;
        while ($is_have) {
            $db_data = M('hao_huo_article')->field('id,article_id,title,url,create_user_id,behot_time')->where(array('is_add_item' => 0, 'article_genre' => 1, 'is_normal' => 1))->index('id')->order('behot_time desc,id desc')->limit(100)->select();
            if (count($db_data) < 100) {
                echo "文章已处理完成\r\n";
                $is_have = false;
            }
            $data = $article_ids = $article_no_normal_ids = array();
            foreach ($db_data as $article) {
                echo "文章ID->【{$article['id']}】正在处理中……\r\n";
                $content = $this->_get($article['url']);
                $reg_exp = '%<textarea id="gallery-data-textarea" .*?>(.*?)</textarea>%si';
                preg_match($reg_exp, $content, $match);
                $match_data = json_decode($match[1], true);
                if (count($match_data) > 0) {
                    $article_ids[] = $article['id'];
                    foreach ($match_data as $k => $item) {
                        if (isset($item['price']) && strpos($item['real_url'], 'haohuo.snssdk.com')) {
                            $data[$item['id']] = array(
                                'article_id'                  => $article['id'],
                                'top_line_article_id'         => $article['article_id'],
                                'create_user_id'              => $article['create_user_id'],
                                'top_line_article_title'      => $article['title'],
                                'top_line_article_behot_time' => $article['behot_time'],
                                'shop_type'                   => $item['shop_type'],
                                'shop_goods_id'               => $item['shop_goods_id'],
                                'price_tag_position'          => $item['price_tag_position'],
                                'img'                         => $item['img'],
                                'goods_json'                  => $item['goods_json'],
                                'name'                        => $item['name'],
                                'price'                       => $item['price'],
                                'description'                 => $item['description'],
                                'real_url'                    => $item['real_url'],
                                'user_url'                    => $item['user_url'],
                                'self_charging_url'           => $item['self_charging_url'],
                            );
                        }
                        if (isset($item['commodity']) && isset($data[$item['commodity']['id']])) {
                            $data[$item['commodity']['id']]['attached_imgs'][] = array(
                                'img'             => $item['location'],
                                'origin_location' => $item['origin_location'],
                                'description'     => $item['description'],
                                'id'              => 'commodity_' . time() . rand(1000, 9999),
                            );
                        }
                    }
                } else {
                    $article_no_normal_ids[] = $article['id'];
                }
            }
            if ($data) {
                $model = M();
                $model->startTrans();
                try {
                    foreach ($data as $key => $val) {
                        if (count($val['attached_imgs']) == 1) {
                            $data[$key]['attached_imgs'] = json_encode($val['attached_imgs']);
                        } else {
                            unset($data[$key]);
                        }
                    }
                    M('hao_huo_items')->addAll(array_values($data));
                    if ($article_ids) {
                        M('hao_huo_article')->where(array('id' => array('in', $article_ids)))->save(array('is_add_item' => 1));
                    }
                    if ($article_no_normal_ids) {
                        M('hao_huo_article')->where(array('id' => array('in', $article_no_normal_ids)))->save(array('is_normal' => 0));
                    }
                    $model->commit();
                    echo "添加至商品库成功\r\n";
                    sleep(3);
                    continue;
                } catch (\Exception $e) {
                    $model->rollback();
                    echo $e->getMessage() . "\r\n";
                    sleep(3);
                    continue;
                }
            } else {
                $is_have = false;
                echo "组合商品数据失败！\r\n";
            }
        }
    }

    /**
     * 淘宝天猫商品抓取
     */
    public function getTaoBaoNews() {
        $is_get_data = true;
        $repeat_num  = 0;
        $fail_num    = 0;
        $page        = I('get.page', 1, 'int');
        $uid         = I('get.uid', '', 'trim');
        if (!in_array($uid, $this->cate)) {
            echo "分类id不合法\r\n";
            $is_get_data = false;
        }
        $start_hour = (int)date('H') - 2;
        $end_hour   = (int)date('H');
        $start_hour = $start_hour > 9 ? $start_hour : '0' . $start_hour;
        $end_hour   = $end_hour > 9 ? $end_hour : '0' . $end_hour;
        while ($is_get_data) {
            $str_time            = date('Y-m-d') . ' ' . $start_hour . ":00:00";
            $end_time            = date('Y-m-d') . ' ' . $end_hour . ":00:00";
            $this->http_time_out = 5;
            $url                 = sprintf($this->news_url, $uid, $str_time, $end_time, '', '', $page);
            $res                 = $this->_get($url);
            $res                 = json_decode($res, true);
            if (count($res) == 0) {
                $fail_num++;
                if ($fail_num == 5) {
                    $is_get_data = false;
                }
                echo "第{$page}页文章抓取失败\r\n";
                sleep(3);
                continue;
            }
            $data = array();
            foreach ($res as $article) {
                if ($article['check_str'] == '放心购') {
                    continue;
                }
                $create_user_id               = get_word($article['url'], 'create_user_id=', '&');
                $data[$article['article_id']] = array(
                    'cate_id'         => $uid,
                    'news_id'         => $article['id'],
                    'article_id'      => $article['article_id'],
                    'title'           => $article['title'],
                    'tag'             => $article['tag'],
                    'comments_count'  => $article['comments_count'],
                    'go_detail_count' => $article['go_detail_count'],
                    'behot_time'      => $article['behot_time'],
                    'url'             => $article['url'],
                    'article_genre'   => $article['article_genre'],
                    'type'            => $article['type'],
                    'check_str'       => $article['check_str'],
                    'create_user_id'  => $create_user_id,
                );
            }
            $key_news_id = array_keys($data);
            $have_data   = M('tao_bao_article')->field('article_id')->where(array('article_id' => array('in', $key_news_id)))->index('article_id')->select();
            foreach ($have_data as $key => $val) {
                unset($data[$key]);
            }
            if (count($data) == 0) {
                $repeat_num++;
                if ($repeat_num == 3) {
                    $is_get_data = false;
                }
                echo "第{$page}页文章抓取成功，但所有数据均已全部添加至文章库\r\n";
                sleep(3);
                $page = $page + 1;
                continue;
            }
            $add_data = array_values($data);
            $add_res  = M('tao_bao_article')->addAll($add_data);
            if ($add_res) {
                echo "第{$page}页文章抓取成功并添加成功\r\n";
                sleep(3);
                $page = $page + 1;
                continue;
            } else {
                echo "第{$page}页文章抓取成功,但添加失败\r\n";
                sleep(3);
                continue;
            }
        }
    }

    /**
     * 抓取商品数据
     */
    public function getTaoBaoItems() {
        $is_have = true;
        while ($is_have) {
            $db_data = M('tao_bao_article')->field('id,article_id,title,url,create_user_id,behot_time,cate_id')->where(array('is_add_item' => 0, 'article_genre' => 1, 'is_normal' => 1))->index('id')->limit(100)->select();
            if (count($db_data) < 100) {
                echo "文章已处理完成\r\n";
                $is_have = false;
            }
            $data = $article_ids = $article_no_normal_ids = array();
            foreach ($db_data as $article) {
                echo "文章ID->【{$article['id']}】正在处理中……\r\n";
                $content = $this->_get($article['url']);
                $reg_exp = '%<textarea id="gallery-data-textarea" .*?>(.*?)</textarea>%si';
                preg_match($reg_exp, $content, $match);
                $match_data = json_decode($match[1], true);
                if (count($match_data) > 0) {
                    $article_ids[] = $article['id'];
                    foreach ($match_data as $k => $item) {
                        if (isset($item['price'])) {
                            $data[$item['id']] = array(
                                'cate_id'                     => $article['cate_id'],
                                'article_id'                  => $article['id'],
                                'top_line_article_id'         => $article['article_id'],
                                'create_user_id'              => $article['create_user_id'],
                                'top_line_article_title'      => $article['title'],
                                'top_line_article_behot_time' => $article['behot_time'],
                                'shop_type'                   => $item['shop_type'],
                                'shop_goods_id'               => $item['shop_goods_id'],
                                'price_tag_position'          => $item['price_tag_position'],
                                'img'                         => $item['img'],
                                'goods_json'                  => $item['goods_json'],
                                'name'                        => $item['name'],
                                'price'                       => $item['price'],
                                'description'                 => $item['description'],
                                'real_url'                    => $item['real_url'],
                                'user_url'                    => $item['user_url'],
                                'self_charging_url'           => $item['self_charging_url'],
                            );
                        }
                        if (isset($item['commodity']) && isset($data[$item['commodity']['id']])) {
                            $data[$item['commodity']['id']]['attached_imgs'][] = array(
                                'img'             => $item['location'],
                                'origin_location' => $item['origin_location'],
                                'description'     => $item['description'],
                                'id'              => 'commodity_' . time() . rand(1000, 9999),
                            );
                        }
                    }
                } else {
                    $article_no_normal_ids[] = $article['id'];
                }
            }
            if ($data) {
                $model = M();
                $model->startTrans();
                try {
                    foreach ($data as $key => $val) {
                        if (count($val['attached_imgs']) == 1) {
                            $data[$key]['attached_imgs'] = json_encode($val['attached_imgs']);
                        } else {
                            unset($data[$key]);
                        }
                    }
                    M('tao_bao_items')->addAll(array_values($data));
                    if ($article_ids) {
                        M('tao_bao_article')->where(array('id' => array('in', $article_ids)))->save(array('is_add_item' => 1));
                    }
                    if ($article_no_normal_ids) {
                        M('tao_bao_article')->where(array('id' => array('in', $article_no_normal_ids)))->save(array('is_normal' => 0));
                    }
                    $model->commit();
                    echo "添加至商品库成功\r\n";
                    sleep(3);
                    continue;
                } catch (\Exception $e) {
                    $model->rollback();
                    echo $e->getMessage() . "\r\n";
                    sleep(3);
                    continue;
                }
            } else {
                $is_have = false;
                echo "组合商品数据失败！\r\n";
            }
        }
    }

    /**
     * 更新账号Cookie
     */
    public function updateSaleCookie() {
        $username = I('post.username', '', 'trim');
        $cookie   = I('post.cookie', '', 'trim');
        if (!$username) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '账号名称不能为空！'));
        }
        if (!$cookie) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '账号cookie不能为空！'));
        }
        $id = M('sale_account')->where(array('username' => $username))->getField('id');
        if (!$id) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '账号不存在！'));
        }
        $cookie = base64_decode(str_replace(array('%2b', ' '), '+', urldecode($cookie)));;
        $data = array('id' => $id, 'cookie' => $cookie, 'update_time' => time());
        $res  = M('sale_account')->save($data);
        if ($res !== false) {
            if ($id == 1) {
                S('sale_cookie', null);
            }
            $this->ajaxReturn(array('code' => 1, 'msg' => '更新成功！'));
        } else {
            $this->ajaxReturn(array('code' => 0, 'msg' => '更新失败！'));
        }
    }

    /**
     * 头条文章抓取
     */
    public function getHighReadNews() {
        $is_get_data = true;
        $repeat_num  = 0;
        $fail_num    = 0;
        $page        = I('get.page', 1, 'int');
        while ($is_get_data) {
            $str_time            = date('Y-m-d', strtotime('-3 days')) . "00:00:00";
            $end_time            = date('Y-m-d', strtotime('-1 days')) . "23:59:59";
            $this->http_time_out = 5;
            $url                 = sprintf($this->news_url, '', $str_time, $end_time, 10000, 1, $page);
            $res                 = $this->_get($url);
            $res                 = json_decode($res, true);
            if (count($res) == 0) {
                $fail_num++;
                if ($fail_num == 5) {
                    $is_get_data = false;
                }
                echo "第{$page}页文章抓取失败\r\n";
                sleep(3);
                continue;
            }
            $data = array();
            foreach ($res as $article) {
                $create_user_id               = get_word($article['url'], 'create_user_id=', '&');
                $data[$article['article_id']] = array(
                    'news_id'         => $article['id'],
                    'article_id'      => $article['article_id'],
                    'title'           => $article['title'],
                    'tag'             => $article['tag'],
                    'comments_count'  => $article['comments_count'],
                    'go_detail_count' => $article['go_detail_count'],
                    'behot_time'      => $article['behot_time'],
                    'url'             => $article['url'],
                    'article_genre'   => $article['article_genre'],
                    'type'            => $article['type'],
                    'check_str'       => $article['check_str'],
                    'create_user_id'  => $create_user_id,
                );
            }
            $key_news_id = array_keys($data);
            $have_data   = M('high_read_article')->field('id,article_id')->where(array('article_id' => array('in', $key_news_id)))->index('article_id')->select();
            foreach ($have_data as $key => $val) {
                M('high_read_article')->where(array('id' => $val['id']))->save(array('comments_count' => $data[$key]['comments_count'], 'go_detail_count' => $data[$key]['go_detail_count']));
                unset($data[$key]);
            }
            if (count($data) == 0) {
                $repeat_num++;
                if ($repeat_num == 3) {
                    $is_get_data = false;
                }
                echo "第{$page}页文章抓取成功，但所有数据均已全部添加至文章库\r\n";
                sleep(3);
                $page = $page + 1;
                continue;
            }
            $add_data = array_values($data);
            $add_res  = M('high_read_article')->addAll($add_data);
            if ($add_res) {
                echo "第{$page}页文章抓取成功并添加成功\r\n";
                sleep(3);
                $page = $page + 1;
                continue;
            } else {
                echo "第{$page}页文章抓取成功,但添加失败\r\n";
                sleep(3);
                continue;
            }
        }
    }

    /**
     * 抓取商品数据
     */
    public function getHighReadItems() {
        $is_have = true;
        while ($is_have) {
            $db_data = M('high_read_article')->field('id,article_id,title,url,create_user_id,behot_time')->where(array('is_add_item' => 0, 'article_genre' => 1, 'is_normal' => 1))->index('id')->order('behot_time desc,id desc')->limit(100)->select();
            if (count($db_data) < 100) {
                echo "文章已处理完成\r\n";
                $is_have = false;
            }
            $data = $article_ids = $article_no_normal_ids = array();
            foreach ($db_data as $article) {
                $temp_num = 0;
                echo "文章ID->【{$article['id']}】正在处理中……\r\n";
                $content = $this->_get($article['url']);
                $reg_exp = '%<textarea id="gallery-data-textarea" .*?>(.*?)</textarea>%si';
                preg_match($reg_exp, $content, $match);
                $match_data = json_decode($match[1], true);
                if (count($match_data) > 0) {
                    $article_ids[] = $article['id'];
                    foreach ($match_data as $k => $item) {
                        if (isset($item['price']) && strpos($item['real_url'], 'haohuo.snssdk.com') && $temp_num < 3) {
                            $data[$item['id']] = array(
                                'article_id'                  => $article['id'],
                                'top_line_article_id'         => $article['article_id'],
                                'create_user_id'              => $article['create_user_id'],
                                'top_line_article_title'      => $article['title'],
                                'top_line_article_behot_time' => $article['behot_time'],
                                'shop_type'                   => $item['shop_type'],
                                'shop_goods_id'               => $item['shop_goods_id'],
                                'price_tag_position'          => $item['price_tag_position'],
                                'img'                         => $item['img'],
                                'goods_json'                  => $item['goods_json'],
                                'name'                        => $item['name'],
                                'price'                       => $item['price'],
                                'description'                 => $item['description'],
                                'real_url'                    => $item['real_url'],
                                'user_url'                    => $item['user_url'],
                                'self_charging_url'           => $item['self_charging_url'],
                            );
                            $temp_num++;
                        }
                        if (isset($item['commodity']) && isset($data[$item['commodity']['id']])) {
                            $data[$item['commodity']['id']]['attached_imgs'][] = array(
                                'img'             => $item['location'],
                                'origin_location' => $item['origin_location'],
                                'description'     => $item['description'],
                                'id'              => 'commodity_' . time() . rand(1000, 9999),
                            );
                        }
                    }
                } else {
                    $article_no_normal_ids[] = $article['id'];
                }
            }
            if ($data) {
                $model = M();
                $model->startTrans();
                try {
                    foreach ($data as $key => $val) {
                        if (count($val['attached_imgs']) == 1) {
                            $data[$key]['attached_imgs'] = json_encode($val['attached_imgs']);
                        } else {
                            unset($data[$key]);
                        }
                    }
                    M('high_read_items')->addAll(array_values($data));
                    if ($article_ids) {
                        M('high_read_article')->where(array('id' => array('in', $article_ids)))->save(array('is_add_item' => 1));
                    }
                    if ($article_no_normal_ids) {
                        M('high_read_article')->where(array('id' => array('in', $article_no_normal_ids)))->save(array('is_normal' => 0));
                    }
                    $model->commit();
                    echo "添加至商品库成功\r\n";
                    sleep(3);
                    continue;
                } catch (\Exception $e) {
                    $model->rollback();
                    echo $e->getMessage() . "\r\n";
                    sleep(3);
                    continue;
                }
            } else {
                $is_have = false;
                echo "组合商品数据失败！\r\n";
            }
        }
    }

    /**
     * 添加大淘客商品数据
     */
    public function getDaTaoKeItem() {
        $url  = "http://api.dataoke.com/index.php?r=Port/index&type=paoliang&appkey=4frqyegyob&v=2";
        $Http = new Http();
        $data = json_decode($Http->get($url), true);
        if ($data['data']['total_num'] > 0 && $data['result']) {
            $save_data = $goods_ids = array();
            foreach ($data['result'] as $val) {
                if (strpos($val['Pic'], 'http') === false) {
                    $val['Pic'] = "https:" . $val['Pic'];
                }
                $save_data[] = array(
                    'goods_id'        => $val['GoodsID'],
                    'pic'             => $val['Pic'],
                    'title'           => $val['Title'],
                    'short_title'     => $val['D_title'],
                    'desc'            => $val['Introduce'],
                    'price'           => $val['Org_Price'],
                    'coupon_price'    => $val['Price'],
                    'coupon_money'    => $val['Quan_price'],
                    'commission_rate' => $val['Commission_jihua'] > $val['Commission_queqiao'] ? $val['Commission_jihua'] : $val['Commission_queqiao'],
                    'coupon_url'      => $val['Quan_link'],
                    'add_time'        => date('Ymd'),
                );
                $goods_ids[] = $val['GoodsID'];
            }
            $db_data = M('dtk_goods')->where(array('goods_id' => array('in', $goods_ids), 'add_time' => date('Ymd')))->getField('goods_id', true);
            foreach ($save_data as $k => $v) {
                if (in_array($v['goods_id'], $db_data)) {
                    unset($save_data[$k]);
                }
            }
            if (count($save_data) > 0) {
                M('dtk_goods')->addAll(array_values($save_data));
                echo "获取大淘客商品成功，添加数据成功！\r\n";
            } else {
                echo "获取大淘客商品成功，但所有数据均以添加至商品库！\r\n";
            }
        } else {
            echo "获取大淘客商品失败！\r\n";
        }
    }
}