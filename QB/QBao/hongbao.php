<?php
include 'adbconsole.php';
include 'func.php';
 
$adbinfo = array(
	'host' => '192.168.3.9',
	'port' => '5555'
);
$yaoqingCode = 'BCF4';
$adb = new adbconsole($adbinfo);

$filePath  = "./qbao-1-1.txt";		//
$startLine = 856;						//起始处理行数
$endLine   = 10000;             	//截止处理行数

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
        echo "line:{$i},\r\n";
    }
    fclose($handle);
}

//获取有效助记词
function getChar($buffer)
{echo $buffer;
	$arr = explode(' ', $buffer);
	if (isset($arr[3])) {
		$arr = array_slice($arr, 0, 12);
	} else {
		return '';
	}
	//去掉开头的字符
	$arr[0]  = str_replace('Success:', '', $arr[0]);
	//去掉结尾多余的字符
	$temp = explode('	', $arr[11]);print_r($temp);
	$arr[11] = trim($temp[0]);
	if (empty($arr[11])) {
		return '';
	}

	$str = implode(' ', $arr);
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
		$res = $adb->shell($cmd);
		
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
		
		$arr = explode(' ', $char);
		$cmd = array();
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
		//$screen = $adb->getScreen();var_dump($screen);die();
		$adb->getPositionAndClick('com.aether.coder.qbao:id/md_buttonDefaultNegative');
		$adb->getPositionAndClick('com.aether.coder.qbao:id/image_close');
		$adb->clickPosition("[0,335][720,459]", 1, '');
		sleep(3);
		$i = 1;
		while ($i<=30 ) {
			$adb->clickPosition("[252,716][468,981]", 1, '');
			$position = $adb->getPositionByAttr('抱歉', 1, '', 'content-desc');
			$adb->clickPosition("[598,218][630,251]", 1, '');
			if (!empty($position)) {
				break;
			}
			$i++;
		}
		// $screen = $adb->getScreen();var_dump($screen);
		// die();
	}
}