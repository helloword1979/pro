<?php

use \Workerman\Worker;
use \Workerman\Lib\Timer;
use Applications\Tools\Lib\Addbetstest as add;
use \helper\RedisConPool as Redis;

require_once __DIR__ . '/../../Workerman/Autoloader.php';

$worker = new Worker("http://0.0.0.0:1100");

$worker->name = 'addBets';

$worker->count = 1;

$worker->onWorkerStart = function($worker) {
    $timer_id =  Timer::add(5, function()use(&$timer_id){
        $redis = Redis::getInstace();
        $tmp_key = 'test_num';
        $current_num = $redis->get($tmp_key);
        $success = add::addbet();
        if(!$current_num) $current_num = 0;
        $success += $current_num;
        echo '当前累计插入数据库成功条数：' . $success . ' 条' . PHP_EOL;
        $redis->set($tmp_key, $success);

        if($success >= 300000){
            echo '当前共插入' . $success . '条数据,达到目标，停止继续插入';
            Timer::del($timer_id);
        }

     });
};

$worker->onMessage = function($connection, $header) {
  
};

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
