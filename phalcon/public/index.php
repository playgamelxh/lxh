<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 15-7-13
 * Time: 下午2:35
 */
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    date_default_timezone_set('Asia/Shanghai');
    try {
        $loader = new \Phalcon\Loader();
        $loader->registerDirs(array(
            '../app/controllers',
            '../app/models'
        ))->register();

        $di = new Phalcon\DI\FactoryDefault();

        $di->set('view', function(){
            $view = new Phalcon\Mvc\View();
            $view->setViewsDir('../app/views/');
            return $view;
        });

        $di->set('url', function(){
            $url = new Phalcon\Mvc\Url();
            $url->setBasePath('/lxh/');
            return $url;
        });

        $di->set('db', function(){
            return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                'host'      => "localhost",
                'username'  => 'root',
                'password'  => '123456',
                'dbname'    => 'test'
            ));
        });

        $application = new Phalcon\Mvc\Application($di);
        echo $application->handle()->getContent();
    }catch (Phalcon\Exception $e){
        echo "PhalconException:", $e->getMessage();
    }