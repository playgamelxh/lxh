<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        echo "<h1>Store. Hello world!</h1>";
        echo Phalcon\Tag::linkTo('signup', 'Sign up Here!');
    }

    public function signupAction()
    {
        echo "Sign up!\r\n";
    }

}

