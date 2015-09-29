<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 15-7-14
 * Time: 上午10:54
 */
class SessionController extends ControllerBase
{
    private function _registerSession($user)
    {
        $this->session->set('auth', array(
            'id'    => $user->id,
            'name'  => $user->name
        ));
    }

    public function startAction()
    {
        if($this->request->isPost()){
            $email      = $this->request->getPost('email');
            $password   = $this->request->getPost('password');
            $user = Users::findFirst(array(
                "(email=:email: or username=:email:) and password=:password:",
                'bind'=>array('email'=>$email,'password'=>sha1($password))
            ));
            if($user != false){
                $this->_registerSession($user);
                $this->flash->success('Welcome'.$user->name);
                return $this->forward('invoices/index');
            }
        }
    }
}