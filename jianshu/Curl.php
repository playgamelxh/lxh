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
}
