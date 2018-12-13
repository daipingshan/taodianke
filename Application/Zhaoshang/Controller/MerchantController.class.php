<?php

namespace Admin\Controller;

/**
 * 商户管理
 */
class MerchantController extends CommonController {

    public function index() {

       $query = urldecode(I('get.query','','trim'));

        $where = array();
        if($query){
            $where['_string'] = "`username` like '%{$query}%' or `mobile` like '%{$query}%'";
        }

        $merchant = M('merchant');
        $count = $merchant->where($where)->count();
        $page = $this->pages($count, $this->reqnum);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = $merchant->where($where)->limit($limit)->order('id desc')->select();

        if ($list) {
            $c_ids = array();
            foreach ($list as &$v) {
                if (isset($v['cid']) && trim($v['cid'])) {
                    $c_ids[$v['cid']] = $v['cid'];
                }
            }
            unset($v);
            $c_info = array();
            if ($c_ids) {
                $c_info = M('category')->where(array('id' => array('in', array_keys($c_ids))))->getField('id,name,status', true);
            }
            foreach ($list as &$v) {
                $v['c_name'] = '';
                if (isset($v['cid']) && trim($v['cid'])) {
                    $v['c_name'] = ternary($c_info[$v['cid']]['name'], '');
                    $v['status'] = ternary($c_info[$v['cid']]['status'], '');
                }
            }
            unset($v);
        }

        $data = array(
            'query'=>$query,
            'pages' => $page->show(),
            'list' => $list,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 上线
     */
    public function shangxian() {
        $mid = I('get.cid', '', 'trim');
        if (!$mid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '修改的小区不存在！'));
        }

        $category = M('category');
        $where = array('id' => $mid);
        $merchant_count = $category->where($where)->count();
        if (!$merchant_count || $merchant_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '你修改的小区不存在！'));
        }
        $data=array(
            'status'=>1
        );
        $res = $category->where($where)->save($data);
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '上线小区失败！'));
        }

        $this->ajaxReturn(array('code' => 0, 'success' => '上线小区成功！'));
    }

    /**
     * 下线小区
     */
    public function xiaxian() {
        $mid = I('get.cid', '', 'trim');
        if (!$mid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '修改的小区不存在！'));
        }

        $category = M('category');
        $where = array('id' => $mid);
        $merchant_count = $category->where($where)->count();
        if (!$merchant_count || $merchant_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '你修改的小区不存在！'));
        }
        $data=array(
            'status'=>0
        );
        $res = $category->where($where)->save($data);
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '下线小区失败！'));
        }

        $this->ajaxReturn(array('code' => 0, 'success' => '下线小区成功！'));
    }

    /**
     * 添加商户
     */
    public function add() {
        if (IS_POST) {
            $merchant_data = I('post.merchant', array(), '');
            $this->handle_add_edit_data($merchant_data);
            $res = M('merchant')->add($merchant_data);
            if (!$res) {
                $this->redirect_message(U('Merchant/add'), array('error' => '商户添加失败！'));
            }
            // 开启环信账号
            $this->_open_ease_mob_username($res);
            $this->redirect_message(U('Merchant/index'), array('success' => '商户添加成功！'));
            die();
        }

        $f_cate_list = M('category')->where(array('fid' => 0))->field('id,name')->select();

        $data = array(
            'f_cate_list' => $f_cate_list,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 根据父id获取子分类
     */
    public function get_c_cate_by_fid() {
        $fid = I('post.fid', '', 'trim');
        if (!$fid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '父id为空！'));
        }
        $f_cate_list = M('category')->where(array('fid' => $fid))->field('id,name')->select();
        $this->ajaxReturn(array('code' => 0, 'data' => $f_cate_list));
    }

    /**
     * 获取区域内的代理商
     */
    public function get_agent_by_cid() {
        $cid = I('post.cid', '', 'trim');
        if (!$cid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '区域id为空！'));
        }
        $agent_list = M('agent')->where(array('city_id' => $cid))->field(array('id','realname'=>'name'))->select();
        $this->ajaxReturn(array('code' => 0, 'data' => $agent_list));
    }

    /**
     * 编辑商户
     */
    public function edit() {
        if (IS_POST) {
            $merchant_data = I('post.merchant', array(), '');
            $merchant_id = I('post.mid', array(), '');
            $this->handle_add_edit_data($merchant_data);
            $res = M('merchant')->where(array('id' => $merchant_id))->save($merchant_data);
            if (!$res) {
                $this->redirect_message(U('Merchant/edit', array('mid' => $merchant_id)), array('error' => '商户编辑失败！'));
            }
            $this->redirect_message(U('Merchant/index'), array('success' => '商户编辑成功！'));
            die();
        }

        $merchant_id = I('get.mid', array(), '');

        $merchant_info = M('merchant')->where(array('id' => $merchant_id))->find();

        if (!$merchant_info) {
            $this->redirect_message(U('Merchant/index'), array('error' => '商户不存在！'));
        }

        if (isset($merchant_info['pic']) && trim($merchant_info['pic'])) {
            $merchant_info['image'] = getImagePath($merchant_info['pic'],'oss');
        }


        if (isset($merchant_info['opentime']) && trim($merchant_info['opentime'])) {
            $merchant_info['opentime'] = date('Y-m-d', $merchant_info['opentime']);
        }


        $f_cate_list = M('category')->where(array('fid' => 0))->field('id,name')->select();

        $data = array(
            'f_cate_list' => $f_cate_list,
            'data' => $merchant_info
        );
        $this->assign($data);
        $this->display();
    }

    /**
     *  添加 编辑  数据监测
     */
    public function check_add_edit_data() {
        $merchant_data = I('post.merchant', array(), '');
        $merchant_id = I('post.mid', array(), '');

        if (!isset($merchant_data['username']) || !trim($merchant_data['username'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '商户登录名不能为空！'));
        }

        if (!$merchant_id && (!isset($merchant_data['password']) || !trim($merchant_data['password']))) {
            $this->ajaxReturn(array('code' => -1, 'error' => '商户登录密码不能为空！'));
        }

        if (!isset($merchant_data['realname']) || !trim($merchant_data['realname'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '姓名不能为空！'));
        }

        if (!isset($merchant_data['mobile']) || !trim($merchant_data['mobile'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '手机号码不能为空！'));
        }

        if (!checkMobile($merchant_data['mobile'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '手机号码格式错误！'));
        }

        if (!isset($merchant_data['province_id']) || !trim($merchant_data['province_id'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '请选择所属区域！'));
        }

        if (!isset($merchant_data['city_id']) || !trim($merchant_data['city_id'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '请选择所属区域！'));
        }

        if (!isset($merchant_data['area_id']) || !trim($merchant_data['area_id'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '请选择所属区域！'));
        }

        if (!isset($merchant_data['cid']) || !trim($merchant_data['cid'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '请选择所属区域！'));
        }

        /*if (!isset($merchant_data['agent_id']) || !trim($merchant_data['agent_id'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '请选择所属代理商！'));
        }*/

        if (!isset($merchant_data['address']) || !trim($merchant_data['address'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '商家地址不能为空！'));
        }

        if (!isset($merchant_data['longlat']) || !trim($merchant_data['longlat'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '商家坐标不能为空！'));
        }

        if (!isset($merchant_data['opentime']) || !trim($merchant_data['opentime'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '开业时间不能为空！'));
        }

        if (isset($merchant_data['business_status']) && trim($merchant_data['business_status']) == 'N') {
            if (!isset($merchant_data['reason']) || !trim($merchant_data['reason'])) {
                $this->ajaxReturn(array('code' => -1, 'error' => '停业原因不能为空！'));
            }
        }

        if (isset($merchant_data['stock_status']) && trim($merchant_data['stock_status']) == '0') {
            if (!$merchant_id && (!isset($merchant_data['stock_passwd']) || !trim($merchant_data['stock_passwd']))) {
                $this->ajaxReturn(array('code' => -1, 'error' => '库存锁密码不能为空！'));
            }
        }

        if (!isset($merchant_data['business_time']) || !trim($merchant_data['business_time'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '营业时间不能为空！'));
        }
        if (!isset($merchant_data['bank_name']) || !trim($merchant_data['bank_name'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '开户行不能为空！'));
        }
        if (!isset($merchant_data['bank_user']) || !trim($merchant_data['bank_user'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '开户名不能为空！'));
        }
        if (!isset($merchant_data['bank_no']) || !trim($merchant_data['bank_no'])) {
            $this->ajaxReturn(array('code' => -1, 'error' => '银行账户不能为空！'));
        }

        $merchant = M('merchant');

        // 登录用户名唯一
        $where = array(
            'username' => trim($merchant_data['username']),
        );
        if ($merchant_id) {
            $where['id'] = array('neq', $merchant_id);
        }
        $merchant_count = $merchant->where($where)->count();
        if ($merchant_count && $merchant_count > 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '该商户登录名已经被占用！'));
        }

        // 手机号码唯一
        $where = array(
            'mobile' => trim($merchant_data['mobile']),
        );
        if ($merchant_id) {
            $where['id'] = array('neq', $merchant_id);
        }
        $merchant_count = $merchant->where($where)->count();
        if ($merchant_count && $merchant_count > 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '该商户手机号已经注册过，不能重复注册！'));
        }

        // 小区商家 一对一
        $where = array(
            'cid' => trim($merchant_data['cid']),
        );
        if ($merchant_id) {
            $where['id'] = array('neq', $merchant_id);
        }
        $merchant_count = $merchant->where($where)->count();
        if ($merchant_count && $merchant_count > 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '该小区已经存在商家，请选择其他小区！'));
        }

        $this->ajaxReturn(array('code' => 0));
    }

    /**
     *  添加编辑数据处理
     * @param type $data
     */
    private function handle_add_edit_data(&$data) {
        if (isset($data['longlat']) && trim($data['longlat'])) {
            $longlat = explode(',', $data['longlat']);
            $data['lng'] = array_pop($longlat);
            $data['lat'] = array_pop($longlat);
        }
        unset($data['longlat']);

        if (isset($data['password'])) {
            if (trim($data['password'])) {
                $data['passwd'] = encryptPwd($data['password']);
            }
            unset($data['password']);
        }

        if (isset($data['stock_passwd'])) {
            if (trim($data['stock_passwd'])) {
                $data['stock_passwd'] = encryptPwd($data['stock_passwd']);
            } else {
                unset($data['stock_passwd']);
            }
        }

        if (isset($data['opentime'])) {
            if (trim($data['opentime'])) {
                $data['opentime'] = strtotime($data['opentime']);
            } else {
                unset($data['opentime']);
            }
        }
    }

    /**
     * 商户删除
     */
    public function delete() {
        $mid = I('get.mid', '', 'trim');
        if (!$mid) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除商户的id不能为空！'));
        }

        $merchant = M('merchant');
        $where = array('id' => $mid);
        $merchant_count = $merchant->where($where)->count();
        if (!$merchant_count || $merchant_count <= 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '你删除的商户不存在！'));
        }
        $where = array('mid' => $mid);
        $m_goods_count = M('goods')->where($where)->count();
        if ($m_goods_count && $m_goods_count > 0) {
            $this->ajaxReturn(array('code' => -1, 'error' => '你删除的商户下有商品，请先前去删除该商户下的所有商品，再删除商户！'));
        }


        $where = array('id' => $mid);
        $res = $merchant->where($where)->delete();
        if (!$res) {
            $this->ajaxReturn(array('code' => -1, 'error' => '删除商户失败！'));
        }

        $this->ajaxReturn(array('code' => 0, 'success' => '删除商户成功！'));
    }

    /**
     *  获取地图 定位经纬度
     */
    public function get_map() {
        $point = I('get.latlng', '39.915,116.404', 'trim');
        $long = $lat = 0;
        if ($point) {
            $pointArr = explode(',', $point);
            $long = array_pop($pointArr);
            $lat = array_pop($pointArr);
        }
        $data = array(
            'long' => trim($long) ? $long : 116.404,
            'lat' => trim($lat) ? $lat : 39.915
        );
        $this->assign('data', $data);
        $this->display();
    }
    /**
     *  开启环信  账号
     * @param type $mid
     * @return type
     */
    private function  _open_ease_mob_username($mid=0){
        if(!$mid){
            return array('code'=>-1,'error'=>'开启的商户id不能为空！');
        }

        $merchant_info = M('merchant')->where(array('id'=>$mid))->field(array('id,ease_mob_id,ease_mob_password'))->find();
        if(!$merchant_info){
            return array('code'=>-1,'error'=>'开启的商户不存在！');
        }

        if(isset($merchant_info['ease_mob_id']) && trim($merchant_info['ease_mob_id'])){
            return array('code'=>-1,'error'=>'已经开启环信账号，不要重复开启！');
        }

        if(isset($merchant_info['ease_mob_password']) && trim($merchant_info['ease_mob_password'])){
            return array('code'=>-1,'error'=>'已经开启环信账号，不要重复开启！');
        }

        $ease_mob = new \Common\Org\Easemob();
        $res = $ease_mob->register_merchant($mid);
        if(isset($res['error']) && trim($res['error'])){
            return array('code'=>-1,'error'=>'开启失败！'.$res['error']);
        }

        $data = array(
            'ease_mob_id'=>isset($res['username'])?$res['username']:'',
            'ease_mob_password'=>isset($res['password'])?$res['password']:'',
        );
        $res = M('merchant')->where(array('id'=>$mid))->save($data);
        if(!$res){
            return array('code'=>-1,'error'=>'开启失败！');
        }
        return array('code'=>0);
    }


    /**
     * 开启环信 账号密码
     */
    public function open_ease_mob_username(){
        $mid = I('get.mid','','trim');

        $res = $this->_open_ease_mob_username($mid);
        if(isset($res['error']) && trim($res['error'])){
            $this->ajaxReturn(array('code'=>-1,'error'=>$res['error']));
        }

        $this->ajaxReturn(array('code'=>0,'success'=>'开启成功！'));
    }

}
