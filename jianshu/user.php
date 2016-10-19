<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2016/10/14
 * Time: 下午2:30
 * Desc: 获取用户的关注专题/文集、关注用户、粉丝、文章
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
while (true) {
    $userArr = $db->get('user', '*', array('id[>]' => $id, 'LIMIT' => 1));
    if (!is_array($userArr) || empty($userArr)) {
        echo "Over!\r\n";
        break;
    }

    //关注专题/文集
    collection($userArr);

    //构造数据
//    $userArr = array('id' => 7231, 'name' => 'f93e84d2e162');

    //关注用户
    following($userArr);

    //粉丝
    follower($userArr);

    //文章
    articles($userArr);

    $id = $userArr['id'];
    file_put_contents('user.txt', $id);
    $db->clear();
}

//关注专题/文集
function collection($userArr)
{
    global $db, $curl;
    $page = 1;
    while (true) {
        $url = "http://www.jianshu.com/users/{$userArr['name']}/subscriptions?page={$page}";
        $curl->setUrl($url);
        $html = $curl->run();
        $p = '/<h4><a href="\/collection\/(.*?)"/';
        preg_match_all($p, $html, $match);
        if (is_array($match[1]) && !empty($match[1])) {
            foreach ($match[1] as $value) {
                $temp = $db->get('collection', '*', array('name' => $value));
                if (!is_array($temp) || empty($temp)) {
                    $id = getCollectionInfo($value);
                    $temp['id'] = $id;
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
        echo "collection:{$page}\r\n";
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
                return $db->insert('collection', $data);
            }
        }
    }
    return 0;
}

//关注用户
function following($userArr)
{
    global $db, $curl;
    $page = 1;
    while (true) {
        $url = "http://www.jianshu.com/users/{$userArr['name']}/following?_pjax=%23list-container&page={$page}";
        $curl->setUrl($url);
        $html = $curl->run();
        $p = '/<a target="_blank" href="\/users\/(.*?)">(?!<)/';
        preg_match_all($p, $html, $match);
//        print_r($match);
        if (is_array($match[1]) && !empty($match[1])) {
            foreach ($match[1] as $value) {
                $user = $db->get('user', '*', array('name' => $value));
                if (!is_array($user) || empty($user)) {
                    $id = $db->insert('user', array('name' => $value));
                    $user['id'] = $id;
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
        echo "following:",$page,"\r\n";
    }
}

//粉丝
function follower($userArr)
{
    global $db, $curl;
    $page = 1;
    while (true) {
        $url = "http://www.jianshu.com/users/{$userArr['name']}/followers?page={$page}";
        $curl->setUrl($url);
        $html = $curl->run();
        $p = '/<a target="_blank" href="\/users\/(.*?)">(?!<)/';
        preg_match_all($p, $html, $match);
//        print_r($match);
        if (is_array($match[1]) && !empty($match[1])) {
            foreach ($match[1] as $value) {
                $user = $db->get('user', '*', array('name' => $value));
                if (!is_array($user) || empty($user)) {
                    $id = $db->insert('user', array('name' => $value));
                    $user['id'] = $id;
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
        echo "follower:".$page,"\r\n";
    }
}

//文章
function articles($userArr)
{
    global $db, $curl;
    $page = 1;
    while (true) {
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
        echo "article:".$page,"\r\n";
    }
}

//获取文章详情
function getArticleInfo($name)
{
    global $db;
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