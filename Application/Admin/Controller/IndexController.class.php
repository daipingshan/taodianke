<?php

namespace Admin\Controller;

use Common\Org\TranslateSDK;

/**
 * Class IndexController
 *
 * @package DDHomeApi\Controller
 */
class IndexController extends CommonController {

    protected $checkUser = true;

    /**
     * @var bool 是否验证uid
     */

    public function index() {
        $model = M('illegal_article');
        $count = $model->count();
        $page  = $this->pages($count, 50);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list  = $model->limit($limit)->order('id desc')->select();
        $data  = array(
            'pages' => $page->show(),
            'list'  => $list,
        );
        $this->assign($data);
        $this->display();
    }

    public function addIllegalArticle() {
        $filename  = $_FILES['filename'];
        $file_path = ROOT_PATH . "/Uploads/" . date("Y-m-d") . $filename['name'];
        if ($filename) {
            @move_uploaded_file($filename['tmp_name'], $file_path);
        }
        if (file_exists($file_path)) {
            require_once(APP_PATH . "/Common/Org/PHPExcel.class.php");
            require_once(APP_PATH . "/Common/Org/PHPExcel/IOFactory.php");
            $reader   = \PHPExcel_IOFactory::createReader('Excel2007');
            $PHPExcel = $reader->load($file_path); // 载入excel文件
            $obj      = $PHPExcel->getSheet(0);// 读取第一個工作表
            $data     = $obj->toArray();
            unset($data[0]);
            $return_data = array();
            foreach ($data as $val) {
                $return_data[] = array('title' => $val[0], 'reason' => $val[1]);
            }
            $add_data = array_reverse($return_data);
            if ($add_data) {
                M('illegal_article')->addAll($return_data);
            }
        }
        $this->redirect('index');
    }

    public function Datetaok() {
        //header("content-type:text/html;charset=utf-8");
        $Taobaoke = new \Common\Org\Taobaoke();
        $url      = $Taobaoke->Getcookes();
        $url      = iconv("GB2312", "UTF-8", $url);
        //$url=str_replace(array("\r\n"), "", $url);
        //$reg = '/<figure>\s.*<h2 .* _price="(\d.*?\.00)" .* _href="(.*)" .*>(.*)<\/h2>\s.*<figcaption>(.*)<\/figcaption>\s.*<img alt-src=\'(.*)\'>\s.*<\/figure>/';
        $reg = '/<tr .*? class=\"tr_bg\">((.|\n)*?)<\/tr>/i';//2017-7-3 18:36:19-[0-9]+-[0-9]+\s[0-9]+:[0-9]+:[0-9]+)
        $reg = '/<tr style\=\"font-size:13px;\" class\=\"tr_bg\">\s\n.*<td class\=\"zs_shuzi\" style\=\"line-height:22px;\">(.*)\s\n.*<\/td>';
        $reg .= '\s\n.*<td height\=\"114\"><a href="(.*)" target=".*" title=".*"><img src="(.*)" width\=\"100\" height\=\"100\" \/><\/a>[\s|\n]*<\/td>'; //<a href="https://item.taobao.com/item.htm?id=548503086180" target="_blank" title="去淘宝查看"><img src="https://img.alicdn.com/imgextra/i3/759474002/TB2PAj2vR8lpuFjSspaXXXJKpXa_!!759474002.jpg_100x100.jpg" width="100" height="100" /></a>
        $reg .= '\s\n.*<td class\=\"zs_shuzi\" style=".*">￥(.*)<\/td>';//券后价
        $reg .= '\s\n.*<td>\s\n.*<div style=".*">[\s|\n]*((.|\n)*?)[\s|\n]*<\/div>\s*<\/td>';//佣金信息
        $reg .= '\s\n.*<td style=".*">[\s|\n]*((.|\n)*?)\s*<\/td>';//优惠券信息
        $reg .= '\s\n.*<td style=".*">[\s|\n]*((.|\n)*?)[\n|\n\s].*<\/td>';//导购文案
        $reg .= '\s\n.*<td style=".*">((.|\n)*?)<\/td>';//营销素材
        $reg .= '\s\n.*<td class\=\"zs_shuzi\" style=".*">(.*)\s*<\/td>';//开始时间
        $reg .= '\s\n.*<td style=".*">[\s|\n]*((.|\n)*?)<\/td>';//状态
        $reg .= '\s\n.*<td .* class\=\"zs_caozuo\">[\s|\n]*((.|\n)*?)<\/td>\s\n.*<\/tr>/i';
        //$reg.='\s\n.*<td .* class\=\"zs_caozuo\">\s\n.*<a id="(.*)" class\=\"edit_my_order\" title=".*">.*<\/a><a .* title=".*">.*<\/a><a id=".*" class\=\"zsbeizhu\" title=".*">.*<\/a>\s.*<\/td>\s\n.*<\/tr>/i';
        preg_match_all($reg, $url, $data);
        $arr = array('strtime' => $data[1], 'url' => $data[2], 'img' => $data[3], 'price' => $data[4], 'yongjin' => $data[5], 'quan' => $data[7], 'wenan' => $data[9], 'endtime' => $data[13], 'dateurl' => $data[13], 'id' => $data[13]);
        //dump($data);
        $this->outPut($arr, 0);
    }

    public function updatepass() {
        if (IS_POST) {
            $uid       = $this->uid;
            $password  = I('post.password', '', 'trim');
            $npassword = I('post.npassword', '', 'trim');
            $qpassword = I('post.qpassword', '', 'trim');
            if ($npassword != $qpassword) {
                $this->redirect_message(U('Index/updatepass'), array('error' => '两次输入密码不对！'));
            }

            $user      = M('tmuser')->where(array('id' => $uid))->find();
            $password  = encryptPwd($password);
            $lpassword = $user['password'];
            if ($password != $lpassword) {
                $this->redirect_message(U('Index/updatepass'), array('error' => '旧密码输入不对！'));
            }

            $map = array(
                'id' => $uid
            );
            $res = M('tmuser')->where($map)->save(array('password' => encryptPwd($npassword)));
            if ($res) {
                session_destroy();
                redirect(U('Public/login'));
            } else {
                $this->redirect_message(U('Index/updatepass'), array('error' => '密码修改是不！'));
            }

            redirect(U('Index/index'));
            die();

        }
        $this->display();
    }

    public function test() {
        $obj = new TranslateSDK();
        $str = "优质面料宽松落肩设计，配同色腰带，免皱好打理，抗起球不变形，多色可选";
        $res = $obj->translate($str, 'zh', 'en');
        dump($res);
        $res = $obj->translate($res['trans_result'][0]['dst'], 'en', 'zh');
        dump($res);
    }
}
