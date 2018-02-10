<?php
namespace Applications\Tools\Lib;
use \config\config;
use \helper\MysqlPdo as pdo;
use \helper\RedisConPool as Redis;
use \workerman\Lib\Timer;
use \helper\IdWork;
use \helper\Curl;
use \helper\MongoDBManager;
use \helper\GetQishuOpentime;

/**
 * Bet controller
 */
class Addbetstest{
    /**
      ***********************************************************
      *  模拟六合彩注单信息          @author ruizuo qiyongsheng    *
      ***********************************************************
    */
    public static function getLiuhecaiPlayData($type =false){
        $lottery = pdo::instance('lottery');
        $manage = pdo::instance('manage');

        $return = array();
        $return['fc_type'] = 'liuhecai';
        $qishu = GetQishuOpentime::get_qishu('liuhecai');
        $return['qishu'] = $qishu;
        //查询赔率表数据
        $redis_key = 'tmp_odds_test';
        $redis = Redis::getInstace();
        $odds = $redis->get($redis_key);
        if($odds){
          $odds = json_decode($odds, true);
        }else{
          $sql = "select * from my_fc_games_type where fc_type='liuhecai'";
          $odds = $manage->query($sql);
          if($odds){
            $redis->setex($redis_key, 10000 , json_encode($odds));
          }
        }

       if($type) return $odds;
       foreach($odds as $key=>$val){
          $odds[$key]['money'] = rand(1,99);
       }
       $keys = array_rand($odds, 40);
        
       $bets = array();
       foreach($keys as $val){
          $bets[] = $odds[$val];
       }

       $return['data'] = $bets;
       return $return;
    }
    /**
          ***********************************************************
          *  查询会员           @author ruizuo qiyongsheng    *
          ***********************************************************
    */
    public static function getUid(){
        $redis = Redis::getInstace();
        $key = 'test_uid_for_addbets';
        if( !$redis->exists($key) || $redis->llen($key) < 10 ){
            $lottery = pdo::instance('lottery');
            $min = 1518087169;
            $max = 1518105599; //模拟会员10000个  于2月8日插入
            $sql = 'select uid from my_user where addtime>=' . $min . ' and addtime<' . $max . ' order by uid limit 11000';
            $user_data = $lottery->query($sql);
            foreach($user_data as $val){
                $redis->rpush($key, $val['uid']);            
            }
        }
        $uid =  $redis->rpop($key);
        return $uid;
    }
        
    
    /**
     * **********************************************************
     *  下注           @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public static function addbet() {
        $post = self::getLiuhecaiPlayData();

        $lottery = pdo::instance('lottery');
        $manage = pdo::instance('manage');

        $fc_type = isset($post['fc_type']) ? trim($post['fc_type'], '"') : null;
        $qishu = isset($post['qishu']) ? trim($post['qishu'], '"') : null;
        $bet_data = isset($post['data']) ? $post['data'] : null;
        $result = array();
        $result['ErrorCode'] = 2;
        if (empty($fc_type) || empty($qishu) || empty($bet_data)) {
            echo '参数缺失'; return;
        }
  
        // 获取用户uid
        $uid = self::getUid();
        $line_id = 'aaa';
        $ptype = 1;
        $is_shiwan = 1;//是否试玩
        $is_wallet =  false; //是否钱包模式
       
       //获取用户信息
        $sql = 'select * from my_user where uid=' . $uid;
        $user_info = $lottery -> row($sql);
      
        //查询代理名称
        $sql = 'select * from my_user_agent where id=' . $user_info['agent_id'];
        $agent_info = $lottery -> row($sql);
        $agent_name = $agent_info['login_user'];
        $agent_money = $agent_info['money'];
        
        //验证注单合法性
        $bet_sum_money = 0; //总下注金额
        if(! $is_wallet){
            $user_money = $user_info['money'];
        }

        $did_str = '';
        foreach ($bet_data as $key => $val) {
            $tmp = false;
            $money = intval($val['money']);
            $bet_sum_money += $money;
            $tmp = self::check_bet($fc_type, $bet_sum_money, $val, $user_info['agent_id']);
            if (!$tmp || !isset($tmp['order_num']) || !isset($tmp['play_id']) || empty($tmp['order_num'])) {
               unset($bet_data[$key]);
               continue;
            }
            $bet_data[$key]['play_id'] = $tmp['play_id'];
            if($is_wallet){//钱包模式不请求余额
                $bet_data[$key]['assets'] = 0;
                $bet_data[$key]['balance'] = 0;
            }else{
                $bet_data[$key]['assets'] = $user_money; //下注前金额
                $user_money -= $money;
                if ($user_money < 0) {
                    echo '您的余额不足，请充值';
                    return;
                }
                $bet_data[$key]['balance'] = $user_money; //下注后金额
            }
            $bet_data[$key]['order_num'] = $tmp['order_num'];
            if ($val['gameplay'] == 'Tema' && $val['pankou'] == 'B') {
                $bet_data[$key]['return_water'] = $money * '0.01';
            } else {
                $bet_data[$key]['return_water'] = 0;
            }

            $did_str .= $tmp['order_num'] . ',';
        }


        $ip = '127.0.0.1';
        $ip = sprintf('%u',ip2long($ip));//转成整形
        //拼接注单sql
        $insert = array();
        $field = [
            'line_id', 'at_id', 'ua_id', 'sh_id', 'at_username', 'uid',
            'uname', 'order_num', 'bet', 'valid_bet', 'balance', 'assets',
            'fc_type', 'odds', 'periods', 'win', 'handicap',
            'addtime', 'addday', 'updatetime', 'updateday', 'bet_info',
            'ptype', 'js', 'status', 'return_water', 'bet_ip', 'play_id', 'is_shiwan'
        ];

        $insert_sql = ''; //原生sql拼接
        $bet_sql = [];
        foreach ($bet_data as $key => $val) {
            $gameplay = $val['gameplay'];
            $pankou = isset($val['pankou']) ? $val['pankou'] : 'A';
            if ($pankou == 'A') {
                $pankou = 1;
            } else {
                $pankou = 2;
            }
            //处理特殊玩法的赔率
            if (
                    (($fc_type == 'liuhecai' || $fc_type == 'jsliuhecai') &&
                    $val['gameplay'] == 'JointMark' &&
                    stripos($val['mingxi'], '/')) ||
                    ( in_array($fc_type, ['bj_kl8', 'jnd_bs', 'dm_klc']) &&
                    stripos($val['mingxi'], '/') )
            ) {
                continue;
            } elseif (($fc_type == 'liuhecai' || $fc_type == 'jsliuhecai') && $gameplay == 'AnimalEven') {
            //生肖连的赔率：如果选中的有本命年的生肖，则使用本命年的生肖的赔率，因为本命年生肖多一个数字       
               continue;
            } elseif(($fc_type == 'liuhecai' || $fc_type == 'jsliuhecai') && $gameplay == 'PassTest'){
                //过关的赔率
                continue;
            } else {
                //正常赔率
                $odds = $val['odd'];
            }

            $bet_info = $val['gameplay'] . '#' . $val['input_name'] . '#' . $val['mingxi'];
            $insert[] = [
                $user_info['line_id'], $user_info['agent_id'], $user_info['ua_id'],
                $user_info['sh_id'], $agent_name, $user_info['uid'], $user_info['uname'],
                $val['order_num'], $val['money'], $val['money'],
                $val['balance'], $val['assets'], $fc_type, $odds, $qishu,
                $val['money'] * $odds, $pankou, time(), date('Ymd'),
                time(), date('Ymd'), $bet_info, $ptype, 1, 1,
                $val['return_water'], $ip, $val['play_id'], $is_shiwan
            ];

            //原生sql用于mycat
            $sql_field = 'insert into `lottery`.`my_bet_record` ( `id`, `line_id`, `at_id`, `ua_id`, `sh_id`, `at_username`, `uid`, `uname`, `order_num`, `bet`, `valid_bet`, `balance`, `assets`, `fc_type`, `odds`, `periods`, `win`, `handicap`, `addtime`, `addday`, `updatetime`, `updateday`, `bet_info`, `ptype`, `js`, `status`, `return_water`, `bet_ip`, `play_id`, `is_shiwan`) values ';
            $insert_sql .=  $sql_field .  '(' . "next value for MYCATSEQ_BETRECORD,'" . $user_info['line_id'] . "', " .  $user_info['agent_id'] . "," . $user_info['ua_id'] . "," . $user_info['sh_id'] . ", '" . $agent_name . "', " . $user_info['uid'] . ", '" . $user_info['uname'] . "', " . $val['order_num'] . ",'" . $val['money'] . "', '" . $val['money'] . "', '" .  $val['balance'] . "', '" . $val['assets'] . "', '" . $fc_type  . "', '" . $odds . "'," . $qishu  . ", '" . $val['money'] * $odds .  "', " . $pankou . ", " . time() . ',' . date('Ymd') . ', ' . time() . ', ' .  date('Ymd') . ", '" . $bet_info . "', " . $ptype . ", 1, 1, '" . $val['return_water'] . "', " . $ip . "," . $val['play_id'] . ', ' . $is_shiwan . ')###';

            $bet_sql[] = "('" . $user_info['line_id'] . "', " .  $user_info['agent_id'] . "," . $user_info['ua_id'] . "," . $user_info['sh_id'] . ", '" . $agent_name . "', " . $user_info['uid'] . ", '" . $user_info['uname'] . "', " . $val['order_num'] . ",'" . $val['money'] . "', '" . $val['money'] . "', '" .  $val['balance'] . "', '" . $val['assets'] . "', '" . $fc_type  . "', " . $odds . "," . $qishu  . ", '" . $val['money'] * $odds .  "', " . $pankou . ", " . time() . ',' . date('Ymd') . ', ' . time() . ', ' .  date('Ymd') . ", '" . $bet_info . "', " . $ptype . ", 1, 1, '" . $val['return_water'] . "', " . $ip . "," . $val['play_id'] . ', ' . $is_shiwan . ')';
        }

        if(empty($bet_data) || !isset($bet_data[0])) return;
        $lottery->beginTrans();

        try{
            //备注内容（现金纪录及请求接口额度转换）
            $work = new IdWork(1023);
            $count = count($bet_data);
            if ($count > 1) {
                $remark_did = $bet_data[0]['order_num'] . '~' . $bet_data[$count - 1]['order_num'];
            } else {
                $remark_did = $bet_data[0]['order_num'];
            }
            //扣除会员金额
       
            $field_money =  'money-' . $bet_sum_money;
            $sql = "update my_user set money=$field_money where uid=$uid and money>=$bet_sum_money";
            $changeMoney = $lottery->query($sql);
            if (!$changeMoney) {
                $lottery->rollBackTrans();
                echo '扣除会员金额失败'; return;
            }
          
            $cash = array(); //现金记录数据
            $remark = 'Lottery note：' . $remark_did . ' , type:' . $fc_type . ',共计:' . $count . '单'; //Lottery note：17100618015013618717~17100618015045352665 , type:bj_kl8 .',共计:'.4.'单'
            if($is_wallet) $remark .= "(requestId:$requestId)";
            $cash['uid'] = $uid;
            $cash['line_id'] = $user_info['line_id'];
            $cash['agent_id'] = $user_info['agent_id'];
            $cash['uname'] = $user_info['uname'];
            $cash['fc_type'] = $fc_type;
            $cash['periods'] = $qishu;
            $cash['cash_type'] = 2;
            $cash['cash_do_type'] = 1;
            $cash['dids'] = rtrim($did_str, ',');
            $cash['cash_num'] = $bet_sum_money;
            $cash['remark'] = $remark;
            $cash['ptype'] = $ptype;
            $cash['addtime'] = time();
            $cash['addday'] = date('Ymd');


            //查看是不是mycat分库（插入注单表数据）
            $is_mycat =  true;
            if($is_mycat){
                $insertBet = 0;
                $sql_arr = explode('###', rtrim($insert_sql, '###'));
                foreach($sql_arr as $sql){
                    $res = $lottery->query($sql);
                    // if(!$res) echo $sql . PHP_EOL;
                    if($res) $insertBet += 1;
                }
                // if (count($sql_arr) != $insertBet) {
                //     $lottery->rollBackTrans();
                //     echo '写入注单失败'; return;
                // }
                $return_count = count($sql_arr);
            }else{
                 $sql_field = 'insert into `lottery`.`my_bet_record` ( `line_id`, `at_id`, `ua_id`, `sh_id`, `at_username`, `uid`, `uname`, `order_num`, `bet`, `valid_bet`, `balance`, `assets`, `fc_type`, `odds`, `periods`, `win`, `handicap`, `addtime`, `addday`, `updatetime`, `updateday`, `bet_info`, `ptype`, `js`, `status`, `return_water`, `bet_ip`, `play_id`, `is_shiwan`) values ';
                 if(empty($bet_sql)) return;
                 if(count($bet_sql) > 1){
                    $sql = $sql_field . implode(',', $bet_sql);
                 }else{
                    $sql = $sql_field . $bet_sql[0];
                 }
                $insertBet = $lottery->query($sql);
                if (!$insertBet) {
                    $lottery->rollBackTrans();
                   echo '写入注单失败'; return;
                }

                $return_count = count($bet_sql);
            }

            //写入现金纪录
            if($is_wallet){
                $cash_balance = $wallet_money - $bet_sum_money;
            }else{
                $cash_balance = $user_info['money'] - $bet_sum_money;
            }
            if($is_mycat){
                $cash_sql = "insert into `lottery`.`my_user_cash_record` (`id`, `uid`, `line_id`, `agent_id`, `cash_type`, `cash_do_type`, `dids`, `cash_num`, `cash_balance`, `remark`, `ptype`, `addtime`, `addday`, `uname`, `fc_type`, `periods`, `is_shiwan`) values ( next value for MYCATSEQ_USERCASHRECORD, " . $uid . ", '" . $user_info['line_id'] . "', " . $user_info['agent_id'] . ", 2, 1, '" . rtrim($did_str, ',') . "', '" . $bet_sum_money . "', '" . $cash_balance . "', '" . $remark . "', " . $ptype . ", " . time() . ", " . date('Ymd') . ", '" . $user_info['uname'] . "', '" . $fc_type . "', " . $qishu . ', ' . $is_shiwan .  ")";
                $insertCash = $lottery->query($cash_sql);
            }else{
                 $cash_sql = "insert into `lottery`.`my_user_cash_record` (`uid`, `line_id`, `agent_id`, `cash_type`, `cash_do_type`, `dids`, `cash_num`, `cash_balance`, `remark`, `ptype`, `addtime`, `addday`, `uname`, `fc_type`, `periods`, `is_shiwan`) values (" . $uid . ", '" . $user_info['line_id'] . "', " . $user_info['agent_id'] . ", 2, 1, '" . rtrim($did_str, ',') . "', '" . $bet_sum_money . "', '" . $cash_balance . "', '" . $remark . "', " . $ptype . ", " . time() . ", " . date('Ymd') . ", '" . $user_info['uname'] . "', '" . $fc_type . "', " . $qishu . ', ' . $is_shiwan .  ")";
                
                $insertCash = $lottery->query($cash_sql);
            }
            if (!$insertCash) {
                $lottery->rollBackTrans();
              echo '纪录失败';return;
            }

            //组装下注注单数据
            $old_bets = array();
            foreach($insert as $val){
                $old_bets[] = array_combine($field, $val);
            }

            $pullredis = Redis::getInstace('pull');
            $mongo_data = array();
            //存储用于外部采集注单的数据
            foreach ($old_bets as $key => $val) {
                $score =  $work->nextId();
                $score = substr($score, -16);//score最大范围16位，超出自动转科学计数
                foreach($val as $k=>$v){
                    if(is_numeric($v)) $val[$k] = strval($v);
                }
                $val['score'] = strval($score);
                $res = $pullredis->zadd('spider_' . $user_info['line_id'] . '_data', $score, json_encode($val));
                if (!$res) {
                    $lottery->rollBackTrans();
                    echo '缓存数据失败';return;
                }
                $mongo_data[] = $val;
            }
            //插入Mongo bets表
            if(empty($mongo_data)){
                $lottery->rollBackTrans();
                echo '获取备份数据失败';return;
            }

            $mongo = new MongoDBManager();
            foreach($mongo_data as $val){
                 $mongo->insertData($val, 'bets');
            }
           
            //存储用于结算的缓存数据
            $redis = Redis::getInstace();
            $redis_key = 'for_balance-' .  $fc_type . '-' . $qishu;
            $user_key = 'user_'. $uid;
            $user_old_bet = $redis->hget($redis_key, $user_key);
            //如果用户有在当前彩种当前期数下过注，取出与现在的注单合并
            if($user_old_bet){
                $user_old_bet = json_decode($user_old_bet, true);
                $old_bets = array_merge($user_old_bet, $old_bets);
            }
            $redis->hset($redis_key, $user_key, json_encode($old_bets));

        }catch (Exception $e) {
            $lottery->rollBackTrans();
            if($is_wallet) self::errorCashRecord($cash);
            echo '下注失败';
            return;;
        }

        $lottery->commitTrans();
        return $return_count;
    }

    /**
     * **********************************************************
     *  验证注单合法性           @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function check_bet($fc_type, $sum_money, $data, $at_id) {
        $gameplay = $data['gameplay'];
        $pankou = isset($data['pankou']) ? $data['pankou'] : 'A';
        $mingxi = $data['mingxi'];
        $remark = $data['remark'];
        $odd = $data['odd'];
        $money = $data['money'];
        $input_name = $data['input_name'];
        $result = $return = array();
        $result['ErrorCode'] = 2;
        if (empty($gameplay) ||  empty($remark) || empty($odd) || empty($money) || $gameplay == '' || $odd == '' || $money == '') {
           echo '关键参数缺失';return;
        }

        //验证下注内容是否存在
        if($input_name === ''){
           // echo '请选择下注内容';return;
            return false;
        }
       
        $odds_arr = self::getLiuhecaiPlayData(true);
        self::checkAllInput($fc_type, $odds_arr, $input_name, $gameplay); 
        //检测金额
        if (!is_numeric($money)) {
            echo '非法金额';return;
        }
        //验证盘口
        if (!in_array($pankou, ['A', 'B'])) {
            echo '不能识别的盘口';return;
        }
        //验证玩法的合法性
        $play_id = self::getNewPlayId($fc_type, $gameplay);
        if (!$play_id) {
            echo '玩法不存在';return;
        } else {
            $return['play_id'] = $play_id;
        }

        //快乐十分连码 任选
        if (in_array($fc_type, ['gd_ten', 'cq_ten']) && in_array($gameplay, ['random_choose_two', 'random_choose_two_group', 'random_choose_three', 'random_choose_four', 'random_choose_five'])) {

            self::check_ten_inputname($gameplay, $input_name);
        }
        //十一选五任选直选组选
        if (in_array($fc_type, ['sd_11', 'gd_11', 'jx_11']) && in_array($gameplay, ['random_choose', 'group_choose', 'vertical_choose'])) {
            self::check_11_inputname($gameplay, $input_name, $mingxi);
        }
        //PC28特码包三
        if($fc_type == 'pc_28' && $input_name == 'Tema_in_Three'){
            self::check_dd_inputname($mingxi);
        }
        //六合彩尾数连 全不中
        if (($fc_type == 'liuhecai' || $fc_type == 'jsliuhecai') && $gameplay == 'EndNum_Even') {
            return;
        }
        if (($fc_type == 'liuhecai' || $fc_type == 'jsliuhecai') && $gameplay == 'AllMiss') {
           return;
        }
        //六合彩尾数
        if(($fc_type == 'liuhecai' || $fc_type == 'jsliuhecai') && $gameplay == 'EndNum'){
           return;
        }

        $work = new IdWork(1023);
        $return['order_num'] =  $work->nextId();

        return $return;
    }

    /**
     * **********************************************************
     *  获取play_id           @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function getNewPlayId($fc_type, $gameplay) {
        //获取初始限额并查看play_id是否存在
          //开奖结果存储方式：同一大分类彩种相同玩法的只存其中一个的play_id
        if ($fc_type == 'pl_3')
            $fc_type = 'fc_3d';
        if ($fc_type == 'cq_ten')
            $fc_type = 'gd_ten';
        if ($fc_type == 'jnd_28')
            $fc_type = $gameplay = 'dm_28';
        if (in_array($fc_type, ['dm_klc', 'jnd_bs']))
            $fc_type = 'bj_kl8';
        if (in_array($fc_type, ['xj_ssc', 'tj_ssc']))
            $fc_type = 'cq_ssc';
        if (in_array($fc_type, ['gx_k3', 'js_k3']))
            $fc_type = 'ah_k3';
        if (in_array($fc_type, ['sd_11', 'jx_11']))
            $fc_type = 'gd_11';
        if(in_array($fc_type, ['jsfc','xdl_10']))
            $fc_type = 'bj_10';
        if(in_array($fc_type, ['lfc_o','els_o','dj_o','mnl_o', 'mg_o']))
            $fc_type = 'ffc_o';
        if($fc_type == 'jsliuhecai') $fc_type = 'liuhecai';
        
        $redis = Redis::getInstace();
        $init_key = 'init_fc_limit';
        $manage = pdo::instance('manage');
        $init_limit = $redis->hget($init_key, $fc_type);
        if (empty($init_limit)) {
            $sql = "select * from my_fc_games_set where fc_type='$fc_type'";
            $init_limit = $manage -> query($sql);
            $redis->hset($init_key, $fc_type, json_encode($init_limit, true));
        } else {
            $init_limit = json_decode($init_limit, true);
        }

        $tmp = array();
        foreach($init_limit as $key=>$val){
            $tmp[$val['gameplay']] = $val['id'];
        }
        if(isset($tmp[$gameplay])) return $tmp[$gameplay];
        echo '获取玩法id失败';return;
    }

    /**
     * **********************************************************
     *  获取下一期期数           @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function getNextQishu($fc_type, $qishu) {
        $type_array = array('tj_ssc', 'cq_ssc', 'xj_ssc', 'gd_ten', 'cq_ten', 'js_k3', 'gx_k3', 'ah_k3', 'gd_11', 'sd_11', 'jx_11', 'ffc_o', 'lfc_o', 'els_o', 'jsfc', 'jsliuhecai', 'dj_o', 'mnl_o', 'xdl_10', 'mg_o');
        if (in_array($fc_type, $type_array)) {
            switch ($fc_type) {
                case 'tj_ssc':
                    $end_qishu = 84;
                    $zero = 2;
                    break;
                case 'cq_ssc':
                    $end_qishu = 120;
                    $zero = 2;
                    break;
                case 'xj_ssc':
                    $end_qishu = 96;
                    $zero = 2;
                    break;
                case 'gd_ten':
                    $end_qishu = 84;
                    $zero = 1;
                    break;
                case 'cq_ten':
                    $end_qishu = 97;
                    $zero = 2;
                    break;
                case 'js_k3':
                    $end_qishu = 82;
                    $zero = 2;
                    break;
                case 'gx_k3':
                    $end_qishu = 78;
                    $zero = 2;
                    break;
                case 'ah_k3':
                    $end_qishu = 80;
                    $zero = 2;
                    break;
                case 'gd_11':
                    $end_qishu = 84;
                    $zero = 1;
                    break;
                case 'sd_11':
                    $end_qishu = 87;
                    $zero = 1;
                    break;
                case 'jx_11':
                    $end_qishu = 84;
                    $zero = 1;
                    break;
                case 'ffc_o':
                case 'jsfc' :
                    $end_qishu = 1440;
                    $zero = 1;
                    break;
                case 'lfc_o':
                case 'jsliuhecai':
                    $end_qishu = 720;
                    $zero = 1;
                break;
                case 'els_o' :
                case 'xdl_10':
                    $end_qishu = 960;
                    $zero = 1;
                break;
                case 'dj_o' :
                    $end_qishu = 920;
                    $zero = 1;
                break;
                case 'mnl_o':
                case 'mg_o' :
                    $end_qishu = 1920;
                    $zero = 1;
                break;
            }
            return self::jiajian($fc_type, $qishu, $end_qishu, $zero);
        } else {
            return $qishu + 1;
        }
    }

    /**
     * **********************************************************
     *  当期数增加或减少到尽头时返回正确的期数                 *
     * **********************************************************
     */
    private static function jiajian($fc_type, $qishu, $end_qishu, $zero = 1) {
        $six_arr = ['cq_ten', 'ffc_o', 'jsfc', 'lfc_o', 'mnl_o'];
        $start_qishu = 1;
        $old_qishu = $qishu;
        $str_len = strlen($qishu);
        if (in_array($fc_type, $six_arr)) {
            $date_len = 6;
        } else {
            $date_len = 8;
        }
        $date = substr($qishu, 0, $date_len);
        $qishu = substr($qishu, $date_len, $str_len - $date_len);
        $qishu = intval($qishu);

        if ($qishu == $end_qishu) { //期数自增
            $tomorrow = date('Ymd', strtotime('+ 1days', strtotime($date)));
            if(in_array($fc_type, $six_arr)){
                $tomorrow = date('ymd', strtotime('+ 1days', strtotime($date)));
            }
            if ($zero == 1) {
                $qishu = $tomorrow . str_pad( $now_minute, 2 ,"0",STR_PAD_LEFT );
            } else {
                $qishu = $tomorrow . str_pad( $now_minute, 3 ,"0",STR_PAD_LEFT );
            }

            if( in_array($fc_type, ['ffc_o', 'jsfc', 'mg_o', 'mnl_o']) ){
                $qishu = $tomorrow . '0001';
            }elseif( in_array($fc_type, ['lfc_o', 'els_o', 'xdl_10', 'dj_o', 'jsliuhecai']) ){
                $qishu = $tomorrow . '001';
            }

        } else {
            $qishu = $old_qishu + 1;
        }
        return $qishu;
    }

   

    /**
     * **********************************************************
     *  获取快乐彩特殊玩法的赔率     @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function getNewOdds($odds) {
        $two = '';
        $three_3 = '';
        $three_2 = '';
        $four_4 = '';
        $four_3 = '';
        $four_2 = '';
        $five_5 = '';
        $five_4 = '';
        $five_3 = '';
        foreach ($odds as $key => $val) {
            if (!isset($val['mingxi']))
                continue;
            switch ($val['mingxi']) {
                case 'two_in_two':
                    $two = '2/2:' . floor($val['odd']);
                    break;
                case 'three_in_three':
                    $three_3 = '3/3:' . floor($val['odd']);
                    break;
                case 'three_in_two':
                    $three_2 = '3/2:' . floor($val['odd']);
                    break;
                case 'four_in_four':
                    $four_4 = '4/4:' . floor($val['odd']);
                    break;
                case 'four_in_three':
                    $four_3 = '4/3:' . floor($val['odd']);
                    break;
                case 'four_in_two':
                    $four_2 = '4/2:' . floor($val['odd']);
                    break;
                case 'five_in_five':
                    $five_5 = '5/5:' . floor($val['odd']);
                    break;
                case 'five_in_four':
                    $five_4 = '5/4:' . floor($val['odd']);
                    break;
                case 'five_in_three':
                    $five_3 = '5/3:' . floor($val['odd']);
                    break;
            }
        }
        foreach ($odds as $key => $val) {
            switch ($val['gameplay']) {
                case 'choose_one':
                    $odds[$key]['remark'] = '选一';
                    break;
                case 'choose_two':
                    $odds[$key]['remark'] = '选二';
                    $odds[$key]['odd'] = $two;
                    $odds[$key]['mingxi'] = $two;
                    break;
                case 'choose_three':
                    $odds[$key]['remark'] = '选三';
                    $odds[$key]['odd'] = $three_3 . ',' . $three_2;
                    $odds[$key]['mingxi'] = $three_3 . ',' . $three_2;
                    break;
                case 'choose_four':
                    $odds[$key]['remark'] = '选四';
                    $odds[$key]['odd'] = $four_4 . ',' . $four_3 . ',' . $four_2;
                    $odds[$key]['mingxi'] = $four_4 . ',' . $four_3 . ',' . $four_2;
                    break;
                case 'choose_five':
                    $odds[$key]['remark'] = '选五';
                    $odds[$key]['odd'] = $five_5 . ',' . $five_4 . ',' . $five_3;
                    $odds[$key]['mingxi'] = $five_5 . ',' . $five_4 . ',' . $five_3;
                    break;
                default:

                    break;
            }
        }
        $new_arr = array();
        foreach ($odds as $key => $val) {
            $new_arr[$val['remark']] = $val;
        }
        $odds = array_values($new_arr);
        return $odds;
    }

    /**
     * **********************************************************
     *  获取六合彩连码二中特特殊赔率     @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function getLianMaOdds($odds) {
        $in_te = '';
        $in_two = '';
        $three_in_three = '';
        $three_in_two = '';
        $tmp = array();
        foreach ($odds as $key => $val) {
            if ($val['mingxi'] == 'in_te') {
                $in_te = $val['odd'];
            }
            if ($val['mingxi'] == 'in_two') {
                $in_two = $val['odd'];
            }
            if ($val['mingxi'] == 'in_three') {
                $three_in_three = $val['odd'];
            }
            if ($val['mingxi'] == 'third_in_second') {
                $three_in_two = $val['odd'];
            }
        }
        foreach ($odds as $key => $val) {
            if ($val['mingxi'] == 'in_te' || $val['mingxi'] == 'in_two') {
                $odds[$key]['odd'] = floor($in_te) . '/' . floor($in_two);
                $odds[$key]['remark'] = '连码#二中特#';
                $odds[$key]['mingxi'] = '2/2:' . floor($in_te) . ',' . '2/1:' . floor($in_two);
            }

            if ($val['mingxi'] == 'in_three' || $val['mingxi'] == 'third_in_second') {
                $odds[$key]['odd'] = floor($three_in_three) . '/' . floor($three_in_two);
                $odds[$key]['remark'] = '连码#三中二#';
                $odds[$key]['mingxi'] = '3/3:' . floor($three_in_three) . ',' . '3/2:' . floor($three_in_two);
            }
        }
        $new_arr = array();
        foreach ($odds as $key => $val) {
            $new_arr[$val['remark']] = $val;
        }
        $odds = array_values($new_arr);
        return $odds;
    }

    /**
     * **********************************************************
     *  获取六合彩生肖连的赔率       @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function getAnimalEvenOdds($odds_arr, $input_name, $mingxi) {
        $result['ErrorCode'] = 2;
        //生肖数组
        $animal_arr = array('mouse', 'cattle', 'tiger', 'rabbit', 'dragon', 'snake', 'horse', 'sheep', 'monkey', 'chicken', 'dog', 'pig');
        $year = date('Y');
        $year_animal = $animal_arr[(($year - 4) % 12)]; //获取本命年生肖
        //检测是否在生肖数组内
        $input_arr = explode(',', $input_name);
        if (count($input_arr) < 2) {
            echo '下注内容错误'; return;
            
        }

        foreach ($input_arr as $val) {
            if (!in_array($val, $input_arr)) {
                echo '不能识别的下注内容';return;
            }
        }

        $other_animal = '';
        $is_year = false;
        if (in_array($year_animal, $input_arr)) {
            $is_year = true; //包含本命年生肖
        } else {
            $other_animal = $input_arr[0]; //除了本命年其它赔率一样
        }
        $tmp_arr = array();
        foreach ($odds_arr as $val) {
            $tmp_arr[$val['mingxi'] . '_' . $val['input_name']] = $val['odd'];
        }
        if ($is_year) {
            //本命年赔率
            $odd = isset($tmp_arr[$mingxi . '_' . $year_animal]) ? $tmp_arr[$mingxi . '_' . $year_animal] : '';
        } else {
            $odd = isset($tmp_arr[$mingxi . '_' . $other_animal]) ? $tmp_arr[$mingxi . '_' . $other_animal] : '';
        }
        if (empty($odd)) {
           echo '获取生肖连赔率异常';return;
        }

        return $odd;
    }

    /**
      ***********************************************************
      *  获取六合彩过关的赔率         @author ruizuo qiyongsheng    *
      ***********************************************************
    */
    private static function getPassTestOdds($odds_arr, $input_name, $mingxi){
        //检测下注合法性
        $result = array();
        $result['ErrorCode'] = 2;
        $tmp = true;
        if(empty($input_name) || empty($mingxi)) $tmp = false;
        $input_arr = explode(',', $input_name);
        $ball_arr = explode(',', $mingxi);
        if(count($input_arr) < 2) $tmp = false;
        if(count($input_arr) != count($ball_arr)) $tmp = false;
        if($tmp != true){
            echo '下注内容错误---';
            return;
        }
        if(count($input_arr) > 8){
            echo '抱歉您最多只能选择8注';return;
           
        }
        if(!$odds_arr){
            echo '赔率异常~';return;
           
        }
        $new_odds_arr = array();
        foreach($odds_arr as $val){
            if(!isset($val['mingxi']) || !isset($val['input_name'])){
                echo '赔率异常~~';return;
               
            }
            $new_odds_arr[$val['mingxi'] . $val['input_name']] = $val['odd'];
        }

        $ball = ['JustCode_one', 'JustCode_two', 'JustCode_three', 'JustCode_four', 'JustCode_five', 'JustCode_six'];
        $play = ['big', 'small', 'single', 'double', 'blue_wave', 'red_wave', 'green_wave'];
        $game_arr = array();
        foreach($input_arr as $key=>$val){
            $game_arr[] = $ball_arr[$key] . ':' . $val;
        }
        $new_odds = array();
        foreach($game_arr as $key=>$val){
            $tmp = array();
            $tmp = explode(':', $val);
            if(count($tmp) != 2){
                echo'下注内容错误--';return;
            }
            if(!in_array($tmp[0], $ball) || !in_array($tmp[1], $play)){
                echo '下注内容错误-';
                return;
            }
            if(!isset($new_odds_arr[$tmp[0] . $tmp[1]])){
                echo '赔率异常~~~';
                return;
            }
            $new_odds[] = $new_odds_arr[$tmp[0] . $tmp[1]];
        }
        //获取赔率（为所有赔率的总乘积）
        $odds =  array_product($new_odds);
        if($odds <= 0){
             if(!isset($new_odds_arr[$tmp[0] . $tmp[1]])){
                echo '赔率异常~~~~';
                return;
            }
        }
        return $odds;
    }
/**
      ***********************************************************
      *  获取六合彩尾数连赔率         @author ruizuo qiyongsheng    *
      ***********************************************************
*/
    private static function getEndNumOdds($odds_arr, $input_name, $mingxi){
        $input_arr = explode(',', $input_name);
        $num = count($input_arr);
        $tmp_num = 0;
        if(in_array($mingxi, ['two_end_in', 'two_end_not_in'])) $tmp_num = 2;
        if(in_array($mingxi, ['three_end_in', 'three_end_not_in'])) $tmp_num = 3;
        if(in_array($mingxi, ['four_end_in', 'four_end_not_in'])) $tmp_num = 4;
        if($num != $tmp_num){
            echo '下注内容错误';return;
        }
        $new_odds = array();
        foreach($odds_arr as $key=>$val){
            if(isset($val['input_name']) && isset($val['mingxi'])){
                $new_odds[$val['input_name'] . '_' .$val['mingxi']] = $val['odd'];
            }
        }
        //选中0尾后，如果是连中，选赔率高的，如果连不中，选赔率低的
        $is_exist = stripos($input_name, '0');
        if($is_exist || $is_exist === 0){
            $odds = isset($new_odds['zero_end_' . $mingxi]) ? $new_odds['zero_end_' . $mingxi] : '';
        }else{
            $odds = isset($new_odds['one_end_' . $mingxi]) ? $new_odds['one_end_' . $mingxi] : '';
        } 
        
        if($odds == ''){
            echo '赔率异常。。。';return;
        }
        return $odds;
    }
    
    
/**
      ***********************************************************
      *  处理尾数 尾数连赔率数据       @author ruizuo qiyongsheng    *
      ***********************************************************
*/
    private static function getEndNum($odds){
        foreach($odds as $key=>$val){
            switch($val['input_name']){
                case 'zero_end':
                    $odds[$key]['input_name'] = 0;
                break;
                 case 'one_end':
                    $odds[$key]['input_name'] = 1;
                break;
                 case 'two_end':
                    $odds[$key]['input_name'] = 2;
                break;
                 case 'three_end':
                    $odds[$key]['input_name'] = 3;
                break;
                 case 'four_end':
                    $odds[$key]['input_name'] = 4;
                break;
                 case 'five_end':
                    $odds[$key]['input_name'] = 5;
                break;
                 case 'six_end':
                    $odds[$key]['input_name'] = 6;
                break;
                 case 'seven_end':
                    $odds[$key]['input_name'] = 7;
                break;
                 case 'eight_end':
                    $odds[$key]['input_name'] = 8;
                break;
                case 'nine_end':
                    $odds[$key]['input_name'] = 9;
                break;
            }
        }
        return $odds;
    }

/**
      ***********************************************************
      *  获取彩种所有合法的input_name数组 @author ruizuo qiyongsheng    *
      ***********************************************************
*/
    private static function checkAllInput( $type, $odds, $input_name, $gameplay = ''){
        //特殊玩法不在此处 单独验证
       if (in_array($type, ['bj_kl8', 'dm_klc', 'jnd_bs']) && in_array($gameplay, ['choose_one', 'choose_two', 'choose_three', 'choose_four', 'choose_five'])) return;
       if(in_array($type, ['gd_ten', 'cq_ten']) && in_array($gameplay, ['random_choose_two', 'random_choose_two_group', 'random_choose_three', 'random_choose_four', 'random_choose_five'])) return;
       if (in_array($type, ['sd_11', 'gd_11', 'jx_11']) && in_array($gameplay, ['random_choose', 'group_choose', 'vertical_choose'])) return;
       if(($type == 'liuhecai' || $type == 'jsliuhecai') && in_array($gameplay, ['PassTest', 'JointMark', 'SumAnimal', 'AnimalEven', 'EndNum_Even', 'AllMiss', 'EndNum'])) return;
       if($type == 'pc_28' && $input_name == 'Tema_in_Three') return;


        //正常玩法 
        $input_arr = array();
        foreach($odds as $key=>$val){
            $input_arr[] = $val['input_name'];
        }
        $input_arr = array_unique($input_arr);
        if(!in_array($input_name, $input_arr)){
          var_dump($input_name,$input_arr);
            echo '下注内容错误'; return;
        }

        return;
    }
    
    /**
     * **********************************************************
     *  检测快乐彩特殊玩法下注内容是否合法@author ruizuo qiyongsheng  *
     * **********************************************************
     */
    private static function check_klc_inputname($gameplay, $input_name) {
        $num = 0;
        switch ($gameplay) {
            case 'choose_one':
                $num = 1;
                break;
            case 'choose_two':
                $num = 2;
                break;
            case 'choose_three':
                $num = 3;
                break;
            case 'choose_four':
                $num = 4;
                break;
            case 'choose_five':
                $num = 5;
                break;
        }
        $tmp = explode(',', $input_name);
        foreach($tmp as $val){
            if(!is_numeric($val)){
                echo '下注内容错误';return;
            }
        }
        if (count($tmp) != $num) {
            echo '下注内容错误';return;
        }
    }

    /**
     * **********************************************************
     *  检测快乐十分下注内容是否合法   @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function check_ten_inputname($gameplay, $input_name) {
        $num = 0;
        switch ($gameplay) {
            case 'random_choose_two':
                $num = 2;
                break;
            case 'random_choose_two_group':
                $num = 2;
                break;
            case 'random_choose_three':
                $num = 3;
                break;
            case 'random_choose_four':
                $num = 4;
                break;
            case 'random_choose_five':
                $num = 5;
                break;
        }
        $tmp = explode(',', $input_name);
        foreach($tmp as $val){
            if(!is_numeric($val)){
                $result['ErrorCode'] = 2;
                echo '下注内容错误';
                return;
            }
        }
        if (count($tmp) != $num) {
            $result['ErrorCode'] = 2;
            echo '下注内容错误';
            return;
        }
    }

    /**
     * **********************************************************
     *  检测十一选五下注内容是否合法   @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function check_11_inputname($gameplay, $input_name, $mingxi) {
        if (empty($gameplay) || empty($input_name) || empty($mingxi)) {
            $result['ErrorCode'] = 2;
            echo '下注内容错误';return;
        }
        $num = 0;
        $res = true;
        $tmp = count(explode(',', $input_name));
        if ($gameplay == 'random_choose') {
            switch ($mingxi) {
                case 'one_in_one':
                    $num = 1;
                    break;
                case 'two_in_two':
                    $num = 2;
                    break;
                case 'three_in_three':
                    $num = 3;
                    break;
                case 'four_in_four':
                    $num = 4;
                    break;
                case 'five_in_five':
                    $num = 5;
                    break;
                case 'six_in_five':
                    $num = 6;
                    break;
                case 'seven_in_five':
                    $num = 7;
                    break;
                default:
                    $num = 'wrong';
                    break;
            }
            if ($tmp != $num)
                $res = false;
        }elseif ($gameplay == 'group_choose' || $gameplay == 'vertical_choose') {
            switch ($mingxi) {
                case 'before_three':
                    $num = 3;
                    break;
                case 'before_two':
                    $num = 2;
                    break;
                default:
                    $num = 'wrong';
                    break;
            }
            if ($tmp != $num)
                $res = false;
        }else {
            $tmp = false;
        }

        if ($tmp == false) {
            echo '下注内容错误';return;
        }
    }
    /**
          ***********************************************************
          *  检测pc_28特码包三下注内容 @author ruizuo qiyongsheng    *
          ***********************************************************
    */
        private static function check_dd_inputname($mingxi){
            $tmp = explode(',', $mingxi);
            $tmp = array_unique($tmp);
             if (count($tmp) != 3) {
                echo '下注内容错误';return;
            }
            foreach($tmp as $v){
                if($v < 0 || $v > 27){
                    echo '下注内容错误';return;
                }
            }
        }
        
    /**
     * **********************************************************
     *  检测六合彩合肖下注内容        @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function check_hexiao_inputname($input_name, $mingxi) {
        $num = 0;
        switch ($mingxi) {
            case 'two_Animal':
                $num = 2;
                break;
            case 'three_Animal':
                $num = 3;
                break;
            case 'four_Animal':
                $num = 4;
                break;
            case 'five_Animal':
                $num = 5;
                break;
            case 'six_Animal':
                $num = 6;
                break;
            case 'seven_Animal':
                $num = 7;
                break;
            case 'eight_Animal':
                $num = 8;
                break;
            case 'nine_Animal':
                $num = 9;
                break;
            case 'ten_Animal':
                $num = 10;
                break;
            case 'elven_Animal':
                $num = 11;
                break;
            default:
                $num = 'wrong';
                break;
        }
        $tmp = explode(',', $input_name);
        foreach ($tmp as $key => $val) {
            if (!in_array($val, self::animal_arr())) {
                unset($tmp[$key]);
            }
        }
        if (count($tmp) != $num) {
            echo '下注内容错误';return;
        }
    }

    /**
     * **********************************************************
     *  检测六合彩生肖连下注内容      @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function check_shengxiaolian_inputname($input_name, $mingxi) {
        $num = 0;
        switch ($mingxi) {
            case 'two_Animal_in':
            case 'two_Animal_not_in':
                $num = 2;
                break;
            case 'three_Animal_in':
            case 'three_Animal_not_in':
                $num = 3;
                break;
            case 'four_Animal_in':
            case 'four_Animal_not_in':
                $num = 4;
                break;
            case 'five_Animal_in':
                $num = 5;
                break;
            default:
                $num = 'wrong';
                break;
        }
        $tmp = explode(',', $input_name);
        foreach ($tmp as $key => $val) {
            if (!in_array($val, self::animal_arr())) {
                unset($tmp[$key]);
            }
        }
        if (count($tmp) != $num) {
            echo '下注内容错误';return;
        }
    }
    /**
      ***********************************************************
      *  检测六合彩尾数下注内容           @author ruizuo qiyongsheng    *
      ***********************************************************
    */
    private static function check_endnum_inputname($input_name){
        if(!is_numeric($input_name) || $input_name < 0 || $input_name > 9){
            echo '下注内容错误';
            return;
        }
    }
        
        
    /**
     * **********************************************************
     *  检测六合彩尾数连下注内容      @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function check_weilian_inputname($input_name, $mingxi) {
        $num = 0;
        switch ($mingxi) {
            case 'two_end_in':
            case 'two_end_not_in':
                $num = 2;
                break;
            case 'three_end_in':
            case 'three_end_not_in':
                $num = 3;
                break;
            case 'four_end_in':
            case 'four_end_not_in':
                $num = 4;
                break;
            default:
                $num = 'wrong';
                break;
        }
        $tmp = explode(',', $input_name);
        foreach ($tmp as $key => $val) {
            if ($val < 0 || $val > 9) {
                unset($tmp[$key]);
            }
        }
        if (count($tmp) != $num) {
           echo '下注内容错误';return;
        }
    }

    /**
     * **********************************************************
     *  检测六合彩全不中下注内容      @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function check_allmiss_inputname($input_name, $mingxi) {
        $num = 0;
        switch ($mingxi) {
            case 'five_not_in':
                $num = 5;
                break;
            case 'six_not_in':
                $num = 6;
                break;
            case 'seven_not_in':
                $num = 7;
                break;
            case 'eight_not_in':
                $num = 8;
                break;
            case 'nine_not_in':
                $num = 9;
                break;
            case 'ten_not_in':
                $num = 10;
                break;
            case 'elven_not_in':
                $num = 11;
                break;
            case 'twelve_not_in':
                $num = 12;
                break;
            default:
                $num = 'wrong';
                break;
        }
        $tmp = explode(',', $input_name);
        foreach ($tmp as $key => $val) {
            if ($val < 1 || $val > 49) {
                unset($tmp[$key]);
            }
        }
        if (count($tmp) != $num) {
           echo '下注内容错误';
            
        }
    }

    /**
     * **********************************************************
     *  十二生肖数组              @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function animal_arr() {
        return array('mouse', 'cattle', 'tiger', 'rabbit', 'dragon', 'snake', 'horse', 'sheep', 'monkey', 'chicken', 'dog', 'pig');
    }

    /**
     * **********************************************************
     *  获取六合彩生肖数组           @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function get_animal($type = '') {
        $animal_arr = self::animal_arr();
        $year = date('Y');
        $year = $animal_arr[(($year - 4) % 12)]; //获取本命年生肖
        $shenxiao_arr = array('pig', 'dog', 'chicken', 'monkey', 'sheep', 'horse', 'snake', 'dragon', 'rabbit', 'tiger', 'cattle', 'mouse');

        $num_arr = array(
            array(1, 13, 25, 37, 49),
            array(2, 14, 26, 38),
            array(3, 15, 27, 39),
            array(4, 16, 28, 40),
            array(5, 17, 29, 41),
            array(6, 18, 30, 42),
            array(7, 19, 31, 43),
            array(8, 20, 32, 44),
            array(9, 21, 33, 45),
            array(10, 22, 34, 46),
            array(11, 23, 35, 47),
            array(12, 24, 36, 48)
        );


        $key = array_search($year, $shenxiao_arr);

        $begin_arr = array_splice($shenxiao_arr, $key);

        $end_arr = array_splice($shenxiao_arr, $key - 12);

        $new_shenxiao_arr = array_merge($begin_arr, $end_arr);

        $shenxiao_num_arr = array();
        foreach ($new_shenxiao_arr as $k => $v) {
            $shenxiao_num_arr[$v] = $num_arr[$k];
        }
        if ($type) { //加上色波
            $return = array();
            foreach ($shenxiao_num_arr as $key => $val) {
                foreach ($val as $k => $v) {
                    $return[$key][$k]['num'] = $v;
                    $return[$key][$k]['color'] = self::wave($v);
                }
            }
            return $return;
        }
        return $shenxiao_num_arr; //返回新的生肖数组
    }

    /**
     * **********************************************************
     *  六合彩波色                 @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function wave($num = '') {
        $ball = [];
        $ball['red'] = [1, 2, 7, 8, 12, 13, 18, 19, 23, 24, 29, 30, 34, 35, 40, 45, 46];
        $ball['blue'] = [3, 4, 9, 10, 14, 15, 20, 25, 26, 31, 36, 37, 41, 42, 47, 48];
        $ball['green'] = [5, 6, 11, 16, 17, 21, 22, 27, 28, 32, 33, 38, 39, 43, 44, 49];
        if (empty($num))
            return $ball;
        foreach ($ball as $wave => $val) {
            if (in_array($num, $val)) {
                return $wave;
            }
        }
    }

    /**
     * **********************************************************
     *  六合彩尾数连           @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    private static function endNum() {
        $arr = $data = $return = array();
        for ($i = 1; $i <= 49; $i++) {
            $arr[] = $i;
        }
        foreach ($arr as $val) {
            $end_num = $val % 10;
            $tmp_arr = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
            $data[$tmp_arr[$end_num] . '_end'][] = $val;
        }

        foreach ($data as $key => $val) {
            foreach ($val as $k => $v) {
                $return[$key][$k]['num'] = $v;
                $return[$key][$k]['color'] = self::wave($v);
            }
        }
        return $return;
    }

  

}
