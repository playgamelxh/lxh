<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2018/1/18
 * Time: 上午11:16
 */

    include "../../lib/Curl.php";
    include "../../lib/Sms.php";

    //
    $invite = 96190;
    $cookieFile = './cookie.txt';
    $accountFile = './account.txt';
    $invArr[0] = array($invite);


    $smsObj  = new Sms(11357);

    $i = 1;
    while ($i<10) {
        echo $i,":";
        work($invite, $cookieFile, $accountFile);
        $i++;
    }



    function work($invite, $cookieFile='./cookie.txt', $accountFile='./account.txt')
    {
        global $smsObj;

        file_put_contents($cookieFile, '');
        sleep(1);
        $curlObj = new Curl();
        $ip = '162.105.'.'.'. rand(1,244).'.'. rand(1,244);
        $header = array(
//            "CLIENT-IP:{$ip}",
//            "X-FORWARDED-FOR:{$ip}",
            "Host:candy.one",
            "Referer:https://candy.one/invite",
            "Upgrade-Insecure-Requests:1",
            "User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36"
        );
        $curlObj->setHttpHeader($header);
        $curlObj->setUrl("https://candy.one/i/{$invite}");
        $curlObj->saveCookie($cookieFile);
        $curlObj->run();

        $curlObj->setCookie($cookieFile);
        $phone = $smsObj->getPhone();
        echo $phone,"\t";
//        file_put_contents($accountFile, $phone."\t", FILE_APPEND);

        $data = array(
            'phone' => $phone,
            'dialcode' => '86',
            'countrycode' => 'cn',
            'status'    => 'login',
            'enroll_id' => $invite,
        );
        $curlObj->setPost($data);
        $curlObj->run();

        $i = 1;
        $code = '';
        $msg = $smsObj->getMsg($phone, 5);
        //Candy code 165041[LNKD]
        if (!empty($msg)) {
            preg_match('/(\d{6})/', $msg, $match);
            $code = isset($match[1]) ?  $match[1] : '';
            echo "\t",$code,"\r\n";
        }

        if (empty($code)) {
            echo "获取短信失败\r\n";
            return '';
        }

        $urlStr = "https://candy.one/check_msg?phone=86{$phone}&code={$code}";
        $curlObj->setUrl($urlStr);
        $curlObj->setGet();
        $html = $curlObj->run();
        if ($html != 'ok') {
            echo "验证码:",$html,"\r\n";
            return;
        }


        $urlStr = 'https://candy.one/user';
        $curlObj->setUrl($urlStr);
        $data = array(
            'code' => $code,
            'status' => 'send_msg',
            'phone' => '86'.$phone,
            'countrycode' => 'CN',
        );
        $curlObj->setPost($data);
        $html = $curlObj->run();
        if (stripos($html, 'Your candy balance') !== false) {
            echo "\n 注册成功：".$phone;
        } else {
            echo "\n 注册失败";
            echo "\n ".$html;
            sleep(1800);
            return;
        }

        $urlStr = 'https://candy.one/invite';
        $curlObj->setUrl($urlStr);
        $curlObj->setGet();
        $html = $curlObj->run();
        preg_match('/https:\/\/candy\.one\/i\/([\d]+)/', $html, $matches);
        $newinvite = $matches['1'];

        echo "\n 新的邀请链接:".$newinvite,"\r\n";
        file_put_contents($accountFile, $phone."\t".$newinvite."\r\n", FILE_APPEND);

        file_put_contents('./a.html', $html);
    }