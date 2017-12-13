<?php
//1:截取图片固定区域
//2：图片二进制化
//3：识别黑条高度
//4：删除黑条
//5：与原图交叉，原图向下移一部分

$filename = "./ar/jpg/s1.jpg";
// 宽： 330px  - 749px = 419px
// 高： 1070 - 1490px = 420

$new_img_width  = 420;
$new_img_height = 420;

$im = step1($filename, $new_img_width, $new_img_height);
$newim = step2($im);
//imagejpeg($newim, "ssss".rand(1,10000).".png");

header("Content-type: image/jpg");

/* 将图印出来 */
imagejpeg($newim);
/* 资源回收 */
imagedestroy($newim);
imagedestroy($im);


function step1($filename, $new_img_width, $new_img_height) {
    /*读取图片 */
    $im = imagecreatefromjpeg($filename);

    /* 图片要截多少, 长/宽 */

    /* 先建立一个 新的空白图片档 */
    $newim = imagecreatetruecolor($new_img_width, $new_img_height);

    // 输出图要从哪边开始x, y , 原始图要从哪边开始 x, y , 要输多大 x, y(resize) , 要抓多大 x, y
    imagecopyresampled($newim, $im, 0, 0, 330, 1072, $new_img_width, $new_img_height, $new_img_width, $new_img_height);

    return $newim;
}

//二进制化，计算条纹宽高、间距
function step2($im) {
    $tiaowen = array();
    for ($x=0;$x<imagesy($im);$x++) {
        $num = $errnum =0;
        for ($y=0;$y<imagesx($im);$y++) {
            $rgb = imagecolorat($im,$y,$x);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = ($rgb) & 0xFF;

            //echo $r." ".$g." ".$b."\n";
            if($r > 20 && $r < 100 && $g > 20 && $g < 100 && $b > 20 && $b < 100 ) {
                $num++;
            } else {
                //不是条纹
                $errnum++;
            }
        }
        //如果条纹数量大于非条纹数量N倍，则认为此行是条纺
        if($num / ($num + $errnum) > 0.9) {
            $tiaowen[] = $x;
        }
    }
    $height = 5; //稍后会通过程序动态计算
    //从上面取像素，补全
    foreach ($tiaowen as $y) {
        for ($i = 0; $i < imagesx($im); $i++) {
            imagesetpixel($im, $i, $y, imagecolorat($im, $i, $y - $height));
        }
    }
    return $im;
}