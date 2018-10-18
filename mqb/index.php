<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2017/11/28
 * Time: 下午7:17
 */

    require "../lib/Curl.php";
    require "../lib/Dama.php";
    require "../lib/medoo.php";
    require "./function.php";


    //账号计入数据库
    $dbConfig = array(
        'database_type' => 'mysql',
        'username' => 'root',
        'password' => '123456',
        'database_name' => 'mqb',
    );
    $db = new medoo($dbConfig);

    //模拟注册 随机60到100秒注册一个
//    while(true) {
//        doRegist();
//        $i = rand(60, 100);
//        sleep($i);
//    }

    function doRegist()
    {
        global $db;
        //创建随机账号
        $account = account();
        echo $account, "\r\n";


        $data = array(
            'user' => $account,
            'passwd' => 'mbqtest666',
            'email' => $account . '@163.com',
        );
        $db->insert('account', $data);


        $curlObj = new Curl();
        //打码


        //模拟注册
        register($account, $curlObj);

        //每日签到
        sign($curlObj);

        //判断金额
        $num = money($curlObj);

        //转账
        if ($num > 1000) {
            charge($curlObj, $num, 'JZTvVNvTsYeosd9cBd5WNdpjwWMsjUMcY1');
        }
    }

    function everyDaySign()
    {
        global $db;

//        //每日签到
//        sign($curlObj);
//
//        //判断金额
//        $num = money($curlObj);
//
//        //转账
//        if ($num > 1000) {
//            charge($curlObj, $num, 'JZTvVNvTsYeosd9cBd5WNdpjwWMsjUMcY1');
//        }
    }



