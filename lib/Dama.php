<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2017/11/29
 * Time: 下午3:29
 */
class Dama
{

    public $username;

    public $password;

    public $softid;

    public $softkey;




    public function __construct()
    {
        $this->damaUrl = 'http://api.ruokuai.com/create.json';

        $this->username = 'playgamelxh';

        $this->password = md5('rk330318747');

        $this->softid = 42913;

        $this->softkey = 'd5fe1c8153e14bb1949af7b8f395d0a2';

        $this->timeout = 60;

        $this->reportUrl = 'http://api.ruokuai.com/reporterror.json';
    }

    //打码
    public function get($filename, $type = '1010')
    {
        $ch = curl_init();
        $postFields = array('username' => $this->username,
            'password' => $this->password,
            'typeid' => $type,	            //4位的数字混合码   类型表http://www.ruokuai.com/pricelist.aspx
            'timeout' => $this->timeout,	//中文以及选择题类型需要设置更高的超时时间建议90以上
            'softid' => $this->softid,	    //改成你自己的
            'softkey' => $this->softkey,	//改成你自己的
//            'image' => '@'.$filename,     //php低版本5.6以下 写法
            'image' => new CURLFile($filename) ////img.jpg是测试用的打码图片，4位的字母数字混合码,windows下的PHP环境这里需要填写完整路径
        );

        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout+5);	//设置本机的post请求超时时间，如果timeout参数设置60 这里至少设置65
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_URL, $this->damaUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    //报错
    public function sendError($id)
    {
        $ch = curl_init();
        $postFields = array(
            'username' => $this->username,	//改成你自己的
            'password' => $this->password,	//改成你自己的
            'softid'   => $this->softid,	//改成你自己的
            'softkey'  => $this->softkey,	//改成你自己的
            'id'       => $id
        );
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $this->reportUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);	//设置本机的post请求超时时间
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $result = curl_exec($ch);
        curl_close($ch);
        var_dump($result);
    }
}