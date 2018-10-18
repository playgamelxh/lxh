<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2018/1/14
 * Time: 下午4:12
 */

    require "../../lib/Curl.php";
    require "../../lib/Sms.php";

    include '../../lib/adb/adbconsole.php';
    include '../../lib/adb/func.php';

    $adbinfo = array(
        'host' => '192.168.3.9',
        'port' => '5555'
    );

    $adb = new adbconsole($adbinfo);

//    $address = '0x1C829E2D053507A45b0DA32C7B40beD8e62cC439';
//    $position = $adb->getPositionByAttr('绑定领币', 1, '', 'content-desc');var_dump($position);
//    $adb->shell("input text {$address}");
//    $adb->clickPosition(end($position));
//
//    $screen = $adb->getScreen();
//    var_dump($screen);

//    die('123');

//
//    regApp();die();

    $startLine = 84;
    $endLine   = 200;
    $ethFile   = "../imtoken/ethaddress.txt";
    $cookieFile= "./cookie.txt";
    $token     = "5a5a4314a3b8f36cf4bcee25";

    $i = 1;
    $file = fopen($ethFile,"r");

    while(!feof($file))
    {
        $str = fgets($file);
        if ($i >= $startLine) {
            echo $i,'-',$startLine,'/',$str,"\r\n";
            preg_match('/(0x.*?)$/', $str, $match);
            if (isset($match[1])) {
                echo $match[1],"\r\n";
                $address = $match[1];

                //推荐注册
                $ip = rand(40,244).'.'. rand(1,244).'.'. rand(1,244).'.'. rand(1,244);
                regWeb($address, $token, $cookieFile, $ip);

                //模拟注册 app
                regApp($address);

            }
        }
        $i++;
    }

    fclose($file);


    //推荐注册
    function regWeb($address, $token, $cookieFile, $ip)
    {
        $md5 = md5($address);
        $curlObj = new Curl();
        $curlObj->saveCookie($cookieFile);

        $header = array(
            "CLIENT-IP:{$ip}",
            "X-FORWARDED-FOR:{$ip}",
        );
        $curlObj->setHttpHeader($header);
        $curlObj->setUrl("https://yeecall.gl.yeecall.com/activity/share/api/reg");
        $data = array(
            'token' => $address,
            'id'    => $token,
            'mid'   => substr($md5, 0, 2).substr($md5, 30, 2),
        );
        $curlObj->setPost($data);
        $resStr = $curlObj->run();
        echo $resStr,"\r\n";

        $resArr = json_decode($resStr, true);
        if (isset($resArr['success']) && $resArr['success']) {
            echo "注册成功\r\n";
        } else {
            echo "注册失败\r\n";
        }

    }

    //下载应用

    //模拟登录引用
    function regApp($address)
    {
        global $adb;

        $cmd = array(
            ADBDIR.' uninstall com.yeecall.app',
            ADBDIR.' install /www/lxh/bbb/yee/yeecall.apk',
            'am start com.yeecall.app/com.zayhu.ui.ZayhuSplashActivity'
        );
        $adb->shell($cmd);

        //进入YEECALL
        $i = 1;
        while ($i<15) {
            $position = $adb->getPosition('进入YeeCall');
            if (!empty($position)) {
                $adb->getPositionAndClick('进入YeeCall', 1);
                break;
            }
            $i++;
            sleep(1);
        }


        $smsObj = new Sms(11282);
        //获取手机号
        $phone = $smsObj->getPhone();

        file_put_contents("./account.txt", $phone."\r\n", FILE_APPEND);

        //先清空,以防有手机号
        for($i=1;$i<=12;$i++) {
            $adb->shell('input keyevent 67');
        }
        //输入手机号
        $adb->shell('input text '.$phone);
        //下一步
//        $position = $adb->getPosition("com.yeecall.app:id/ab2");
//        $adb->clickPosition($position, 1);
        $adb->getPositionAndClick('com.yeecall.app:id/ab2', 1);

        //获取手机短信
        $i = 1;
        $code = '';
        while($i<=10) {
            $res = $smsObj->getMsg($phone);
            if (!empty($res)) {
                preg_match('/验证码：(\d{4})/', $res, $match);
                $code = $match[1];
                break;
            }
            $i++;
            sleep(3);
        }

        //四位验证码
        for($i=0;$i<=3;$i++) {
            $adb->shell('input text ' . substr($code, $i, 1));
        }

        $i = 1;
        while ($i<15) {
            $position = $adb->getPosition('请设置昵称');
            if (!empty($position)) {
                break;
            }
            $i++;
            sleep(1);
        }
        //设置昵称
        $name = 'lxh'.rand(1, 10000);
        $adb->shell('input text ' . $name );
        //完成
        $adb->getPositionAndClick('完成', 1);
        //调过
        $adb->getPositionAndClick('跳过', 1);

        //Android 5.1以下版本 图标bug,无法展示,需要手动点过去

        //绑定eth地址
        while (true) {
            $position = $adb->getPositionByAttr('绑定领币', 2, '', 'content-desc');
            if (!empty($position)) {
                var_dump($position);
                $adb->shell("input text {$address}");
                $adb->clickPosition(end($position), 1);
                $screen = $adb->getScreen();
                var_dump($screen);die();
                break;
            } else {
                echo "等待绑定页面\r\n";
            }
            sleep(1);
        }
        //手动到退出登录

        while (true) {
            $position = $adb->getPosition('退出登录');
            if (!empty($position)) {
                break;
            }
            sleep(1);
        }
        $adb->getPositionAndClick('退出登录', 1);
        //设置密码
        $i = 1;
        while ($i<15) {
            $position = $adb->getPosition('设置密码');
            if (!empty($position)) {
                break;
            }
            $i++;
            sleep(1);
        }
        $adb->getPositionAndClick('设置密码', 1);
        $pwd = 'yeecall330318747';
        $adb->shell('input text ' . $pwd );
        $adb->getPositionAndClick('确认', 1);
        $i = 1;
        while ($i<15) {
            $position = $adb->getPosition('密码设置成功');
            if (!empty($position)) {
                $adb->getPositionAndClick('确定', 1);
                break;
            }
            $i++;
            sleep(1);
        }

        $i = 1;
        while ($i<15) {
            $position = $adb->getPosition('退出登录');
            if (!empty($position)) {
                $adb->getPositionAndClick('退出登录', 1);
                break;
            }
            $i++;
            sleep(1);
        }
    }