<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/5 0005
 * Time: 下午 2:25
 */

namespace AppApi\Controller;

/**
 * 投诉建议
 * Class SuggestController
 *
 * @package AppApi\Controller
 */
class SuggestController extends CommonController {

    /**
     * 投诉建议
     */
    public function add() {
        $info = I('post.info', '', 'trim');
        if (!$info) {
            $this->outPut(null, -1, null, '请输入建议内容！');
        }
        $data = array('fuid' => $this->uid, 'tuid' => 1, 'fname' => $this->_getUserField($this->uid), 'info' => $info, 'add_time' => time());
        $res  = M('msg')->add($data);
        if ($res) {
            $this->outPut(null, 0, null, '提交成功');
        } else {
            $this->outPut(null, -1, null, '提交失败！');
        }
    }
}