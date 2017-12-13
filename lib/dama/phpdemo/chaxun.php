<?php
$infoUrl = 'http://api.ruokuai.com/info.json';

$ch = curl_init();
$postFields = array('username' => '用户名',	//改成你自己的
			'password' => md5('密码')		//改成你自己的
);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_URL,$infoUrl);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);	//设置本机的post请求超时时间
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
$result = curl_exec($ch);
curl_close($ch);
var_dump($result);