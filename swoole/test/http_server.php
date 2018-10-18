<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2017/12/26
 * Time: 下午3:11
 */
$http = new swoole_http_server("127.0.0.1", 9501);

$http->on("start", function ($server) {
    echo "Swoole http server is started at http://127.0.0.1:9501\n";
});

$http->on("request", function ($request, $response) {
    $data = isset($request->get) ? $request->get : array('name' => 'null');
    $response->header("Content-Type", "text/plain");
    $response->end("Hello World {$data['name']}\n");
});

$http->start();