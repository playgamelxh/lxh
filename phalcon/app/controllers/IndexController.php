<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 15-7-13
 * Time: 下午4:30
 */
class IndexController extends Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        echo "<h1>Hello world!</h1>";
    }

    public function infoAction()
    {
        phpinfo();
    }

}
