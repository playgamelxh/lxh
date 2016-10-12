<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 16/9/15
 * Time: 上午12:03
 */

date_default_timezone_set('Asia/Shanghai');
include "Curl.php";

//$url = "http://stock.eastmoney.com/news/1839,20160914664156377.html";
$url =  $_POST['url'];

$resStr = getHtml($url);
echo $resStr;

function getHtml($url)
{
    $curlObj = new Curl();
    $curlObj->setUrl($url);
    $html = $curlObj->run();

    //标题
    $p = '/<h1>(.*?)<\/h1>/';
    preg_match($p, $html, $match);
    $title = isset($match[1]) ? $match[1] : '';
    //echo $title;

    //日期
    $p = '/<div class="time">(.*?)<\/div>/';
    preg_match($p, $html, $match);
    $time = isset($match[1]) ? $match[1] : 0;
    if (!empty($time)) {
        $time = str_replace(array('年','月'), '', $time);
        $time = str_replace('日', '', $time);
        $time = strtotime($time);
    }
    //echo $time;

    //摘要
    $p = '/<div class="b-review">(.*?)<\/div>/';
    preg_match($p, $html, $match);
    $short = isset($match[1]) ? $match[1] : '';
    //echo $short;

    //内容
    $p = '/<!--文章主体-->([\s|\S]*?)<!--原文标题-->/';
    preg_match($p, $html, $match);
    $content = isset($match[1]) ? $match[1] : '';
    //echo $content;

    //分页
    while(true) {
        $p = '/<a href="[^<]*?(_(\d+)\.html)" target="_self" class="page-btn">下一页<\/a>/';
        preg_match($p, $html, $match);
        $next = isset($match[1]) ? $match[1] : '';
        if ($next) {
            $url = str_replace('.html', $match[1], $url);
            $curlObj->setUrl($url);
            $html = $curlObj->run();

            //内容
            $p = '/<!--文章主体-->([\s|\S]*?)<!--原文标题-->/';
            preg_match($p, $html, $match);
            $content .= isset($match[1]) ? $match[1] : '';
        } else {
            break;
        }
    }
//    echo str_replace("　　", "\n", strip_tags($content));die();
    $data = array(
        'title' => $title,
        'time'  => $time,
        'short' => $short,
        'content' => str_replace("　　", "\n", strip_tags($content)),
    );

    echo json_encode($data);
}