<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/14 0014
 * Time: 下午 3:44
 */
$data = S('tdk_config');
if (!$data) {
    $content = M('config')->getFieldById(1, 'content');
    $data    = unserialize($content);
    S('tdk_config', $data);
}
return $data;