<?php

include 'func.php';
date_default_timezone_set('Asia/Shanghai');

$itemid = 63441;
$invite = 92424;
$invitelevel = 0;

//mixin 用户注册
//for($i = 1; $i < 100; $i++) {
while (1) {
    `/sbin/ifdown ppp0; /sbin/ifup ppp0`;

    $phone = gettelNo($itemid);
    if (strlen($phone) != 11) {
        echo "\n 手机号格式错误 ：";
        sleep(3);
        continue;
    }
    var_dump($phone);

    $cookieFile = './tmp/candy_x_'.generateStr().'.log';

    $url = 'https://candy.one/i/'.$invite;
    curl_get_contents($url, $cookieFile);
    echo "\n 获取推荐cookie成功";
    //sleep(rand(10,30));

    //发送验证码
    $url = 'https://candy.one/i/'.$invite;
    $data = array(
        'phone' => $phone,
        'dialcode' => '86',
        'countrycode' => 'cn',
        'status' => 'login',
        'enroll_id' => $invite,
    );
    $res = curl_get_contents($url, $cookieFile, $data, array('Origin:https://candy.one', 'Referer:https://candy.one/i/'.$invite));
    if (stripos($res, 'Enter Verification Code') === false) {
        echo "\n 验证码发送失败";
        continue;
    } else {
        echo "\n 短信发送成功";
    }

    //接收短信验证码
    $msg = gettelMsg($itemid, $phone);
    if (!preg_match('/[\d]{6}/', $msg, $matches)) {
        echo "\n 未接收到短信验证码";
        continue;
    }
    $code = $matches['0'];
    echo "\n code :".$code;

    //填入验证码
    $url = 'https://candy.one/check_msg?phone=86'.$phone.'&code='.$code;
    $res = curl_get_contents($url, $cookieFile, '', array('Referer:https://candy.one/i/'.$invite));
    if ($res == 'ok') {
        echo "\n 短信验证码正确 ";
    } else {
        echo "\n 验证码错误";
        continue;
    }

    $url = 'https://candy.one/user';
    $data = array(
        'code' => $code,
        'status' => 'send_msg',
        'phone' => '86'.$phone,
        'countrycode' => 'CN',
    );
    $res = curl_get_contents($url, $cookieFile, $data, array('Referer:https://candy.one/i/'.$invite));
    if (stripos($res, 'Your candy balance') !== false) {
        echo "\n 注册成功：".$phone;
    } else {
        echo "\n 注册失败";
        echo "\n ".$res;
        sleep(1800);
        continue;
    }

    //sleep(rand(10,30));
    $url = 'https://candy.one/invite';
    $rs = curl_get_contents($url, $cookieFile);
    preg_match('/https:\/\/candy\.one\/i\/([\d]+)/', $rs, $matches);
    $newinvite = $matches['1'];

    echo "\n 新的邀请链接:".$newinvite;

    echo "\n ".date('Y-m-d H:i:s').' 注册信息:'.$phone."\t".$newinvite;
    if (rand(1, 3) == 1 && $newinvite > 0) {
        $invite = $newinvite;//更换邀请信息    
        ++$invitelevel;
        if ($invitelevel > 7) {
            break;
        }
    }
}
