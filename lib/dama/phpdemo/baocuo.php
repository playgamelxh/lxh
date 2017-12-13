<?php
$reportUrl = 'http://api.ruokuai.com/reporterror.json';

$Id = "335d1425-a211-4a79-8974-9fc8066d8eb8";	//多数打码工人都有不同程度残疾,出于人道切勿恶因报错

$ch = curl_init();
$postFields = array('username' => '用户名',	//改成你自己的
		'password' => md5('密码'),	//改成你自己的
		'softid' => 1,	//改成你自己的
		'softkey' => 'b40ffbee5c1cf4e38028c197eb2fc751',	//改成你自己的
		'id' => $Id
);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_URL,$reportUrl);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);	//设置本机的post请求超时时间
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
$result = curl_exec($ch);
curl_close($ch);
var_dump($result);