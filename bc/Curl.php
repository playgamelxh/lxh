<?php
class Curl
{
    protected $ch;
    protected $info;
    protected $cookiePath;

    public function __construct()
    {
        $this->ch = curl_init();
    }

    public function setUrl($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        return $this;
    }

    public function setPost($data)
    {
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        return $this;
    }

    public function saveCookie($path)
    {
        curl_setopt($this->ch, CURLOPT_COOKIEJAR,  $path);
        return $this;
    }

    public function setCookie($path)
    {
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $path);
        $this->cookiePath = $path;
        return $this;
    }

    public function setHttpHeader(array $headers)
    {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        return $this;
    }
    public function setTimeout($t)
    {
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $t);
    }
    public function run()
    {
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch,CURLOPT_CONNECTTIMEOUT, 5);
        $html = curl_exec($this->ch);
        $this->info = curl_getinfo($this->ch);
        return $html;
    }
    public function getInfo()
    {
        return $this->info;
    }

    public function setProxyIp($ip)
    {
        curl_setopt($this->ch, CURLOPT_PROXY, $ip);
    }

    public function close()
    {
        curl_close($this->ch);
    }

    //保存图片
    public function saveImage($filename)
    {
        $fp = fopen($filename, 'wb');
        curl_setopt($this->ch, CURLOPT_FILE, $fp);
        curl_setopt($this->ch, CURLOPT_HEADER,0);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION,1);
        //curl_setopt($hander,CURLOPT_RETURNTRANSFER,false);//以数据流的方式返回数据,当为false是直接显示出来
        curl_setopt($this->ch, CURLOPT_TIMEOUT,60);
        curl_exec($this->ch);
        curl_close($this->ch);
        fclose($fp);
        return true;
    }

    //并发多个请求
    public function multi($urlArr, $cookiefile)
    {
        $resArr = array();
        if (is_array($urlArr) && !empty($urlArr)) {
            $mh = curl_multi_init();
            foreach ($urlArr as $i => $url) {
                $conn[$i] = curl_init($url);
                curl_setopt($conn[$i], CURLOPT_COOKIEJAR,  $cookiefile);
                curl_setopt($conn[$i], CURLOPT_COOKIEFILE, $cookiefile);
                curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, 1);
                curl_multi_add_handle($mh,  $conn[$i]);
            }
            do {
                $n=curl_multi_exec($mh,$active);
            } while ($active);

            foreach ($urlArr as $i => $url) {
                $resArr[$i]=curl_multi_getcontent($conn[$i]);
                $arr = json_decode($resArr[$i], true);
                if ($arr['message'] != '短信验证码不正确') {
                    return true;
                }
                curl_close($conn[$i]);
            }
        }
        return false;
    }
}
