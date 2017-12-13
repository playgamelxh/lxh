<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2017/12/4
 * Time: 上午8:39
 */

//    echo posix_getpid();die();
    include "./Curl.php";
//    $num = '614147';//大智若愚 微 已 +100
//    $num = '624544';//木木 支 已 +10
//    $num = '666540';//明月清风 +++
//    $num = '173100';    //大笨牛 10 Q
    $num = '054530';

    $i = 1;
    while (true) {
        test($num);
        $i++;
    }

    function test($num)
    {

//    echo createName();die();
        $cookfile = isset($argv[1]) ? $argv[1] : posix_getpid();
        $cookfile = "./cookie/" . $cookfile;
        file_put_contents($cookfile, '');
        echo $cookfile, "\r\n";

        $curlObj = new Curl();
        $ip = rand(1,244).'.'. rand(1,244).'.'. rand(1,244).'.'. rand(1,244);
        $ip = '172.16.13.50';
        $header = array(
            "CLIENT-IP:{$ip}",
            "X-FORWARDED-FOR:{$ip}",
        );
        $curlObj->setHttpHeader($header);
        $curlObj->saveCookie($cookfile);
        $curlObj->setCookie($cookfile);
        $curlObj->setProxyIp("http://localhost:8899/");//每次移动换IP,需要重新绑定adb forward tcp:8899 tcp:8899
//        $url = "https://in.ibeechat.com/spread/{$num}/";
//        $curlObj->setUrl($url);
//        $resStr = $curlObj->run();
        $url = "https://in.ibeechat.com/enroll/p/{$num}";
        $curlObj->setUrl($url);
        $resStr = $curlObj->run();
        echo $resStr;

//    $phone = '13803895149';
        $phone = createPhone();
        echo $phone, "\r\n";
        $url = "https://in.ibeechat.com/enroll/sendCode?phone={$phone}&countryCode=86";
        $curlObj->setUrl($url);

        $str = $curlObj->run(); var_dump($str);
        print_r(json_decode($str, true));
        $curlObj->setProxyIp("");
        
        $code = '';
        for ($i = 1; $i <= 9999; $i++) {
            $code = str_pad($i, 4, '0', STR_PAD_LEFT);
            echo $code, "\r\n";
            $url = "https://in.ibeechat.com/enroll/register?uid=86{$phone}&code={$code}";
            $curlObj->setUrl($url);
//        echo $url;
            $resStr = $curlObj->run();
            $resArr = json_decode($resStr, true);
            var_dump($resArr);
            if ($resArr['message'] != '短信验证码不正确') {
                print_r($resArr);

                $name = createName();
                $url = "https://in.ibeechat.com/enroll/updateName?name={$name}";
                $curlObj->setUrl($url);
                $str = $curlObj->run();
                print_r(json_decode($str, true));
                break;
            }
        }
    }



    //伪造电话号
    function createPhone()
    {
        return rand(13000000000, 18999999999);
    }

    //伪造姓名
    function createName()
    {
        $str  = '赵钱孙李周吴郑王冯陈褚卫蒋沈韩杨朱秦尤许何吕施孔曹严华金魏陶姜戚谢邹喻柏水窦';
        $str .= '云苏潘葛奚范彭郎鲁韦昌马苗凤花方俞任袁柳鲍史唐费廉岑薛雷贺倪汤滕殷罗毕郝邬安常';
        $str .= '乐于时皮卞齐康伍余卜顾孟平黄和穆萧尹姚邵湛汪祁毛禹狄米贝明臧计伏成戴谈宋庞熊纪';
        $str .= '舒屈项祝董梁杜阮蓝闵席季麻强贾路娄危江童颜郭盛林刁钟徐邱骆高夏蔡田樊胡凌霍虞万';
        $str .= '支柯昝管卢莫经房裘缪干解应宗丁宣贲邓郁单杭洪诸左石崔吉钮龚程嵇邢滑裴陆荣翁荀羊';
        $str .= '於甄麴家封芮羿储靳汲邴糜松井段富巫乌焦巴弓牧隗山谷车侯宓蓬郗班仰秋仲伊宫宁仇暴';
        $str .= '甘钭厉戎祖武符刘景詹束龙叶幸司韶郜黎蓟薄印宿白怀蒲邰从鄂索咸籍赖卓蔺蒙乔阴郁胥';
        $str .= '能苍双闻莘翟谭贡劳逄姬申扶冉宰郦雍舄璩桑桂濮牛寿通边扈燕冀郏浦尚农温别庄晏柴瞿';
        $str .= '阎连茹?宦艾鱼容向古易戈廖庾终暨衡步都耿满弘匡国文寇广禄阙东殴殳沃利蔚越夔隆师巩';
        $str .= '厍聂晁勾敖融冷訾辛阚那简饶空曾毋沙乜须丰巢蒯相查後荆红游竺权逯盖益桓万俟司马上官';
        $str .= '欧阳夏侯诸葛闻人东方赫连皇甫尉迟羊澹台冶宗政濮阳淳于单于太叔申孙仲孙轩辕令狐钟离';
        $str .= '宇文长孙容鲜于闾丘司徒司空亓官司寇仉督子车颛孙端木巫马西漆雕乐正壤驷良拓跋夹谷宰';
        $str .= '父谷梁晋楚闫法汝鄢涂钦段干百里东郭南门呼延归海羊舌微生岳?缑亢况后有琴梁丘左丘东门西门商牟佘佴伯赏南宫墨哈谯笪年爱阳佟';
        $length = mb_strlen($str, 'utf-8');
        $i = rand(1, $length-2);
        $t = rand(0, 1);
        return mb_substr($str, $i, 1) . ($t ? '先生' : '女士');
    }