<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2016/12/24
 * Time: 上午10:34
 */
//ini_set('display_errors', 'ON');
//error_reporting(E_ALL);


$filename = './jpg/s14.jpg';

header("Content-type: image/jpg");
/* 读取图档 */
$im = imagecreatefromjpeg($filename);
/* 图片要截多少, 长/宽 */
//$new_img_width = 410;
//$new_img_height = 420;
$new_img_width = 210;
$new_img_height = 210;
/* 先建立一个 新的空白图档 */
$newim = imagecreatetruecolor($new_img_width, $new_img_height);
// 输出图要从哪边开始 x, y ，原始图要从哪边开始 x, y ，要画多大 x, y(resize) , 要抓多大 x, y
//imagecopyresampled($newim, $im, 0, 0, 335, 1072, $new_img_width, $new_img_height, $new_img_width, $new_img_height);
imagecopyresampled($newim, $im, 0, 0, 165, 535, $new_img_width, $new_img_height, $new_img_width, $new_img_height);
//横线处理
for ($y=0;$y<=imagesy($newim);$y++) {
    $num = 0;
    for ($x=0;$x<=imagesx($newim);$x++) {
        $rgb = imagecolorat($newim,$x,$y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        $isRead = false;
        if (($r>10 && $r<220) && ($g>10 && $g<220) && ($b>10 && $b<220)) {
            $isRead = true;
            $num++;
        }
//        if (!$isRead) {
//            echo $r,$g,$b,' ';
//        } else {
//            echo "<label style='color:red'>",$r,$g,$b,"</label> ";
//        }
//        if ($isRead) {
//            if ($y-2>0) {
//                $newRgb = imagecolorat($newim, $x, $y-2);
//                imagesetpixel($newim, $x, $y, $newRgb);
////                die();
//                $num++;
//            }
//        }
    }
    if ($num/imagesx($newim) > 0.98) {
        for ($x=0;$x<=imagesx($newim);$x++) {
            $newRgb = imagecolorat($newim, $x, $y-2);
            imagesetpixel($newim, $x, $y, $newRgb);
        }
    }
//    echo "\r\n<hr />";
}



/* 将图印出来 */
imagejpeg($newim);
/* 资源回收 */
imagedestroy($newim);
imagedestroy($im);