<?php 
//namespace Test;
class index
{
	public static function info()
	{
		echo "This is Test";
	}

	public function trun()
	{
		$trun = array(
			'安徽' => 'anhui',
			'北京' => 'beijing',
			'重庆' => 'chonging',
			'福建' => 'anhui',
			'甘肃' => 'anhui',
			'广东' => 'anhui',
			'广西' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
			'安徽' => 'anhui',
		);
	}
}

include "../lib/Curl.php";
$curlObj = new Curl();
$curlObj->setTimeout(100);
$curlObj->setUrl("https://www.newrank.cn/xdnphb/data/weixinuser/searchWeixinDataByCondition");

$curlObj->disableSSL();
//$file = 'privkey.pem';
//$file = "CA-G4.cer";
//$curlObj->ssl($file);

$header = array(
    'Cookie:token=1DF08671D21DAEB497F629DC68CC916F; tt_token=true; UM_distinctid=160e33d3087196-004ffa594c2886-32637402-13c680-160e33d30889f4; __root_domain_v=.newrank.cn; _qddaz=QD.xyy8i5.uesr4j.jc9xh44a; token=1DF08671D21DAEB497F629DC68CC916F; _qddamta_2852150610=3-0; Hm_lvt_a19fd7224d30e3c8a6558dcb38c4beed=1515640926,1515661692; tt_token=true; _qdda=3-1.1; _qddab=3-bta3l6.jcabk3ub; Hm_lpvt_a19fd7224d30e3c8a6558dcb38c4beed=1515664598; CNZZDATA1253878005=562192867-1515639644-%7C1515662145',
    'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
);
$curlObj->setHttpHeader($header);

$data = array(
    'filter' => '',
    'hasDeal' => "false",
    'keyName' => 'abc',
    'order' => 'relation',
    'nonce' => '74d6cabf9',
    'xyz' => '8cb24316778126967b02c45e422b9d5f'
);

$curlObj->setPost($data);
$str = $curlObj->run();

var_dump($str);
//var_dump($curlObj->getInfo());