<?php

/**
 * 公共函数库
 */

namespace helper;

use \Workerman\Connection\AsyncTcpConnection;
use \Workerman\Lib\Timer;
use \config\config;
use \helper\Curl;
use \helper\RedisConPool;
use \helper\MysqlPdo as pdo;
use helper\MongoDBManager;
use \helper\GetQishuOpentime;

//服务端口：10001 结算程序处理sql  task_sql
//        10002 方便curl访问端口 common
//        10003 注单回滚端口     rollback
//        10004 注单自动统计端口  total_bet
//        10005 为六合彩专用结算sql  task_sql_liuhecai
class Common_helper {

    static $type_array = array('tj_ssc', 'cq_ssc', 'xj_ssc', 'gd_ten', 'cq_ten', 'js_k3', 'gx_k3', 'ah_k3', 'gd_11', 'sd_11', 'jx_11', 'ffc_o', 'lfc_o', 'els_o', 'mg_o', 'mnl_o', 'xdl_10', 'dj_o', 'jsfc', 'jsliuhecai'); //此数组用于处理期数增长函数
    static $rollback = [];
    static $rollback_count = 0;
    //根据时间戳转化出相应美东时间的日期
    public static function timechange($time) {
        // return date('Ymd', $time - 3600 * 12);
        return date('Ymd', $time);
    }

    //根据彩种获取注单表名
    public static function GetBetTableNameByType($type) {
        return config::$tablePrefix . 'bet_record';
    }

    //根据彩种获取开奖结果表名
    public static function GetAutoTableNameByType($type) {
        // $lottery = pdo::instance('manage');

        return config::$tablePrefix . 'auto_' . $type;
    }

    //根据彩种获取开关盘时间表名
    public static function GetOpenTimeTableNameByType($type) {
        $arr = ['liuhecai', 'jnd_bs', 'jnd_28'];
        if (in_array($type, $arr)) {
            if ($type == 'jnd_28')
                $type = 'jnd_bs';
            return config::$tablePrefix . $type . '_opentime';
        }
        return config::$tablePrefix . 'opentime';
    }

    //根据彩种 获取期数rediskey
    public static function GetQiShuRedisKeyByType($type) {
        return $type . '_balance';
    }

    ////根据彩种寻找往期未结算注单
    // public static function getNumberOnStart($type) {
    //     $lottery = pdo::instance('lottery');
    //     $addtime = time() - 7 * 24 * 3600;

    //     $table = self::GetBetTableNameByType($type);
    //     $sql = "SELECT periods FROM $table where js = 1 AND fc_type= '$type' AND addtime >= '$addtime' GROUP BY periods";
    //     $qishu_list = $lottery->query($sql);
    //     $return = [];
    //     $class = '\libraries\balance\\' . ucfirst($type) . 'Balance';
    //     $qishu = $class::get_qishu();
    //     if ($qishu_list !== FALSE) {
    //         foreach ($qishu_list as $val) {
    //             // if ($qishu == $val['periods'])//本期（当前期）不结算
    //             //     continue;
    //             $return[$val['periods']] = 1;
    //         }
    //     } else {
    //         return FALSE;
    //     }

    //     return $return;
    // }

    //根据彩种期数获取未结算注单
    public static function getAllByNumber($type, $qishu) {
        ini_set("memory_limit","-1");
        $redis = RedisConPool::getInstace();
        $lottery = pdo::instance('lottery');
        $class = '\libraries\balance\\' . ucfirst($type) . 'Balance';
        $all_time = $class::getTimeByQishu($qishu);
        $time = $all_time['kaipan'];
        $close = $all_time['fengpan'];
        $addday = date('Ymd', $time);
        $close_day = date('Ymd', $close);
        $table = self::GetBetTableNameByType($type);
        $count_sql = "select count(id) as betcount from $table where js=1 and status=1 and periods=$qishu and fc_type='$type' and addday>=$addday and addday<=$close_day";
        $bet_count = $lottery->single($count_sql);
        $sql = "select * from $table where js=1 and status=1 and periods=$qishu and fc_type='$type' and addday>=$addday and addday<=$close_day order by addtime asc limit $bet_count";
        //优先从redis读取 redis数据是下注的时候存入的，前台控制器bet/addbet
        $redis_key = 'for_balance-' .  $type . '-' . $qishu;
        $data = $redis->hgetall($redis_key);
        if (!empty($data)) {
            $tmp_arr = array();
            foreach ($data as $val) {
                $tmp_arr[] = json_decode($val, true);
            }
            $data = array();
            foreach ($tmp_arr as $val) {
                foreach ($val as $v) {
                    $data[] = $v;
                }
            }
            $count = count($data); //对比数量
            
            if ($count != $bet_count) {
                $redis->delete($redis_key);
                $bets = $lottery->query($sql);
                return $bets;
            } else {
                $redis->delete($redis_key);
                return $data;
            }
        } else {
            $bets = $lottery->query($sql);
            return $bets;
        }
    }

    ///定期LOAD注单
    public static function LoadAllBets($class, $type) {
        //初始化初始数据
        if (empty($class::$qishu))
            $class::$qishu = $class::get_qishu();

        if (empty($class::$open)){
            $all_time = $class::getTimeByQishu($class::$qishu);
            $class::$open = $all_time['kaipan'];
        }

        if (empty($class::$close)){
            $all_time = $class::getTimeByQishu($class::$qishu);
            $class::$close = $all_time['fengpan'];
        }

        if (time() > $class::$close) {                              ////封盘时间触发读取所有注单重任务
            ///装载当前期数注单
            $qishu = strval($class::$qishu);
            $data = self::getAllByNumber($type, $qishu);
            if($data && !empty($data)){
                $class::$bets[$qishu] = $data;
            }
            //赛车十递增，时时彩等彩种需要完善期数增长函数(已经结算类完善)
            if (in_array($type, static::$type_array)) {
                $class::$qishu = $class::jiajian($class::$qishu);
            } else {
                $class::$qishu++;
            }
            $all_time = $class::getTimeByQishu($class::$qishu);
            $class::$open = $all_time['kaipan'];
            $class::$close = $all_time['fengpan'];
            $class::$run[$qishu] = 1;
        }
    }

    //从结算队列中获取数据
    public static function GetBalanceData($time, $type, $qishu) {
        $class = '\libraries\balance\\' . ucfirst($type) . 'Balance';
        if (!empty($class::$info[$qishu])) {
            $data = [];
            $data['info'] = current($class::$info[$qishu]);
            $data['time'] = $time;
            $data['type'] = $type;
            $data['uid'] = key($class::$info[$qishu]);
            $data['qishu'] = $qishu;
            unset($class::$info[$qishu][$data['uid']]);
            return $data;
        } else {
            return FALSE;
        }
    }

    //根据type 获取最后一期开奖结果的期数
    public static function GetNewNumber($type) {
        $manage = pdo::instance('manage');
        $auto_table = self::GetAutoTableNameByType($type);
        $sql = "SELECT qishu FROM $auto_table ORDER BY id DESC LIMIT 1";
        $qishu = $manage->single($sql);
        return $qishu;
    }

    /**
     * **********************************************************
     *  开奖结果插入数据库                                  *****
     * **********************************************************
     * @author ruizuo
     * @param array 采集来的结果数组
     * @param string 相应彩种类型
     * @param int 球的个数
     * @return boolean 是否插入成功
     * **********************************************************
     */
    public static function addNewNumber($auto_arr, $type, $ballcount = '') {
        if (!isset($auto_arr[0]))
            return false;
        $data = $auto_arr[0];
        $sql_data = $auto_arr[1];
        if (empty($ballcount)) {
            $ballcount = self::getBallNum($type);
        }
        $manage = pdo::instance('manage');
        $auto_table = self::GetAutoTableNameByType($type);
        ////采集内容入库
        $qishu = $data['expect'];
        $find = "SELECT id from $auto_table where qishu = " . $qishu;
        $find = $manage->row($find);
        if ($find) {
            return TRUE;
        }

        $open_time = strtotime($data["opentime"]);
        $addtime = time();
        $open_res = explode(',', $data["opencode"]);
        if ($ballcount != count($open_res)) {
            return false;
        }

        //拼接sql
        $field = '';
        $str = '';
        foreach ($sql_data as $key => $val) {
            $field .= $key . ',';
            $str .= "'{$val}'" . ',';
        }
        $sql = "insert into $auto_table (qishu,datetime," . $field . "addtime) values('$qishu','$open_time'," . $str . "$addtime)";
        //采集结果插入数据库
        $res = $manage->query($sql);
        if ($res) {
            $auto_key = 'all_list_auto';
            $redis_data = array();
            $redis_data = $sql_data;
            $redis_data['datetime'] = $open_time;
            $redis_data['qishu'] = $qishu;
            $redis_data['addtime'] = $addtime;
            $redis_data['type'] = $type;
            $redis = RedisConPool::getInstace();
            $redis->hset($auto_key, $type, json_encode($redis_data));
            self::sendAuto($type,$data);
            return TRUE;
        }
        self::write_error_log('The type ' . $type . " qishu=$qishu " . ' can not insert to database ' . PHP_EOL . $sql, 2, $type . '.txt');

        return FALSE;
    }
/**
      ***********************************************************
      *  开奖结果推送           @author ruizuo qiyongsheng    *
      ***********************************************************
*/
    public static function sendAuto($type, $data){
        $data['opencode'] = explode(',', $data['opencode']);
        if(in_array($type, ['bj_10', 'jsfc', 'xdl_10'])){//附加颜色数据
            $bj_color_arr = ['','bj-yellow','bj-blue','bj-black','bj-orange','bj-azure','bj-deepblue','bj-silver','bj-red','bj-brown','bj-green'];
            $tmp_auto = array();
            foreach($data['opencode'] as $key=>$val){
                $tmp_auto[$key]['ball'] = $val;
                $tmp_auto[$key]['color'] = isset($bj_color_arr[intval($val)]) ? $bj_color_arr[intval($val)] : $val;
            }
            $data['opencode'] = $tmp_auto;
        }elseif(in_array($type, ['liuhecai', 'jsliuhecai'])){//附加颜色数据
            $ball['red'] = [1, 2, 7, 8, 12, 13, 18, 19, 23, 24, 29, 30, 34, 35, 40, 45, 46];
            $ball['blue'] = [3, 4, 9, 10, 14, 15, 20, 25, 26, 31, 36, 37, 41, 42, 47, 48];
            $ball['green'] = [5, 6, 11, 16, 17, 21, 22, 27, 28, 32, 33, 38, 39, 43, 44, 49];
            $zodiac_list_ball = self::get_zodiac_list_ball();
            $tmp = [];
            foreach($data['opencode'] as $key => $val){
                $tmp[$key]['number'] = $val;
                foreach ($ball as $color => $v2) {
                    if (in_array($val, $v2)) {
                        $tmp[$key]['color'] = $color . '_wave';
                        break;
                    }
                }
                foreach ($zodiac_list_ball as $zodiac => $v3) {
                    if (in_array($val, $v3)) {
                        $tmp[$key]['animal'] = $zodiac;
                        break;
                    }
                }
            }
            $data['opencode'] = $tmp;
        }

        $return['cmd'] = 'lottery';
        $return['qishu'] = $data['expect'];
        $return['fc_type'] = $type;
        $return['datetime'] = strtotime($data["opentime"]);
        $return['ball'] = $data['opencode'];

        Curl::run(config::$sendAuto, 'get', $return);
    }
        
    public static function get_zodiac_list_ball($zodiac = '')
    {
        $zodiac_list = [
            'pig', 'dog', 'chicken', 'monkey', 'sheep','horse',
            'snake', 'dragon', 'rabbit', 'tiger', 'cattle', 'mouse'
        ];
        $zodiac_list = [
            '猪', '狗', '鸡', '猴', '羊','马',
            '蛇', '龙', '兔', '虎', '牛', '鼠'
        ];
        $zodiac_ball = [
            [ 1, 13, 25, 37, 49 ],
            [ 2, 14, 26, 38 ],
            [ 3, 15, 27, 39 ],
            [ 4, 16, 28, 40 ],
            [ 5, 17, 29, 41 ],
            [ 6, 18, 30, 42 ],
            [ 7, 19, 31, 43 ],
            [ 8, 20, 32, 44 ],
            [ 9, 21, 33, 45 ],
            [ 10, 22, 34, 46 ],
            [ 11, 23, 35, 47 ],
            [ 12, 24, 36, 48 ]
        ];

        if($zodiac)
        {
            $zodiac_key = array_search($zodiac, $zodiac_list);
        }
        else
        {
            $zodiac_key = self::get_zodiac_key();
        }
        $zodiac_list = array_merge( array_splice( $zodiac_list, $zodiac_key ), $zodiac_list );

        $result = [];
        foreach($zodiac_list as $key => $zodiac)
        {
            $result[$zodiac] = $zodiac_ball[$key];
        }
        return $result;
    }

    public static function get_zodiac_key()
    {
        $start_year = 1970;// 1970;
        $start_zodiac = 2;// 1970年生肖 狗;
        $zodiac_offset = $start_zodiac - ( date('Y') - $start_year ) % 12;
        if($zodiac_offset < 0) $zodiac_offset += 12;

        $result = $zodiac_offset - 1;

        return $result;
    }

    //根据type和期数 进入结算（彩种，期数，批量结算的id，手动结算的注单）
    public static function BalanceByType($type, $qishu, $id = null, $memory = array()) {
        $qishu = strval($qishu); //避免期数数值过大超出范围
        //$id存在的话，为单条注单结算
        $lottery = pdo::instance('lottery');
        $manage = pdo::instance('manage');
        $redis = RedisConPool::getInstace();
        $redis_key = self::GetQiShuRedisKeyByType($type); //存放结算期数 "type"_balance
        $task_count = 16;

        $class = '\libraries\balance\\' . ucfirst($type) . 'Balance';
        if ($class::$run[$qishu] == 0) {
            self::write_error_log('waiting read a lot data for qishu=' . $qishu . ' type=' . $type, 2, $type . '.txt');
            return FALSE;
        }
        if ($class::$run[$qishu] == 2) { //2 . 更新redis_key所存的当前期数后
            self::write_error_log('running for qishu=' . $qishu . ' type=' . $type, 2, $type . '.txt');
            return FALSE;
        }
        //开盘时间
        $all_time = $class::getTimeByQishu($qishu);
        $time = $all_time['kaipan'];
        $close = $all_time['fengpan'];
        $addday = date('Ymd', $time);
        $close_day = date('Ymd', $close);
        ///获取彩票结果
        $auto_table = self::GetAutoTableNameByType($type); //获取auto表名
        $bet_table = self::GetBetTableNameByType($type); //获取bet表名
        $qishu_sql = "select * from $auto_table where qishu = $qishu"; //查询开奖数据
        $balance_result = $manage->row($qishu_sql);
        if (!$balance_result) { //如果查询不到相应期数开奖数据
            //如果是下列类型彩种无接口不能复采，直接跳出结算
            $not_arr = array(
                'fc_3d', //福彩3d
                'pl_3', //排列3
                'dm_28', //丹麦28
                'dm_klc', //丹麦快乐彩
                'liuhecai',//六合彩
                'jsliuhecai',//极速六合彩
                'jsfc',//极速飞车
                'ffc_o',//分分彩
                'lfc_o',//两分彩
                'dj_o',//东京1.5分
                'mg_o' //美国45秒
            );
            if (in_array($type, $not_arr)) {
                self::write_error_log($type . $qishu . ' balance result is empty', 2, $type . '.txt');
                return FALSE;
            }

            //获取端口号
            $port = self::getPort($type);

            /* 请求补采 */
            $res = Curl::run('http://127.0.0.1:' . $port[0], 'post', array(
                        'todo' => 'spider', //采集
                        'type' => $type, //彩种类型
                        'time' => date('Y-m-d', $time), //开盘时间
                        'qishu' => $qishu //期数
            ));

            if (!$res) {
                //没有结算结果 补采不到结果  结算失败
                self::write_error_log($type . $qishu . ' balance result is empty', 2, $type . '.txt');
                return FALSE;
            }
        }
        ///获取彩票结果
        if (!$id) {
            $sql = "SELECT count(id) as betcount FROM $bet_table where periods=$qishu and js = 1 and fc_type='$type' and addday>=$addday and addday<=$close_day";
        } else {//单条数据结算
            $sql = "SELECT count(id) as betcount FROM $bet_table where periods=$qishu and id in($id) and js = 1 and fc_type='$type' and addday>=$addday and addday<=$close_day";
        }
        $no_js_count = $lottery->single($sql);  //没有过期 未结算的注单
        if (empty($no_js_count)) {                      ///总数为空咯
            //注单为空 更改开奖结果为已经结算
            $sql = "update $auto_table set status=2 where qishu=" . $qishu . " and status=1";
            if(isset($class::$bets[$qishu])) unset($class::$bets[$qishu]);//释放内存
            $manage->query($sql);
            return false;
        }
        ///对比数据是否差距(注单数据数量 与 刚查询的注单数量)
        if (!empty($memory)) {
            //如果是手动结算整期 (主要是六合彩要在输入开奖结果后结算)
            $class::$bets[$qishu] = $memory;
        }
        $bets = array();
        if ((isset($class::$bets[$qishu])) && (!empty($class::$bets[$qishu])) ) {
            if($no_js_count != count($class::$bets[$qishu])){
                $bets = self::getAllByNumber($type, $qishu);
                if(isset($class::$bets[$qishu])) unset($class::$bets[$qishu]);//释放内存
                self::write_error_log($type . ' ' . $qishu . ' The memory count bet is wrong', 2, $type . '.txt');
            }else{
                $bets = $class::$bets[$qishu];
            }
        } else {
            $bets = array();
            $bets = self::getAllByNumber($type, $qishu);
            // self::write_error_log($type . ' ' . $qishu . ' Can not get data form memory', 2, $type . '.txt');
        }
        //如果该期没注单，将视为该期已经结算
        if (empty($bets)) {
            $sql = "update $auto_table set status=2 where qishu=" . $qishu . " and status=1";
            $res = $manage->query($sql);

            if(isset($class::$bets[$qishu])) unset($class::$bets[$qishu]);//释放内存
            if ($res) {
                return false;
            } else {
                self::write_error_log($type . ' ' . $qishu . ' update auto status=2 error ' . PHP_EOL . $sql, 2, $type . '.txt');
                return false;
            }
        }

        if ($class::$run[$qishu] == 1) {              ///更新当前结算期数
            $redis->SET($redis_key, $qishu);
            $class::$run[$qishu] = 2;
        }

        // 更新结算状态  是否结算 1:未结算，2:已结算，3为正在结算
        if (!$id) {
            $sql = "update $auto_table set status=3 where qishu=" . $qishu . " and status in(1,2)";
            $res = $manage->query($sql);
        } else {
            $res = true;
        }
        //如果更新不成功 重置"type"_balance redis_key 减一期
        if (!$res) {
            self::write_error_log($type . ' ' . $qishu . ' update auto error ' . PHP_EOL . $sql, 2, $type . '.txt');
            $class::$run[$qishu] = 1;
            //判断期数是否自减
            if (in_array($type, static::$type_array)) {
                $qishu = $class::jiajian($qishu, 2);
            } else {
                $qishu--;
            }
            $redis->SET($redis_key, $qishu);
            return FALSE;
        }
        ////计算彩票结果
        $class::$info[$qishu] = []; //用户输胜数组
        foreach ($bets as $bet) {
            $bet_data = explode('#', $bet['bet_info']);
            if(count($bet_data) != 3 || empty($bet_data[0])  || (empty($bet_data[1]) && $bet_data[1] != 0 ) ){
                 self::write_error_log('illegal wrong bet:' . $bet['order_num'], 2, $type . '.txt');
                 continue;
            }
            $new_bet[0] = $bet_data[0];
            $new_bet[1] = $bet_data[1];
            $new_bet[2] = $bet_data[2];

            try {
                $result = self::get_ball_name($balance_result, $bet['play_id'], $type); //查询该玩法所属字段及特殊情况处理
                if (empty($result)) {
                    if (!in_array($type, ['ah_k3', 'gx_k3', 'js_k3']) && !in_array($play_id, [107, 109])) {//豹子对子可能为空
                        echo 'result is null for play_id=' . $bet['play_id'] . 'type=' . $type . PHP_EOL;
                        continue;
                    }
                }
                $res = $class::Balance_bet($result, $new_bet);
            } catch (Exception $e) {
                var_dump($new_bet);
            }

            //根据结算类的返回结果处理数据
            $uid = $bet['uid'];
            $bet['is_wallet'] = self::is_wallet($bet['line_id']);//是否钱包模式
            $order_num = strval($bet['order_num']);
            $class::$info[$qishu][$uid][$order_num] = $bet;
            switch ($res) {
                case $class::WIN:
                    //六合彩正肖特殊处理
                    if ($type == 'liuhecai' && $new_bet[0] == 'Just_Animal') {
                        $result = explode(',', $result);
                        $count_win = 0;
                        foreach ($result as $animal) {
                            if ($new_bet[1] == $animal) {
                                $count_win += 1;
                            }
                        }
                        //每中一个，赔率翻倍
                        $bet['odds'] = $count_win * $bet['odds'];
                        $class::$info[$qishu][$uid][$order_num]['odds'] = $bet['odds'];;
                    }

                    //$info[期数][用户id][主键id][派彩金额] = 注单金额*倍率
                    $class::$info[$qishu][$uid][$order_num]['win'] = $bet["bet"] * $bet["odds"];
                    //1为未结算 2为赢 3为输 4为和局 5为无效
                    $class::$info[$qishu][$uid][$order_num]['status'] = $class::WIN;
                    break;
                case $class::FAIL:
                    $class::$info[$qishu][$uid][$order_num]['win'] = 0;
                    $class::$info[$qishu][$uid][$order_num]['status'] = $class::FAIL;
                    break;
                case $class::DRAW:
                    $class::$info[$qishu][$uid][$order_num]['win'] = $bet["bet"];
                    $class::$info[$qishu][$uid][$order_num]['valid_bet'] = 0;
                    $class::$info[$qishu][$uid][$order_num]['status'] = $class::DRAW;
                    break;
                    //  3/3:12,3/2:3  4/4:50,4/3:5,4/2:3 2/2:10   30/50  20/85
                case $class::ONE:
                    $odds_arr = explode(',', $new_bet[2]);
                    $odds_arr = explode(':', $odds_arr[1]); //赔率
                    $class::$info[$qishu][$uid][$order_num]['status'] = $class::ONE;
                    $class::$info[$qishu][$uid][$order_num]['odds'] = $odds_arr[1];
                    $class::$info[$qishu][$uid][$order_num]['win'] = $odds_arr[1] * $bet["bet"];
                    break;
                case $class::TWO: //快乐彩特殊玩法赔率处理
                    $odds_arr = explode(',', $new_bet[2]); //数据例子5/5:250;5/4:20;5/3:5
                    $odds_arr = explode(':', $odds_arr[2]); //赔率
                    $class::$info[$qishu][$uid][$order_num]['status'] = $class::TWO;
                    $class::$info[$qishu][$uid][$order_num]['odds'] = $odds_arr[1];
                    $class::$info[$qishu][$uid][$order_num]['win'] = $odds_arr[1] * $bet["bet"];
                    break;
                default://输
                    echo '找不到算法' . PHP_EOL;
                    $class::$info[$qishu][$uid][$order_num]['win'] = 0;
                    $class::$info[$qishu][$uid][$order_num]['status'] = $class::FAIL;
                    break;
            }
        }
    
        //已运算注单总数量（不是结算）
        $class::$count[$qishu] = count($class::$info[$qishu]); //总用户数

        if ($type == 'liuhecai' || $type == 'jsliuhecai') {
            $task_port = 'text://127.0.0.1:10005';
        } else {
            $task_port = 'text://127.0.0.1:10001';
        }
        ////分发处理
        for ($i = 1; $i <= $task_count; $i++) {
            $all_time = $class::getTimeByQishu($qishu);
            $time = $all_time['kaipan'];
            $data = self::GetBalanceData($time, $type, $qishu); //array('info注单集合','time','type','uid')
            if ($data) {
                $return_count = count($data['info']);
                $$i = new AsyncTcpConnection($task_port);
                $$i->onConnect = function($Async)use($data) {
                    $res = $Async->send(json_encode($data));
                };

                if ($id) {
                    $result = array();
                    $result['ErrorCode'] = 2;
                    $result['ErrorMsg'] = '无法连接到task_sql进程！';
                    $$i->onError = function($connection)use($result) {
                        return $result;
                    };
                }

                $$i->onMessage = function($Async, $responed)use($time, $type, $qishu, $class) {
                    if ($responed != 'TRUE') {
                        self::write_error_log($type . ' ' . $qishu . ':' . $responed, 2, $type . '.txt');
                        // file_put_contents("error_log.txt",'异常'.PHP_EOL, FILE_APPEND);
                    }
                    $class::$count[$qishu] --; //注单总用户数-1
                    $data = self::GetBalanceData($time, $type, $qishu);
                    if ($data) {
                        $Async->send(json_encode($data));
                    } else {
                        $Async->close();
                    }
                };
                $$i->connect();
            } else {
                break;
            }
        }
        ///检测处理情况回收资源
        $timer_id = Timer::add(1, function()use(&$timer_id, $class, $qishu, $type, $lottery, $manage, $auto_table, $id) {
                    if ($class::$count[$qishu] === 0) { //如果注单为空
                        $class::$open = 0;
                        $class::$close = 0;                   ///重置封盘时间
                        unset($class::$run[$qishu]);
                        unset($class::$info[$qishu]);
                        unset($class::$bets[$qishu]);
                        Timer::del($timer_id);
                        /*
                          操作数据库   设置这期为已结算
                         */
                        if (!$id) {
                            $sql = "update $auto_table set status=2 where qishu=" . $qishu . " and status = 3";
                            $res = $manage->query($sql);
                            if (!$res) {
                                self::write_error_log($type . ' ' . $qishu . ' update auto over error' . PHP_EOL . $sql, 2, $type . '.txt');
                                return FALSE;
                            }
                        }
                    }
                });

        if ($id) { //批量结算时返回参数
            $result = array();
            $result['ErrorCode'] = 1;
            $result['ErrorMsg'] = '批量结算完成';
            $result['ok'] = $return_count;
            return $result;
        } else {
            return TRUE;
        }
    }
    /**
      ***********************************************************
      *  根据线路id判断线路的模式      @author ruizuo qiyongsheng    *
      ***********************************************************
    */
    public static function is_wallet($line_id){
        //获取所有线路
        $redis = RedisConPool::getInstace();
        $redis_key = 'line_list';
        $data = array();
        $data = $redis->get($redis_key);
        if ($data) {
            $data = json_decode($data, true);
        } else {
            $lottery = pdo::instance('lottery');
            $sql = 'select * from my_sys_line_list order by line_name asc';
            $data = $lottery->query($sql);
            $redis->set($redis_key, json_encode($data));
        }

        //处理验证
        $return = false;
        foreach($data as $key=>$val){
            if($val['line_id'] == $line_id && $val['type'] == 1){
                $return = true;
                break;
            }
        }

        return $return;
    }      
    /**
     * **********************************************************
     *  处理从外部进程访问结算类内存中的数据                           *
     * **********************************************************
     */
    public static function getDataFromMemory($class, $qishu) {
        $data = array();
        if ( !isset($class::$bets[$qishu]) || empty($class::$bets[$qishu])) return null;
        
        $data['balanceList'] = count($class::$bets);
        $data['bet_sum'] = count($class::$bets[$qishu]);
        $data['bets'] = $class::$bets[$qishu];
        $data['bet_status'] = $class::$run[$qishu];
        $data['bet_open'] = $class::$open;
        $data['qishu'] = $qishu;
        return json_encode($data);
    }
    /**
      ***********************************************************
      *  获取内存中注单数目           @author ruizuo qiyongsheng    *
      ***********************************************************
    */
    public static function getMemoryCount($class) {
        $data = array();
        if ( !isset($class::$bets) || empty($class::$bets)) return null;
        $keys = array_keys($class::$bets);
        if(empty($keys)) return null;
        foreach($keys as $k => $key){
            $data[$k]['qishu'] = $key;
            $data[$k]['count'] = count($class::$bets[$key]);
        }

        return json_encode($data);
    }
        
    /**
     * **********************************************************
     *  开奖结果单字段获取           @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public static function get_ball_name($auto, $play_id, $fc_type) {
        $ball_num = self::getBallNum($fc_type);
        $result = array();
        for ($i = 1; $i <= $ball_num; $i++) {
            $result[] = $auto['ball_' . $i];
        }
        if (!isset($auto['ball_' . $play_id])) {
            if (in_array($fc_type, array('ah_k3', 'js_k3', 'gx_k3'))) {
                if ($play_id == 108)
                    $play_id = 106; //独胆时取两连的值
            }elseif (in_array($fc_type, array('gd_11', 'jx_11', 'sd_11'))) {
                if (in_array($play_id, array(117, 118)))
                    $play_id = 116; //任选组选
            }elseif (in_array($fc_type, array('gd_ten', 'cq_ten'))) {
                if (in_array($play_id, array(39, 40, 41, 42)))
                    $play_id = 38; //任选组选
            }elseif (in_array($fc_type, array('bj_kl8', 'dm_klc', 'jnd_bs'))) {
                if (in_array($play_id, array(44, 45, 46, 47)))
                    $play_id = 43; //选一选五
            }elseif ($fc_type == 'liuhecai') {

            }
            //并非存在，直接返回正常球的数组
            if (!isset($auto['ball_' . $play_id])) {
                return $result; //???
            }

            $res = $auto['ball_' . $play_id];
        } else {
            $res = $auto['ball_' . $play_id];
        }
        return $res;
    }

    /*
     * 数字补0函数2，当数字小于10的时候在前面自动补00，当数字大于10小于100的时候在前面自动补0
     */

    public static function func_BuLings($num) {
        if (strlen($num) == 3) {
            return $num;
        }
        if ($num < 10) {
            $num = '00' . $num;
        }
        if ($num > 9 && $num < 100) {
            $num = '0' . $num;
        }
        return $num;
    }

    /*
     * 数字补0函数，当数字小于10的时候在前面自动补0
     */

    public static function func_BuLing($num) {
        if ($num < 10) {
            $num = '0' . $num;
        }
        return $num;
    }

    /**
     * **********************************************************
     * 根据type返回指定个数的ball @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    //球个数   表名（type）
    // 3        ah_k3 bj_28 dm_28 fc_3d gx_k3 jl_k3 jnd_28 js_k3 pc_28 pl_3
    // 5        cq_ssc gd_11 jx_11 jx_ssc sd_11 tj_ssc xj_ssc
    // 7        liuhecai
    // 8        cq_ten gd_ten
    //10        bj_10
    //20        bj_8 dm_klc jnd_bs

    public static function getBallNum($type) {
        $num = null;
        switch ($type) {
            case 'ah_k3':
            case 'bj_28':
            case 'dm_28':
            case 'fc_3d':
            case 'gx_k3':
            case 'jl_k3':
            case 'jnd_28':
            case 'js_k3':
            case 'pc_28':
            case 'pl_3' :
                $Num = 3;
                break;
            case 'cq_ssc':
            case 'gd_11' :
            case 'jx_11' :
            case 'jx_ssc':
            case 'sd_11' :
            case 'tj_ssc':
            case 'xj_ssc':
            case 'ffc_o' :
            case 'lfc_o' :
            case 'els_o' :
            case 'mnl_o' :
            case 'mg_o'  :
            case 'dj_o'  :
                $Num = 5;
                break;
            case 'liuhecai':
            case 'jsliuhecai':
                $Num = 7;
                break;
            case 'cq_ten':
            case 'gd_ten':
                $Num = 8;
                break;
            case 'bj_10' :
            case 'jsfc'  :
            case 'xdl_10':
                $Num = 10;
                break;
            case 'bj_kl8' :
            case 'dm_klc':
            case 'jnd_bs':
                $Num = 20;
                break;
            default:
                $Num = false;
                break;
        }

        return $Num;
    }

    /**
     * **********************************************************
     * 根据type返回相应端口号     @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public static function getPort($type) {
        $port = null;
        switch ($type) {
            case 'liuhecai':
                $port[] = 'null';
                $port[] = '8686';
            case 'bj_10':
            case 'xdl_10':
            case 'jsfc' :
                $port[] = '3333'; //采集端口
                $port[] = '8687'; //结算端口
                break;
            case 'ah_k3':
            case 'js_k3':
            case 'gx_k3':
                $port[] = '3334';
                $port[] = '8688';
                break;
            default:
                $port = false;
                break;
            case 'gd_11':
            case 'jx_11':
            case 'sd_11':
                $port[] = '3335';
                $port[] = '8689';
                break;
            case 'gd_ten':
            case 'cq_ten':
                $port[] = '3336';
                $port[] = '8690';
                break;
            case 'tj_ssc':
            case 'cq_ssc':
            case 'xj_ssc':
                $port[] = '3337';
                $port[] = '8691';
                break;
            case 'bj_kl8':
            case 'dm_klc':
            case 'jnd_bs':
                $port[] = '3338';
                $port[] = '8692';
                break;
            case 'bj_28':
            case 'pc_28':
            case 'jnd_28':
            case 'dm_28':
                $port[] = '3339';
                $port[] = '8693';
                break;
            case 'fc_3d':
            case 'pl_3':
                $port[] = '3340';
                $port[] = '8694';
                break;
            case 'jsliuhecai':
                $port[] = '3342';
                $port[] = '8686';
                break;
            case 'ffc_o':
            case 'lfc_o':
                $port[] = '3343';
                $port[] = '8695';
                break;
            case 'els_o':
            case 'dj_o' :
                $port[] = '3344';
                $port[] = '8696';
                break;
            case 'mnl_o':
            case 'mg_o' :
                $port[] = '3345';
                $port[] = '8697';
                break;
        }
        return $port;
    }

  
    /**
     * **********************************************************
     * 根据期数或ID进行注单回滚  @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public static function rollback($type, $periods, $id = '', $memory = array()) {
        //结算更改：bet: js status win 
        $lottery = pdo::instance('lottery');
        $manage = pdo::instance('manage');
        $auto_table = self::GetAutoTableNameByType($type); //获取开奖结果表名
        $bet_table = self::GetBetTableNameByType($type); //获取注单表名
        $user_record_tab = config::$tablePrefix . "user_cash_record"; //现金记录表
        $user_tab = 'my_user'; //用户表
        if (!is_numeric($periods)) {
            $result['ErrorMsg'] = '找不到期数';
            $result['ErrorCode'] = 2;
            return $result;
        }
        //从注单表中查出要回滚的注单
        $all_time = GetQishuOpentime::getOpentime($type, $periods);
        $time = $all_time['kaipan'];
        $close = $all_time['fengpan'];
        $addday = date('Ymd', $time);
        $close_day = date('Ymd', $close);
        //拼接查询条件
        $where = '';
        $where = ' js=2 and '; //查询条件
        $field = '*'; //要查询的字段
        if ($id) {
            $where .= " id in($id)" . ' and periods=' . $periods . " and addday>=$addday and addday<=$close_day";
        } else {
            $where .= 'periods=' . $periods . " and addday>=$addday and addday<=$close_day";
        }
        $count_sql = '';
        $count_sql = 'SELECT ' . "count(id) as betcount " . 'from ' . "`$bet_table`" . ' where' . $where;
        $bet_count = $lottery->single($count_sql);
        if (empty($bet_count)) {
            // echo $count_sql . PHP_EOL;
            self::write_error_log('Have no bet for rollback ' . 'type=' . $type . ' qishu=' . $periods . PHP_EOL . $count_sql, 2, $type . '.txt');
            $result['ErrorMsg'] = '没有需要回滚的注单';
            $result['ErrorCode'] = 2;
            return $result;
        }
        $sql = '';
        $sql = 'SELECT ' . $field . 'from ' . "`$bet_table`" . ' where' . $where . ' limit ' . $bet_count;
        $bet_arr = $lottery->query($sql); //所有需要回滚的注单
        //重新组装注单数据并将应扣除的金额计算出
        $bets = array();
        foreach ($bet_arr as $key => $val) {
            $bets[$val['uid']][$val['id']] = $val;
            $bets[$val['uid']][$val['id']]['is_wallet'] = self::is_wallet($val['line_id']);
            $bet_info = array();
            $bet_info = explode('#', $val['bet_info']);

            //计算要扣除的金额(所得金额+本金)
            switch ($val['status']) {
                case '2': // 胜
                    $bets[$val['uid']][$val['id']]['back'] = $val['odds'] * $val['bet'] + $val['return_water']; //扣所胜金额
                    //六合彩正肖 赔率计算
                    if($bet_info[0] == 'Just_Animal' && $type == 'liuhecai'){
                        $auto_table = self::GetAutoTableNameByType($type);

                        $sql = 'select `ball_180` as ball from ' . $auto_table . ' where qishu=' . $val['periods'];
                        $result = $manage->row($sql);
                        if($result){
                            $result = $result['ball'];
                            $count = 0;
                            $result = explode(',', $result);
                            foreach($result as $animal){
                                if($bet_info[1] == $animal){
                                    $count += 1;
                                }
                            }
                            $tmp_odds = $val['odds']/$count;
                            $bets[$val['uid']][$val['id']]['odds'] = $tmp_odds;
                            $bets[$val['uid']][$val['id']]['win'] =$tmp_odds * $val['bet'];
                            break;
                        }

                    }
                    $bets[$val['uid']][$val['id']]['odds'] = $val['odds'];
                    $bets[$val['uid']][$val['id']]['win'] = $val['win'];
                    break;
                case '3'://输
                    if ($val['return_water'] > 0) {
                        $bets[$val['uid']][$val['id']]['back'] = $val['return_water']; //扣返水
                    } else {
                        $bets[$val['uid']][$val['id']]['back'] = 0;
                    }
                    $bets[$val['uid']][$val['id']]['win'] = $val['odds'] * $val['bet'];
                    $bets[$val['uid']][$val['id']]['odds'] = $val['odds'];
                    break;
                case '4'://和
                    $bets[$val['uid']][$val['id']]['back'] = $val['bet']; //扣本金
                    $bets[$val['uid']][$val['id']]['win'] = $val['odds'] * $val['bet'];
                    $bets[$val['uid']][$val['id']]['odds'] = $val['odds'];
                    break;
                case '6'://特珠玩法
                    $odds_arr = explode(',', $bet_info[2]);
                    if (count($odds_arr) == 3) {
                        $tmp_odds = explode(':', $odds_arr[2]);
                        $tmp_odds = $tmp_odds[1];
                    }
                    if (count($odds_arr) == 2) {
                        $tmp_odds = explode(':', $odds_arr[1]);
                        $tmp_odds = $tmp_odds[1];
                    }
                    $odds_arr = explode(':', $odds_arr[0]); //赔率
                    $bets[$val['uid']][$val['id']]['back'] = $val['win']; //扣所胜
                    $bets[$val['uid']][$val['id']]['odds'] = $tmp_odds; //取最小赔率
                    $bets[$val['uid']][$val['id']]['win'] = $tmp_odds * $val['bet'];
                    break;
                    //  3/3:12,3/2:3  4/4:50,4/3:5,4/2:3 2/2:10   30/50  20/85
                case '7'://特珠玩法2
                    $odds_arr = explode(',', $bet_info[2]);
                    if (count($odds_arr) == 3) {
                        $tmp_odds = explode(':', $odds_arr[2]);
                        $tmp_odds = $tmp_odds[1];
                    }
                    if (count($odds_arr) == 2) {
                        $tmp_odds = explode(':', $odds_arr[1]);
                        $tmp_odds = $tmp_odds[1];
                    }
                    $odds_arr = explode(':', $odds_arr[1]); //赔率
                    $bets[$val['uid']][$val['id']]['back'] = $val['win']; //扣所胜
                    $bets[$val['uid']][$val['id']]['odds'] = $tmp_odds; //取最小赔率
                    $bets[$val['uid']][$val['id']]['win'] = $tmp_odds * $val['bet'];
                    break;
            }
        }
      

        //现金流水表里cash_do_type字段：新增6 为彩票回滚时扣除用户已胜的金额
        $arr = array();
        $arr['type'] = $type;
        $arr['bet_table'] = $bet_table;
        $arr['periods'] = $periods;
        static::$rollback = $bets;  //载入注单
        static::$rollback_count = count($bets);
        for ($i = 1; $i <= 8; $i++) {
          $data = self::getRollbackData($arr);
          if ($data) {
                $$i = new AsyncTcpConnection('text://127.0.0.1:10003');
                $$i->onConnect = function($Async)use($data) {
                    $res = $Async->send(json_encode($data));
                };

                $$i->onMessage = function($Async, $responed)use($arr) {
                    if ($responed != 'TRUE') {
                        // self::write_error_log($type . ' ' . $qishu . ':' . $responed, 2, $type . '.txt');
                    }
                    static::$rollback_count --; 
                    $data = self::getRollbackData($arr);
                    if ($data) {
                        $Async->send(json_encode($data));
                    } else {
                        $Async->close();
                    }
                };
                $$i->connect();
            } else {
                break;
            }
        }
        if(static::$rollback_count <= 0){
            unset(static::$rollback);
            $Async->close();
        }
        $result['ErrorCode'] = 1;
        $result['ErrorMsg'] = '回滚成功';
        $result['ok'] = $bet_count;
        return $result;
    }
    public static function getRollbackData($arr){
            $data = [];
            $uid = key(static::$rollback);
            // var_dump($uid);
            if(isset(static::$rollback[$uid]) && !empty($uid)){
                $data = static::$rollback[$uid];
            }else{
                return false;
            }
            unset(static::$rollback[$uid]);
            $arr['info'] = $data;
            $arr['uid'] = $uid;
            return $arr;
       
    }
    /**
     * **********************************************************
     *  结算模块错误日志整理                                *****
     * **********************************************************
     * @author ruizuo qiyongsheng
     * @param string 写入日志的内容
     * @param int 使用现有的结算目录
     * @param string 指定完整的文件格式
     * @return boolean
     * **********************************************************
     */
    public static function write_error_log($content, $type, $file_name = '.txt') {


        $basedir = str_replace('\\', '/', dirname(__FILE__));
        $basedir .= '/../logs/';
        //指定日志目录
        $spider_log_dir = $basedir . 'spider_log/'; //采集错误日志
        $balance_log_dir = $basedir . 'balance_log/'; //结算错误日志
        $other_log_dir = $basedir . 'other_log/'; //结算错误日志
        //使用现有目录
        if ($type == 1) {
            $dir = $spider_log_dir;
        } elseif ($type == 2) {
            $dir = $balance_log_dir;
        } elseif ($type == 3) {
            $dir = $other_log_dir;
        }
        if (!file_exists($dir)) {
            mkdir($dir, 777, true);
        }
        //文件名称
        if ($file_name == '.txt') {
            $name = $dir . date('Ymd') . '_log' . $file_name;
        } elseif ($type == 1 || $type == 2) {
            $name = $dir . date('Ym') . '_log_' . $file_name;
        } else {
            $name = $dir . $file_name;
        }
        //文件内容
        $content = date('Y-m-d H:i:s', time()) . PHP_EOL . $content . PHP_EOL;
        $res = error_log($content, 3, $name);

        return $res;
    }

    /**
     * **********************************************************
     *  获取所有彩种             @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public static function getAllType($type = 'new') {
        $manage = pdo::instance('manage');
        $redis = RedisConPool::getInstace();
        $type_arr = $redis->get('all_fc_games_data');
        if ($type_arr) {
            $type_arr = json_decode($type_arr, true);
        } else {
            //查询所有的彩种
            $sql = "select id,type,name from `my_fc_games` where state=1";
            $type_arr = $manage->query($sql);
            if (!$type_arr) {
                self::write_error_log('未查到彩种列表', 3);
            } else {
                $redis->setex('all_fc_games_data', 600, json_encode($type_arr));
            }
        }
        //组装新数据
        $new_type_arr = array();
        foreach ($type_arr as $val) {
            if (empty($val['name'])) {
                $new_type_arr[$val['type']] = '未知彩种';
            } else {
                $new_type_arr[$val['type']] = $val['name'];
            }
        }

        if ($type == 'new') {
            return $new_type_arr; //已经组装的数据
        } elseif ($type == 'fc_id') {//方便获取彩种id
            $fc_id_arr = array();
            foreach ($type_arr as $key => $val) {
                $fc_id_arr[$val['type']] = $val['id'];
            }
            return $fc_id_arr;
        } else {
            return $type_arr; //未组装的数据
        }
    }

    /**
     * **********************************************************
     *  检测表名是否存在         @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public static function tableNameExist($tabname, $db = 'lottery') {
        $sql = 'show tables';
        if ($db == 'lottery') {
            $table_array = pdo::instance('lottery')->query($sql);
        } elseif ($db == 'manage') {
            $table_array = pdo::instance('manage')->query($sql);
        } else {
            return false;
        }

        foreach ($table_array as $val) {
            foreach ($val as $v) {
                $tables_name[] = $v;
            }
        }
        return in_array($tabname, $tables_name);
    }

  
    /**
     * **********************************************************
     *  根据fc_type获取大玩法      @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public static function getGameplay($fc_type) {
        $manage = pdo::instance('manage');
        $redis = RedisConPool::getInstace();
        $init_key = 'init_fc_limit';
        $gameplay = $redis->hget($init_key, $fc_type);
        if ($gameplay) {
            $gameplay = json_decode($gameplay, true);
        } else {
            //查询所有的彩种(只使用id,gameplay,加上其它字段是为了和lotteryApi/BetController/getNewPlayId 共享缓存)
            $sql = "select id,fc_type,gameplay,name,limit_min,single_field_max,single_note_max from `my_fc_games_set` where fc_type='{$fc_type}'";
            $gameplay = $manage->query($sql);
            if (!$gameplay) {
                self::write_error_log('未查到玩法列表', 3);
            } else {
                $redis->hset($init_key, $fc_type, json_encode($gameplay, true));
            }
        }

        return $gameplay;
    }

}
