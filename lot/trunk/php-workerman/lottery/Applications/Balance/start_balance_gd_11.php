<?php

ini_set("memory_limit", "-1");

use \Workerman\Worker;
use \Workerman\Lib\Timer;
use \Workerman\Connection\AsyncTcpConnection;
use \helper\MysqlPdo as pdo;
use \helper\RedisConPool;
use \helper\Common_helper;
use \helper\Curl;

require_once __DIR__ . '/../../Workerman/Autoloader.php';

Worker::$stdoutFile = __DIR__ . '/../../logs/worker.log';

$worker = new Worker('http://0.0.0.0:8689');
$worker->name = '11balanceWorker';
$worker->onWorkerStart = function($worker) {
    $typelist = [
        'sd_11',
        'gd_11',
        'jx_11'
    ];


    Timer::add(10, function()use($typelist)               ///定期将下期数据缓冲到内存
    {
        foreach ($typelist as $type) {
            $class = '\libraries\balance\\' . ucfirst($type) . 'Balance';                      ///结算类
            Common_helper::LoadAllBets($class, $type);
        }
    });
};


$worker->onMessage = function($connection, $data) {

    if (!isset($_POST['todo'])) {                     ///无指定命令
        $connection->send("invaild commond");
        return;
    }

    if (!isset($_POST['type'])) {                     ///无指定彩种
        $connection->send("invaild type");
        return;
    }

    $type = $_POST['type'];
    $qishu = $_POST['qishu'];
    $class = '\libraries\balance\\' . ucfirst($type) . 'Balance';                            ///结算类
    switch ($_POST['todo']) {
        case 'get':                                 ///查询指令
            if (isset($_POST['qishu'])) {
                $data = Common_helper::getDataFromMemory($class, $_POST['qishu']);
                $connection->send($data);
            } else {
                $connection->send(null);
            }

            return;
            break;

        case 'balance':
            if (!isset($_POST['qishu'])) {                        ///期数
                $connection->send("invaild qishu");
                return false;
            }
            $redis_key = Common_helper::GetQiShuRedisKeyByType($type); //存放期数
            $redis = RedisConPool::getInstace();
            if ($redis->EXISTS($redis_key)) {                    ////比对期数
                $content = trim($redis->get($redis_key));
                if ($_POST['qishu'] == $content) {
                    $connection->send("repeated qishu");
                    return false;
                }
            }

            break;
    }

    if (!isset($class::$run[$qishu])) {
        $connection->send("this server qishu noisset");
        return false;
    }

    Common_helper::BalanceByType($type, $qishu);
    $connection->send("start");
    return;
};


// 运行worker
if(!defined('GLOBAL_START')) {
    Worker::runAll();
}
?>