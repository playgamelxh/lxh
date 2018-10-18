<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2018/1/6
 * Time: 下午7:24
 * Desc: 常用函数
 */

    //伪造IP
    function getIP()
    {
        return rand(60, 150).".".rand(1 ,255).".".rand(1, 255).'.'.rand(1, 255);
    }

    //伪造邮箱
    function getEmail()
    {
        $email = '';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $num = rand(4, 10);
        for ($i=1;$i<=$num;$i++) {
            $t = rand(1, 52);
            $email .= substr($chars, $t, 1);
        }
        $email .= rand(100, 99999);
        $email .= "@126.com";
        return $email;
    }