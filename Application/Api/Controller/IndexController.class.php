<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2016/12/29
 * Time: 22:05
 */

namespace Api\Controller;


class IndexController extends CommonController{

    /**
     * @var bool 是否验证uid
     */
    protected $checkUser = false;


    /**
     * 首頁
     */
    public function index(){
        echo '测试是否可以正常访问';
    }

    public function login(){
        $obj = new \Common\Org\Http();
        $url = 'https://temai.snssdk.com/article/feed/index?id=1563691&subscribe=5569547953&source_type=6&content_type=2&create_user_id=2872&classify=2&tt_group_id=6371391214315421954';
        $res = $obj->get($url);
        var_dump($res);
    }

    public function test(){
        $url = "https://s.click.taobao.com/t?e=m%3D2%26s%3DtBkygg%2FD%2FMdw4vFB6t2Z2ueEDrYVVa64XoO8tOebS%2BdRAdhuF14FMWkfyvqTwI3679%2FTFaMDK6Slsz15bND7ZLSrHdiTfhSKJa1TSeQwx53mlDIRZDQHTVgeO%2FR3F2op78FqzS29vh5nPjZ5WWqolKY9qrO0YyPvzX2E8xcqgB37XnfZRPS%2F5qz9Z7do4iStvA46MUyTb%2FKbw3Ska4r6zm2xQj18quAhraxSXcDxxL5U5JD0IQd6senG%2BV9M%2FptSOYQwrhPE0iw%3D";
        $title = '名媛小香风不规则打底裙';
        $content = "潮感吊带裙，前领口修饰脸型的V字剪裁，叠穿起来层次感满满。 细节消肩小性感，精致蕾丝花边~彰显高贵品气质";
        $image = "https://p3a.bytecdn.cn/large/144f000bd262e1e0f771?imageView2/2/w/640/h/640";
        $data = array('news_id'=>1,'image'=>$image,'title'=>$title,'content'=>$content,'url'=>$url);
        M('news_item')->add($data);

    }

}