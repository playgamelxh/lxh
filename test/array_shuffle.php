<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 15-7-15
 * Time: 上午10:33
 */
ini_set('memory_limit', "10000M");
$time = microtime(true);
$numbers = range(1,1000000000);
echo "1:".(microtime(true)-$time)."\r\n";
srand((float)microtime()*1000000);
shuffle($numbers);
echo "2:".(microtime(true)-$time)."\r\n";
for($i=0;$i<=10;$i++){
    echo $numbers[$i],"\r\n";
}