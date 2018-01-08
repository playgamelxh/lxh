<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2018/1/4
 * Time: 下午2:38
 */

    include "../../lib/Curl.php";
    include "../../lib/Dama.php";

    $file = fopen("wallet.txt","r");
    $startLine = 247;

    $i = 1;
    while(!feof($file))
    {
        $address = fgets($file, 1024);

        if ($i >= $startLine) {
            echo $i,":",$address,"\r\n";
            sign($address);

            $t = rand(10, 20);
            sleep($t);
        }
        $i++;
    }

    fclose($file);

    //处理函数
    function sign($address)
    {
        $curlObj = new Curl();
        $cookieFile = "./cookie.txt";
        $imgFile    = "./code.png";

        //生成cookie
        $curlObj->setUrl("http://www.pccoin.top/qd.html");
        $curlObj->saveCookie($cookieFile);
        $curlObj->run();

        while(true) {
            //下载验证码
            $curlObj->setCookie($cookieFile);
            $curlObj->setUrl("http://www.pccoin.top/cccc.php");
            $curlObj->saveImage($imgFile);

            //打码
            $codeStr = getCode($imgFile);
            var_dump($codeStr);
            $codeArr = json_decode($codeStr, true);
            $code = strtolower($codeArr['Result']);

            //签到
            $curlObj = new Curl();
            $curlObj->setCookie($cookieFile);
//            $curlObj->saveCookie($cookieFile);
            $curlObj->setUrl("http://www.pccoin.top/qd.php?sub=qd");

            $ip = getIP();
            $header = array(
                "CLIENT-IP:{$ip}",
                "X-FORWARDED-FOR:{$ip}",
            );
            $curlObj->setHttpHeader($header);

            $data = array(
                'qb' => $address,
                'ip' => $ip,
                'code' => $code,
            );
            $curlObj->setPost($data);

            $html = $curlObj->run();
            echo $html,"\r\n";
            if ($html != '验证码输入不正确') {
                break;
            } else {
                //打码报错
                sendError($codeArr['Id']);
//                die();
            }
        }

    }

    //伪造IP
    function getIP()
    {
        return rand(60, 150).".".rand(1 ,255).".".rand(1, 255).'.'.rand(1, 255);
    }

    //打码
    function getCode($imageFile)
    {
        $damaObj = new Dama();
        $resStr = $damaObj->get($imageFile, '4050');
        return $resStr;
    }

    //报错
    function sendError($id)
    {
        $damaObj = new Dama();
        $resStr = $damaObj->sendError($id);
        return $resStr;
    }