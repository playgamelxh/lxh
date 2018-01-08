<?php
include 'adbconsole.php';
include 'func.php';
 
$adbinfo = array(
	'host' => '192.168.3.9',
	'port' => '5555'
);
$yaoqingCode = 'BCF4';
$adb = new adbconsole($adbinfo);

$filePath  = "./qbao_test.txt";	//
$startLine = 1;				//起始处理行数
$endLine   = 3;             //截止处理行数

$handle = @fopen($filePath, "r");
if ($handle) {
	$i = 1;
    while (!feof($handle)) {
    	//取一行数据
        $buffer = fgets($handle, 1024);
        if($i >= $startLine && $i <= $endLine) {
        		echo $buffer,"\r\n";
        		//开始处理
        		dowork($buffer);
        }
        $i++;
    }
    fclose($handle);
}

//获取有效助记词
function getChar($buffer)
{
	$arr = explode('\t', $buffer);
	if (isset($arr[0])) {
		$arr = explode(' ', $arr[0]);
	} else {
		return '';
	}
	if (count($arr) >= 12) {
		$arr[0]  = str_replace('Success:', '', $arr[0]);
		$temp    = explode('\t', $arr[11]);
		$arr[11] = trim($temp[0]);
	}
	$arr = array_slice($arr, 0, 12);
	$str = implode(' ', $arr);
	$str = str_replace('Success:', '', $str);
	return $str;
}		

//主要处理流
function dowork($buffer='')
{
	global $adb;
	//获取有效的助记词
	$char = getChar($buffer);
	if (!empty($char)) {
		echo $char,"\r\n";


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

		$adb->getPositionAndClick('导入钱包', 2, '邀请码(选填)');
		
		$cmd = array();
		$arr = explode(' ', $char);
		foreach ($arr as $key => $value) {
			$cmd[] = 'input text '. $value;
			if ($key != 11) {
				$cmd[] = 'input keyevent 62';
			}
		}
		$adb->shell($cmd);
		$cmd = array(
			'input keyevent 61',
			'input text 8888',
			'input keyevent 61',
			'input text 8888',
			'input keyevent 61',
			'input text ', //输入邀请码
		);
		$adb->shell($cmd);
		$adb->getPositionAndClick('com.aether.coder.qbao:id/mButtonStartImport', 1, '');
		$adb->getPositionAndClick('取消');
		$adb->clickPosition("[0,38][84,122]", 1, '');
		$adb->getPositionAndClick('com.aether.coder.qbao:id/image_close');
		$adb->clickPosition("[0,335][720,459]", 1, '');
		sleep(3);
		while (true ) {
			$adb->clickPosition("[252,716][468,981]", 1, '');
			$position = $adb->getPositionByAttr('抱歉', 1, '', 'content-desc');
			$adb->clickPosition("[598,218][630,251]", 1, '');
			if (!empty($position)) {
				break;
			}
		}
		// $screen = $adb->getScreen();var_dump($screen);
		// die();
	}
}