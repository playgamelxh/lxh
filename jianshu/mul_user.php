<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2016/10/21
 * Time: 下午3:51
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
$str = file_get_contents('user.txt');
$id = intval($str);
$worker_num = 100;
while (true) {
    $userArr = getInfo($id, $worker_num);
    if (!is_array($userArr) || empty($userArr)) {
        echo "Over!\r\n";
        break;
    }

    for($i = 0; $i < $worker_num; $i++)
    {
        $process = new swoole_process('callback_function', true);
        $pid = $process->start();
        $workers[$pid] = $process;
    }

    $i = 0;
    foreach($workers as $pid => $process)
    {
        $process->write(json_encode($userArr[$i]));
        echo $process->read(),"\r\n";
        $id = $userArr[$i]['id'];
        $i++;
    }

    for($i = 0; $i < $worker_num; $i++)
    {
        $ret = swoole_process::wait();
        $pid = $ret['pid'];
        unset($workers[$pid]);
    }
//    die();
    file_put_contents('user.txt', $id);
}

function getConnection()
{
    $db = new medoo(array(
        'database_type' => 'mysql',
        'database_name' => 'jianshu',
        'server' => 'localhost',
        'username' => 'root',
        'password' => '123456',
        'port' => 3306,
        'charset' => 'utf8mb4',
        'option' => array(PDO::ATTR_CASE => PDO::CASE_NATURAL)
    ));
    return $db;
}

function getInfo($id, $num)
{
    $db = getConnection();
    $userArr = $db->select('user', '*', array('id[>]' => $id, 'LIMIT' => $num));
    print_r($userArr);
//    print_r($db->error());
    $db->clear();
    return $userArr;
}

function callback_function(swoole_process $worker)
{
    $db = getConnection();

    $recv = $worker->read();

    $userArr = json_decode($recv, true);

    //关注专题/文集
    collection($userArr);

    //关注用户
    following($userArr);

    //粉丝
    follower($userArr);

    //文章
    articles($userArr);

    $worker->write(json_encode($db->error()));
//    sleep(10);
    $worker->exit(0);
}

//关注专题/文集
function collection($userArr)
{
    $curl = new Curl();
    $page = 1;
    while (true) {
        $db = getConnection();
        $url = "http://www.jianshu.com/users/{$userArr['name']}/subscriptions?page={$page}";
        $curl->setUrl($url);
        $html = $curl->run();
//        $p = '/<h4><a href="\/collection\/(.*?)"/';
        $p = '/<h4><a href="\/collection\/(.*?)">(.*?)<\/a><\/h4>/';
        preg_match_all($p, $html, $match);
        if (is_array($match[1]) && !empty($match[1])) {
            foreach ($match[1] as $key => $value) {
                $temp = $db->get('collection', '*', array('name' => $value));
                if (!is_array($temp) || empty($temp)) {
                    $id = getCollectionInfo($value, $match[2][$key]);
                    $temp['id'] = $id;
                } else {
                    if (empty($temp['title'])) {
                        $db->update('collection', array('title' => $match[2][$key]), array('id' => $temp['id']));
                    }
                }
                //关系表
                $temp = $db->get('collection_user_relation', '*', array('AND' => array('cid' => $temp['id'], 'uid' => $userArr['id'])));
                if (!is_array($temp) || empty($temp)) {
                    $db->insert('collection_user_relation', array('cid' => $temp['id'], 'uid' => $userArr['id']));
                }
                $db->clear();
            }
        } else {
            break;
        }
        $page++;
        echo "collection:{$page}|";
    }
}

//获取专题信息
function getCollectionInfo($str, $title)
{
    global $curl;
    $db = getConnection();
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
                $content = '';
                if (isset($info['content'])) {
                    $content = $info['content'];
                } else {
                    $p = '/<div class="description">(\s|\S)*?<\/div>\s*<div class="footer">/';
                    preg_match($p, $html, $ma);
                    $content = isset($ma[1]) ? $ma[1] : '';
                }
                $data = array(
                    'name' => $info['slug'],
                    'title' => $title,
                    'content' => $content,
                );
                return $db->insert('collection', $data);
            }
        }
    }
    return 0;
}

//关注用户
function following($userArr)
{
    global $curl;

    $page = 1;
    while (true) {
        $db = getConnection();
        $url = "http://www.jianshu.com/users/{$userArr['name']}/following?_pjax=%23list-container&page={$page}";
        $curl->setUrl($url);
        $html = $curl->run();
//        $p = '/<a target="_blank" href="\/users\/(.*?)">(.*?)(?!<)/';
        $p = '/<h4>\s*<a target="_blank" href="\/users\/(.*?)">(.*?)<\/a>\s*<\/h4>/';
        preg_match_all($p, $html, $match);
//        print_r($match);
        if (is_array($match[1]) && !empty($match[1])) {
            foreach ($match[1] as $key => $value) {
                $user = $db->get('user', '*', array('name' => $value));
                if (!is_array($user) || empty($user)) {
                    $id = $db->insert('user', array('name' => $value, 'title' => $match[2][$key]));
                    $user['id'] = $id;
                } else {
                    if (empty($user['title'])) {
                        $db->update('user', array('title' => $match[2][$key]), array('id' => $user['id']));
                    }
                }
                //关注关系
                $temp = $db->get('user_relation', '*', array('AND' => array('uid' => $user['id'], 'follower_uid' => $userArr['id'])));
                if (!is_array($temp) || empty($temp)) {
                    $db->insert('user_relation', array('uid' => $user['id'], 'follower_uid' => $userArr['id']));
                }
                $db->clear();
            }
        } else {
            break;
        }
        $page++;
        echo "following:",$page,"|";
    }
}

//粉丝
function follower($userArr)
{
    global $curl;
    $page = 1;
    while (true) {
        $db = getConnection();
        $url = "http://www.jianshu.com/users/{$userArr['name']}/followers?page={$page}";
        $curl->setUrl($url);
        $html = $curl->run();
//        $p = '/<a target="_blank" href="\/users\/(.*?)">(?!<)/';
        $p = '/<h4>\s*<a target="_blank" href="\/users\/(.*?)">(.*?)<\/a>\s*<\/h4>/';
        preg_match_all($p, $html, $match);
//        print_r($match);
        if (is_array($match[1]) && !empty($match[1])) {
            foreach ($match[1] as $key => $value) {
                $user = $db->get('user', '*', array('name' => $value));
                if (!is_array($user) || empty($user)) {
                    $id = $db->insert('user', array('name' => $value, 'title' => $match[2][$key]));
                    $user['id'] = $id;
                } else {
                    if (empty($user['title'])) {
                        $db->update('user', array('title' => $match[2][$key]), array('id' => $user['id']));
                    }
                }
                //关注关系
                $temp = $db->get('user_relation', '*', array('AND' => array('uid' => $userArr['id'], 'follower_uid' => $user['id'])));
                if (!is_array($temp) || empty($temp)) {
                    $db->insert('user_relation', array('uid' => $userArr['id'], 'follower_uid' => $user['id']));
                }
                $db->clear();
            }
        } else {
            break;
        }
        $page++;
        echo "follower:".$page,"|";
    }
}

//文章
function articles($userArr)
{
    global $curl;
    $page = 1;
    while (true) {
        $db = getConnection();
        $url = "http://www.jianshu.com/users/{$userArr['name']}/latest_articles?page={$page}";
        $curl->setUrl($url);
        $html = $curl->run();
        $p = '/<h4 class="title"><a target="_blank" href="\/p\/(.*?)">(.*?)<\/a><\/h4>/';
        preg_match_all($p, $html, $match);
        print_r($match);
        if (is_array($match[1]) && !empty($match[1])) {
            foreach ($match[1] as $key => $value) {
                $temp = $db->get('article', '*', array('name' => $value));
                if (!is_array($temp) || empty($temp)) {
                    $data = getArticleInfo($value);
//                    echo $value;print_r($data);
                    $data['uid']   = $userArr['id'];
                    $data['name']  = $value;
                    $data['title'] = trim($match[2][$key]);
                    $commentUserArr = $data['comment_user'];
                    unset($data['comment_user']);
                    $id = $db->insert('article', $data);
                    if (is_array($commentUserArr) && !empty($commentUserArr)) {
                        foreach ($commentUserArr as $name) {
                            $temp = $db->get('user', '*', array('name' => $name));
                            if (!is_array($temp) || empty($temp)) {
                                $db->insert('user', array('name' => $name));
                            }
                            $db->clear();
                        }
                    }
                }
                $db->clear();
            }
        } else {
            break;
        }
        $page++;
        echo "article:".$page,"|";
    }
}

//获取文章详情
function getArticleInfo($name)
{
    $db = getConnection();
    $curl = new Curl();
    $url = "http://www.jianshu.com/p/{$name}";
    $ip = rand(1,255).'.'.rand(1,255).'.'.rand(1,255).'.'.rand(1,255);
    $curl->setUrl($url);
    $head = array("CLIENT-IP:{$ip}", "X-FORWARDED-FOR:{$ip}");
    $curl->setHttpHeader($head);
    $html = $curl->run();
//    echo $url,"\r\n";
//    echo $html;
    $p = "/<script type='application\/json' data-name='note'>\s*(.*?)\s*<\/script>/";
    preg_match($p, $html, $match);
    $arr = isset($match[1]) ? json_decode($match[1], true) : array();
    $data['read_num'] = isset($arr['views_count']) ? $arr['views_count'] : 0;
    $data['comment_num'] = isset($arr['comments_count']) ? $arr['comments_count'] : 0;
    $data['like_num'] = isset($arr['likes_count']) ? $arr['likes_count'] : 0;
    $data['image_url'] = isset($arr['image_url']) ? $arr['image_url'] : 0;

    //文章详情
    $p = '/<div class="show-content">([\s\S]*?)<\/div>\s*<\/div>\s*<\/div>\s*<div class="visitor_edit"/';
    preg_match($p, $html, $match);
//    print_r($match);
    $data['content'] = isset($match[1]) ? $match[1] : '';

    //评论用户
    $data['comment_user'] = array();
    $p = "/<script type='application\/json' data-name='uuid'>\s*(.*?)\s*<\/script>/";
    preg_match($p, $html, $match);
//    print_r($match);
    $arr = isset($match[1]) ? json_decode($match[1], true) : array();
    $uuid = $arr['uuid'];
    if (!empty($uuid)) {
        $curl->setUrl("http://www.jianshu.com/notes/cae7cda41db4/mark_viewed.json");
        $curl->setPost($arr);
        $html = $curl->run();
        $temp = json_decode($html, true);
        //print_r($temp);
        if (is_array($temp['likes']) && !empty($temp['likes'])) {
            foreach ($temp['likes'] as $value) {
                $data['comment_user'][] = $value['user']['slug'];
            }
        }
    }
    return $data;
}