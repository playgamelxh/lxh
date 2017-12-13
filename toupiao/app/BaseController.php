<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2017/2/21
 * Time: 下午3:52
 */
class BaseController
{
   public $view;

    public function __construct()
    {

        $this->view = new View();
    }



    public function view($name = ACTION, $path = CONTROLLER)
    {
        //加载布局
        $layout = VIEW . $this->layout . '.php';
        if (file_exists($layout)) {
            include_once $layout;
        } else {
            die('Layout is not exists!');
        }


        //获取内容
        $file = VIEW . "{$path}/{$name}.html";
        if (file_exists($file)) {
            $content = file_get_contents($file);
        } else {
            die('View is not exists!');
        }

    }
}