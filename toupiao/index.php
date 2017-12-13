<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2017/2/21
 * Time: 下午2:21
 */

define('ROOT', dirname(__FILE__));
define('APP', ROOT . '/app/');
define('LIB', ROOT . '/lib/');
define('VIEW', ROOT . '/view/');

$controller = isset($_GET['c']) ? $_GET['c'] : 'index';
$action     = isset($_GET['a']) ? $_GET['a'] : 'index';
define('CONTROLLER', $controller);
define('ACTION', $action);

$c = ucfirst($controller . 'Controller');
$a = $action . 'Action';

include_once LIB . 'Loader.php';
$loader = new Loader();

include_once APP . '/BaseController.php';
if (file_exists(APP . $c .".php")) {
    include_once APP . $c .".php";
} else {
    die('Controller not exists!');
}

$obj = new $c();
$obj->$a();

