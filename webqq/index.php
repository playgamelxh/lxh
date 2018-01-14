<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2018/1/12
 * Time: 下午5:41
 */
    use Slince\SmartQQ\Client;
    use Slince\SmartQQ\Message\Request\GroupMessage;
    use Slince\SmartQQ\Message\Content;

    ini_set('date.timezone', 'Asia/Shanghai');

    include "./smartqq-master/vendor/autoload.php";

    //生成登录二维码
    $smartQQ = new Client();
    $pid = posix_getpid();
    $smartQQ->login("./img/{$pid}.png");

    //持久化登录凭证
    $credential = $smartQQ->getCredential();
    $credentialParameters = $credential->toArray();

    $groups = $smartQQ->getGroups();
    var_dump($groups);

    $time = 0;
    $day  = 0;
    $hour = 0;
    while (true) {
        sleep(10);

        //1、找到群
        $group = $groups->firstByAttribute('name', 'PCC石油链官方五群');

        //每天签到
        if (time() - $day > 86400) {
            $message = new GroupMessage($group, new Content('签到'));
            $result = $smartQQ->sendMessage($message);
            var_dump($result);
            $day = time();
        }
        //每小时抽奖
        if (time() - $hour > 3600) {
            $message = new GroupMessage($group, new Content('我要PCC'));
            $result = $smartQQ->sendMessage($message);
            var_dump($result);
            $hour = time();
        }

        $time = time();

    }
