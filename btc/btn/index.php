<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2018/1/6
 * Time: 下午5:39
 */

    include "/www/lxh/lib/Curl.php";
    include "/www/lxh/lib/Sms.php";
    include "/www/lxh/lib/lib.php";

    $num = 100;
    $i = 1;
    while (true) {


        $smsObj = new Sms('10895');
        $phone  = $smsObj->getPhone();

        $curlObj = new Curl();
        $curlObj->disableSSL();
        $ip = getIP();
        $header = array(
            "CLIENT-IP:{$ip}",
            "X-FORWARDED-FOR:{$ip}",
        );
        $curlObj->setHttpHeader($header);
        $cookieFile = "./cookie.txt";
        $curlObj->saveCookie($cookieFile);
        $curlObj->setCookie($cookieFile);
        //邀请链接
        $curlObj->setUrl("https://www.btcnano.org/index_cn.html?invitedId=523078");
        $curlObj->run();
        //发短信
        $curlObj->setUrl("https://www.btcnano.org/THBOM/userAction/getVerifyCode.Action?parms=%7B%22phoneNumber%22%3A%22%2B86{$phone}%22%7D");
        $str = $curlObj->run();
        echo $str,"\r\n";
        $arr = json_decode($str, true);
        if ($arr['message'] != 'OK') {
            continue;
        }

        $msg = $smsObj->getMsg($phone, 10);
        echo $msg;
        if (!empty($msg)) {
            $p = "/验证码:(\d{4})/";
            preg_match($p, $msg, $match);
            print_r($match);
            $code = $match[1];

            $curlObj->setUrl("https://www.btcnano.org/THBOM/userAction/validateSMSValid.Action?parms=%7B%22phoneNumber%22%3A%22%2B86{$phone}%22%2C%22verifyCode%22%3A{$code}%7D");
            $res = $curlObj->run();
            echo $res,"\r\n";

            $email = getEmail();
            $url  = "https://www.btcnano.org/THBOM/userAction/register.Action?parms=%7B%22phoneNumber%22%3A%22%2B86{$phone}%22%2C%22";
            $url .= "password%22%3A%22ae4bd0142f936c6fe90d31d41a78d019%22%2C%22verifyCode%22%3A{$code}%2C%22emailAddress%22%3A%22{$email}%22%2C%22beInvitedId%22%3A%22523078%22%7D";

            $curlObj->setUrl($url);
            $html = $curlObj->run();
            echo $html,"\r\n";
            file_put_contents("./account.txt", $phone."\r\n", FILE_APPEND);
        }

        $i++;
        if ($i>$num) {
            break;
        }
    }