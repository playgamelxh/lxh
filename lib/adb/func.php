<?php
$token = '';

function generateStr($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $password = $chars[ mt_rand(0, strlen($chars) - 11) ];
    for ($i = 0; $i < $length - 1; ++$i) {
        $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }

    return $password;
}

//设置邮箱地址
function setEmail($username)
{
    $url = 'https://www.guerrillamail.com/ajax.php?f=set_email_user';
    $cookieFile = './tmp/'.$username.'_email.log';
    $data = array(
        'email_user' => $username,
        'lang' => 'zh',
        'site' => 'guerrillamail.com',
    );
    $rs = json_decode(curl_get_contents($url, $cookieFile, $data), true);
    if ($rs['email_addr']) {
        return $cookieFile;
    } else {
        echo "\n 出错，重试\n";
        return false;
    }
}

function sendMessage($email)
{
    for ($i = 1; $i < 10; ++$i) {
        $firsturl = 'http://www.maibiqu.com/reg/index.html';
        $cookieFile = './tmp/'.$email.'_x_email.log';
        curl_get_contents($firsturl, $cookieFile);

        $url = 'http://www.maibiqu.com/reg/send.html?name='.urlencode($email).'&type=email&note=reg';
        $headers = array(
            'Referer:http://www.maibiqu.com/reg/index.html',
            'X-Requested-With:XMLHttpRequest',
        );
        $s = json_decode(curl_get_contents($url, $cookieFile, '', $headers), true);
        print_r($s);
        if ($s['info'] == '邮件已发送') {
            return true;
        } else {
            echo "\n 邮件发送失败，重试";
            sleep(3);
        }
    }

    return false;
}

//从邮箱获取验证内容
function getMessage($cookieFile, $email)
{
    for ($i = 1; $i < 600; ++$i) {
        $url = 'https://www.guerrillamail.com/ajax.php?f=get_email_list&offset=0&site=guerrillamail.com&_='.substr(microtime(true) * 1000, 0, 13);

        $rs = json_decode(curl_get_contents($url, $cookieFile), true);
        if (!isset($rs['list']['0'])) {
            echo "\n 返回为空";
        } elseif ($rs['list']['0']['mail_from'] == 'no-reply@guerrillamail.com') {
            echo "\n 没有最新邮件\n";
            sleep(5);
        } else {
            echo "\n 获取验证邮件： $email \t ".$rs['list']['0']['mail_subject'];
            print_r($rs);
            break;
        }
    }
}

function gettelNo($itemId)
{
    $username = 'dormscript';
    $password = 'wsy552211';
    $devcode = 'vIt0m3xIVX12piM0U%2fV0cQ%3d%3d';

    global $token;
    if(empty($token)) {
        //token
        $url = 'http://api.ema666.com/Api/userLogin?uName='.$username.'&pWord='.$password.'&Developer='.$devcode;
        $token = curl_get_contents($url);
    }
    
    //获取手机号
    $url2 = 'http://api.ema666.com/Api/userGetPhone?ItemId='.$itemId.'&token='.$token.'&PhoneType=3&Code=UTF8';
    $rs = curl_get_contents($url2);
    var_dump($rs);
    $phone = substr($rs, 0, -1);

    return $phone;
}

function getAllNo($itemId, $phone) {
    $username = 'dormscript';
    $password = 'wsy552211';
    $devcode = 'vIt0m3xIVX12piM0U%2fV0cQ%3d%3d';

    global $token;
    if(empty($token)) {
        //token
        $url = 'http://api.ema666.com/Api/userLogin?uName='.$username.'&pWord='.$password.'&Developer='.$devcode;
        $token = curl_get_contents($url);
    }
    
    $url2 = 'http://api.ema666.com/Api/userSingleGetMessage?token='.$token.'&itemId='.$itemId.'&phone='.$phone.'&Code=UTF8';
    $rs = curl_get_contents($url2);
    var_dump($rs);

    //获取手机号
    $url2 = 'http://api.ema666.com/Api/userGetMessage?token='.$token;
    $rs = curl_get_contents($url2);
    var_dump($rs);
    
    return $rs;

}

//锁定手机号
function lockNO($itemId, $phone) {
    global $token;
    if(empty($token)) {
        $username = 'dormscript';
        $password = 'wsy552211';
        $devcode = 'vIt0m3xIVX12piM0U%2fV0cQ%3d%3d';

        //token
        $url = 'http://api.ema666.com/Api/userLogin?uName='.$username.'&pWord='.$password.'&Developer='.$devcode;
        $token = file_get_contents($url);
    }

    $url2 = 'http://api.ema666.com/Api/userGetPhone?ItemId='.$itemId.'&token='.$token.'&Code=UTF8&Phone='.$phone;
    $rs = curl_get_contents($url2);

    var_dump($rs);
    


}
function freeNo($itemId, $phone) {
    global $token;
    if(empty($token)) {
        $username = 'dormscript';
        $password = 'wsy552211';
        $devcode = 'vIt0m3xIVX12piM0U%2fV0cQ%3d%3d';

        //token
        $url = 'http://api.ema666.com/Api/userLogin?uName='.$username.'&pWord='.$password.'&Developer='.$devcode;
        $token = file_get_contents($url);
    }
    $url = "http://api.ema666.com/Api/userReleasePhone?token=".$token."&phoneList=".$phone."-".$itemId;
    //$url = "http://api.ema666.com/Api/userReleasePhone?token=".$token;
    $rs = curl_get_contents($url);
    return $rs;
}

//测试手机号是否在线
function testPhone($itemId, $phone) {
    global $token;
while(1) {
    if(empty($token)) {
        $username = 'dormscript';
        $password = 'wsy552211';
        $devcode = 'vIt0m3xIVX12piM0U%2fV0cQ%3d%3d';

        //token
        $url = 'http://api.ema666.com/Api/userLogin?uName='.$username.'&pWord='.$password.'&Developer='.$devcode;
        $token = file_get_contents($url);
    }
    //获取手机号
    $url2 = 'http://api.ema666.com/Api/userGetPhone?ItemId='.$itemId.'&token='.$token.'&Code=UTF8&Phone='.$phone;
    $rs = curl_get_contents($url2); 
    if(stripos($rs, 'Session') !== false) {
        $token = '';
    } else {
        break;
    }
}
    freeNo($itemId, $phone);
    return $rs;
}
function gettelMsg($itemId, $phone)
{
    $username = 'dormscript';
    $password = 'wsy552211';
    $devcode = 'vIt0m3xIVX12piM0U%2fV0cQ%3d%3d';

    global $token;
    if(empty($token)) {
        //token
        $url = 'http://api.ema666.com/Api/userLogin?uName='.$username.'&pWord='.$password.'&Developer='.$devcode;
        $token = file_get_contents($url);
    }

    for($i = 1; $i < 15; $i++) {
        //获取手机号
        $url2 = 'http://api.ema666.com/Api/userSingleGetMessage?token='.$token.'&itemId='.$itemId.'&phone='.$phone.'&Code=UTF8';
        $rs = curl_get_contents($url2);
        var_dump($rs);
        if(empty($rs)) {
            echo "\n 获取手机短信信息出错，返回空";
            sleep(1);
        } elseif ($rs == 'False:Session 过期') {
            echo "\n 登陆session过期，重新登陆";
            //token
            $url = 'http://api.ema666.com/Api/userLogin?uName='.$username.'&pWord='.$password.'&Developer='.$devcode;
            $token = file_get_contents($url);
        } elseif($rs == 'False:暂时没有此项目号码，请等会试试...') {
            return null;
        } elseif($rs == 'False:此号码已经被释放') {
            echo "\n 此号码已经被释放，重新获取";
            //获取手机号
            $url2 = 'http://api.ema666.com/Api/userGetPhone?ItemId='.$itemId.'&token='.$token.'&Code=UTF8&Phone='.$phone;
            $rs = curl_get_contents($url2);

            var_dump($rs);
            continue;
        } elseif(stripos($rs, 'False:') === false) {
            return $rs;
        }
        sleep(5);
    }
    return false;
}

//封装curl操作
function curl_get_contents($url, $cookieFile = '', $data = array(), $headers = array(), $isproxy = false)
{
    //echo "\n 请求url : $url ";
    $proxyip = '';
    do {
        $ch = curl_init();
        if($isproxy) {
            $proxyip = proxyip::getIp();
            curl_setopt ($ch, CURLOPT_PROXY, $proxyip);
            echo "\n $url 使用代理 ： $proxyip ";
        } else {
            //curl_setopt ($ch, CURLOPT_PROXY, '127.0.0.1:8080');
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $isproxy ? 100 : 200); //设置超时时间,单位秒
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if(is_array($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            } 
        }
        if (!empty($cookieFile)) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        }
        $str = curl_exec($ch); //执行请求，获取响应内容
        $httpStatus = curl_getinfo($ch); //获取http的响应状态
        curl_close($ch);
        if($httpStatus['http_code'] == 0 || $httpStatus['http_code'] > 400 ) {
            echo "\n 请求失败 $url ，重试".$httpStatus['http_code'];
            continue;
        }
        if ($httpStatus['http_code'] == 200) {
            return $str;
        } else {
            // print_r($httpStatus);
            // echo "\n === $url \t".$str."\n====\n";
            return $httpStatus['http_code'];
        }
    } while(1);
    
}

function getRandDomain()
{
    $domain = array(
        'sharklasers.com',
        // "guerrillamail.info",
        // "grr.la",
        // "guerrillamail.biz",
        'guerrillamail.com',
        // "guerrillamail.de",
        // "guerrillamail.net",
        'guerrillamail.org',
        'guerrillamailblock.com',
        // "pokemail.net",
        'spam4.me',
    );

    return $domain[rand(0, count($domain) - 1)];
}

//获取图片验证码
function getPhotoCode($validPicUrl, $cookieFile, $typeid = 7100) {
    $picTmpName = dirname(__FILE__) . "/tmp/" . microtime(true) . ".jpg";

    $m = curl_get_contents($validPicUrl, $cookieFile);
    if(stripos($m, "<html") !== false) {
        preg_match('/\'uid_name\' : "([^"]*)"/', $m, $matches);
        preg_match('/\'uid_value\' : "([^"]*)"/', $m, $matches2);
        preg_match('/\'upi_name\' : "([^"]*)"/', $m, $matches3);
        preg_match('/\'upi_value\' : "([^"]*)"/', $m, $matches4);


        $cookieStr = "www.qunbi.pro\tFALSE\t/\tFALSE\t0\t".$matches['1']."\t".$matches2['1']."\n"."www.qunbi.pro\tFALSE\t/\tFALSE\t0\t".$matches3['1']."\t".$matches4['1']."\n";
        var_dump($cookieStr);

        file_put_contents($cookieFile, $cookieStr, FILE_APPEND);
        echo "\n 添加cookie后重试";
        var_dump($cookieStr);

        $m = curl_get_contents($validPicUrl, $cookieFile);
    }
    if (empty($m)) {
        return false;
    }

    file_put_contents($picTmpName, $m);

    $rs = dama($picTmpName, $typeid);
    //unlink($picTmpName);
    echo "\n 验证码确认：\t".$picTmpName."\t".$rs;
    return $rs;
}
function getRealPic() {

}

//将本地图片上传到打码平台
function dama($checkImg, $typeid = 7100)
{
    //echo "\n 请求一次打码接口 " . date("Y-m-d H:i:s") . "\n";
    $damaUrl = 'http://api.ruokuai.com/create.json';
    $ch      = curl_init();
    //$ccheckImg  = curl_file_create($checkImg);
    //$ccheckImg  = '@' . $checkImg;
    $postFields = array(
        'username' => 'bishuilantian',
        'password' => '123456abc',
        'typeid' => $typeid,   //4位的字母数字混合码   类型表http://www.ruokuai.com/pricelist.aspx
        'timeout' => 60,    //中文以及选择题类型需要设置更高的超时时间建议90以上
        'softid' => 67735,  //改成你自己的
        'softkey' => '2791d48e4d84425a929c5625b172df33',    //改成你自己的
        'image' => new \CURLFILE($checkImg)
    );


    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $damaUrl);
    curl_setopt($ch, CURLOPT_TIMEOUT, 65); //设置本机的post请求超时时间，如果timeout参数设置60 这里至少设置65 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_INFILESIZE, filesize($checkImg)); //CURLOPT_FILE 
    $result = curl_exec($ch);
    curl_close($ch); //'{"Result":"1","Id":"96b36f7f-f747-4517-89d8-c2a9addba2b0"}' 
    $result = json_decode($result, true);
    //print_r($result);
    if (!isset($result['Result'])) {
        return '';
    }
    $checkNo = $result['Result'];
    //echo "\n 打码： $checkImg \t ".$checkNo;
    return $checkNo;
}

$id = array();
//获取一个身份证信息
function getID() {
    global $id;

    if(empty($id)) {
        //在线获取身份证信息
        $url = 'http://id.8684.cn/ajax.php?act=getCardList&type=2';
        $content =  curl_get_contents($url);
        if(preg_match_all('@<span class="table-td1">([^<]*)</span><span class="table-td2">@', $content, $matches)) {
            $id = $matches['1'];
        }
    }
    return array_pop($id);
}


//轮循获取代理IP
class proxyip
{
    public static $ip;
    public static $rand     = 1;
    public static $num      = 50;
    public static $proxyurl = "http://192.168.8.18:12345/?opt=get&num=";
    public static function getIp()
    {
        if (empty(proxyip::$ip)) {
            proxyip::reflash();
        }

        return trim(array_shift(proxyip::$ip));
    }
    public static function reflash()
    {
        echo "\n" . date("Y-m-d H:i:s") . "重新获取代理ip\n";
        proxyip::$ip = curl_get_contents(proxyip::$proxyurl . proxyip::$num);
        proxyip::$ip = array_filter(explode("\n", proxyip::$ip));
    }
}

