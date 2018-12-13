<?php

namespace Sign\Org;

class Pay {

    /**
     * 去支付
     * @param string $payType
     */
    public function doPay($data){
        include_once(__DIR__.'/wxPay/wechatPay.class.php');
        $obj = new \wechatPay();
        $res = $obj->doPay($data);
        return $res;
    }


    /**
     * 异步回掉
     */
    public function notifyCallBack() {
        include_once(__DIR__.'/wxPay/wechatPayNotify.class.php');
        $obj = new \wechatPayNotify();
        $obj->Handle(false);
        $code = $obj->GetReturn_code();
        $msg = $obj->GetReturn_msg();
        file_put_contents('wxpay.txt','code->'.$code."\n".'msg->'.$msg."\n",FILE_APPEND);
        if (strtolower(trim($code)) == 'success' && strtolower(trim($msg)) == 'ok') {
            $pars = $obj->getOrderData();
            $data = array(
                'out_trade_no'=> isset($pars['out_trade_no']) ? $pars['out_trade_no'] : '',
                'trade_no'    => isset($pars['transaction_id']) ? $pars['transaction_id'] : '',
                'total_fee'   => isset($pars['total_fee']) ? $pars['total_fee'] * 0.01 : '',
            );
            $return_data = array('status'=>true,'data'=>$data);
        }else{
            $return_data = array('status'=>false);
        }
        file_put_contents('wxpay.txt',var_export($return_data,true)."\n",FILE_APPEND);
        return $return_data;
    }
}

?>