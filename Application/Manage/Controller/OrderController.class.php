<?php

namespace Manage\Controller;

use Manage\Controller\CommonController;

/**
 * 用户控制器
 * Class UserController
 * @package Manage\Controller
 */
class OrderController extends CommonController {
    public function index() {
        $formget = [
            'ad_position_name' => I('get.ad_position_name','','trim'),
            'kword' => I('get.kword','','trim')
        ];
        $map = [
            'id' => ['GT', 0]
        ];
        if ($formget['ad_position_name']) {
            $map['ad_position_name'] = $formget['ad_position_name'];
        }
        if (strlen($formget['kword']) > 0) {
            $map['goods_info'] = ['LIKE','%'.$formget['kword'].'%'];
        }
    	$model = M('order');
    	$count = $model->where($map)->count();
    	$pages = new \Think\Page($count,$this->reqnum);
    	$list = $model->where($map)->limit($pages->firstRow.','.$pages->listRows)->select();
    	$this->assign('list',$list);
    	$this->assign('pages', $pages->show());
        $this->assign('formget', $formget);
        $this->display();
    }

    public function import() {
        $this->display();
    }

    public function views() {
    	$filename = session('filename');
    	$upload = new \Think\Upload();
        $upload->maxSize   = 3145728 ;
        $upload->exts      = array('csv');
        $info   =   $upload->uploadOne($_FILES['orders']); 
        if(!$info) {
            $this->error($upload->getError());    
        } else {
            $filename = $upload->rootPath.$info['savepath'].$info['savename'];
            $list = $this->_getDataFromCsv($filename);
            session('orderList',$list);
	        $this->assign('list', $list);
	        $this->display();
        }
    }

    public function saveList() {
    	$fields = [
			'create_time',
			'goods_info', 
			'goods_id', 
			'goods_num', 
			'goods_price', 
			'order_status', 
			'payed_money', 
			'xiaoguo', 
			'jiesuan_money', 
			'yugu_money', 
			'jiesuan_time', 
			'offer_ratio', 
			'offer_money', 
			'deal_plat',
			'order_id', 
			'from_id', 
			'from_name', 
			'ad_position_id', 
			'ad_position_name',
			'sign'
		];
    	$list = session('orderList');
    	if (!$list) {
    		$this->error('信息不存在，请重新导入',U('index'));
    	}
    	unset($list[0]);
    	foreach ($list as $i => $row) {
    		foreach ($row as $k => $value) {
    			$data[$fields[$k]] = trim($value);
    		}
    		$order = M('order')->where(['order_id'=>$data['order_id']])->find();
    		if ($order) {
    			$res = M('order')->where(['order_id'=>$data['order_id']])->save($data);
    		} else {
    			$res = M('order')->add($data);
    		}
    		if (false === $res) {
				$error[] = $data['order_id'];
			}
    	}
    	if (empty($error)) {
    		$this->success('数据已导入',U('index'));
    	} else {
    		$this->error('部分数据失败:'.implode(',', $error));
    	}
    	session('orderList',null);
    }

    private function _getDataFromCsv($filename) {
    	foreach ($this->file_line_generator($filename) as $row) {
        	unset($row[3],$row[4],$row[8],$row[9],$row[10],$row[18],$row[19],$row[20],$row[22],$row[24]);
        	$list[] = array_values($row);
        }
        return $list;
    }

    private function file_line_generator($file) {
        if (!$fh = fopen($file, 'r')) {
            return;
        }
        while (false !== ($line = fgetcsv($fh))) {
            yield $line;
        }
        fclose($fh);
     }

}

