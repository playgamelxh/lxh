<?php
	ini_set('display_errors', 'ON');
	error_reporting(E_ALL);

	require './../../lib/Curl.php';

	/**
	 * CEO
	 */
	class Ceo
	{
		//
		public $curl;

		//卖出价格
		public $salePrice = 0.00092;

		//买入价格
		public $buyPrice = 0.00091;

		//最大买单价
		public $maxBuyPrice = 0;

		//最小卖单价
		public $minSalePrice = 0;

		//价格阶梯
		public $step = 0.00001;

		//单次交易最大金额
		public $maxMoneyPreTimes = 30;

		//单次数量最大
		public $maxNumPreTimes = 30000;

		public function __construct()
		{
			$this->curl = new Curl();
			$header = array(
				"Cookie: aliyungf_tc=AQAAAFK5iGwQaQYARusP3RjauIpst7z/; JSPSESSID=pcc9agi0m9jni3a37gtovo9qb7; title286=1; Hm_lvt_8ea3f9ee7328affe1c09a675ba5961a6=1526779332,1526825469,1526880967,1526904245; Hm_lpvt_8ea3f9ee7328affe1c09a675ba5961a6=1526905681",
				"Host: ceo.bi",
			);
			$this->curl->setHttpHeader($header);
		}

		/**
		 * 保险，低买高卖
		 */
		public function main_index()
		{
			while(true) {
				$this->index();

				sleep(60*3);
			}
		}

		/**
		 * 快速刷单
		 */
		public function main_quick()
		{
			while(true) {
				$this->quick();

				sleep(30);
			}
		}

		/**
		 * 快速刷单
		 */
		public function quick()
		{
			$tradeArr = $this->getTrade();
			if (!is_array($tradeArr) || empty($tradeArr)) {
				return ;
			}
			//最高买价
			$maxBuyPrice  = $tradeArr['depth']['b'][0][0];
			//最高买价数目
			$maxBuyNum    = $tradeArr['depth']['b'][0][1];
			//最低卖价
			$minSalePrice = $tradeArr['depth']['s'][9][0];
			//最低卖价数目
			$minSaleNum   = $tradeArr['depth']['s'][0][1];
			//可用金额
			$money = $tradeArr['finance'][0];
			//锁定金额
			$lockMoney = $tradeArr['finance'][1];
			//可用数目
			$num = $tradeArr['finance'][2];
			//锁定数目
			$lockNum = $tradeArr['finance'][3];
			//总共价值
			$totalMoney = $tradeArr['finance'][4];

			//用最高买单价格刷
			$price = $maxBuyPrice;
			$price = 0.00091;
			//下单买 type=1
			if ($money > 10) {
				$this->trade(1, $price, $money/(2*$maxBuyPrice));
			}
			//挂单卖 type=2
			if ($num > 50000) {
				$this->trade(2, $price, $num/2);
			}

			//如果有挂单，价格和当前不匹配，撤销订单
			if (is_array($tradeArr['order']) && !empty($tradeArr['order'])) {
				foreach ($tradeArr['order'] as  $order) {
					if ($order['price'] != $price) {
						//取消订单
						$this->cancel($order);
					}
				}
			}
		}

		/**
		 * 低买，高卖
		 */
		public function index()
		{
			$tradeArr = $this->getTrade();
			if (!is_array($tradeArr) || empty($tradeArr)) {
				return ;
			}
			//最高买价
			$maxBuyPrice  = $tradeArr['depth']['b'][0][0];
			//最高买价数目
			$maxBuyNum    = $tradeArr['depth']['b'][0][1];
			//最低卖价
			$minSalePrice = $tradeArr['depth']['s'][9][0];
			//最低卖价数目
			$minSaleNum   = $tradeArr['depth']['s'][0][1];
			//可用金额
			$money = $tradeArr['finance'][0];
			//锁定金额
			$lockMoney = $tradeArr['finance'][1];
			//可用数目
			$num = $tradeArr['finance'][2];
			//锁定数目
			$lockNum = $tradeArr['finance'][3];
			//总共价值
			$totalMoney = $tradeArr['finance'][4];

			if ($minSaleNum > $maxBuyNum) {
				//卖的多，优先低价卖
				echo "卖的多，优先低价买\r\n";
				if (($minSalePrice-$this->step) > $maxBuyPrice) {
					$minSalePrice -= $this->step;
				}
				if (($maxBuyPrice+$this->step) < $minSalePrice) {
					$maxBuyPrice += $this->step;
				}
			} else {
				//买的多，优先买
				echo "买的多，优先高价卖\r\n";
				if (($maxBuyPrice+$this->step) < $minSalePrice) {
					$maxBuyPrice += $this->step;
				}
				if (($minSalePrice-$this->step) > $maxBuyPrice) {
					$minSalePrice -= $this->step;
				}
			}

			//下单买 type=1
			if ($money > $this->maxMoneyPreTimes) {
				$this->trade(1, $maxBuyPrice, $this->maxMoneyPreTimes/$maxBuyPrice);
			} elseif($money > 5) {
				$this->trade(1, $maxBuyPrice, ($money-1)/$maxBuyPrice);
			}

			//挂单卖 type=2
			if ($num > $this->maxNumPreTimes) {
				$this->trade(2, $minSalePrice, $this->maxNumPreTimes);
			} elseif($num > 10000) {
				$this->trade(2, $minSalePrice, $num-100);
			}

			//取消订单,永远不撤销
			if ($money < 1 && $num < 10000) {
			}
		}

		/**
		 * 交易
		 */
		public function trade($type, $price, $num, $paypassword='1xh330318747?', $market='oioc_cny')
		{
			$urlStr = "https://ceo.bi/trade/up.html";
			$data = array(
				'price' 		=> $price,
				'num' 			=> $num,
				'paypassword' 	=> $paypassword,
				'market' 		=> $market,
				'type'			=> $type,
			);
			$this->curl->setUrl($urlStr);
			$this->curl->setPost($data);
			$resStr = $this->curl->run();
			$resArr = json_decode($resStr, true);
			if ($type == 2) {
				echo "挂单卖，";
			} else {
				echo "下单买，";
			}
			echo "价格：{$price}, 数量：{$num}\r\n";
			print_r($resArr);
		}

		/**
		 * 获取当前行情
		 */
		public function getTrade()
		{
			$url = "https://ceo.bi/trade/index_json";
			$this->curl->setUrl($url);
			$data = array(
				'market' => 'oioc_cny'
			);
			$this->curl->setPost($data);
			$resStr = $this->curl->run();
			$resArr = json_decode($resStr, true);
			if (json_last_error()) {
				// print_r(json_last_error());
				// print_r($resArr);
				return array();
			} else {
				$arr = json_decode($resStr, true);
				print_r($arr['finance']);
				return $arr;
			}
		}

		/**
		 * 撤销订单,手动取消，暂不自动取消
		 */
		public function cancel($order)
		{
			echo "撤销订单!\r\n";
			print_r($order);
			$id = $order['id'];
			$urlStr = "https://ceo.bi/trade/chexiao.html";
			$data   = array(
				'id' => $id,
			);
			$this->curl->setUrl($urlStr);
			$this->curl->post($data);
			$resStr = $this->curl->run();
			$resArr = json_decode($resStr, true);

			print_r($resArr);
			echo "撤销订单操作完成！\r\n";
		}
	}

	$ceoObj = new Ceo();
	$type = isset($argv[1]) ? $argv[1] : '';
	if ($type == 'quick') {
		$ceoObj->main_quick();
	} else {
		$ceoObj->main_index();
	}
?>
