<?php
	ini_set('display_errors', 'ON');
	error_reporting(E_ALL);

	require './../../lib/Curl.php';

	/**
	 * CEO
	 */
	class Ceo
	{
		//域名
		public $domain = 'ceo.bi';

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
		public $maxMoneyPreTimes = 20;

		//单次数量最大
		public $maxNumPreTimes = 10000;

		//锁仓资金
		public $minMoney = 1600;

		public function __construct()
		{
			$this->curl = new Curl();
			$header = array(
				"Cookie: aliyungf_tc=AQAAAEOyWCKSzQoARusP3UVBVmCt7Fsg; JSPSESSID=r31d9ansgq2ocaa16lht0mipe7; title284=1; Hm_lvt_8ea3f9ee7328affe1c09a675ba5961a6=1526825469,1526880967,1526904245,1527000566; Hm_lpvt_8ea3f9ee7328affe1c09a675ba5961a6=1527000592",
				"Host: {$this->domain}",
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

				sleep(60);
			}
		}

		/**
		 * 快速刷单
		 */
		public function main_quick()
		{
			while(true) {
				$this->quick();
				// sleep(1);
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
			$price = $minSalePrice;
			$price = 0.00091;
			//下单买 type=1
			$orderMoney = 100;
			$n = $orderMoney/$price;
			if ($money > $orderMoney) {
				$this->trade(1, $price, $n);
			} elseif($money > 1) {
				$n = ($money-1)/$price;
				$this->trade(1, $price, $n);
			}
			//挂单卖 type=2
			if ($num > $n) {
				$this->trade(2, $price, $n);
			} elseif($num > 10000) {
				$n = $num-10000;
				$this->trade(2, $price, $n);
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

			//抢卖模式，容易亏,快速成交，适合交易高峰期
 			if ($minSaleNum > $maxBuyNum) {
 				//卖的多，优先低价卖
 				echo "卖的多，优先更低的价卖\r\n";
 				if (($minSalePrice-$this->step) > $maxBuyPrice) {
 					$minSalePrice -= $this->step;
 				}
 				if (($maxBuyPrice+$this->step) < $minSalePrice) {
 					$maxBuyPrice += $this->step;
 				}
 			} else {
 				//买的多，优先买
 				echo "买的多，优先更高的价买\r\n";
 				if (($maxBuyPrice+$this->step) < $minSalePrice) {
 					$maxBuyPrice += $this->step;
 				}
 				if (($minSalePrice-$this->step) > $maxBuyPrice) {
 					$minSalePrice -= $this->step;
 				}
			}


 			//下单买 type=1
 			if ($money > $this->minMoney) {
	 			if ($money > $this->maxMoneyPreTimes) {
	 				$this->trade(1, $maxBuyPrice, $this->maxMoneyPreTimes/$maxBuyPrice);
	 			} elseif($money > 5) {
	 				$this->trade(1, $maxBuyPrice, ($money-1)/$maxBuyPrice);
	 			}
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
			$urlStr = "https://{$this->domain}/trade/up.html";
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
			$url = "https://{$this->domain}/trade/index_json";
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
			$urlStr = "https://{$this->domain}/trade/chexiao.html";
			$data   = array(
				'id' => $id,
			);
			$this->curl->setUrl($urlStr);
			$this->curl->setPost($data);
			$resStr = $this->curl->run();
			$resArr = json_decode($resStr, true);

			print_r($resArr);
			echo "撤销订单操作完成！\r\n";
		}

		/**
		 * 极速刷单
		 */
		public function main_thunder()
		{
			$i = 0;
			while(true) {
				$this->thunder();
				// sleep(1);
				$i++;

				//如果有挂单，价格和当前不匹配，撤销订单
				// if ($i>=10) {
				// 	$tradeArr = $this->getTrade();
				// 	if (!is_array($tradeArr) || empty($tradeArr)) {
				// 		return ;
				// 	}
				// 	if (is_array($tradeArr['order']) && !empty($tradeArr['order'])) {
				// 		foreach ($tradeArr['order'] as  $order) {
				// 			if ($order['price'] != $price) {
				// 				//取消订单
				// 				$this->cancel($order);
				// 			}
				// 		}
				// 	}
				// 	$i = 0;
				// }
			}
		}

		/**
		 * 极速刷单
		 */
		public function thunder()
		{
			//用最高买单价格刷
			$price = 0.00090;
			//下单买 type=1
			$orderMoney = 90;
			$n = $orderMoney/$price;
			$this->trade(1, $price, $n);
			//挂单卖 type=2
			$this->trade(2, $price, $n);
		}
	}

	$ceoObj = new Ceo();
	$type = isset($argv[1]) ? $argv[1] : '';
	if ($type == 'safe') {
		$ceoObj->main_index();
	} elseif($type == 'thunder') {
		$ceoObj->main_thunder();
	} else {
		$ceoObj->main_quick();
	}
?>
