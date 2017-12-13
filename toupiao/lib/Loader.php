<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2017/2/23
 * Time: 上午10:16
 */
class Loader
{
    public function __construct()
    {
        spl_autoload_register(array($this, 'loader'));
    }

    public function loader($name) {
        $libPath = array(
            LIB,
            VIEW,
        );
        foreach ($libPath as $path) {
            if (file_exists($path . $name . '.php')) {
                include_once $path . $name . '.php';
            }
        }
    }
}