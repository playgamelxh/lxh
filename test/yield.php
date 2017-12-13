<?php
//迭代生成器
function xrange($start, $end, $step = 1) {
	for($i=$start;$i<=$end;$i+=$step){
		yield $i;
	}
}
foreach(xrange(1, 10) as $num){
	echo $num,"\r\n";
}

//协程
function logger($fileName) {
    $fileHandle = fopen($fileName, 'a');
    while (true) {
        fwrite($fileHandle, yield . "\n");
    }
}

$logger = logger(__DIR__ . '/log');
$logger->send('Foo');
$logger->send('Bar');

//
function gen() {
    $ret = (yield 'yield1');
    var_dump($ret);
    $ret = (yield 'yield2');
    var_dump($ret);
}

$gen = gen();
var_dump($gen->current());
var_dump($gen->send('ret1'));
//var_dump($gen->send('ret2'));

