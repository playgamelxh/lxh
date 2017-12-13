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
        $this->username = 'playgamelxh';

        $this->password = md5('rk330318747');

        $this->softid = 42913;

        $this->softkey = '';
    }

    public function get($filename)
    {
        $damaUrl = 'http://api.ruokuai.com/create.json';
//        $filename = 'img.jpg';	//img.jpg是测试用的打码图片，4位的字母数字混合码,windows下的PHP环境这里需要填写完整路径
        $ch = curl_init();
        $postFields = array('username' => $this->username,
            'password' => $this->password,
            'typeid' => 1040,	//4位的数字混合码   类型表http://www.ruokuai.com/pricelist.aspx
            'timeout' => 60,	//中文以及选择题类型需要设置更高的超时时间建议90以上
            'softid' => 42913,	    //改成你自己的
//            'softkey' => $this->softkey,	//改成你自己的
            'image' => '@'.$filename
        );

        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 65);	//设置本机的post请求超时时间，如果timeout参数设置60 这里至少设置65
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_URL, $damaUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $result = curl_exec($ch);

        curl_close($ch);

        var_dump($result);
    }
}