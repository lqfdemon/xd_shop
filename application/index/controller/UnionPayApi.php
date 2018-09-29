<?php
namespace app\index\controller;
use think\Controller;
use think\Loader;
use think\Log;

Loader::import('unionpay.SDKConfig');
Loader::import('unionpay.acp_service');
header ( 'Content-type:text/html;charset=utf-8' );


class UnionPayApi extends Controller{
    public function start_pay($orderid,$amount){
        $config=new \SDKConfig();
        $acp=new \AcpService();

        Log::record($acp);
        Log::record($config);
        $params = array(   
            //以下信息非特殊情况不需要改动
            'version' => $config->version,             //版本号
            'encoding' => 'utf-8',                //编码方式
            'txnType' => '01',                    //交易类型
            'txnSubType' => '01',                 //交易子类
            'bizType' => '000201',                //业务类型
            'frontUrl' =>  $config->frontUrl,  //前台通知地址
            'backUrl' => $config->backUrl,    //后台通知地址
            'signMethod' =>$config->signMethod,  //签名方法
            'channelType' => '08', //渠道类型，07-PC，08-手机
            'accessType' => '0', //接入类型
            'currencyCode' => '156',//交易币种，境内商户固定156        
            //TODO 以下信息需要填写
            'merId' => $config->merId,
            //商户代码，请改自己的测试商户号
            'orderId' =>$orderid,  
            //商户订单号，8-32位数字字母，不能含“-”或“_”
            'txnTime' => date('YmdHis'),    
            //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间
            'txnAmt' =>$amount*100, //交易金额，单位分，
        );
        Log::record($params);
        
        $acp->sign ( $params ); // 签名
        $url = $config->frontTransUrl;
        $html =$acp->createAutoFormHtml($params,$url);
        Log::record($html);
        echo $html;
        exit;
        /*
        Log::record("@@@ result");
        Log::record($result_arr);
        if(count($result_arr)<=0) { //没收到200应答的情况
            printResult ($url, $params, "" );
            return;
        }
        
        if (!$acp->validate ($result_arr) ){
            return;
        }
        if ($result_arr["respCode"] == "00"){
            //成功
        $return['status']=1;
        $return['msg']="success";   
        $data['tn']=$result_arr["tn"];
        $return['data']['tn']= $data['tn'];
        $this->ajaxReturn($return, 'JSON');
        //后续请将此tn传给手机开发，他们用此tn调起控件后完成支付;
        } else {
        }
        */
        
    }
}