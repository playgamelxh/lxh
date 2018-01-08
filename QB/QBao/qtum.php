<?php

//安卓模拟器分辨率720*1280，使用前需要手动登陆

include 'adbconsole.php';
include 'func.php';
 
$adbinfo = array(
	'host' => '192.168.3.9',
	'port' => '5555'
);
$yaoqingCode = 'S9VY';
$adb = new adbconsole($adbinfo);

while(1) {
	echo "\n ==============重新开始=====\n";
	
	$cmd = array(
		ADBDIR.' uninstall com.aether.coder.qbao',
		ADBDIR.' install /Users/lxh/Desktop/QB/QBao-release.apk',
		'am start com.aether.coder.qbao/.ui.splash.SplashActivity'
	);
	$adb->shell($cmd);
	
	sleep(1);
	echo "\n 程序安装并启动.. \n";
	//等待出现界面
	for($i = 1; $i < 20; $i++) {
		$position = $adb->getPosition('您暂时还没有钱包');
		if(!empty($position)) {
			sleep(2);
			break;
		} else {
			echo "\n 程序启动中 ";
			sleep(3);
		}
	}
	echo "\n 程序启动成功 \n";

	$adb->getPositionAndClick('创建钱包', 2, '邀请码(选填)');

	$cmd = array(
		'input text mywallet',
		'input keyevent 66',
		'input text 8888',
		'input keyevent 66',
		'input text 8888',
		'input keyevent 66',
		'input text '.$yaoqingCode, //输入邀请码
	);
	$adb->shell($cmd);
	$adb->getPositionAndClick('com.aether.coder.qbao:id/mButtonCreateWallet', 1, '是否开启登录保护功能');
	
	$adb->getPositionAndClick('取消');

	//获取助记词
	$zhujiWord = $adb->getText('com.aether.coder.qbao:id/text_help_words');
	usleep(500000);
	$adb->clickPosition('[70,110]');
	usleep(500000);
	$adb->getPositionAndClick('com.aether.coder.qbao:id/image_close');


	$address = $adb->getText('com.aether.coder.qbao:id/text_address');

	$adb->clickPosition('[70,110]');
	usleep(500000);
	$adb->getPositionAndClick('分享邀请码', 1, '分享邀请码');

	$adb->getPositionAndClick('com.aether.coder.qbao:id/action_share', 1, '今日头条');
	$adb->getPositionAndClick('今日头条', 1);

	// sleep(3);
	$url = $adb->getText('com.ss.android.article.news:id/pk');
	// echo $url,"\r\n";
	// $res = $adb->getText('com.ss.android.article.news:id/');
	// var_dump($res);
	// $screen = $adb->getScreen();
	// var_dump($screen);
	$newyaoqingCode = substr($url, strlen('https://qbao.fund/qbao/Activities/envelopeCenter.html?shareCode='), 4);
	// echo $newyaoqingCode;

	$s= "Success:\t".$zhujiWord."\t".$address."\t".$newyaoqingCode."\r\n";
	file_put_contents('./qbao.txt', $s, FILE_APPEND);

	echo $s;

	if(rand(1,3) == 2 && $newyaoqingCode) {
		$yaoqingCode = $newyaoqingCode;
	}
	// $adb->getPositionAndClick('com.ss.android.article.news:id/sj', 1);
	// $screen = $adb->getScreen();
	// var_dump($screen);
	// die();
}
 
