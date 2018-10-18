<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2018/1/6
 * Time: 下午5:39
 * Desc: 短信验证码
 */
class Sms
{
    //网站编号
    public $sid = '10895';

    //token
    public $token = 'e387d8ba763f5b63b052563ff44db736';

    public $getPhoneUrl = "";

    public $getMsgUrl = "";

    public function __construct($sid)
    {
        $this->sid = $sid;

        $this->getPhoneUrl = "http://api.ixinsms.com/api/do.php?action=getPhone&sid={$this->sid}&token={$this->token}";

        $this->getMsgUrl  = "http://api.ixinsms.com/api/do.php?action=getMessage&sid={$this->sid}&token={$this->token}&phone=";
    }

    //获取手机号
    public function getPhone()
    {
        $opts = array(
            'http'=>array(
                'timeout' => 60,
            )
        );
        $context = stream_context_create($opts);
        $str = file_get_contents($this->getPhoneUrl, false, $context);
        if (!empty($str)) {
            $temp = explode('|', $str);
            return isset($temp[1]) ? $temp[1] : '';
        } else {
            return '';
        }
    }

    //接收验证码
    public function getMsg($phone = '', $num = 10)
    {
        $i = 1;
        while ($i <= $num) {
            $opts = array(
                'http'=>array(
                    'timeout' => 60,
                )
            );
            $context = stream_context_create($opts);
            $str = file_get_contents($this->getMsgUrl . $phone, false, $context);
            echo $str,"\r\n";
            $temp = explode('|', $str);
            if ($temp[0] == 1) {
                return $temp['1'];
            }

            $i++;
            sleep(5);
        }
        return '';
    }
}