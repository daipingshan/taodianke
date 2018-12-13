<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/4/9
 * Time: 11:00
 */

namespace Admin\Controller;


class TopLineArticleController extends CommonController {

    /**
     * @var bool
     */
    protected $checkUser = false;

    /**
     * @var array
     */
    protected $cateArr = array(5500950648, 5500838410, 5500803015, 5500367585, 5501814762, 5501679303, 5500311915, 5500358214, 5500903267, 5501832587, 6768458493);

    /**
     * 添加文章数据
     */
    public function addArticle() {
        $media_id = I('get.media_id', '', 'trim');
        $this->_addLogs("add_temai_article_msg_{$media_id}.log", date('Y-m-d H:i:s') . "-开始添加请求");
        if (!in_array($media_id, $this->cateArr)) {
            $msg = "文章类型不合法";
            $this->_addLogs("add_temai_article_msg_{$media_id}.log", $msg);
            exit;
        }
        $start_time = time();
        $end_time   = time() - 2400;
        $get_num    = 0;
        $temp_time  = $start_time;
        $data       = $add_data = $new_ids_data = $news_ids = array();
        while ($end_time < $temp_time) {
            $param_arr = $this->_getParam();
            $param     = array(
                'media_id'       => $media_id,
                'page_type'      => 1,
                'max_behot_time' => $start_time,
                'count'          => 10,
                'version'        => 2,
                'platform'       => 'wap',
                'as'             => $param_arr['as'],
                'cp'             => $param_arr['cp'],
            );
            $url       = "https://www.toutiao.com/pgc/ma/";
            $proxy     = $this->_getProxy();
            $res       = json_decode($this->_get($url, $param, $proxy), true);
            if ($res['message'] == 'success' && count($res['data']) > 0) {
                $get_num = 0;
                foreach ($res['data'] as $val) {
                    $news_id        = get_word($val['article_url'], "\?id=", '&');
                    $data[$news_id] = array(
                        'news_id'         => $news_id,
                        'article_id'      => $val['str_group_id'],
                        'title'           => $val['title'],
                        'comments_count'  => $val['comments_count'],
                        'go_detail_count' => $val['total_read_count'],
                        'behot_time'      => strtotime($val['datetime']),
                        'url'             => $val['article_url'],
                        'article_genre'   => get_word($val['article_url'], "content_type=", '&') == 1 ? 2 : 1,
                        'media_id'        => $media_id,
                        'create_user_id'  => get_word($val['article_url'], "create_user_id=", '&'),
                    );
                    $news_ids[]     = $news_id;
                }
                $temp_time  = $res['next']['max_behot_time'];
                $start_time = $temp_time;
                $msg        = date('Y-m-d H:i', $start_time == 0 ? time() : $start_time) . "，请求IP：{$proxy}，请求数据成功，分类ID:{$media_id}";
            } else {
                if ($get_num > 20) {
                    break;
                }
                $get_num++;
                $msg = date('Y-m-d H:i', $start_time == 0 ? time() : $start_time) . "，请求IP：{$proxy}，请求数据第{$get_num}次失败，分类ID:{$media_id}";
            }
            $this->_addLogs("add_temai_article_msg_{$media_id}.log", $msg);
            sleep(10);
        }
        if ($get_num > 20) {
            $msg = "分类ID:{$media_id}，获取数据失败次数超过20次，请检查请求参数";
            $this->_addLogs('add_temai_article_error.log', array('param' => $param, 'msg' => $msg));
            exit;
        }
        $news_ids_arr = array_chunk($news_ids, 500);
        foreach ($news_ids_arr as $in_id) {
            $temp_ids     = M('temai_article')->where(array('news_id' => array('in', $in_id)))->getField('news_id', true);
            $new_ids_data = array_merge($new_ids_data, $temp_ids ? : array());
        }
        foreach ($new_ids_data as $id) {
            if (isset($data[$id]) && $data[$id]) {
                unset($data[$id]);
            }
        }
        if (count($data) == 0) {
            $msg = "分类ID:{$media_id}，已全部采集完成";
            $this->_addLogs('add_temai_article_success.log', $msg);
            exit;
        }
        $add_data = array_chunk($data, 500);
        $model    = M('temai_article');
        $model->startTrans();
        try {
            foreach ($add_data as $db_data) {
                $model->addAll($db_data);
            }
            $model->commit();
            $date = date('Y-m-d H:i');
            $msg  = "分类ID:{$media_id}，{$date}采集成功";
            $this->_addLogs('add_temai_article_success.log', $msg);
        } catch (\Exception $e) {
            $model->rollback();
            $this->_addLogs('db_add_temai_article_error.log', $e->getMessage());
        }
        $this->_addLogs("add_temai_article_msg_{$media_id}.log", date('Y-m-d H:i:s') . "-结束添加请求");
    }

    /**
     * 添加文章数据
     */
    public function addTempArticle() {
        $media_id = I('get.media_id', '', 'trim');
        $this->_addLogs("add_temai_article_msg_temp_{$media_id}.log", "开始添加请求");
        if (!in_array($media_id, $this->cateArr)) {
            $msg = "文章类型不合法";
            $this->_addLogs("add_temai_article_msg_{$media_id}.log", $msg);
            exit;
        }
        $start_time = I('get.start_time', '', 'trim');
        $end_time   = I('get.end_time', '', 'trim');
        $get_num    = 0;
        if (!$start_time) {
            $start_time = time();
        } else {
            $start_time = strtotime($start_time);
        }
        $end_time  = strtotime($end_time);
        $temp_time = $start_time;
        $data      = $add_data = $new_ids_data = $news_ids = array();
        while ($end_time < $temp_time) {
            $param_arr = $this->_getParam();
            $param     = array(
                'media_id'       => $media_id,
                'page_type'      => 1,
                'max_behot_time' => $start_time,
                'count'          => 10,
                'version'        => 2,
                'platform'       => 'wap',
                'as'             => $param_arr['as'],
                'cp'             => $param_arr['cp'],
            );
            $url       = "https://www.toutiao.com/pgc/ma/";
            $proxy     = $this->_getProxy();
            $res       = json_decode($this->_get($url, $param, $proxy), true);
            if ($res['message'] == 'success' && count($res['data']) > 0) {
                $get_num = 0;
                foreach ($res['data'] as $val) {
                    $news_id        = get_word($val['article_url'], "\?id=", '&');
                    $data[$news_id] = array(
                        'news_id'         => $news_id,
                        'article_id'      => $val['str_group_id'],
                        'title'           => $val['title'],
                        'comments_count'  => $val['comments_count'],
                        'go_detail_count' => $val['total_read_count'],
                        'behot_time'      => strtotime($val['datetime']),
                        'url'             => $val['article_url'],
                        'article_genre'   => get_word($val['article_url'], "content_type=", '&') == 1 ? 2 : 1,
                        'media_id'        => $media_id,
                        'create_user_id'  => get_word($val['article_url'], "create_user_id=", '&'),
                    );
                    $news_ids[]     = $news_id;
                }
                $temp_time  = $res['next']['max_behot_time'];
                $start_time = $temp_time;
                $msg        = date('Y-m-d H:i', $start_time == 0 ? time() : $start_time) . "，请求IP：{$proxy}，请求数据成功，分类ID:{$media_id}";
            } else {
                if ($get_num > 20) {
                    break;
                }
                $get_num++;
                $msg = date('Y-m-d H:i', $start_time == 0 ? time() : $start_time) . "，请求IP：{$proxy}，请求数据第{$get_num}次失败，分类ID:{$media_id}";
            }
            $this->_addLogs("add_temai_article_msg_temp_{$media_id}.log", $msg);
            sleep(10);
        }
        if ($get_num > 20) {
            $msg = "分类ID:{$media_id}，获取数据失败次数超过20次，请检查请求参数";
            $this->_addLogs('add_temai_article_error.log', array('param' => $param, 'msg' => $msg));
            exit;
        }
        $news_ids_arr = array_chunk($news_ids, 500);
        foreach ($news_ids_arr as $in_id) {
            $temp_ids     = M('temai_article')->where(array('news_id' => array('in', $in_id)))->getField('news_id', true);
            $new_ids_data = array_merge($new_ids_data, $temp_ids ? : array());
        }
        foreach ($new_ids_data as $id) {
            if (isset($data[$id]) && $data[$id]) {
                unset($data[$id]);
            }
        }
        if (count($data) == 0) {
            $msg = "分类ID:{$media_id}，已全部采集完成";
            $this->_addLogs('add_temai_article_success.log', $msg);
            exit;
        }
        $add_data = array_chunk($data, 500);
        $model    = M('temai_article');
        $model->startTrans();
        try {
            foreach ($add_data as $db_data) {
                $model->addAll($db_data);
            }
            $model->commit();
            $date = date('Y-m-d H:i');
            $msg  = "分类ID:{$media_id}，{$date}采集成功";
            $this->_addLogs('add_temai_article_success.log', $msg);
        } catch (\Exception $e) {
            $model->rollback();
            $this->_addLogs('db_add_temai_article_error.log', $e->getMessage());
        }
        $this->_addLogs("add_temai_article_msg_{$media_id}.log", date('Y-m-d H:i:s') . "-结束添加请求");
    }

    /**
     * 更新当天数据
     */
    public function updateDayArticle() {
        $media_id   = I('get.media_id', '', 'trim');
        $start_time = date("Y-m-d H") . ":00:00";
        $end_time   = date("Y-m-d") . " 00:00:00";
        $this->_updateArticle($media_id, $start_time, $end_time, 'day');
    }

    /**
     * 更新昨天前天数据
     */
    public function updateTwoArticle() {
        $media_id   = I('get.media_id', '', 'trim');
        $start_time = date("Y-m-d");
        $end_time   = date('Y-m-d', strtotime("-2 days"));
        $this->_updateArticle($media_id, $start_time, $end_time, 'two');
    }

    /**
     * 更新昨天前天数据
     */
    public function updateFourArticle() {
        $media_id   = I('get.media_id', '', 'trim');
        $start_time = date('Y-m-d', strtotime('-2 days'));
        $end_time   = date('Y-m-d', strtotime('-7 days'));
        $this->_updateArticle($media_id, $start_time, $end_time, 'four');
    }

    /**
     * 更新文章数据
     *
     * @param $media_id
     * @param $start_time
     * @param $end_time
     * @param $type
     */
    public function _updateArticle($media_id, $start_time, $end_time, $type) {
        $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", date('Y-m-d H:i:s') . "-开始更新{$start_time}-{$end_time}请求");
        if (!in_array($media_id, $this->cateArr)) {
            $msg = "文章类型不合法";
            $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", $msg);
            exit;
        }

        if (!$start_time || !$end_time) {
            $msg = "时间不能为空";
            $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", $msg);
            exit;
        }
        $get_num    = 0;
        $start_time = strtotime($start_time);
        $end_time   = strtotime($end_time);
        $temp_time  = $start_time;
        $data       = $add_data = $new_ids_data = $news_ids = array();
        while ($end_time < $temp_time) {
            $param_arr = $this->_getParam();
            $param     = array(
                'media_id'       => $media_id,
                'page_type'      => 1,
                'max_behot_time' => $start_time,
                'count'          => 10,
                'version'        => 2,
                'platform'       => 'wap',
                'as'             => $param_arr['as'],
                'cp'             => $param_arr['cp'],
            );
            $url       = "https://www.toutiao.com/pgc/ma/";
            if ($type == 'four') {
                $proxy = $this->_getProxy('update_four');
            } else {
                $proxy = $this->_getProxy('update');
            }
            $res = json_decode($this->_get($url, $param, $proxy), true);
            if ($res['message'] == 'success' && count($res['data']) > 0) {
                $get_num = 0;
                foreach ($res['data'] as $val) {
                    $news_id        = get_word($val['article_url'], "\?id=", '&');
                    $data[$news_id] = array(
                        'news_id'         => $news_id,
                        'article_id'      => $val['str_group_id'],
                        'title'           => $val['title'],
                        'comments_count'  => $val['comments_count'],
                        'go_detail_count' => $val['total_read_count'],
                        'behot_time'      => strtotime($val['datetime']),
                        'url'             => $val['article_url'],
                        'article_genre'   => get_word($val['article_url'], "content_type=", '&') == 1 ? 2 : 1,
                        'media_id'        => $media_id,
                        'create_user_id'  => get_word($val['article_url'], "create_user_id=", '&'),
                    );
                    $news_ids[]     = $news_id;
                }
                $temp_time  = $res['next']['max_behot_time'];
                $start_time = $temp_time;
                $msg        = date('m-d H:i') . '数据获取start_time：' . date('Y-m-d H:i', $start_time) . "，请求IP：{$proxy}，请求数据成功，分类ID:{$media_id}";
            } else {
                if ($get_num > 20) {
                    break;
                }
                $get_num++;
                $msg = date('m-d H:i') . '数据获取start_time：' . date('Y-m-d H:i', $start_time) . "，请求IP：{$proxy}，请求数据第{$get_num}次失败，分类ID:{$media_id}";
            }
            $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", $msg);
            sleep(10);
        }
        if ($get_num > 20) {
            $msg = "分类ID:{$media_id}，获取数据失败次数超过20次，请检查请求参数";
            $this->_addLogs("update_temai_article_{$type}_error.log", $msg);
            $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", date('Y-m-d H:i:s') . "-结束更新请求，请求超过20次，IP被封");
            exit;
        }
        $news_ids_arr = array_chunk($news_ids, 500);
        foreach ($news_ids_arr as $in_id) {
            $temp_ids     = M('temai_article')->where(array('news_id' => array('in', $in_id)))->getField('news_id', true);
            $new_ids_data = array_merge($new_ids_data, $temp_ids ? : array());
        }
        foreach ($new_ids_data as $id) {
            $update_data = array('go_detail_count' => $data[$id]['go_detail_count'], 'comments_count' => $data[$id]['comments_count']);
            M('temai_article')->where(array('news_id' => $id))->save($update_data);
            if (isset($data[$id]) && $data[$id]) {
                unset($data[$id]);
            }
        }
        if (count($data) == 0) {
            $msg = "分类ID:{$media_id}，已全部更新完成";
            $this->_addLogs('update_temai_article_success.log', $msg);
            $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", date('Y-m-d H:i:s') . "-结束更新请求,已全部添加，仅更新");
            exit;
        }
        $add_data = array_chunk($data, 500);
        $model    = M('temai_article');
        $model->startTrans();
        try {
            foreach ($add_data as $db_data) {
                $model->addAll($db_data);
            }
            $model->commit();
            $msg = "分类ID:{$media_id}，采集遗漏数据";
            $this->_addLogs('update_temai_article_success.log', $msg);
        } catch (\Exception $e) {
            $model->rollback();
            $this->_addLogs('db_update_temai_article_error.log', $e->getMessage());
        }
        $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", date('Y-m-d H:i:s') . "-结束更新请求，更新并添加数据完成");
    }


    /**
     * @param $url
     * @return mixed
     */
    protected function _getUrlInfo($url) {
        preg_match("/[0-9]+/", $url, $matches);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // 下面两行为不验证证书和 HOST，建议在此前判断 URL 是否是 HTTPS
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // $ret 返回跳转信息
        curl_exec($ch);
        // $info 以 array 形式返回跳转信息
        $info = curl_getinfo($ch);
        // 跳转后的 URL 信息
        $retURL = $info['url'];
        // 记得关闭curl
        curl_close($ch);
        $url_data = array(
            'news_id'        => get_word($retURL, "\?id=", '&'),
            'create_user_id' => get_word($retURL, 'create_user_id=', '&'),
            'article_id'     => $matches[0],
            'article_genre'  => get_word($retURL, 'content_type=', '&') == 1 ? 2 : 1,
            'url'            => $retURL,
        );
        return $url_data;
    }

    /**
     * @return array
     */
    protected function _getParam() {
        $i = time() - mt_rand(100, 300);
        $b = dechex($i);
        $t = strtoupper($b);
        $e = strtoupper(md5($i));
        $s = substr($e, 0, 5);
        $o = substr($e, -5);
        $a = $c = '';

        for ($n = 0; $n < 5; $n++) {
            $a .= substr($s, $n, 1) . substr($t, $n, 1);
        }
        for ($r = 0; $r < 5; $r++) {
            $c .= substr($t, $r + 3, 1) . substr($o, $r, 1);
        }
        return array('as' => "A1{$a}" . substr($t, -3), 'cp' => substr($t, 0, 3) . $c . 'E1');
    }

    /**
     * @param $url
     * @param array $params
     * @param null $proxy
     * @param string $cookie
     * @return bool|mixed
     */
    protected function _get($url, $params = array(), $proxy = null, $cookie = '') {
        $oCurl = curl_init();
        if ($proxy) {
            list($ip, $port) = explode(':', $proxy);
            curl_setopt($oCurl, CURLOPT_PROXY, $ip);
            curl_setopt($oCurl, CURLOPT_PROXYPORT, $port);
            curl_setopt($oCurl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
        }
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        $header = array(
            'Accept:application/json, text/javascript, */*; q=0.01',
            'Content-Type:application/x-www-form-urlencoded; charset=UTF-8',
            'Referer: https://www.toutiao.com/m6768458493/',
            'X-Requested-With:XMLHttpRequest',
        );
        if ($cookie) {
            $header[] = "cookie:{$cookie}";
        }
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        if (!empty($params)) {
            if (strpos($url, '?') !== false) {
                $url .= "&" . http_build_query($params);
            } else {
                $url .= "?" . http_build_query($params);
            }
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->http_time_out);
        $sContent = curl_exec($oCurl);
        $aStatus  = curl_getinfo($oCurl);
        curl_close($oCurl);
        dump($aStatus);exit;
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * 获取代理ip
     *
     * @param string $type
     * @return null
     */
    protected function _getProxy($type = 'add') {
        if ($type == 'add') {
            $proxy_data = array(
                "116.255.162.107:16816",
                "43.226.164.60:16816",
            );
        } else if ($type == 'update') {
            $proxy_data = array(
                "42.123.83.108:16816",
                "115.28.141.184:16816",
            );
        } else {
            $proxy_data = array(
                "122.114.234.157:16816"
            );
        }
        $num = date('i') % 2;
        $ip  = null;
        if (isset($proxy_data[$num])) {
            $ip = $proxy_data[$num];
        }
        return $ip;
    }

    /**
     * @param $filename
     * @param $data
     */
    protected function _addLogs($filename, $data) {
        $path = "/logs/" . date('Y-m-d');
        if (!is_dir($path)) {
            @mkdir($path);
        }
        $path = $path . "/" . $filename;
        if (is_array($data)) {
            file_put_contents($path, var_export($data, true) . "\r\n", FILE_APPEND);
        } else {
            file_put_contents($path, $data . "\r\n", FILE_APPEND);
        }
    }

    public function test() {
        $param_arr = $this->_getParam();
        $param     = array(
            'media_id'       => 6768458493,
            'page_type'      => 1,
            'max_behot_time' => 0,
            'count'          => 10,
            'version'        => 2,
            'platform'       => 'wap',
            'as'             => $param_arr['as'],
            'cp'             => $param_arr['cp'],
        );
        $url       = "https://www.toutiao.com/pgc/ma/";
        $ip        = $this->_getProxy();
        $res       = json_decode($this->_get($url, $param, $ip), true);
        var_dump($res);
    }

    public function getIp() {
        $param_arr = $this->_getParam();
        $param     = array(
            'media_id'       => 6768458493,
            'page_type'      => 1,
            'max_behot_time' => 0,
            'count'          => 10,
            'version'        => 2,
            'platform'       => 'wap',
            'as'             => $param_arr['as'],
            'cp'             => $param_arr['cp'],
        );
        $this->_addLogs('test.log', 2132132132133);
    }

}