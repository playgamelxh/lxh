<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 15-7-13
 * Time: 下午4:47
 */
class SignupController extends Phalcon\Mvc\Controller
{
    public function indexAction()
    {

    }

    public function registerAction()
    {
        $user = new Users();print_r($this->request->getPost());
        $success = $user->save($this->request->getPost(), array('name', 'email'));
        var_dump($success);
        if($success){
            echo "Thanks for registering!";
        }else{
            echo "Sorry, the following problems were generated:";
            foreach($user->getMessages() as $message){
                echo $message->getMessage(),"<br/>";
            }
        }
        $this->view->disable();
    }
}