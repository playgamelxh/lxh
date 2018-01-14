<?php
ini_set('display_errors', 'ON');
error_reporting(E_ALL);

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
        if (is_array($data)) {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } else {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        }
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

    public function disableSSL()
    {
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        return $this;
//        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 2);
    }

    public function setTimeout($t)
    {
    	curl_setopt($this->ch, CURLOPT_TIMEOUT, $t);
        return $this;
    }
    public function run()
    {
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 0);
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

    //ssl
    public function ssl($file)
    {
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书
        curl_setopt($this->ch, CURLOPT_CAINFO, $file);      // CA根证书（用来验证的网站证书是否是CA颁布）
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 2);

//        curl_setopt($this->ch,  CURLOPT_SSL_VERIFYPEER,false);
//        curl_setopt($this->ch,  CURLOPT_SSL_VERIFYHOST,false);
//        curl_setopt($this->ch,  CURLOPT_SSLCERTTYPE,'PEM');
//        curl_setopt($this->ch,  CURLOPT_SSLCERT,'/data/cert/php.pem');
//        curl_setopt($this->ch,  CURLOPT_SSLCERTPASSWD,'1234');
//        curl_setopt($this->ch,  CURLOPT_SSLKEYTYPE,'PEM');
//        curl_setopt($this->ch,  CURLOPT_SSLKEY,'/data/cert/php_private.pem');
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
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);//以数据流的方式返回数据,当为false是直接显示出来
        curl_setopt($this->ch, CURLOPT_TIMEOUT,60);
        curl_exec($this->ch);
        curl_close($this->ch);
        fclose($fp);
        return true;
    }

    //并发多个请求
    public function multi($urlArr)
    {
        $mrc = array();
        if (is_array($urlArr) && !empty($urlArr)) {
            $mch = curl_multi_init();
            foreach ($urlArr as $url) {
                $this->setUrl($url);
                curl_multi_add_handle($mch, $this->ch);
            }
            $mrc = curl_multi_exec($mch);
        }
        return $mrc;
    }

}
