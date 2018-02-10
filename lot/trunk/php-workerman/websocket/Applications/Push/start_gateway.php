<?php

use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

require_once __DIR__ . '/../../Workerman/Autoloader.php';

$gateway = new Gateway("websocket://0.0.0.0:9527");

$gateway->name = 'PushGateway';

$gateway->count = 4;
// 本机ip，分布式部署时使用内网ip
$gateway->lanIp = '127.0.0.1';
// 内部通讯起始端口，假如$gateway->count=4，起始端口为2999
// 则一般会使用2999 3000 3001 3002 4个端口作为内部通讯端口
$gateway->startPort = 2999;

$gateway->registerAddress = '127.0.0.1:1258';

$gateway->router = function($worker_connections, $client_connection, $cmd, $buffer) {
    // foreach ($worker_connections as $k => $v) {
    //     echo $k . PHP_EOL;
    //     foreach ($v as $kk => $vv) {
    //         echo ' - ' . $kk . PHP_EOL;
    //     }
    // }
    // print_r($worker_connections);
    // print_r($client_connection);
    // print_r($cmd);
    // print_r($buffer);
    return $worker_connections[array_rand($worker_connections)];
};

// 心跳间隔
$gateway->pingInterval = 30;
// 客户端有 {$pingNotResponseLimit} 次未回复就断开连接
$gateway->pingNotResponseLimit = 6;
// 心跳数据
$gateway->pingData = '{"cmd":"ping"}';

/*
  // 当客户端连接上来时，设置连接的onWebSocketConnect，即在websocket握手时的回调
  $gateway->onConnect = function($connection)
  {
  $connection->onWebSocketConnect = function($connection , $http_header)
  {
  // 可以在这里判断连接来源是否合法，不合法就关掉连接
  // $_SERVER['HTTP_ORIGIN']标识来自哪个站点的页面发起的websocket链接
  if($_SERVER['HTTP_ORIGIN'] != 'http://kedou.workerman.net')
  {
  $connection->close();
  }
  // onWebSocketConnect 里面$_GET $_SERVER是可用的
  // var_dump($_GET, $_SERVER);
  };
  };
 */

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
