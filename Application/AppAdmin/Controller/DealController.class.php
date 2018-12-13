<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/18 0018
 * Time: 下午 4:47
 */

namespace AppAdmin\Controller;

/**
 * CRM 商品管理
 * Class DealController
 *
 * @package AppAdmin\Controller
 */
class DealController extends CommonController {

    private $get_shop_name_url= 'https://acs.m.taobao.com/h5/mtop.taobao.detail.getdetail/6.0/?data=ppp';

    /**
     * 当前登录用户自己未认领的商品
     */
    public function adminDeal() {
        $model    = M('deal');
        $admin_id = session('auth_user');
        $where    = array( 'admin_id' => $admin_id, 'claim_status' => 'N' );
        $order    = 'id asc';
        $count    = $model->where($where)->count();
        $page     = $this->pages($count, $this->limit);
        $data     = $model->where($where)->order($order)->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign(array( 'data' => $data, 'page' => $page->show() ));
        $this->display();
    }

    /**
     * 搜索商品
     */
    public function searchDeal() {
        $tao_bao_id        = I('get.tao_bao_id', '', 'trim');
        $da_tao_ke_id      = I('get.da_tao_ke_id', '', 'trim');
        $update_tao_bao_id = I('get.update_tao_bao_id', '', 'trim');
        $department_id     = session('department_id');
        if ($tao_bao_id || $da_tao_ke_id || $update_tao_bao_id) {
            if ($department_id) {
                $where = array( 'department_id' => $department_id );
                if ($tao_bao_id) {
                    $where['taobao_item_id|taobao_url'] = $tao_bao_id;
                    $where['claim_status']              = 'N';
                }
                if ($da_tao_ke_id) {
                    $where['dataoke_id|dataoke_url'] = $da_tao_ke_id;
                    $where['claim_status']           = 'N';
                }
                if ($update_tao_bao_id) {
                    $where['id'] = $update_tao_bao_id;
                }
                $info = M('deal')->where($where)->order('add_time asc')->find();
                if ($info) {
                    $info['merchant_name'] = '';
                    if ($info['merchant_id'] > 0) {
                        $info['merchant_name'] = M('merchant')->getFieldById($info['merchant_id'], 'merchant_name');
                    } elseif (empty($info['merchant_id'])) {
                        // 获取商家名称
                        $TaoBao = new \Common\Org\TaoBaoApi();
                        $res    = $TaoBao->getTaoBaoItemInfo($info['taobao_item_id']);

                        if (isset($res['data']['nick'])) {
                            $merchant_res = M('merchant')->where(array('shop_name' => $res['data']['nick'],'admin_id' => session('auth_user')))->find();
                            if ($merchant_res) {
                                $info['merchant_id']   = $merchant_res['id'];
                                $info['merchant_name'] = $merchant_res['merchant_name'];
                            } else {
                                $info['merchant_seller_nick'] = $res['data']['nick'];
                            }
                        }
                    }
                    $this->assign('deal_info', $info);
                } else {
                    $this->assign('error', '商品信息不存在或已被其他人认领！');
                }
            } else {
                $this->assign('error', '您的账号没有认领权限，无法认领！');
            }
        }
        $this->display();
    }

    /**
     * 选择商家
     */
    public function selectMerchant() {
        $keyword = I('get.keyword', '', 'trim');
        $where   = array( 'admin_id' => session('auth_user') );
        if ($keyword) {
            $where['mobile|merchant_name|shop_name|weixin|qq|wangwang'] = array( 'like', "%{$keyword}%" );
        }
        $count = M('merchant')->where($where)->count('id');
        $page  = $this->pages($count, 10);
        $data  = M('merchant')->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign(array( 'page' => $page->show(), 'data' => $data ));
        $html = $this->fetch();
        $this->ajaxReturn(array( 'html' => $html ));
    }

    /**
     * 认领商品
     */
    public function claimDeal() {
        if (!IS_AJAX || !IS_POST) {
            $this->error('非法请求！');
        }
        $model            = M('deal');
        $deal_id          = I('post.deal_id', 0, 'int');
        $claim_time       = time();
        $admin_id         = session('auth_user');
        $department_id    = session('department_id');
        $merchant_id      = I('post.merchant_id', 0, 'int');
        $service_fee      = I('post.service_fee', '', 'trim');
        $service_fee_type = I('post.service_fee_type', '', 'trim');
        $button_type      = I('post.button_type', '', 'trim');
        if (!$merchant_id && $button_type == 'add') {
            $this->error('请选择商家后再认领商品！');
        }
        if (!$service_fee_type) {
            $this->error('请选择服务费类型！');
        }
        if (!$service_fee) {
            $this->error('请输入服务费单价或服务费率！');
        }
        $info = $model->find($deal_id);
        if (!$info) {
            $this->error('商品信息不存在，无法认领');
        }
        if ($info['claim_status'] == "Y" && $button_type == 'add') {
            $this->error('商品已被认领，不能重复认领！');
        }
        $data['id'] = $deal_id;
        if ($button_type == 'add') {
            $data['claim_time']    = $claim_time;
            $data['department_id'] = $department_id;
            $data['merchant_id']   = $merchant_id;
            $data['admin_id']      = $admin_id;
            $data['claim_status']  = "Y";
        }
        $data['service_fee']      = $service_fee;
        $data['service_fee_type'] = $service_fee_type;
        $res                      = $model->save($data);
        if ($res !== false) {
            if ($info['status'] == 'finished') {
                $url = U('Deal/finishList');
            } else {
                $url = U('Deal/dealList', array( 'status' => $info['status'] ));
            }
            $this->success('认领成功', $url);
        } else {
            $this->success('认领失败，请咨询管理员！');
        }
    }

    /**
     * 商品列表
     */
    public function dealList() {
        $model                = M('deal');
        $admin_id             = session('auth_user');
        $department_id        = session('department_id');
        $position_level       = session('position_level');
        $status               = I('get.status', '', 'trim');
        $tao_bao_id           = I('get.tao_bao_id', '', 'trim');
        $keyword              = I('get.keyword', '', 'trim');
        $claim_status         = I('get.claim_status', '', 'trim');
        $search_department_id = I('get.department_id', 0, 'int');
        $search_admin_id      = I('get.admin_id', 0, 'int');
        $ids                  = I('get.ids', '', 'trim');
        $where                = $status_num = $map = $admin_user = array();
        $order                = 'add_time desc,id desc';
        $department           = $this->_getDepartment();
        if ($position_level == 'middle') {
            $admin_user           = M('admin')->where(array( 'department_id' => $department_id ))->getField('id,full_name as name');
            $map['department_id'] = $department_id;
        } elseif ($position_level == 'basic') {
            $map['admin_id'] = $admin_id;
        }
        if ($status) {
            $where['status'] = $status;
            if ($status == 'finished') {
                $order = "add_time asc,id desc";
            }
        }
        if ($search_department_id) {
            $where['department_id'] = $search_department_id;
        }
        if ($search_admin_id) {
            $where['admin_id'] = $search_admin_id;
        }
        if ($claim_status) {
            $where['claim_status'] = $claim_status;
            if ($claim_status == "N") {
                $order = "add_time asc,id asc";
            }
        }
        if ($tao_bao_id) {
            $where['taobao_item_id'] = $tao_bao_id;
        }
        if ($keyword) {
            $where['copy_writer|remark'] = array( 'like', "%{$keyword}%" );
        }
        if ($ids) {
            $where['id'] = array( 'in', $ids );
        }
        $count = $model->where($map)->where($where)->count();
        $page  = $this->pages($count, $this->limit);
        $data  = $model->where($map)->where($where)->order($order)->limit($page->firstRow.','.$page->listRows)->select();

        // 关联用户和商家
        $user = $user_ids = $merchant = $merchant_ids = array();
        foreach ($data as $k => $v) {
            $user_ids[$v['admin_id']]        = $v['admin_id'];
            $merchant_ids[$v['merchant_id']] = $v['merchant_id'];
        }
        // 关联用户和商家
        if ($user_ids && $merchant_ids) {
            $user     = M('admin')->where(array( 'id' => array( 'in', array_keys($user_ids) ) ))->field('id, full_name')->index('id')->select();
            $merchant = M('merchant')->where(array( 'id' => array( 'in', array_keys($merchant_ids) ) ))->field('id,merchant_name,mobile,qq,weixin,wangwang')->index('id')->select();
        }
        $status_array = array( 'ing', 'finished', 'reject', 'bad', 'apply_settle', 'pending_paid', 'confirmed_paid' );
        foreach ($status_array as $v) {
            $where['status'] = $v;
            $status_num[$v]  = $model->where($map)->where($where)->count();
        }
        $this->assign(array( 'status_num' => $status_num, 'data' => $data, 'page' => $page->show(), 'department' => $department, 'admin_user' => $admin_user, 'status' => $status, 'user' => $user, 'merchant' => $merchant ));
        $this->display();
    }

    /**
     * 申请结算列表
     */
    public function finishList() {
        $model         = M('deal');
        $tao_bao_id    = I('get.tao_bao_id', '', 'trim');
        $keyword       = I('get.keyword', '', 'trim');
        $merchant_info = I('get.merchant_info', '', 'trim');
        $admin_id      = session('auth_user');
        $where         = array( 'd.status' => 'finished', 'd.claim_status' => 'Y', 'd.admin_id' => $admin_id );
        if ($tao_bao_id) {
            $where['d.taobao_item_id'] = $tao_bao_id;
        }
        if ($keyword) {
            $where['d.copy_writer|d.remark'] = array( 'like', "%{$keyword}%" );
        }
        if ($merchant_info) {
            $where['m.mobile|m.qq|m.weixin|m.wangwang'] = $merchant_info;
        }
        $data = $model->alias('d')->field('d.*,m.merchant_name,m.mobile,m.qq,m.weixin,m.wangwang')->where($where)->join('left join ytt_merchant m ON d.merchant_id = m.id')->order('merchant_id desc,add_time asc')->select();
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 申请结算信息
     */
    public function applySettle() {
        $deal_ids = I('param.deal_ids', '', 'trim');
        if (!$deal_ids) {
            $this->error('请勾选相关商品');
        }
        $model = M('deal');
        if (is_array($deal_ids)) {
            $where = array( 'id' => array( 'in', $deal_ids ), 'status' => 'finished' );
            $data  = $model->where($where)->select();
            $this->assign(array( 'data' => $data, 'deal_ids' => implode(',', $deal_ids) ));
        } else {
            $where = array( 'id' => $deal_ids );
            $data  = $model->where($where)->find();
            $this->assign(array( 'deal_info' => $data, 'deal_ids' => $deal_ids ));
        }
        $this->display();
    }

    /**
     * 客户经理申请结算操作
     */
    public function doApplySettle() {
        if (!IS_AJAX || !IS_POST) {
            $this->error('非法请求！');
        }
        $model                     = M('deal');
        $deal_ids                  = I('post.deal_ids', '', 'trim');
        $data['order_num']         = I('post.order_num', 0, 'int');
        $data['order_total_money'] = I('post.order_total_money', '', 'trim');
        $data['coupon_img_url']    = I('post.coupon_img_url', '', 'trim');
        $data['real_settle_money'] = I('post.real_settle_money', '', 'trim');
        $data['remark']            = I('post.remark', '', 'trim');
        $data['status']            = "apply_settle";
        $data['settle_time']       = time();
        $button_type               = I('post.button_type', '', 'trim');
        $admin_id                  = session('auth_user');
        $list                      = $model->where(array( 'admin_id' => $admin_id, 'id' => array( 'in', $deal_ids ) ))->select();
        if (!$deal_ids || !$list) {
            $this->error('商品信息不存在！');
        }
        if (!strpos($deal_ids, ',')) {
            $info = $list[0];
            if ($info['service_fee_type'] == 'ratio') {
                $data['order_num'] = (int)$data['order_total_money'] / $info['coupon_after_price'];
                $money             = $data['order_total_money'] * ($info['service_fee'] / 100);
            } else {
                if (!$data['order_num'] || $data['order_num'] < 0) {
                    $this->error('订单支付数量不能为空或不能小于0！');
                }
                $money = $data['order_num'] * $info['service_fee'];
            }
        }
        if (!$data['order_total_money'] || $data['order_total_money'] < 0) {
            $this->error('订单总金额不能为空或不能小于0！');
        }
        if (!$data['real_settle_money'] || $data['real_settle_money'] < 0) {
            $this->error('订单结算金额不能为空或不能小于0！');
        }
        if (!$data['coupon_img_url']) {
            $this->error('请上传领券截图！');
        }
        /* $min_money = $money * 0.9;
         if ($data['real_settle_money'] < $min_money) {
             $this->error('结算金额与预估结算金额差距超过10%，请检查！');
         }*/
        if ($data['remark'] && $button_type == 'add') {
            $data['remark'] = "申请结算备注：{$data['remark']}";
        }
        $model->startTrans();
        try {
            if (strpos($deal_ids, ',')) {
                $count             = count($list);
                $temp_settle_money = round($data['real_settle_money'] / $count, 2);
                $temp_total_money  = round($data['order_total_money'] / $count, 2);
                foreach ($list as $val) {
                    if ($val['service_fee_type'] == 'ratio') {
                        $temp_order_num = ceil($temp_settle_money * ($val['service_fee'] / 100));
                    } else {
                        $temp_order_num = ceil($temp_settle_money / $val['service_fee']);
                    }
                    $data['order_total_money'] = $temp_total_money;
                    $data['real_settle_money'] = $temp_settle_money;
                    $data['order_num']         = $temp_order_num;
                    $model->where(array( 'id' => $val['id'] ))->save($data);
                }
            } else {
                $model->where(array( 'id' => $deal_ids ))->save($data);
            }
            $model->commit();
            $this->success('申请结算成功，在相关订单全部申请结算完毕后，请进行确认支付申请', U('Deal/confirmDealList'));
        } catch (\Exception $e) {
            $model->rollback();
            $this->error('申请结算失败，请咨询管理员！'.$e->getMessage());
        }
    }

    /**
     * 客户经理设置订单赖账
     */
    public function doStatusSetBad() {
        if (!IS_AJAX || !IS_POST) {
            $this->error('非法请求！');
        }
        $model     = M('deal');
        $id        = I('post.id', 0, 'int');
        $remark    = I('post.remark', '', 'trim');
        $admin_id  = session('auth_user');
        $deal_info = $model->where(array( 'admin_id' => $admin_id ))->find($id);
        if (!$deal_info) {
            $this->error('商品信息不存在');
        }
        if ($deal_info['status'] != 'finished') {
            $this->error('商品状态不正确，当商品处于结束状态才可以设置异常。');
        }
        if (!$remark) {
            $this->error('请输入备注信息');
        } else {
            $remark = "异常备注：{$remark}";
        }
        $res = $model->where(array( 'id' => $id ))->save(array( 'status' => 'bad', 'settle_time' => time(), 'remark' => $remark ));
        if ($res !== false) {
            $this->success('设置成功。', U('Deal/dealList', array( 'status' => 'bad' )));
        } else {
            $this->error('设置失败，请咨询管理员！');
        }
    }

    /**
     * 取消异常,商品状态恢复原状
     */
    public function cancelUnnormal(){
        if (!IS_AJAX || !IS_POST) {
            $this->error('非法请求！');
        }
        $model     = M('deal');
        $id        = I('post.id', 0, 'int');
        $admin_id  = session('auth_user');
        $deal_info = $model->where(array('admin_id' => $admin_id))->find($id);
        if (!$deal_info) {
            $this->error('商品信息不存在');
        }
        if ($deal_info['status'] != 'bad') {
            $this->error('商品状态不正确。');
        }
        $res = $model->where(array('id' => $id))->save(array('status' => 'finished', 'settle_time' => '0', 'remark' => ''));
        if ($res !== false) {
            $this->success('设置成功。', U('Deal/dealList', array('status' => 'finished')));
        } else {
            $this->error('设置失败，请咨询管理员！');
        }
    }

    /**
     * 待确认支付列表
     */
    public function confirmDealList() {
        $model         = M('deal');
        $tao_bao_id    = I('get.tao_bao_id', '', 'trim');
        $keyword       = I('get.keyword', '', 'trim');
        $merchant_info = I('get.merchant_info', '', 'trim');
        $admin_id      = session('auth_user');
        $where         = array( 'd.status' => 'apply_settle', 'd.claim_status' => 'Y', 'd.admin_id' => $admin_id );
        if ($tao_bao_id) {
            $where['d.taobao_item_id'] = $tao_bao_id;
        }
        if ($keyword) {
            $where['d.copy_writer|d.remark'] = array( 'like', "%{$keyword}%" );
        }
        if ($merchant_info) {
            $where['m.mobile|m.qq|m.weixin|m.wangwang'] = $merchant_info;
        }
        $data = $model->alias('d')->field('d.*,m.merchant_name,m.mobile,m.qq,m.weixin,m.wangwang')->where($where)->join('left join ytt_merchant m ON d.merchant_id = m.id')->order('merchant_id desc,add_time asc')->select();
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 批量提单页面
     */
    public function settlePaid() {
        $deal_ids = I('param.deal_ids', '', 'trim');
        if (!$deal_ids) {
            $this->error('请勾选相关商品');
        }
        $model     = M('deal');
        $where     = array( 'id' => array( 'in', $deal_ids ), 'status' => 'apply_settle' );
        $count     = $model->where($where)->count();
        $sum_money = $model->where($where)->sum('real_settle_money');
        $field     = "id,img_url,coupon_img_url,real_settle_money,service_fee_type,service_fee";
        $data      = $model->where($where)->field($field)->select();
        $this->assign(array( 'data' => $data, 'count' => $count, 'sum_money' => $sum_money, 'deal_ids' => is_array($deal_ids) ? implode(',', $deal_ids) : $deal_ids ));
        $this->display();
    }

    /**
     * 执行批量提单操作
     */
    public function doSettlePaid() {
        if (!IS_AJAX || !IS_POST) {
            $this->error('非法请求！');
        }
        $data['department_id']        = session('department_id');
        $data['admin_id']             = session('auth_user');
        $data['paid_total_money']     = I('post.paid_total_money', '', 'trim');
        $data['merchant_payment_day'] = I('post.merchant_payment_day', '', 'trim');
        $data['paid_img_url']         = I('post.paid_img_url', '', 'trim');
        $data['deal_ids']             = I('post.deal_ids', '', 'trim');
        $data['status']               = 'pending_paid';
        $data['remark']               = I('post.remark', '', 'trim');
        $data['apply_settle_time']    = time();
        if (!$data['deal_ids']) {
            $this->error('请求参数不合法！');
        }
        if (!$data['paid_total_money']) {
            $this->error('请输入实际打款金额！');
        }
        if (!$data['paid_img_url']) {
            $this->error('请上传打款截图后在进行批量结算！');
        }
        if (!$data['merchant_payment_day']) {
            $this->error('请选择商家打款时间!');
        } else {
            $data['merchant_payment_day'] = strtotime($data['merchant_payment_day']);
        }
        $sum_money = M('deal')->where(array( 'id' => array( 'in', $data['deal_ids'] ), 'status' => 'apply_settle' ))->sum('real_settle_money');
        /*  $min_money = $sum_money * 0.9;
          if ($data['paid_total_money'] < $min_money) {
              $this->error('实际打款金额与预估打款金额差距超过10%，请检查！');
          }*/
        $data['estimate_paid_total_money'] = $sum_money;
        $model                             = M('deal_settle');
        $model->startTrans();
        $add_res  = $model->add($data);
        $save_res = M('deal')->where(array( 'id' => array( 'in', $data['deal_ids'] ) ))->save(array( 'status' => 'pending_paid', 'paid_time' => time() ));
        if ($add_res && $save_res) {
            $model->commit();
            $this->success('申请成功，请等待财务审核并结算。', U('Deal/dealSettle'));
        } else {
            $model->rollback();
            $this->error('申请失败，请咨询管理员！');
        }
    }

    /**
     * 财务查看提单列表
     */
    public function dealSettleList() {
        $model     = M('deal_settle');
        $status    = I('get.status', '', 'trim');
        $full_name = I('get.full_name', '', 'trim');
        $time      = I('get.time', '', 'trim,urldecode');

        $status_where = $name_where = $where = $status_num = array();
        $order  = 's.status desc,s.id asc';
        if ($status) {
            if ($status == 'pending_paid') {
                $order = 's.apply_settle_time asc';
            } else {
                $order = 's.confirmed_settle_time desc';
            }
            $where['s.status'] = $status;
        }
        if ($full_name) {
            $name_where['a.full_name'] = urldecode($full_name);
        }

        // if(empty($time)){
        //     $time = date('m/01/Y').'-'.date('m/d/Y');
        // }

        if ($time) {
            list($start_time, $end_time) = explode('-', $time);
            $status_where['s.merchant_payment_day'] = $where['s.merchant_payment_day'] = array('between', array(strtotime($start_time), strtotime("$end_time +1 days") - 1));
        }

        $count = $model->alias('s')->where($where)->where($name_where)
            ->join('left join ytt_admin a ON a.id = s.admin_id')
            ->field('s.*,a.full_name,d.department_name')->count();
        $page  = $this->pages($count, $this->limit);
        $data  = $model->where($where)->alias('s')->where($name_where)
            ->join('left join ytt_admin a ON a.id = s.admin_id')
            ->join('left join ytt_department d ON d.id = s.department_id')
            ->field('s.*,a.full_name,d.department_name')
            ->order($order)
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        $status_array = array( 'pending_paid', 'confirmed_paid' );
        foreach ($status_array as $v) {
            $status_num[$v] = $model->alias('s')
                ->where(array('s.status' => $v))->where($status_where)
                ->where($name_where)
                ->join('left join ytt_admin a ON a.id = s.admin_id')
                ->field('s.*,a.full_name')->count();
        }
        $this->assign('data', $data);
        $this->assign('time', $time);
        $this->assign('status_num', $status_num);
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 财务更改实际打款金额
     */
    public function updateSettleMoney() {
        if (!IS_AJAX || !IS_POST) {
            $this->error('非法请求！');
        }
        $id                   = I('post.id', 0, 'int');
        $money                = I('post.money', '', 'trim');
        $merchant_payment_day = I('post.merchant_payment_day', '', 'trim');
        $remark               = I('post.remark', '', 'trim');
        $info                 = M('deal_settle')->find($id);
        if (!$id || !$info) {
            $this->error('结算信息不存在，请联系管理员!');
        }
        if (!$money) {
            $this->error('请输入商家打款金额!');
        }
        if (!$merchant_payment_day) {
            $this->error('清选择商家打款时间!');
        }
        /* $min_money = $info['estimate_paid_total_money'] * 0.9;
         if ($money < $min_money) {
             $this->error('实际打款金额与预估打款金额差距超过10%，请检查！');
         }*/
        $data = array( 'paid_total_money' => $money, 'merchant_payment_day' => strtotime($merchant_payment_day), 'confirmed_settle_time' => time(), 'status' => 'confirmed_paid' );
        if ($remark) {
            $data['remark'] = $info['remark'] ? $info['remark']."<br />财务修改原因：".$remark : "财务修改原因：".$remark;
        }
        $model = M('deal_settle');
        $model->startTrans();
        try {
            M('deal_settle')->where(array( 'id' => $id ))->save($data);
            $deal_data = array( 'confirmed_settle_time' => time(), 'status' => 'confirmed_paid' );
            M('deal')->where(array( 'id' => array( 'in', $info['deal_ids'] ) ))->save($deal_data);
            $model->commit();
            $this->success('审核成功', U('Deal/dealSettleList'));
        } catch (\Exception $e) {
            $model->rollback();
            $this->error('审核失败，请联系管理员！');
        }
    }

    /**
     * 财务打回填写错误的结算信息
     */
    public function recoverPaid() {
        $id   = I('get.id', 0, 'int');
        $info = M('deal_settle')->find($id);
        if (!$id || !$info) {
            $this->error('结算信息不存在！');
        }
        $model = M();
        $model->startTrans();
        try {
            $save_data = array( 'status' => 'apply_settle', 'paid_time' => 0 );
            M('deal')->where(array( 'id' => array( 'in', $info['deal_ids'] ) ))->save($save_data);
            M('deal_settle')->delete($id);
            $model->commit();
            $this->success('打回成功');
        } catch (\Exception $e) {
            $model->rollback();
            $this->error('打回失败！');
        }
    }

    /**
     * 用户提现列表
     */
    public function dealSettle() {
        $model           = M('deal_settle');
        $status          = I('get.status', '', 'trim');
        $search_admin_id = I('get.admin_id', 0, 'int');
        $admin_id        = session('auth_user');
        $where           = array();

        $position_level = session('position_level');
        if ($position_level == 'middle') {
            $department_id            = session('department_id');
            $where['s.department_id'] = $department_id;
            $admin_user               = M('admin')->where(array( 'department_id' => $department_id ))->getField('id,full_name as name');
            $this->assign('admin_user', $admin_user);
        } else if ($position_level == 'basic') {
            $where['s.admin_id'] = $admin_id;
        } else {
            $this->assign('department', $this->_getDepartment());
        }

        if ($search_admin_id > 0) {
            $where['s.admin_id'] = $search_admin_id;
        }

        if ($status) {
            $where['s.status'] = $status;
        }
        $count = $model->where($where)->alias('s')->count();
        $page  = $this->pages($count, $this->limit);
        $data  = $model->where($where)->alias('s')
            ->join('left join ytt_admin a ON a.id = s.admin_id')
            ->join('left join ytt_department d ON d.id = s.department_id')
            ->field('s.*,a.full_name,d.department_name')
            ->order('id desc')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 上传图片
     * @DateTime 2017-11-23
     * @param    string     $value [description]
     */
    public function uploadImg(){
        $base64_data = I('post.base64_data', '', 'trim');

        $temp = explode(',', $base64_data);
        $time = time();
        file_put_contents(C('uploadPath') . '/uploadImg_' . $time . '.png', base64_decode(str_ireplace(' ', '+', $temp[1])));
        $result = $this->saveFile(C('uploadPath') . '/uploadImg_' . $time . '.png', $time . '.png');

        $fileName = ROOT_PATH . C('uploadPath') . '/uploadImg_' . $time . '.png';
        if (file_exists($fileName)) {
            @unlink($fileName);
        }

        $this->ajaxReturn($result);
    }

}