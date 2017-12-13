<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2017/11/28
 * Time: 下午7:20
 */

//创建随机账号
function account()
{
    $account = "";
    $str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $str = "abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz";
    for($i=0;$i<=8;$i++) {
        $r = rand(0, 51);
        $account .= $str[$r];
    }

    $account .= rand(1, 100);

    return $account;
}

//模拟注册
function register($account, $curlObj)
{

    $data['pid']        = 0;
    $data['type']       = "email";
    $data['name']       = $account . "@163.com";
    $data['username']   = $account;
    $data['password']   = 'mbqtest666';
    $data['password2']  = 'mbqtest666';

    $curlObj->setUrl("http://www.maibiqu.com/reg/index.html");
    $curlObj->setPost($data);
    $curlObj->saveCookie("./cookie/");
    $curlObj->run();
}

//签到
function sign($curlObj)
{

    $curlObj->setUrl("http://www.maibiqu.com/qdao/index.html");
    $curlObj->setCookie("./cookie/");
    $curlObj->run();
}

//判断金额是否可大于1000,可以提现
function money($curlObj)
{
    $curlObj->setUrl("http://www.maibiqu.com/user/money.html");
    $curlObj->setCookie("./cookie/");
    $html = $curlObj->run();
    $p = '/<tr>(\s|\S)*?<td width=\"195\">OIOC<\/td>(\s|\S)*?<td width="195">(.*?)<\/td>/';
    preg_match($p, $html, $match);
//    var_dump($match);
    return intval($match[3]);
}

//转账
function charge($curlObj, $num = 1000, $address = 'JZTvVNvTsYeosd9cBd5WNdpjwWMsjUMcY1')
{
    $curlObj->setUrl("http://www.maibiqu.com/coin/in.html");
    $curlObj->setCookie("./cookie/");
    $data = array(
        'id' => 10,
        'address' => $address,
        'num' => $num,
        'pwd' => 'mbqtest666',
    );
    $curlObj->setPost($data);
    $curlObj->run();
}
