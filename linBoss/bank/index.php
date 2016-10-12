<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 16/9/13
 * Time: 下午9:34
 */

require "../../lib/Curl.php";
$curlObj = new Curl();

$uid = 746404757;
$pas = 'cdd17828491b413b960de812c7015bb1';

$payRequestUrl = "http://pay.t3pay.cn/tsbAction_doPayment.action";

$payOrderRequestUrl = "http://pay.t3pay.cn/pay_orderQuery.action";

$data = array(
    //必填  是  分配的唯一商家号
    'merchantId' => $uid,
    //必填  是  商家密钥
    'merchantKey' => $pas,
    //必填  是  双方统一用的签名方式MD5
    'signType' => 'MD5',
    //必填  是  商户发送请求的URL
    'shRequestUrl' => 'http://localhost/',
    //必填  是  支付成功后，返回商户地址
    'shReturn_url' => 'http://localhost/',
    //必填  是  该用户当前所使用机器的IP
    'ipAddress' => '127.0.0.1',
    //必填  是  该用户当前所使用的域名
    'yuMing' => 'localhost',
    //必填  是  商户的简称
    'merchantAbbreviation' => '商户简称abc',
    //必填  是  WX_1（扫码支付）
    'bankMark' => 'WX_1',
    //必填  是  utf-8
    'inputCharset' => 'utf-8',
    //必填  是  商户网站唯一订单号
    'orderNo' => 'NO000001',
    //必填  是  提交订单时间
    'orderTime' => time(),
    //必填  是  商户订单总金额  以元为单位，精确到小数后两位
    'payPrice' => '580.00',
    //必填  是  产品名称
    'productName' => '产品名称',
    //必填  是  订单描述
    'orderDes' => '产品描述',
    //必填  是  注：填写商户ID
    'payCurrency' => 100,
    //可选  否  扩展字段
    'extendParam' => '',
    //必填  否  签名值
    'merchantSignString' => 'true',
);
createPayQRcode($payRequestUrl, $data);

//生成支付二维码
function createPayQRcode($url, $data)
{
    header("Content-Type: image/png");
    $img = post($url, $data);
    echo $img;
    exit();
}

//发送post数据
function post($url, $data)
{
    $curlObj = new Curl();
    $curlObj->setUrl($url);
    $curlObj->setPost($data);
    return $curlObj->run();
}
