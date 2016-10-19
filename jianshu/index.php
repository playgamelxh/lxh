<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2016/10/12
 * Time: 下午4:02
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

//采集专题
//getCollection();

//采集专题的关注用户
getColFollower();

//采集专题的文章

//采集专题的关注用户
function getColFollower()
{
    global $curl, $db;

    while (true) {
        $resArr = $db->get('collection', '*', array('ORDER' => 'is_caiji asc'));
        if (is_array($resArr) && !empty($resArr)) {
            getFlower($resArr['id'], $resArr['name']);
        } else {
            echo "Over!\r\n";
            break;
        }
    }
}

//获取flower
function getFlower($id, $name)
{
    global $curl, $db;
    $page = 1;
    while (true) {
        $url = "http://www.jianshu.com/collection/{$name}/subscribers?page={$page}";
        $curl->setUrl($url);
        $html = $curl->run();
//        echo $html;
        $p = '/<a class="avatar" href="\/users\/(.*?)"><img/';
        preg_match_all($p, $html, $match);
//        print_r($match);die();
        if (isset($match[1]) && !empty($match[1])) {
            foreach ($match[1] as $value) {
                $resArr = $db->get('user', '*', array('name' => $value));
                if (!is_array($resArr) || empty($resArr)) {
                    $uid = $db->insert('user', array('name' => $value));
                    if ($uid > 0) {
                        $resArr['id'] = $uid;
                    } else {
                        print_r($db->error());
                        die();
                    }
                }
                //写入关联表
                $temp = $db->get('collection_user_relation', '*', array('AND' => array('cid' => $id, 'uid' => $resArr['id'])));
                if (!is_array($temp) || empty($temp)) {
                    $data = array(
                        'cid' => $id,
                        'uid' => $resArr['id'],
                    );
                    $db->insert('collection_user_relation', $data);
                }
                //清理占用内存
                $db->clear();
            }
        } else {
            break;
        }
        $page++;
        echo $page,"\r\n";
    }
    $db->update('collection', array('is_caiji[+]' => 1), array('id' => $id));
}

//采集专题
function getCollection()
{
    global $curl;
    $page = 1;
    while (true) {
        //热门
        //$url = "http://www.jianshu.com/collections?category_id=53&page={$page}&_=1476265581231";
        //推荐
        //$url = "http://www.jianshu.com/collections?order_by=score&page={$page}&_=1476259795034";
        //城市
        $url = "http://www.jianshu.com/collections?category_id=69&page={$page}&_=1476346480280";

        $curl->setUrl($url);
        $html = $curl->run();
        $p = '/"\/collection\/(.*?)\\"/';
        preg_match_all($p, $html, $math);
        if (isset($math[1]) && !empty($math[1])) {
            $temp = array_unique($math[1]);
//        print_r($temp);
            foreach ($temp as $value) {
                getCollectionInfo($value);
            }
        } else {
            echo "Over!\r\n";
            break;
        }
        $page++;
        echo $page,"\r\n";
    }
}

//获取专题信息
function getCollectionInfo($str)
{
    global $curl, $db;
    $url = "http://www.jianshu.com/collection/{$str}";
    $curl->setUrl($url);
    $html = $curl->run();
    $p = "/<script type='application\/json' data-name='collection'>\s*(.*?)\s*<\/script>/";
    preg_match_all($p, $html, $match);
//    print_r($match);
    if (isset($match[1][0])) {
        $info = json_decode($match[1][0], true);
        if (is_array($info) && !empty($info)) {
            $resArr = $db->get('collection', '*', array('name' => $info['slug']));
            if (!is_array($resArr) || empty($resArr)) {
                $data = array(
                    'name' => $info['slug'],
                    'content' => isset($info['content']) ? $info['content'] : '',
                );
                $db->insert('collection', $data);
            }
        }
    }
}