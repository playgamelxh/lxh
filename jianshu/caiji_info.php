<?php
/**
 * Created by PhpStorm.
 * User: Jam
 * Date: 2015/4/2
 * Time: 18:21
 * Desc: 采集详情
 */

header("Content-type:text/html;charset=utf-8");
ini_set('display_errors', 'ON');
error_reporting(E_ALL);
date_default_timezone_set('PRC');
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__FILE__));

include(ROOT_PATH . '/../lib/CurlMulti/Core.php');
include(ROOT_PATH . '/../lib/CurlMulti/Exception.php');
include(ROOT_PATH . '/../lib/medoo.php' );
include(ROOT_PATH . '/../lib/RabbitMQ.php' );
include(ROOT_PATH . '/../lib/Curl.php');

$env = isset($argv[1]) ? $argv[1] : 'dev';
if($env == 'dev') {
    include(ROOT_PATH . '/../config/config_local.php');
}elseif($env == 'test'){
    include(ROOT_PATH . '/../config/config_test.php');
} elseif($env == 'pro') {
    include(ROOT_PATH . '/../config/config.php');
}else{
    die('config');
}

$db = new medoo($dbConfig);
$dbCom = new medoo($dbcomConfig);
$rabbitmqObj = new RabbitMQ($rabbitmqConfig);

$ip = '';
$baseUrl = 'http://139.129.76.139/enterprises/w1/getDataById?id=';
$baseUrl = "http://app.qichacha.com/enterprises/new/a1/getData";
//初始化项目
$curl = new CurlMulti_Core ();
$curl->opt[CURLOPT_TIMEOUT] = 10;
$curl->maxThread = 2;
$curl->maxTry = 0;
$curl->cbTask = array('addCollectTask', array());
$curl->cbInfo = 'getStatusInfo';
$curl->start();


//初始化采集任务，取队列构造
function addCollectTask()
{
    global $curl, $baseUrl, $rabbitmqObj, $db, $dbCom, $dbConfig, $dbcomConfig;

    $list = array();
    while (count($list) < $curl->maxThread) {
        $rs = $rabbitmqObj->get('combusiness_unique1_ssdb');
        if (!empty($rs)) {
            $resArr = json_decode($rs, true);
            if ($resArr['id'] == 44937651) {
                continue;
            }
            if(!isset($resArr['unique_id'])){
//                print_r($resArr);
                continue;
            }
//            $resArr['unique'] = getId($resArr['unique_id']);
            if(!empty($resArr['unique_id'])){
                $list[] = $resArr;
            }
        }
    }

    if (!empty($list)) {
        foreach ($list as $v) {

            //先判断数据是否已经采集，然后再采
            $tempIp = rand(1,255).'.'.rand(1,255).'.'.rand(1,255).'.'.rand(1,255);
//            $tempIp = '1.192.121.217';
            $agent = array(
                "Mozilla/5.0 (Linux; U; Android 2.3.6; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko)",
                "Mozilla/5.0 (Linux; U; Android 2.4.5; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko)",
                "Mozilla/5.0 (Linux; U; Android 2.5.7; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko)",
                "Mozilla/5.0 (Linux; U; Android 2.6.9; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko)",
                "Mozilla/5.0 (Linux; U; Android 2.7.3; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko)",
                "Mozilla/5.0 (Linux; U; Android 2.8.1; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko)",
                "Mozilla/5.0 (Linux; U; Android 2.9.6; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko)",
                "Mozilla/5.0 (Linux; U; Android 3.3.6; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko)",
                "Mozilla/5.0 (Linux; U; Android 4.3.8; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko)",
                "Mozilla/5.0 (Linux; U; Android 5.0.1; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko)",
                "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B176 MicroMessenger/4.3.2",
                "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B176 MicroMessenger/4.1.2",
                "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B176 MicroMessenger/4.2.2",
                "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B176 MicroMessenger/4.0.2",
                "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B176 MicroMessenger/5.0.2",
                "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B176 MicroMessenger/5.1.2",
                "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B176 MicroMessenger/5.2.2",
                "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B176 MicroMessenger/5.3.2",
                "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B176 MicroMessenger/5.4.2",
                "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B176 MicroMessenger/5.5.2",
                "User-Agent:Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11"
            );
            $agent = array_rand($agent);
            $header = array(
                "CLIENT-IP:{$tempIp}",
                "X-FORWARDED-FOR:{$tempIp}",
                "User-Agent:{$agent}",
            );
            $token = md5($v['unique_id'] . '2c2c401f52b79fbc9740168d134b8954');
            $url = "?unique={$v['unique_id']}&token={$token}&user=";
            $task = array(
                'url' => $baseUrl . $url,
                'args' => array('id' => $v['id'], 'unique' => $v['unique_id']),
                //'opt' => array(CURLOPT_PROXY => $ip, CURLOPT_TIMEOUT=>5)
                'opt' => array(CURLOPT_TIMEOUT=>5,
                    CURLOPT_HTTPHEADER => $header
                )
            );
            $curl->add($task, 'cbProcess', 'cbFail');
        }
    }

    $db->clear();
    $dbCom->clear();

    unset($db);
    unset($dbCom);
    $db = new medoo($dbConfig);
    $dbCom = new medoo($dbcomConfig);
}

//回调采集成功后业务处理
function cbProcess($r, $args)
{
    global $curl, $db, $dbCom, $rabbitmqObj;
    $db->clear();
    if ($r['info']['http_code'] == 200) {
//        print_r($r);die();
        $res = json_decode($r['content'], true);
        if (isset($res['status']) && $res['status'] == 1) {
            $id = $args['id'];
//            echo "detail:id:$id\r\n";
            $data = $res['data']['Company'];
            if(isset($data['Area'])){
                $update = array(
                    'province_store' => $data['Area']['Province'],
                    'province'       => province($data['Area']['Province']),
                    'city'           => $data['Area']['City'],
                    'county'         => $data['Area']['County']
                );
                $db->update('company', $update, array('id'=>$id));
            }
            //如果注册号为空，添加上
            if(empty($comArr['reg_id']) || $comArr['reg_id']==1){
                $db->update('company', array('reg_id' => $data['No']), array('id'=>$id));
            }
            //更改状态
            $db->update('company', array('status' => 2, 'updatetime' => time()), array('id'=>$id));

            //数据入库
            $cid = insert($data);
            if($cid>0) {
                //打入更新缓存队列
                $rabbitmqObj->set('combusiness', 'cbupdate', $cid, 'ssdb');
            }
        }else{
            print_r($res);
        }
    }else{
        print_r($r);
//        echo 2, "\r\n";
    }
}

//回调采集失败后业务处理
function cbFail($r, $args)
{
    global $curl, $ip;
//    echo "Fail:{$ip}\r\n";
//    echo $r['info']['url'],"\r\n";
}

//查看采集各种状态信息 流量 等
function getStatusInfo($r)
{
    if (mt_rand(1, 10) == 1) {
        //print_r($r['all']);
    }
}

//想数据库写入数据
function insert($data)
{
    global $dbCom;
    $temp = $dbCom->get("cb_combusiness", array('cid'), array('comname' => $data['Name'], 'LIMIT' => 1));
    if(!empty($temp['cid'])){
        //数据存在不采集
        //根据注册号 判断是否有重复
        if(!empty($data['No']) && (strlen($data['No'])==13 || strlen($data['No'])==15)){
            $temp = $dbCom->get("cb_combusiness", array('cid'), array('regno' => $data['No'], 'LIMIT' => 1));
            if(is_array($temp) && !empty($temp) && $temp['cid']>0){
                $update = array(
                    'comname'       => $data['Name'],
                    'uniqueno'      => unique($data['Name'], $data['No']),
                    'scope'         => $data['Scope'],
                    'state'         => $data['Status'],
                    'comtype'       => $data['EconKind'],
                    'regcapital'    => regcap($data['RegistCapi']),
                    'address'       => $data['Address'],
                    'businessstart' => strtotime($data['TermStart']),
                    'businessend'   => strtotime($data['TeamEnd']),
                    'checkdate'     => strtotime($data['CheckDate']),
                    'regagency'     => $data['BelongOrg'],//工商局
                    'legal'         => $data['OperName'],
                    'startdate'     => strtotime($data['StartDate']),
                    'enddate'       => strtotime($data['EndDate']),
                    'cate1'         => 0,
                    'cate2'         => 0,
                    'uptime'        => strtotime($data['UpdatedDate']),
                    'areaid'        => getAreaId($data['No']),
                    'RegistCapi'    => $data['RegistCapi'],
                );
                $update = area($update, $data);
                $dbCom->update("cb_combusiness", $update, array('cid' => $temp['cid']));
            }
        }
        return $temp['cid'];
    }

    //写入主表
    $insert = array(
        'comname'       => $data['Name'],
        'regno'         => $data['No'],
        'uniqueno'      => unique($data['Name'], $data['No']),
        'scope'         => $data['Scope'],
        'state'         => $data['Status'],
        'comtype'       => $data['EconKind'],
        'regcapital'    => regcap($data['RegistCapi']),
        'address'       => $data['Address'],
        'businessstart' => strtotime($data['TermStart']),
        'businessend'   => strtotime($data['TeamEnd']),
        'checkdate'     => strtotime($data['CheckDate']),
        'regagency'     => $data['BelongOrg'],//工商局
        'legal'         => $data['OperName'],
        'startdate'     => strtotime($data['StartDate']),
        'enddate'       => strtotime($data['EndDate']),
        'cate1'         => 0,
        'cate2'         => 0,
        'uptime'        => strtotime($data['UpdatedDate']),
        'areaid'        => getAreaId($data['No']),
        'RegistCapi'    => $data['RegistCapi'],
    );
    //bug处理
    empty($insert['scope']) && $insert['scope'] = '无';
    empty($insert['regno']) && $insert['regno'] = '0';
//    empty($insert['comtype']) && $insert['comtype'] = '无';
//    empty($insert['state']) && $insert['state'] = '无';
//    empty($insert['regcapital']) && $insert['regcapital'] = 0;
//    empty($insert['address']) && $insert['address'] = '无';
    empty($insert['regagency']) && $insert['regagency'] = '无';
//    empty($insert['legal']) && $insert['legal'] = '无';

    //省 市 区处理
    $insert = area($insert, $data);
    $cid = $dbCom->insert('cb_combusiness', $insert);
    if($cid<=0){
        print_r($dbCom->error());
        die();
        return;
    }

    //分公司
    if(is_array($data['Branches']) && !empty($data['Branches'])){
        $insert = array();
        foreach($data['Branches'] as $value){
            $insert[] = array(
                'cid'      => $cid,
                'regno'    => $value['RegNo'],
                'comname'  => $value['Name'],
            );
        }
        if(is_array($insert) && !empty($insert)){
            $dbCom->insert('cb_branch', $insert);
        }
    }

    //联系人
    if(isset($data['ContactInfo']) && ($data['ContactInfo']) && !empty($data['ContactInfo'])){
        $insert = array(
                'cid'           => $cid,
                'WebSite'       => isset($data['ContactInfo']['WebSite']) ? json_encode($data['ContactInfo']['WebSite'], JSON_UNESCAPED_UNICODE) : json_encode(array()),
                'PhoneNumber'   => isset($data['ContactInfo']['PhoneNumber']) ? $data['ContactInfo']['PhoneNumber'] : '',
                'Email'         => isset($data['ContactInfo']['Email']) ? $data['ContactInfo']['Email'] : array(),
        );
        if(is_array($insert) && !empty($insert)){
            $dbCom->insert('cb_com_contact', $insert);
        }
    }

    //变更记录
    if(is_array($data['ChangeRecords']) && !empty($data['ChangeRecords'])){
        $insert = array();
        foreach($data['ChangeRecords'] as $value){
            $insert[] = array(
                'cid'      => $cid,
                'infoname' => $value['ProjectName'],
                'oldvalue' => $value['BeforeContent'],
                'newvalue' => $value['AfterContent'],
                'uptime'   => strtotime($value['ChangeDate']),
            );
        }
        if(is_array($insert) && !empty($insert)){
            $dbCom->insert('cb_changelog', $insert);
        }
    }

    //主要员工
    if(is_array($data['Employees']) && !empty($data['Employees'])){
        $insert = array();
        foreach($data['Employees'] as $value){
            $insert[] = array(
                'cid'    => $cid,
                'name'   => $value['Name'],
                'job'    => $value['Job'],
                'certno' => isset($value['CerNo']) && !empty($value['CerNo']) ? $value['CerNo'] : '无',
            );
        }
        if(is_array($insert) && !empty($insert)){
            $dbCom->insert('cb_employee', $insert);
        }
    }

    //合伙人
    if(is_array($data['Partners']) && !empty($data['Partners'])){
        $insert = array();
        foreach($data['Partners'] as $value){
            $insert[] = array(
                'cid'          => $cid,
                'stockholder'  => $value['StockName'],
                'stocktype'    => $value['StockType'],
                'stockpercent' => $value['StockPercent'],
                'identifyname' => $value['IdentifyType'],
                'identifyno'   => $value['IdentifyNo'],
                'shouldcapi'   => $value['ShouldCapi'],
                'shoulddate'   => is_string($value['ShoudDate']) ? strtotime($value['ShoudDate']) : '',
                'shouldtype'   => $value['InvestType'],
                'realtype'     => $value['InvestType'],
                'realcapi'     => $value['RealCapi'],
                'realdate'     => is_string($value['CapiDate']) ? strtotime($value['CapiDate']) : '',
            );
            if(!is_string($value['ShoudDate']) || !is_string($value['CapiDate'])){
//                print_r($value);
            }
        }
        if(is_array($insert) && !empty($insert)){
            $dbCom->insert('cb_partner', $insert);
        }
    }
    return $cid;
}

//转换注册资本
function regcap($regcapStr)
{
    $regcap = 0;
    $temp = str_replace(array(',', '，'), '', $regcapStr);
    if(strpos($temp, '万') != false) {
        $regcap = floatval($temp);
    } else {
        $regcap = floatval($temp)/10000;
    }
    return $regcap;
}

//自定义唯一unique
function unique($name, $reg_id)
{
    $salt = '48$%Yd&s4i';
    $str = md5($name.$reg_id.$salt);
    return substr($str, 3, 26);
}

function getId($unique)
{
    $url = "http://app.qichacha.com/enterprises/new/getShareURL?unique=".$unique;
    $tempIp = rand(1,255).'.'.rand(1,255).'.'.rand(1,255).'.'.rand(1,255);
    $header = array(
        "CLIENT-IP:{$tempIp}",
        "X-FORWARDED-FOR:{$tempIp}",
    );
    $curl = new Curl();
    $curl->setUrl($url);
    $resStr = $curl->run();
    preg_match('/share\/(.*?)"/', $resStr, $match);
    if(isset($match[1]))
        return $match[1];
    else
        return '';
}

/**
 * 功能描述 根据注册号 获取地区编号id
 */
function getAreaId($regno)
{
    if (empty($regno)) {
        return 0;
    }
    $regno = trim($regno);
    $code = substr($regno, 0, 6);
    if (!is_numeric($code) || !in_array(strlen($regno), array(13, 15))) {
        return 0;
    }
    $areaid = findAreaById($code);
    if ($areaid==0) {
        $code = intval(substr($code, 0, 4))*100;
        $areaid = findAreaById($code);
        if ($areaid==0) {
            $code = intval(substr($code, 0, 2))*10000;
            $areaid = findAreaById($code);
        }
    }
    return $areaid;
}
function findAreaById($areaid)
{
    global $dbCom;
    $resArr = $dbCom->get('area', '*', array('id' => $areaid));
    if (empty($resArr)) {
        return 0;
    } else {
        return $resArr['id'];
    }
}
function findAreaByName($name)
{
    global $dbCom;
    $resArr = $dbCom->get('area', '*', array('areaname' => $name));
    if (empty($resArr)) {
        return 0;
    } else {
        return $resArr['id'];
    }
}

//处理省
function province($str)
{
    $arr = array(
        "总局"  => "CN",
        "北京"  => "BJ",
        "天津"  => "TJ",
        "河北"  => "HB",
        "山西"  => "SX",
        "内蒙古" => "NMG",
        "辽宁"  => "LN",
        "吉林"  => "JL",
        "黑龙江" => "HLJ",
        "上海"  => "SH",
        "江苏"  => "JS",
        "浙江"  => "ZJ",
        "安徽"  => "AH",
        "福建"  => "FJ",
        "江西"  => "JX",
        "山东"  => "SD",
        "广东"  => "GD",
        "广西"  => "GX",
        "海南"  => "HAIN",
        "河南"  => "HEN",
        "湖北"  => "HUB",
        "湖南"  => "HUN",
        "重庆"  => "CQ",
        "四川"  => "SC",
        "贵州"  => "GZ",
        "云南"  => "YN",
        "西藏"  => "XZ",
        "陕西"  => "SAX",
        "甘肃"  => "GS",
        "青海"  => "QH",
        "宁夏"  => "NX",
        "新疆"  => "XJ"
    );
    $temp = rtrim($str, "省市");
    if (isset($arr[$temp])) {
        return $arr[$temp];
    }
    foreach ($arr as $key => $value) {
        $p = "/{$key}/";
        if (preg_match($p, $str)) {
            return $value;
        }
    }
}
function findAreaByNameParent($name='', $p = 0)
{
    global $db;

    if(empty($name))
        return 0;
    $resArr = $db->get('area', '*', array('AND' => array('areaname' => $name, 'parentid' => $p)));
    if (empty($resArr)) {
        return 0;
    } else {
        return $resArr['id'];
    }
}
function area($arr, $data)
{
    global $db;
    $areaArr = array(
        "CN" => 110000,
        "BJ" => 110000,
        "TJ" => 120000,
        "HB" => 130000,
        "SX" => 140000,
        "NMG" => 150000,
        "LN" => 210000,
        "JL" => 220000,
        "HLJ" => 230000,
        "SH" => 310000,
        "JS" => 320000,
        "ZJ" => 330000,
        "AH" => 340000,
        "FJ" => 350000,
        "JX" => 360000,
        "SD" => 370000,
        "HEN" => 410000,
        "HUB" => 420000,
        "HUN" => 430000,
        "GD" => 440000,
        "GX" => 450000,
        "HAIN" => 460000,
        "CQ" => 500000,
        "SC" => 510000,
        "GZ" => 520000,
        "YN" => 530000,
        "XZ" => 540000,
        "SAX" => 610000,
        "GS" => 620000,
        "QH" => 630000,
        "NX" => 640000,
        "XJ" => 650000
    );
    $comArr = $db->get('company', array('province', 'city', 'county'), array('comname' => $data['Name'], 'LIMIT' => 1));
    if(is_array($comArr) && empty($comArr)){
        $p = trim($comArr['province']);
        if(isset($areaArr[$p])){
            $arr['province'] = $areaArr[$p];
        }
    } else {
        if(isset($data['Area']) && !empty($data['Area'])) {
            $temp = findAreaByNameParent($data['Area']['Province'], 0);
            if($temp>0){
                $arr['province'] = $temp;
            }
        }
    }
    $arr['areaid'] = isset($arr['province']) ? $arr['province'] : 0;
    //市
    if(!empty($comArr['city'])){
        $city = findAreaByNameParent($data['Area']['City'], $arr['province']);
        if($city>0){
            if($arr['province'] <= 0) {//防止部分省份有问题，导致更新出错
                $arr['province'] = intval($city/10000)*10000;
            }
            $arr['city'] = $city;
            $arr['areaid'] = $city;
            //区
            if(!empty($comArr['county'])){
                $aid = findAreaByNameParent($data['Area']['County'], $city);
                if($aid>0){
                    $arr['zone'] = $aid;
                    $arr['areaid'] = $aid;
                }
            }
        }
    }
    //如果没有地区信息，根据注册号来生成
    if ($arr['province'] <= 0 && $arr['city'] <= 0) {
        $areaid = 0;
        if(mb_strlen($arr['regno'], 'utf-8') == 13 || mb_strlen($arr['regno'], 'utf-8') == 15){
            $areaid = substr($arr['regno'], 0, 6);
        }
        //通过地址 获取地区编号
        if($areaid<=0){
            $areaid = getZoneByAddress($arr['address'], $arr['regagency'], $arr['comname']);
            if(strlen($areaid)>6){
                $areaid = substr($areaid, 0, 6);
            }
        }
        if($areaid <= 0){
            $arr['province']  = 0;
            $arr['city']      = 0;
            $arr['zone']      = 0;
            $arr['areaid']    = 0;
            return $arr;
        } else {
            //总局数据
            if($areaid == 100000) {
                $areaid = 110000;
            }
        }
        $arr['areaid'] = 0;
        $p = findArea(intval($areaid/10000)*10000);
        if($p > 0){
            $arr['province'] = $p;
            $arr['areaid']   = $p;
        }
        $temp = substr($areaid, 0, 2);
        $city = 0;
        if (in_array($temp, array(11, 12, 31, 50))) {
            $city = intval($temp . '0100');
        } else {
            $city = intval($areaid/100)*100;
        }
        $city = findArea($city);
        if($city > 0){
            $arr['city'] = $city;
            $arr['areaid'] = $city;
        }
        $areaid = findArea($areaid);
        if($areaid > 0){
            $arr['zone'] = $areaid;
            $arr['areaid'] = $areaid;
        }
    }

    return $arr;
}

function findArea($areaid)
{
    global $db;
    $resArr = $db->get('area', '*', array('id' => $areaid));
    if (empty($resArr)) {
        return 0;
    } else {
        return $resArr['id'];
    }
}

function getZoneByAddress($address, $regagency, $comname)
{
//    echo $address,'---',$regagency;
    $zone = '';
    //匹配地址
//    $p = '/[^省](.*?市)/';
//    $p = '/([^省](.*?市)|[^市](.*?区|.*?县))/';
    //去掉空格
    $address = str_replace(' ', '', $address);
    $regagency = str_replace(array(' ', '市场'), '', $regagency);

    $pArr = array(
        '/[^省](.*?市)/',
        '/(.*?省)/',
        '/(.*?县|.*?区)/',
    );
    foreach($pArr as $p){

        preg_match($p, $address, $match);
        print_r($match);
        if(!empty($match)) {
            $zone = $match[0];
            if (!empty($zone)) {
                $areaid = findAreaByName($zone);
                if($areaid>0)
                    return $areaid;
            }
        }

        //匹配工商局地址
        preg_match($p, $regagency, $match);
        print_r($match);
        $zone = isset($match[0]) ? $match[0] : '';
        if (!empty($zone)) {
            $areaid = findAreaByName($zone);
            if($areaid>0)
                return $areaid;
        }
    }
    //区域匹配，先不错县区级别
    global $areaArr;
    if(!is_array($areaArr) || empty($areaArr)) {
        global $db;
        $areaArr = $db->select('area', array('id', 'shortname'), array('level[<=]' => 2));
    }

    foreach ($areaArr as $value){
        if(strpos($address, $value['shortname']) === 0){
            return $value['id'];
        }
        if(strpos($regagency, $value['shortname']) === 0){
            return $value['id'];
        }
    }
    //企业名称 地址判断先 去掉 分公司的情况
    //注意分公司的情况
    if(strpos($comname, '分公司') === false){
        foreach ($areaArr as $value) {
            if (strpos($comname, $value['shortname']) === 0) {
                return $value['id'];
            }
        }
    }
    return 0;
}

