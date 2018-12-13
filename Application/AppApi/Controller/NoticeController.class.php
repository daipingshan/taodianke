<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/5 0005
 * Time: 上午 9:54
 */

namespace AppApi\Controller;

/**
 * 公告控制器
 * Class NoticeController
 *
 * @package AppApi\Controller
 */
class NoticeController extends CommonController {

    /**
     * @var int
     */
    protected $limit = 6;

    /**
     * 首页公告
     */
    public function index() {
        $data = S('tdk_notice');
        if(!$data){
            $data = M('article')->field('cate_id,title')->where(array( 'cate_id' => 1, 'status' => 1 ))->order('id desc')->limit($this->limit)->select();
            S('tdk_notice',$data);
        }
        $this->outPut($data, 0);
    }
}