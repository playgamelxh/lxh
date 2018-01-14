<?php
define("ADBDIR", '/usr/local/bin/adb');

class adbconsole {
	public $adbinfo = array();
	public function __construct($adbinfo) {
		$this->adbinfo = $adbinfo;
		$cmd = ADBDIR.' connect '.$this->adbinfo['host'].':'.$this->adbinfo['port'];
		//`$cmd`;
	}

	//在adb中执行命令
	public function shell($cmdArr) {
		if(empty($cmdArr)) {
			return '';
		}
		if(!is_array($cmdArr)) {
			$cmdArr = array($cmdArr);
		}
		$cmdPre = ADBDIR." -s ".$this->adbinfo['host'].":".$this->adbinfo['port']." shell ";
		$cmdPre = ADBDIR." shell ";
		foreach($cmdArr as $v) {
			if(substr($v, 0, strlen(ADBDIR)) != ADBDIR) {
				$cmd = $cmdPre.$v;
			} else {
				$cmd = $v;
			}
			$ret = `$cmd`;
		}
		return $ret;
	}

	//获取屏幕信息。wait = 1,等待页面加载完成  wait=0,不等待页面加载
	public function getScreen($wait = 1) {
		//echo "\ninfo: 获取屏幕 ";
		$dir = '/storage/emulated/legacy';
		//$dir = '/storage/sdcard0';
		$cmd = array(
			'uiautomator dump '.$dir.'/homeinfo.xml',
			'cat '.$dir.'/homeinfo.xml'
		);

		//测试中发现，获取到的UI信息是上一屏的。因此推断存在屏幕延迟情况，所以这里sleep 2
		while(1) {
			$screen = $this->shell($cmd);
			if(empty($screen)) {
				echo "\n无法获取屏幕，因为当前屏幕有无法识别的特殊字符，向下滑动一屏重新获取";
				$this->shell('input touchscreen swipe 139 1125 139 270 2000');
				continue;
			}
			//如果有弹窗，关闭或者等待弹窗
			if(stripos($screen, '此版本包括多个重要优化，请务必升级') !== false) {
				echo "\t 关闭版本提示";
				$position = $this->getPosition("下次",1, $screen);
				$this->clickPosition($position['0']);
				continue;
			}
			if(stripos($screen, '专家的预测仅供参考，预测成绩有起伏') !== false) {
				echo "\t 关闭专业提示";
				$position = $this->getPosition("知道了",  1, $screen);
				$this->clickPosition($position['0']);
				continue;
			}
			if(stripos($screen, '正在加载') !== false && $wait) {
				//echo "\n 页面加载慢，请等待2秒";
				sleep(2);
				continue;
			}
			if(stripos($screen, '出错了') !== false) {
				//echo "\n 出错了";
				$position = $this->getPosition("确定", 1, $screen);
				$this->clickPosition($position['1']);
				echo "\t重要错误，请关闭app重新尝试";
				continue;
			}
			return $screen;
		}
	}
	public function getText($text) {
		$screen = $this->getScreen();

		$doc = new DOMDocument();
		//echo "\n ========\n".$screen."\n======\n";
		$doc->loadXML($screen);
		$node = $doc->getElementsByTagName('node');
		foreach ($node as $key => $value) {
			$resource_id  = $value->getAttribute('resource-id');
			if($resource_id == $text) {
				return  $value->getAttribute('text');
			}
		}
		return '';
	}
	//获取某个文字的坐标, wait = 1,等待页面加载完成  wait=0,不等待页面加载
	public function getPosition($text, $wait = 1, $screen = '') {
		//echo "\ninfo: 查找 $text 的坐标 ";
		$position = array();
		while(empty($screen)) {
			$screen = $this->getScreen($wait);
		}
		$doc = new DOMDocument();
		//echo "\n ========\n".$screen."\n======\n";
		$doc->loadXML($screen);
		$node = $doc->getElementsByTagName('node');
		foreach ($node as $key => $value) {
			$attribute_text = $value->getAttribute('text');
			if(preg_match('/'.preg_quote($text, '/').'/', $attribute_text, $matches)) {
				//如果是正则查找，返回$1;
				if(isset($matches['1'])) {
					$position[] = $value->getAttribute('bounds')."\t".$matches['1'];
				} else {
					$position[] = $value->getAttribute('bounds');
				}
			}
			$resource_id  = $value->getAttribute('resource-id');
			if($resource_id == $text) {
				$position[] = $value->getAttribute('bounds');
			}
		}
		if(empty($position)) {
			echo "\t 获取 $text 失败";
		}
		return $position;
		 
	}
	//根据文本找出所在节点的兄弟节点的文本
	public function getBrotherNode($text, $screen = '') {
		$brotherNodeText = '';

		$currentNode = '';
		if(empty($screen)) {
			$screen = $this->getScreen();
		}
		 
		$doc = new DOMDocument();
		$doc->loadXML($screen);
		$xpath = new DOMXpath($doc);
		$node = $doc->getElementsByTagName('node');
		foreach ($node as $key => $value) {
			$attribute_text = $value->getAttribute('text');
			if(preg_match('/'.$text.'/', $attribute_text)) {
				$currentNode = $value;
			}
		}

		if(empty($currentNode)) {
			echo "\n 获取屏幕文本'{$text}'失败(查找你节点时）";
			return false;
		}
		$parentNode = $currentNode->parentNode;
		if(stripos($text, '次查看') !== false) {
			$path = $parentNode->getNodePath();
			$newpath = substr($path, 0,-3).'[1]'; 
			$parentNode = $xpath->query($newpath)->item('0');
			if(!empty($parentNode)){
				return $parentNode->getAttribute('text');	
			} else {
				return false;
			}	 
		} else {
			$parentNode->removeChild($currentNode);
		}
		
		if($parentNode->getElementsByTagName('node')->length > 0) {
			return $parentNode->getElementsByTagName('node')->item('0')->getAttribute('text');
		} else {
			return false;
		}
	}

	//点击指定坐标，等待下一页面出来validText文本
	public function clickPosition($position, $sleeptime = 200000, $validText = '') {
		if(stripos($position, "\t") !== false) {
			echo "\ninfo:".date("H-i-s")." 点击 ". explode("\t", $position)['1'];
		}
		list($x, $y) = explode(',', substr(strstr($position, ']', true), 1));
		$this->shell('input tap '.($x+2).' '.($y + 2));
		
		if(!empty($validText)) {
			usleep(200000);
			$i = 0;
			while(1) {
				$screen = $this->getScreen();
				if(stripos($screen, $validText) === false) {
					echo "\n 等待验证文本 $validText ";
					usleep(200000);
					if($i++ > 10) {
						echo "\n 点击 $position 之后，无法获取文本 $validText ，当前屏幕信息：".$screen;
						return false;
					}
					continue;
				} else {
					break;
				}
			}
		} else {
			usleep($sleeptime);
			$screen = $this->getScreen();
		}
		return $screen;
	}

	public function getPositionAndClick($text, $wait = 1, $validText = '') {
		$position = $this->getPosition($text);
		if(empty($position)) {
			return '';
		}
		return $this->clickPosition(end($position), 100000, $validText); 
	}

	//获取某个文字的坐标, wait = 1,等待页面加载完成  wait=0,不等待页面加载
	public function getPositionByAttr($text, $wait = 1, $screen = '', $attr='text') {
		//echo "\ninfo: 查找 $text 的坐标 ";
		$position = array();
		while(empty($screen)) {
			$screen = $this->getScreen($wait);
		}
		$doc = new DOMDocument();
		//echo "\n ========\n".$screen."\n======\n";
		$doc->loadXML($screen);
		$node = $doc->getElementsByTagName('node');
		foreach ($node as $key => $value) {
			$attribute_text = $value->getAttribute($attr);
			if(preg_match('/'.preg_quote($text, '\\').'/', $attribute_text, $matches)) {
				//如果是正则查找，返回$1;
				if(isset($matches['1'])) {
					$position[] = $value->getAttribute('bounds')."\t".$matches['1'];
				} else {
					$position[] = $value->getAttribute('bounds');
				}
			}
			$resource_id  = $value->getAttribute('resource-id');
			if($resource_id == $text) {
				$position[] = $value->getAttribute('bounds');
			}
		}
		if(empty($position)) {
			echo "\t 获取 $text 失败";
		}
		return $position;
		 
	}
}