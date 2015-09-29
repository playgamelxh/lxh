<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 15-7-14
 * Time: 上午11:10
 */
use Phalcon\Acl;
use Phalcon\Events\Event; use Phalcon\Mvc\User\Plugin; use Phalcon\Mvc\Dispatcher;
class SecurityPlugin extends Plugin {
// ...
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {

    }
}
