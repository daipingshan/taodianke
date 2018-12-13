<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/29 0029
 * Time: 下午 4:35
 */

namespace AppApi\Controller;

use Common\Org\DaTaoKe;

/**
 * Class BrowserController
 * @package AppApi\Controller
 */
class BrowserController extends CommonController {

    /**
     * 判断用户是否登陆
     * @var bool
     */
    protected $checkUser = false;

    /**
     * 判断商品是否在大淘客上线
     */
    public function getItemStatus() {
        $item_id = I('get.item_id', '', 'trim');
        $DTK     = new DaTaoKe();
        $data    = $DTK->getItem($item_id);
        ob_clean();
        if (isset($data['result']) && $data['result']) {
            echo json_encode(array('code' => 1, 'msg' => '该商品已在大淘客上架'));
        } else {
            echo json_encode(array('code' => -1, 'msg' => '该商品未在大淘客上架'));
        }
        exit;
    }

    /**
     * 判断大淘客商品是否采集到淘店客
     */
    public function getDTKItem() {
        $item_id    = I('get.item_id', '', 'int');
        $info       = M('deal')->where(array('dataoke_url' => array('like', "%$item_id")))->find();
        $department = array(3 => '小刘联盟', 4 => '胜天联盟', 5 => '野狼联盟');
        ob_clean();
        if ($info) {
            if ($info['claim_status'] == 'Y') {
                echo json_encode(array('code' => 2, 'msg' => $department[$info['department_id']] . '：该商品已认领'));
            } else {
                echo json_encode(array('code' => 1, 'msg' => $department[$info['department_id']] . '：该商品未认领', 'dataoke_id' => $info['dataoke_id']));
            }
        } else {
            echo json_encode(array('code' => -1, 'msg' => '该商品不是淘店客数据'));
        }
        exit;
    }

    /**
     * json
     */
    public function getJson() {
        $call_back = I('get.callback', '', 'trim');
        echo $call_back . "(" . json_encode(array('code' => 1, 'msg' => '获取成功', 'result' => 'ok')) . ")";
    }
}