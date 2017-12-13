<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2017/2/24
 * Time: 下午2:13
 * Desc: 简书 批量关注
 */

ini_set('display_errors', 'ON');
error_reporting(E_ALL);

header("Content-type:text/html;charset=utf-8");
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__FILE__));
include(ROOT_PATH . '/medoo.php');
include(ROOT_PATH . '/Curl.php');

$db = new medoo(array(
    'database_type' => 'mysql',
    'database_name' => 'jianshu',
    'server' => 'localhost',
    'username' => 'root',
    'password' => '123456',
    'port' => 3306,
    'charset' => 'utf8',
    'option' => array(PDO::ATTR_CASE => PDO::CASE_NATURAL)
));
$curl = new Curl();

//$id = 0;
//while (true) {
//    $resArr = $db->get('user', '*', array('id>' => $id, 'is_watch' => 0));
//    if (is_array($resArr)) {
//        $id = $resArr['id'];
//    } else {
//        die("Over!\r\n");
//    }
//
//    watch($resArr);
//}
//
//function watch($resArr)
//{
//    global $db,$curl;
//
//    $curl->setUrl("http://www.jianshu.com/u/{$resArr['name']}");
//    $html = $curl->run();
//}

$cookie = "_gat=1; remember_user_token=W1syODk3Mjc0XSwiJDJhJDEwJHNCUUNrMUJwUnYwcUJQOEI0RmpDY08iLCIxNDg4MjQ1NjYwLjgzNTM0OTYiXQ%3D%3D--90a60e70468814ff5cf52d89c8c176c91910b2c5; CNZZDATA1258679142=831777588-1488241750-null%7C1488241750; _ga=GA1.2.1727628866.1488245634; _session_id=QVdlVjNLQjZ6dUxuZktZd2llQlgzY0UxOWlaRXh6SmMrNlIxdjIwZlFGZC9HeXB6OU80ZTMzc3RBZmpJSE41Uk9BVDBuOVZJK1ZjeXNBalJ1VFVvSGJucVlES25OckppbUVUb3g5RUo2Q2JXRWRRazdNWDlrOXRkaitCM0p4NmUzTjZLVVB0YUt5MUVyc1MxTXhYR1N6eTdiQWJGTjZKNStXa0RyMTdzQTNCenBiUFVBMnF0REhYa2VDa0lQMUFiaUEyMFNMUHNhUzlqSGtuRXpqSzhXaEJzK3RxOVQvSlJMRC9uZmN6ZUxXMjArL2lqaHhwc0dOcXFmNVM4SjRZS1Vncy9tbjFnTVZwUXJEam9WWngrdWIvZE1kZm1KTkphdC9XWklWTktYQUFPNVNHOVNhWWFBemFQTDdieElGS1F3R05oK1BqTXRJUnVlWkJ1ZVpxdnhhSC92b1ArQVVNRmNZeUhtRHplcDhEUGEvVGxWd1U0R0tVZ2Q1TXNXZDJ1SFRGcVhCZGVTZFBxWDhlZVZxVlJVVmhwaDB1TVNXTmlLZlp1SjBKbHpBMD0tLUxJRWZhYlhVb3cxbjlUU3Q2Vjl0dEE9PQ%3D%3D--dc444e601828a22e1854b41225ed53f48ab96dc0";
for($i = 5000;$i<=5990;$i++) {
    $curl->setUrl("http://www.jianshu.com/users/{$i}/toggle_like");
    $curl->setHttpHeader(
        array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Cookie: '.$cookie,

        )
    );
    $curl->setPost('{"fuckuc":"1"}');
    $html = $curl->run();
    echo $html."\r\n";
}