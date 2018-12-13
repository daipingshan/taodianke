<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2016/12/29
 * Time: 22:45
 */

/**
 * 获取分类图片地址
 */
function getImg($img){
    return C('web_url').'data/upload/mlink/'.$img;
}

/**
 * 获取分类图片地址
 */
function getAdImg($img){
    return C('web_url').'data/upload/'.$img;
}

/**
 * 获取用户头像
 */
function getHeadImg($uid){
    $avatar_dir = avatar_dir($uid);
    $avatar_file = $avatar_dir . md5($uid) . "_100.jpg";
    if (!is_file(C('web_url').'data/upload/avatar/' . $avatar_file)) {
        $avatar_file = "default_100.jpg";
    }

    return C('web_url').'data/upload/avatar/'.$avatar_file;
}

function avatar_dir($uid) {
    $uid = abs(intval($uid));
    $suid = sprintf("%09d", $uid);
    $dir1 = substr($suid, 0, 3);
    $dir2 = substr($suid, 3, 2);
    $dir3 = substr($suid, 5, 2);
    return $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
}