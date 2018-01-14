<?php

//安卓模拟器分辨率720*1280，使用前需要手动登陆

include '/www/lxh/lib/adb/adbconsole.php';
include '/www/lxh/lib/adb/func.php';
 
$adbinfo = array(
	'host' => '192.168.3.9',
	'port' => '5555'
);

$adb = new adbconsole($adbinfo);

$i = 476;
while(1) {

	$adb->clickPosition('[648,98]', 1, '创建钱包');
	$adb->getPositionAndClick('创建钱包', 1);
    $adb->getPositionAndClick('跳过', 1);

	$adb->getPositionAndClick('钱包名称');
	$adb->shell('input text lxh'.$i++);

	$adb->clickPosition('[30,292]');
	$adb->shell('input text lxh330318747+');
	
	$adb->clickPosition('[30,413]');
	$adb->shell('input text lxh330318747+');

	$adb->getPositionAndClick('我已经仔细阅读');
	sleep(1);
	$adb->clickPosition('[318,645]', 1, '最后一步：立即备份你的钱包！');

	$adb->clickPosition('[318,528]');
	sleep(1);
	$adb->shell('input text lxh330318747+');
	$adb->getPositionAndClick('确认', 1, '截图');
	$adb->getPositionAndClick('知道了');

	$screen = $adb->getScreen();
	preg_match('/text="([ a-z]{20,})"/', $screen, $matches);
	$word = $matches['1'];
	echo "\n word:".$word;

	$screen = $adb->getPositionAndClick('下一步', 1, '确认你的钱包助记词');

	$words = explode(' ', $word);
	foreach (explode(' ', $word) as $value) {
		echo "\n 点击 ".$value;
		$adb->getPositionAndClick($value);
	}
	$adb->getPositionAndClick("确认", 1, '你备份的助记词顺序验证正确');
	$adb->getPositionAndClick('确认', 1, '行情');

	$adb->getPositionAndClick('ETH', 1, '收款');
	$screen = $adb->getPositionAndClick('收款', 1, '收款码');


	preg_match('/text="0x([a-zA-Z\d]*)"/', $screen, $matches);
	$ethaddress = '0x'.$matches['1'];
	$rs = "\nETHRSFLAG:".$word."\t".$ethaddress;
	echo $rs;
	if(!empty($matches)) {
		file_put_contents('ethaddress.txt', $rs, FILE_APPEND);
	}
	
	$adb->clickPosition("[8,48]");
	sleep(1);
	$adb->clickPosition("[8,48]");
	sleep(1);

	//删除钱包
	$adb->getPositionAndClick("我", 1, '管理钱包');
	$adb->getPositionAndClick("管理钱包", 1, '导入钱包');
	$adb->clickPosition('[48,198]');
	$adb->getPositionAndClick('删除钱包', 1, '请输入密码');
	$adb->shell('input text lxh330318747+');
	$adb->getPositionAndClick('确认', 1, '管理钱包');
	
	$adb->clickPosition("[10,58]");
	$adb->getPositionAndClick('资产', 1, 'ETH');


}
 
