<?php

namespace common\Controllers;

use Yii;
use common\models\LogModel;
use common\models\BetModel as Bet;
use common\models\CommonModel;
use common\helpers\mongoTables;
use common\helpers\Helper;
use common\helpers\lotteryOrm;

class Controller extends \yii\web\Controller {
    /*
     *
     * 前台(pc wap app) 后台(业主后台,代理后台,总后台) 记录所有请求
     *
     * mongodb  程序按天分 (查询时不能跨天)
     */

    public function beforeAction($action) {
        //写入mongo
        $request = Yii::$app->request;

        $httpMethod = $request->getMethod(); //get post put head ......
        $url = $request->getAbsoluteUrl(); //请求地址
        $params = $request->getRawBody(); //请求体

        $uid = Yii::$app->session->get('uid') ? Yii::$app->session->get('uid') : 0;
        $uname = Yii::$app->session->get('uname') ? Yii::$app->session->get('uname') : "Guest";
        $line_id = Yii::$app->session->get('line_id') ? Yii::$app->session->get('line_id') : "pk";
        $ip = Helper::getIpAddress();

        $projectName = Yii::$app->id; //配置文件main.php中的配置
        $module = Yii::$app->controller->module->id;

        $ptype = $this->returnPtypeByAppId($projectName);

        $controller = Yii::$app->controller->id;
        $action = Yii::$app->controller->action->id;
        //拼接当前路由
        if ($projectName == $module) {
            $route = $controller . '/' . $action;
        } else {
            $route = $module . '/' . $controller . '/' . $action;
        }
        $CN_name = Yii::$app->session->get('CN_name') ? Yii::$app->session->get('CN_name') : "ukown";
        $table = mongoTables::getTable('httpRequest');
        $insert = [
            'httpMethod' => $httpMethod,
            'url' => $url,
            'route' => $route,
            'params' => $params,
            'addtime' => time(),
            'date' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'ptype' => $ptype,
            'line_id' => $line_id,
            'uid' => $uid,
            'uname' => $uname,
            'menu_name' => isset($CN_name[$route]) ? $CN_name[$route] : "ukown"
        ];


        if (!in_array($route, $this->logFilter())) {
            LogModel::insertLog($table, $insert);
        }

        return parent::beforeAction($action);
    }

    //操作日志
    public function insertOperateLog($old, $new, $remark) {
        $table = mongoTables::getTable('operate');

        $projectName = Yii::$app->id; //配置文件main.php中的配置
        $module = Yii::$app->controller->module->id;
        $controller = Yii::$app->controller->id;
        $action = Yii::$app->controller->action->id;

        $ptype = $this->returnPtypeByAppId($projectName);
        $uid = Yii::$app->session->get('uid') ? Yii::$app->session->get('uid') : 0;
        $uname = Yii::$app->session->get('uname') ? Yii::$app->session->get('uname') : "Guest";
        $line_id = Yii::$app->session->get('line_id') ? Yii::$app->session->get('line_id') : "pk";
        $CN_name = Yii::$app->session->get('CN_name') ? Yii::$app->session->get('CN_name') : "ukown";
        $ip = Helper::getIpAddress();

        //拼接当前路由
        if ($projectName == $module) {
            $route = $controller . '/' . $action;
        } else {
            $route = $module . '/' . $controller . '/' . $action;
        }

        $insert = [
            "ptype" => $ptype,
            "old" => $old,
            "new" => $new,
            "uid" => $uid,
            "uname" => $uname,
            "remark" => $remark,
            "adddate" => date('Y-m-d H:i:s'),
            "line_id" => $line_id,
            "addtime" => time(),
            'ip' => $ip,
            'menu_name' => isset($CN_name[$route]) ? $CN_name[$route] : "ukown"
        ];
        LogModel::insertLog($table, $insert);
    }

    //查看日志此操作 不入库(mongo)
    public function logFilter() {
        return [
                //'log/request'
        ];
    }

    /*
     * 根据项目id返回平台类型
     *
     * 1=>总后台,2=>业主后台,3=>前台
     *
     */

    public function returnPtypeByAppId($appId) {
        $return = 0;
        switch ($appId) {
            case 'our_backend':
                $return = 1;
                break;
            case 'agent_backend':
                $return = 2;
                break;
            case 'frontend':
                $return = 3;
                break;
            default :
                $return = 0;
        }
        return $return;
    }

    // 获取当前期数，$type 表示类别，例：'liuhecai'
    public function get_fc_qishu($type) {
        if (empty($type)) {
            return false;
        }
        switch ($type) {
            case 'fc_3d':
            case 'pl_3':
                $action = 'get_fc_3d_qishu';
                break;
            case 'ah_k3':
            case 'gx_k3':
            case 'js_k3':
            case 'cq_ssc':
            case 'tj_ssc':
            case 'xj_ssc':
                $action = 'get_all_qishu';
                break;
            case 'bj_10':
                $action = 'get_bj_10_qishu';
                break;
            case 'ffc_o':
            case 'lfc_o':
            case 'els_o':
            case 'jsfc':
            case 'mg_o':
            case 'xdl_10':
            case 'dj_o':
            case 'mnl_o':
            case 'jsliuhecai':
                $action = 'get_gpc_qishu';
                break;
            case 'bj_28':
            case 'bj_kl8':
            case 'pc_28':
                $action = 'get_bj_28_qishu';
                break;
            case 'cq_ten':
                $action = 'get_cq_ten_qishu';
                break;
            case 'dm_28':
            case 'dm_klc':
                $action = 'get_dm_28_qishu';
                break;
            case 'gd_11':
            case 'gd_ten':
            case 'jx_11':
            case 'sd_11':
                $action = 'get_gd_11_qishu';
                break;
            case 'jnd_28':
            case 'jnd_bs':
                $action = 'get_jnd_28_qishu';
                break;
            case 'liuhecai':
                $action = 'get_liuhecai_qishu';
                break;

            default:
                return 1;
                break;
        }
        return $this->$action($type);
    }

    /**
     * **********************************************************
     *  各种获取期数   福彩3d 排列3                          *
     * **********************************************************
     */
    public static function get_fc_3d_qishu($type) {
        $opentime = self::getAllOpentime($type, 'one');
        $kaijiang = strtotime($opentime['kaijiang']);
        $now_time = time();
        if ($type == 'pl_3') {
            $date_Y = date('y', $now_time);
        } else {
            $date_Y = date('Y', $now_time);
        }
        $qishu = date("z", $now_time) + 1;
        if ($now_time >= $kaijiang && $now_time <= strtotime('23:59:59')) {
            $qishu += 1;
        }
        //初一到初七维护，从配置文件获取是否在初一和初七之后
        $tmp = isset(Yii::$app->params['fc_3d_pl_3']) ? Yii::$app->params['fc_3d_pl_3'] : true;
        if ($tmp) {
            $qishu -= 7;
        }
        $qishu = $date_Y . substr(strval($qishu + 1000), 1, 3);
        return $qishu;
    }

    /**
     * **********************************************************
     *  获取通用期数*
     * **********************************************************
     */
    public function get_all_qishu($type) {
        $result = self::getAllOpentime($type, 'all');
        $now_time = date("H:i:s");
        foreach ($result as $val) {
            //当前时间<开奖时间 并且开盘时间<当前时间
            if ($val['kaijiang'] == '00:00:00') {
                if (strtotime($val['kaipan']) <= strtotime($now_time)) {
                    $data_time = $val;
                    break;
                }
            }else{
                if (strtotime($now_time) < strtotime($val['kaijiang']) && strtotime($val['kaipan']) <= strtotime($now_time)) {
                    $data_time = $val;
                    break;
                }
            }
        }

        $date = date("Ymd");
        if ($type == 'cq_ssc') {
            if (strtotime($now_time) < strtotime('09:50:00') && strtotime($now_time) >= strtotime('01:55:00')) {
                return $date . '024';
            }
        }

        if (empty($data_time)) {
            //如果当前时间在当天十点后，默认期数取第二天第一期
            if ($type == 'ah_k3')
                $end = '22:00:00';
            if ($type == 'gx_k3')
                $end = '22:27:00';
            if ($type == 'js_k3')
                $end = '22:10:00';
            if ($type == 'cq_ssc')
                $end = '23:59:59';
            if ($type == 'tj_ssc')
                $end = '23:00:00';
            if ($type != 'xj_ssc') {
                if ($now_time > $end) {
                    $date = date("Ymd", strtotime("+1 day"));
                }
            }
            $data_time = self::getAllOpentime($type, 'one');
        }

        //判断是否跨天
        if ((strtotime($now_time) < strtotime('02:00:00')) && (strtotime($now_time) >= strtotime('00:00:00')) && $type == 'xj_ssc') {
            $date = date("Ymd", strtotime("-1 day"));
        }
        if ((strtotime($now_time) < strtotime('10:00:00')) && (strtotime($now_time) >= strtotime('02:00:00')) && $type == 'xj_ssc') {
            $data_time = self::getAllOpentime($type, 'one');
            $date = date("Ymd");
        }
        //返回ymd.补零后的期数
        return $date . str_pad($data_time['qishu'], 3, '0',STR_PAD_LEFT);
    }

    /**
     * **********************************************************
     *  获取北京pk10期数                                               *
     * **********************************************************
     */
    public function get_bj_10_qishu() {
        $now = time();
        // bj_pk10初始数据
        $old_qishu = 489173;
        $old_lizi = 1431446220; // 固定不要动
        $left = 9 * 60 + 2; // 每天第一期开盘时间(分钟)
        $now_time = ceil(($now - $old_lizi - 3 * 60) / 60 % (60 * 24) + 0.1); // 已过去的当天分钟总数

        $time = $now - $old_lizi - 3 * 60; // 秒数
        $day = floor(($time / (60 * 60 * 24)) - (7 * 2)); // 天数
        // 判断期数
        if ($time > 0) {
            $today = ceil(($now_time - $left) / 5);
            if ($today > 179)
                $today = 179;
            if ($now_time >= $left) {
                $old_qishu += ($day * 179 + $today);
                return $old_qishu;
            } else {
                $old_qishu += ($day * 179); // 当天第一期
                return $old_qishu;
            }
        } else {
            return false;
        }
    }

    //获取高频彩期数
    public function get_gpc_qishu($type) {
        switch ($type) {
            case 'ffc_o':
            case 'jsfc':
                $date = date('ymd');
                $time = 60;
                $zero = 4;
                break;
            case 'lfc_o':
                $date = date('ymd');
                $time = 120;
                $zero = 3;
                break;
            case 'jsliuhecai':
                $date = date('Ymd');
                $time = 120;
                $zero = 3;
                break;
            case 'els_o':
            case 'xdl_10':
                $date = date('Ymd');
                $time = 90;
                $zero = 3;
                break;
            case 'mg_o':
                $date = date('Ymd');
                $time = 45;
                $zero = 4;
                break;
            case 'mnl_o':
                $date = date('ymd');
                $time = 45;
                $zero = 4;
                break;
            case 'dj_o':
                return $this->get_dj_qishu();
                break;
        }
        $seconds = strtotime(date('Y-m-d')); //当天开始
        $now_seconds = time() - $seconds;
        $now_minute = ceil($now_seconds / $time);
        return $date . str_pad($now_minute, $zero, "0", STR_PAD_LEFT);
    }

    /**
     * **********************************************************
     *  获取东京1.5分彩期数        @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public function get_dj_qishu() {
        $now_time = time();
        $dj_time = $now_time + 3600; //东京时间比北京时间快一小时
        $date = date('Ymd', $dj_time);
        $seconds = strtotime(date('Y-m-d', $dj_time)); //当天开始
        $eight = $seconds + 8 * 3600; //八点钟
        $nine = $seconds + 9 * 3600; //九点钟

        if ($dj_time <= $eight) {
            $now_seconds = $dj_time - $seconds;
            $now_minute = ceil($now_seconds / 90);
        } elseif ($dj_time > $eight && $dj_time <= $nine) {
            $now_minute = (8 * 3600) / 90 + 1; //进入下一期
        } else {
            $now_seconds = $dj_time - $seconds;
            $now_minute = ceil($now_seconds / 90);
            $now_minute -= 40;
        }
        return $date . str_pad($now_minute, 3, "0", STR_PAD_LEFT);
    }

    /**
     * **********************************************************
     *  获取北京28 北京快乐8期数               *
     * **********************************************************
     */
    public function get_bj_28_qishu($now = '') {
        $now = time();
        // bj_kl8初始数据
        $old_qishu = 694602 - 1;
        ///$old_lizi = strtotime("2015-05-12 23:55:00"); // 固定不要动
        $old_lizi = 1431446100; // 固定不要动
        $left = 9 * 60; // 每天第一期开盘时间(分钟)
        $now_time = ceil(($now - $old_lizi - 5 * 60) / 60 % (60 * 24) + 0.1); // 已过去的当天分钟总数
        $time = $now - $old_lizi - 3 * 60; // 秒数
        $day = floor(($time / (60 * 60 * 24)) - (7 * 2)); // 天数
        // 判断期数
        if ($time > 0) {
            $today = ceil(($now_time - $left) / 5);
            if ($today > 179)
                $today = 179;
            if ($now_time >= $left) {
                $old_qishu += ($day * 179 + $today);
                return $old_qishu;
            } else {
                $old_qishu += ($day * 179); // 当天第一期
                return $old_qishu;
            }
        } else {
            return false;
        }
    }

    /**
     * **********************************************************
     *  获取重庆10分期数                                               *
     * **********************************************************
     */
    public function get_cq_ten_qishu($type) {
        $result = self::getAllOpentime($type, 'all');
        $now_time = date("H:i:s");
        $date = date('ymd');
        $tomorrow = date('ymd', strtotime("+1days"));
        //第一期 第十四期 特殊情况
        if (strtotime($now_time) >= strtotime('23:53:00') || strtotime($now_time) < strtotime('00:03:00')) {
            return $tomorrow . '001';
        }
        if (strtotime($now_time) >= strtotime('01:53:00') && strtotime($now_time) < strtotime('09:53:00')) {
            return $date . '014';
        }
        foreach ($result as $val) {
            //当前时间<开奖时间 并且开盘时间<当前时间
            if (strtotime($now_time) < strtotime($val['kaijiang']) && strtotime($val['kaipan']) <= strtotime($now_time)) {
                $data_time = $val;
                break;
            }
        }
        if (!$data_time) {
            $data_time = self::getAllOpentime($type, 'one');
        }
        //返回ymd.补零后的期数
        return $date . str_pad($data_time['qishu'], 3, '0',STR_PAD_LEFT);
    }

    /**
     * **********************************************************
     *  获取丹麦28丹麦快乐彩期数                    
     * **********************************************************
     */
    public function get_dm_28_qishu($type) {
        $time = time() - 1481864680;
        return 1788567 + (($time - $time % 300) / 300);
    }

    /**
     * **********************************************************
     *  获取广东11选5期数                                               *
     * **********************************************************
     */
    public function get_gd_11_qishu($type) {
        $result = self::getAllOpentime($type, 'all');
        $now_time = date("H:i:s");
        $date = date("Ymd");
        foreach ($result as $val) {
            //当前时间<开奖时间 并且开盘时间<当前时间
            if (strtotime($now_time) < strtotime($val['kaijiang']) && strtotime($val['kaipan']) <= strtotime($now_time)) {
                $data_time = $val;
                break;
            }
        }
        if (empty($data_time)) {
            $end = '23:00:00';
            if ($type == 'sd_11')
                $end = '22:55:00';
            if ($now_time >= $end) {
                $date = date("Ymd", strtotime("+1 day"));
            }

            $data_time = self::getAllOpentime($type, 'one');
        }
        //返回ymd.补零后的期数
        return $date . str_pad($data_time['qishu'], 2, '0',STR_PAD_LEFT);
    }

    /**
     * **********************************************************
     *  获取加拿大28期数                                               *
     * **********************************************************
     */
    public function get_jnd_28_qishu($type) {
        //取redis 判断当前时间和取出的期数是否相符
        //相符Ok，不符删除更新 此redis时效五分钟
        date_default_timezone_set("Asia/Shanghai");
        $redis = Yii::$app->redis;
        $hours = date("H");
        $now_time = date('Y-m-d H:i:s', time());
        $redis_key = 'c_lot_time_' . $type;
        $info = $redis->get($redis_key);
        $qishu = '';
        if ($info && $info !== 'false') {
            $info = json_decode($info, true);
            //查看当前时间是否在开封盘时间之间
            if (($info['kaipan'] <= $now_time ) && ($info['kaijiang']) > $now_time) {
                $qishu = $info['qishu'];
            } else {
                $where = 'status=1 and kaipan<=' . "'{$now_time}'" . ' and ' . "'{$now_time}'" . '<kaijiang';
                $result = CommonModel::getOhterOpentime($type, $where);
                if ($result) {
                    $qishu = $result['qishu'];
                    $redis->setex($redis_key, 300, json_encode($result)); //五分钟时效}
                }
            }
        } else {
            $where = 'status=1 and kaipan<=' . "'{$now_time}'" . ' and ' . "'{$now_time}'" . '<kaijiang';
            $result = CommonModel::getOhterOpentime($type, $where);
            if ($result) {
                $qishu = $result['qishu'];
                $redis->setex($redis_key, 300, json_encode($result)); //五分钟时效
            }
        }
        //如果查询不到取默认值
        if (empty($qishu)) {
            $default_redis_key = 'c_lot_defalt_time_' . $type;
            $default_result = $redis->get($default_redis_key);
            if ($default_result) {
                $data_time = json_decode($default_result, true);
                $qishu = $data_time['qishu'];
            } else {
                $data_time = CommonModel::getOhterOpentime($type, ['status' => 1]);
                $redis->setex($default_redis_key, 300, json_encode($data_time));
                $qishu = $data_time['qishu'];
            }
        }
        return $qishu;
    }

    public function get_liuhecai_qishu($type) {
        $redis = Yii::$app->redis;
        $hours = date("H");
        $now_time = date('Y-m-d H:i:s', time());
        $redis_key = 'c_lot_time_' . $type;
        $info = $redis->get($redis_key);
        $qishu = '';
        if ($info && $info !== 'false') {
            $info = json_decode($info, true);
            //查看当前时间是否在开封盘时间之间
            if (($info['kaipan'] <= $now_time ) && ($info['kaijiang']) > $now_time) {
                $qishu = $info['qishu'];
            } else {
                $where = "status ='1' and ('{$now_time}'>=kaipan) and ('{$now_time}'<kaijiang) ";
                $result = CommonModel::getOhterOpentime($type, $where);
                if ($result) {
                    $qishu = $result['qishu'];
                    $redis->setex($redis_key, 300, json_encode($result)); //五分钟时效}
                }
            }
        } else {
            $where = "status ='1' and ('{$now_time}'>kaipan) and ('{$now_time}'<kaijiang) ";
            $result = CommonModel::getOhterOpentime($type, $where);
            if ($result) {
                $redis->setex($redis_key, 300, json_encode($result)); //五分钟时效
                $qishu = $result['qishu'];
            }
        }
        //如果查询不到取默认值
        if (empty($qishu)) {
            $default_redis_key = 'c_lot_defalt_time_' . $type;
            $default_result = $redis->get($default_redis_key);
            if ($default_result) {
                $data_time = json_decode($default_result, true);
                $qishu = $data_time['qishu'];
            } else {
                $data_time = CommonModel::getOhterOpentime($type, ['status' => 1]);
                if ($data_time) {
                    $qishu = $data_time['qishu'];
                    $redis->setex($default_redis_key, 300, json_encode($data_time));
                }
            }
        }
        return $qishu;
    }

    /**
     * **********************************************************
     *  获取所有的开封盘时间         @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public static function getAllOpentime($type, $get = 'all') {

        $redis = Yii::$app->redis;
        $redis_key = 'opentime_list';
        $opentime = $redis->get($redis_key);
        if ($opentime) {
            $opentime = json_decode($opentime, true);
        } else {
            $opentime = CommonModel::getAllOpentime();
            $redis->set($redis_key, json_encode($opentime));
        }
        $new_arr = array();
        foreach ($opentime as $val) {
            if ($val['fc_type'] == $type) {
                $new_arr[] = $val;
            }
        }
        if ($get == 'all')
            return $new_arr; //所有
        if ($get == 'one') {//第一期
            foreach ($new_arr as $val) {
                if ($val['qishu'] == 1) {
                    return $val;
                }
            }
        }
        return false;
    }

    /**
     * **********************************************************
     *  根据彩种和期数获取开封盘时间@author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public function getFengpanByQishu($type, $qishu) {
        if (empty($type))
            return array();
        if ($type == 'bj_28' || $type == 'pc_28')
            $type = 'bj_kl8';
        if ($type == 'jnd_28')
            $type = 'jnd_bs';
        if ($type == 'dm_28')
            $type = 'dm_klc';
        $redis = Yii::$app->redis;
        $redis_key = 'fengpan_time_' . $type;
        $time_arr = $redis->get($redis_key);
        if ($time_arr)
            $time_arr = json_decode($time_arr, true);

        if ($type == 'liuhecai' || $type == 'jnd_bs') {
            $where = array();
            $where['qishu'] = $qishu;
            if ($time_arr && isset($time_arr['qishu']) && ($time_arr['qishu'] == $qishu)) {
                //缓存数据合法  什么也不干
            } else {
                $time_arr = Bet::getTimeByQishu($where, $type);
                if (!$time_arr)
                    return false;
                $redis->set($redis_key, json_encode($time_arr));
            }

            return $time_arr;
        }

        if ($type == 'fc_3d' || $type == 'pl_3') {//固定的开封盘
            $where = array();
            if ($type == 'pl_3')
                $qishu = '20' . $qishu;
            $where['fc_type'] = $type;
            $where['qishu'] = 1;
            if (!$time_arr) {
                $time_arr = Bet::getTimeByQishu($where, $type);
                if (!$time_arr)
                    return false;
                $redis->set($redis_key, json_encode($time_arr));
            }
            $kaijiang = $time_arr['kaijiang'];
            $fengpan = $time_arr['fengpan'];
            $kaipan = $time_arr['kaipan'];
            $str_len = strlen($qishu);
            $date = substr($qishu, 0, 4);
            $qishu = substr($qishu, 4, $str_len - 4);
            $qishu = intval($qishu) - 1;
            //初一到初七维护，从配置文件获取是否在初一和初七之后
            $tmp = isset(Yii::$app->params['fc_3d_pl_3']) ? Yii::$app->params['fc_3d_pl_3'] : true;
            if ($tmp) {
                $qishu += 7;
            }
            $kaipan_day = date('Y-m-d', strtotime('+' . $qishu - 1 . ' days', strtotime($date . '-01-01')));
            $fengpan_day = date('Y-m-d', strtotime('+' . $qishu . ' days', strtotime($date . '-01-01')));
            $data = array();
            $data['kaipan'] = $kaipan_day . ' ' . $kaipan;
            $data['fengpan'] = $fengpan_day . ' ' . $fengpan;
            $data['kaijiang'] = $fengpan_day . ' ' . $kaijiang;
            return $data;
        }
        //数据库中现有数据的彩种开封盘时间
        //gd_11 jx_11 9:00-23:00  sd_11 8:25-22:55
        //cq_ten 23:53-23:53 gd_ten 9:00-23:00
        //xj_ssc 10:00-2:00  tj_ssc 9:00-23:00 cq_ssc 00:00-23:00
        //js_k3 8:30-22:10 gx_k3 9:27-22:27 ah_k3 8:50-22:00

        $qishu_arr = ['gd_11', 'jx_11', 'sd_11', 'gd_ten', 'cq_ten', 'xj_ssc', 'tj_ssc', 'cq_ssc', 'js_k3', 'gx_k3', 'ah_k3'];
        if (in_array($type, $qishu_arr)) {
            $str_len = strlen($qishu);
            if ($type == 'cq_ten') {
                $date = '20' . substr($qishu, 0, 6);
                $qishu = substr($qishu, 6, $str_len - 6);
            } else {
                $date = substr($qishu, 0, 8);
                $qishu = substr($qishu, 8, $str_len - 8);
            }
            $qishu = intval($qishu);
            $where = array();
            $where['fc_type'] = $type;
            $where['qishu'] = $qishu;
            if ($time_arr && isset($time_arr['qishu']) && ($time_arr['qishu'] == $qishu)) {
                //缓存数据合法  什么也不干
            } else {
                $time_arr = Bet::getTimeByQishu($where, $type);
                if (!$time_arr)
                    return false;
                $redis->set($redis_key, json_encode($time_arr));
            }
            $data = array();
            $data['kaipan'] = date('Y-m-d H:i:s', strtotime($date . ' ' . $time_arr['kaipan']));
            $data['fengpan'] = date('Y-m-d H:i:s', strtotime($date . ' ' . $time_arr['fengpan']));
            $data['kaijiang'] = date('Y-m-d H:i:s', strtotime($date . ' ' . $time_arr['kaijiang']));
            if ($type == 'cq_ten' && $qishu == 1) {
                $yestoday = date('Y-m-d', strtotime('-1 days', strtotime($date)));
                $data['kaipan'] = $yestoday . ' ' . $time_arr['kaipan'];
            }
            if ($type == 'xj_ssc' && $qishu >= 85) {
                $tomorrow = date('Y-m-d', strtotime('+1 days', strtotime($date)));
                $data['kaipan'] = $tomorrow . ' ' . $time_arr['kaipan'];
                $data['fengpan'] = $tomorrow . ' ' . $time_arr['fengpan'];
                $data['kaijiang'] = $tomorrow . ' ' . $time_arr['kaijiang'];
            }

            return $data;
        }

        if ($type == 'bj_10' || $type == 'bj_kl8') {//五分钟一期，4分封盘
            if ($type == 'bj_kl8') {
                $begin_qishu = 694601;
                $begin_time = ' 09:00:00';
            }
            if ($type == 'bj_10') {
                $begin_qishu = 624497;
                $begin_time = ' 09:02:00';
            }
            if (strtotime(date('Y-m-d') . $begin_time) + 24 * 3600 < time()) {
                $first_time = strtotime(date('Y-m-d') . $begin_time) + 24 * 3600;
            } else {
                $first_time = strtotime(date('Y-m-d') . $begin_time);
            }
            if ($qishu < $begin_qishu) {
                return FALSE;
            }
            $ToDayNow = ($qishu - $begin_qishu) % 179;                            ////今天多少期
            if ($ToDayNow == 0) {
                $ToDayNow = 179;
            } else {
                $ToDayNow--;
            }
            $data = array();
            $data['kaipan'] = date('Y-m-d H:i:s', $first_time + $ToDayNow * 300);
            $data['fengpan'] = date('Y-m-d H:i:s', $first_time + $ToDayNow * 300 + 240);
            $data['kaijiang'] = date('Y-m-d H:i:s', $first_time + $ToDayNow * 300 + 300);
            return $data;
        }

        if ($type == 'dm_klc') {
            $data = array();
            $start_qishu = 1788567;  //初始期数
            $start_time = 1481864680; //初始时间
            $over_qishu = $qishu - $start_qishu; //已过去期数
            $over_time = $over_qishu * 5 * 60; //已经过去时间 五分钟一期
            $data['kaipan'] = date('Y-m-d H:i:s', $start_time + $over_time);
            $data['fengpan'] = date('Y-m-d H:i:s', $start_time + $over_time + 240);
            $data['kaijiang'] = date('Y-m-d H:i:s', $start_time + $over_time + 300);
            return $data;
        }

        //高频彩
        if ($type == 'jsliuhecai') {
            return $this->getGpctimeByQishu($qishu, 120, 8);
        }

        if (in_array($type, ['lfc_o'])) {
            return $this->getGpctimeByQishu($qishu, 120, 6);
        }

        if (in_array($type, ['els_o', 'xdl_10'])) {
            return $this->getGpctimeByQishu($qishu, 90, 8);
        }

        if ($type == 'jsfc' || $type == 'ffc_o') {
            return $this->getGpctimeByQishu($qishu, 60, 6);
        }

        if (in_array($type, ['mg_o'])) {
            return $this->getGpctimeByQishu($qishu, 45, 8);
        }

        if (in_array($type, ['mnl_o'])) {
            return $this->getGpctimeByQishu($qishu, 45, 6);
        }

        if ($type == 'dj_o') {
            $data = array();
            $date = substr($qishu, 0, 8);
            $str_len = strlen($qishu);
            $qishu = substr($qishu, 8, $str_len - 8);
            $dj_time = time() + 3600; //东京时间比北京时间快一小时
            $seconds = strtotime(date('Y-m-d', $dj_time)); //当天开始
            if ($qishu <= 320) { //东京八点钟以前
                $seconds += intval($qishu) * 90 - 90;
            } else {//东京八点钟以后
                $seconds += intval($qishu + 40) * 90 - 90;
            }
            $seconds -= 3600; //北京时间
            $data['kaipan'] = date('Y-m-d H:i:s', $seconds);
            $data['fengpan'] = date('Y-m-d H:i:s', $seconds + 35);
            $data['kaijiang'] = date('Y-m-d H:i:s', $seconds + 45);
            return $data;
        }
    }

    public function getGpctimeByQishu($qishu, $qishu_time, $strlen) {
        $data = array();
        $date = substr($qishu, 0, $strlen);
        if ($strlen == 6)
            $date = '20' . $date;
        $str_len = strlen($qishu);
        $qishu = substr($qishu, $strlen, $str_len - $strlen);
        //秒数
        $seconds = strtotime($date) + $qishu * $qishu_time - $qishu_time;
        $data['kaipan'] = date('Y-m-d H:i:s', $seconds);
        $data['fengpan'] = date('Y-m-d H:i:s', $seconds + $qishu_time - 10);
        $data['kaijiang'] = date('Y-m-d H:i:s', $seconds + $qishu_time);
        return $data;
    }

    //根据当前域名获取线路id
    public static function get_url_line_id($url) {
        //从redis取开盘时间数据，取不到从数据库查询并存入redis
        $redis = Yii::$app->redis;
        $redis_key = $url;
        $temp = $redis->get($redis_key);
        if ($temp) {
            $data = json_decode($temp, true);
        } else {
            $connection = \Yii::$app->db;             ///防止数据为空
            $open_table = Helper::GetLineTableNameByType();
            $where = "url ='$url'";
            $sql = "select `line_id` from $open_table where " . $where . ' limit 1';
            $data = $connection->createCommand($sql)->queryOne();
            $redis->set($redis_key, json_encode($data));
        }
        return $data;
    }

    //所有彩种(公共方法不要瞎改)
    public function getAllFcTypes() {
        $redis = Yii::$app->redis;
        $redis_key = 'c_lot_all_game_site';
        $redis_data = $redis->get($redis_key);
        if (!empty($redis_data)) {
            $data = json_decode($redis_data, true);
        } else {
            $data = CommonModel::getAllFcTypes();
            if (!empty($data)) {
                $redis->set($redis_key, json_encode($data));
            }
        }
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                $data[$key]['img_path'] = Yii::$app->params['cdn_href'] . '/images/lotterytype/' . $val['img_path'];
            }
        }

        return $data;
    }

    //所有彩种分类(公共方法不要瞎改)
    public function getAllFcCates() {
        $redis = Yii::$app->redis;
        $redis_key = 'games_cat';
        $redis_data = $redis->get($redis_key);
        if (!empty($redis_data)) {
            $data = json_decode($redis_data, true);
        } else {
            $data = CommonModel::getAllFcCates();
            if (!empty($data)) {
                $redis->set($redis_key, json_encode($data));
            }
        }
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                $data[$key]['img_path'] = Yii::$app->params['cdn_href'] . '/images/lotterytype/' . $val['img_path'];
            }
        }
        return $data;
    }

    /**
     * **********************************************************
     *  获取所有线路下拉菜单数据      @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public function getLines() {
        $redis = Yii::$app->redis;
        $redis_key = 'line_list';
        $data = array();
        $data = $redis->get($redis_key);
        if ($data) {
            $data = json_decode($data, true);
        } else {
            $count = CommonModel::count(array(), 'sys_line_list');
            if ($count == 0)
                return $data;
            $data = CommonModel::getData('*', array(), 0, $count, 'line_name ASC', 'sys_line_list');
            if (empty($data))
                return $data;
            $redis->set($redis_key, json_encode($data));
        }

        return $data;
    }

    /**
     * **********************************************************
     *  根据IP返回地址                       *****
     * **********************************************************
     * @author ruizuo
     * @param string 要查询的IP地址
     * @return string 当前IP城市名称
     * **********************************************************
     */
    public function convertip($ip) {
        //IP数据文件路径
        $dat_path = str_replace("\\", '/', dirname(__FILE__)) . "/other/qqwry-utf8.dat";
        //检查IP地址
        if (!preg_match("/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/", $ip)) {
            return 'IP地址错误';
        }
        //打开IP数据文件
        if (!$fd = @fopen($dat_path, 'rb')) {
            return 'IP数据文件不存在或拒绝访问';
        }

        //分解IP进行运算，得出整形数
        $ip = explode('.', $ip);
        $ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

        //获取IP数据索引开始和结束位置
        $DataBegin = fread($fd, 4);
        $DataEnd = fread($fd, 4);
        $ipbegin = implode('', unpack('L', $DataBegin));
        if ($ipbegin < 0) {
            $ipbegin += pow(2, 32);
        }

        $ipend = implode('', unpack('L', $DataEnd));
        if ($ipend < 0) {
            $ipend += pow(2, 32);
        }

        $ipAllNum = ($ipend - $ipbegin) / 7 + 1;

        $BeginNum = 0;
        $EndNum = $ipAllNum;

        $ip1num = '';
        $ip2num = '';
        //使用二分查找法从索引记录中搜索匹配的IP记录
        while ($ip1num > $ipNum || $ip2num < $ipNum) {
            $Middle = intval(($EndNum + $BeginNum) / 2);

            //偏移指针到索引位置读取4个字节
            fseek($fd, $ipbegin + 7 * $Middle);
            $ipData1 = fread($fd, 4);
            if (strlen($ipData1) < 4) {
                fclose($fd);
                return '系统错误';
            }
            //提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
            $ip1num = implode('', unpack('L', $ipData1));
            if ($ip1num < 0) {
                $ip1num += pow(2, 32);
            }

            //提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
            if ($ip1num > $ipNum) {
                $EndNum = $Middle;
                continue;
            }

            //取完上一个索引后取下一个索引
            $DataSeek = fread($fd, 3);
            if (strlen($DataSeek) < 3) {
                fclose($fd);
                return '系统错误';
            }
            $DataSeek = implode('', unpack('L', $DataSeek . chr(0)));
            fseek($fd, $DataSeek);
            $ipData2 = fread($fd, 4);
            if (strlen($ipData2) < 4) {
                fclose($fd);
                return '系统错误';
            }
            $ip2num = implode('', unpack('L', $ipData2));
            if ($ip2num < 0) {
                $ip2num += pow(2, 32);
            }

            //没找到提示未知
            if ($ip2num < $ipNum) {
                if ($Middle == $BeginNum) {
                    fclose($fd);
                    return '未知错误';
                }
                $BeginNum = $Middle;
            }
        }

        $ipFlag = fread($fd, 1);
        if ($ipFlag == chr(1)) {
            $ipSeek = fread($fd, 3);
            if (strlen($ipSeek) < 3) {
                fclose($fd);
                return '系统错误';
            }
            $ipSeek = implode('', unpack('L', $ipSeek . chr(0)));
            fseek($fd, $ipSeek);
            $ipFlag = fread($fd, 1);
        }
        $ipAddr1 = '';
        $ipAddr2 = '';
        if ($ipFlag == chr(2)) {
            $AddrSeek = fread($fd, 3);
            if (strlen($AddrSeek) < 3) {
                fclose($fd);
                return '系统错误';
            }
            $ipFlag = fread($fd, 1);
            if ($ipFlag == chr(2)) {
                $AddrSeek2 = fread($fd, 3);
                if (strlen($AddrSeek2) < 3) {
                    fclose($fd);
                    return '系统错误';
                }
                $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
                fseek($fd, $AddrSeek2);
            } else {
                fseek($fd, -1, SEEK_CUR);
            }

            while (($char = fread($fd, 1)) != chr(0)) {
                $ipAddr2 .= $char;
            }

            $AddrSeek = implode('', unpack('L', $AddrSeek . chr(0)));
            fseek($fd, $AddrSeek);

            while (($char = fread($fd, 1)) != chr(0)) {
                $ipAddr1 .= $char;
            }
        } else {
            fseek($fd, -1, SEEK_CUR);
            while (($char = fread($fd, 1)) != chr(0)) {
                $ipAddr1 .= $char;
            }

            $ipFlag = fread($fd, 1);
            if ($ipFlag == chr(2)) {
                $AddrSeek2 = fread($fd, 3);
                if (strlen($AddrSeek2) < 3) {
                    fclose($fd);
                    return '系统错误';
                }
                $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
                fseek($fd, $AddrSeek2);
            } else {
                fseek($fd, -1, SEEK_CUR);
            }
            while (($char = fread($fd, 1)) != chr(0)) {
                $ipAddr2 .= $char;
            }
        }
        fclose($fd);

        //最后做相应的替换操作后返回结果
        if (preg_match('/http/i', $ipAddr2)) {
            $ipAddr2 = '';
        }
        $ipaddr = "$ipAddr1 $ipAddr2";
        $ipaddr = preg_replace('/CZ88.Net/is', '', $ipaddr);
        $ipaddr = preg_replace('/^s*/is', '', $ipaddr);
        $ipaddr = preg_replace('/s*$/is', '', $ipaddr);
        if (preg_match('/http/i', $ipaddr) || $ipaddr == '') {
            $ipaddr = '未知错误';
        }

        return $ipaddr;
    }

    /* ------------------------下面是查询处理开奖结果的模块--------------------- */
 /**
          ***********************************************************
          *  获取所有彩种近期开奖结果      @author ruizuo qiyongsheng    *
          ***********************************************************
    */
    public function getLastAuto(){
        $redis = Yii::$app->redis;
        $auto_key = 'all_list_auto';
        $data = $redis->hgetall($auto_key);
        $all_data = $this->getAllFcTypes();
        $type_arr = array();
        foreach($all_data as $val){
            $type_arr[] = $val['type'];
        }

        $return = array();
        foreach($type_arr as $type){
            if(isset($data[$type])){
                $return[$type] = $this->getAutoBall($type, json_decode($data[$type],true));
            }else{
                $return[$type] = $this->getLastAutoByType($type);
            }
        }
       
       return $return;

    }
        
        
    /**
     * **********************************************************
     *  获取单彩种开奖结果            @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public function getLastAutoByType($fc_type){
        $return  = array();
        $redis = Yii::$app->redis;
        $auto_key = 'all_list_auto';
        $tmp_auto = $redis->hget($auto_key, $fc_type);
        if ($tmp_auto == '') {//如果为空，从数据库查询并存入hash表
            $last_auto = CommonModel:: getAutoData($fc_type, array(), 'qishu desc', 0, 1); //最近一期开奖结果
            if (!empty($last_auto) && isset($last_auto[0])) {
                $tmp_auto = $last_auto[0];
                $tmp_auto['type'] = $fc_type;
                $redis->hset($auto_key, $fc_type, json_encode($tmp_auto));
            } else {
                return array();
            }
        } else {
            $tmp_auto = json_decode($tmp_auto, true);
        }

        $return = $this->getAutoBall($fc_type, $tmp_auto); //获取基本球

        return $return;
    }

    /**
      ***********************************************************
      *  获取开奖结果基本球       @author ruizuo qiyongsheng    *
      ***********************************************************
    */
    public function getAutoBall($fc_type, $auto){
        $return  = array();
        //根据彩种获取球的个数并处理开奖结果
        $lotteryOrm = new lotteryOrm();
        $ball_num = $lotteryOrm->getBallNum($fc_type);
        //取出开奖结果
        $ball_arr = array();
        for ($i = 1; $i <= $ball_num; $i++) {
            $ball_arr[] = 'ball_' . $i;
        }
        $return['qishu'] = $auto['qishu'];
        $return['type'] = $auto['type'];
        $return['datetime'] = date('Y-m-d H:i:s', $auto['datetime']);
        $tmp = array();
        foreach ($auto as $key => $val) {
            if (in_array($key, $ball_arr)) {
                $ball = explode(',', $val);
                $tmp[] = $ball[0];
            }
        }
        //赛车系列 颜色
        if(in_array($fc_type, ['bj_10', 'jsfc', 'xdl_10'])){
            $bj_auto = array();
            foreach($tmp as $k=>$v){
                $bj_color_arr = ['', 'bj-yellow', 'bj-blue', 'bj-black', 'bj-orange', 'bj-azure', 'bj-deepblue', 'bj-silver', 'bj-red', 'bj-brown', 'bj-green'];
                $v = intval($v);
                $bj_auto[$k]['number'] = $v;
                $bj_auto[$k]['color'] = isset($bj_color_arr[$v]) ? $bj_color_arr[$v] : '';
            }
            $tmp = $bj_auto;
        }

        //六合彩 极速六合彩 （生肖）
        if (in_array($fc_type, ['liuhecai', 'jsliuhecai'])) {
            $ch_arr = $lotteryOrm->getCH('liuhecai');
            $ball = [];
            $ball['red_wave'] = [1, 2, 7, 8, 12, 13, 18, 19, 23, 24, 29, 30, 34, 35, 40, 45, 46];
            $ball['blue_wave'] = [3, 4, 9, 10, 14, 15, 20, 25, 26, 31, 36, 37, 41, 42, 47, 48];
            $ball['green_wave'] = [5, 6, 11, 16, 17, 21, 22, 27, 28, 32, 33, 38, 39, 43, 44, 49];
            $animal_arr = $this->get_all_animal();
            $tran = [''];
            $new_arr = array();
            foreach($animal_arr as $animal=>$num_arr){
                foreach($num_arr as $num){
                    $new_arr[$num]['animal'] = isset($ch_arr[$animal]) ? $ch_arr[$animal] : $animal;
                    $new_arr[$num]['number'] = $num;
                    $new_arr[$num]['color'] = '';
                    foreach($ball as $color=>$val){
                        if(in_array($num, $val)){
                             $new_arr[$num]['color'] = $color;
                        }
                    }
                }
            }
            $liuhecai_auto = array();
            foreach($tmp as $v){
                $liuhecai_auto[] = $new_arr[intval($v)];
            }
            $tmp = $liuhecai_auto;
        }

       $return['ball'] = $tmp;
       return $return;
    }

    /**
     * **********************************************************
     *  获取六合彩生肖数组           @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public function get_all_animal($type = '') {
        $animal_arr = array('mouse', 'cattle', 'tiger', 'rabbit', 'dragon', 'snake', 'horse', 'sheep', 'monkey', 'chicken', 'dog', 'pig');
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
                    foreach ($ball as $wave => $val) {
                        $return[$key][$k]['color'] = '';
                        if (in_array($v, $val)) {
                            $return[$key][$k]['color'] = $wave;
                        }
                    }
                }
                return $return;
            }
         }
        return $shenxiao_num_arr; //返回新的生肖数组
    }

    /* ------------------------上面是查询处理开奖结果的模块--------------------- */

    /**
     * **********************************************************
     *  翻译注单的bet_info       @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public function new_bet_info($fc_type, $bet_info) {
        $lotteryOrm = new lotteryOrm();
        $return = array();
        $tmp_bet_info = explode('#', rtrim($bet_info, '#'));
        //翻译投注内容
        if ($fc_type == 'pc_28')
            $fc_type = 'pc_dd';
        $ch_arr = $lotteryOrm->getCH($fc_type);
        if (!$ch_arr) {
            $mingxi = isset($tmp_bet_info[2]) ? $tmp_bet_info[2] : '';
            $return['gameplayTxt'] = $tmp_bet_info[0];
            $return['input_nameTxt'] = $tmp_bet_info[1] . $mingxi;
            return $return;
        }
        //斗牛的屁股和梭哈一样，这无语的设计。。。
        if ($tmp_bet_info[0] == 'Bullfighting') {
            $tmp_str = '';
            if ($tmp_bet_info[1] == 'cow_five')
                $tmp_str = '牛5';
            if ($tmp_bet_info[1] == 'cow_four')
                $tmp_str = '牛4';
            if ($tmp_bet_info[1] == 'cow_cow')
                $tmp_str = '牛牛';
            if ($tmp_bet_info[1] == 'cow_big')
                $tmp_str = '牛大';
            if ($tmp_bet_info[1] == 'cow_small')
                $tmp_str = '牛小';
            if ($tmp_bet_info[1] == 'cow_single')
                $tmp_str = '牛单';
            if ($tmp_bet_info[1] == 'cow_double')
                $tmp_str = '牛双';
            if (in_array($tmp_bet_info[1], ['cow_five', 'cow_four', 'cow_big', 'cow_small', 'cow_single', 'cow_double', 'cow_cow'])) {

                $return['gameplayTxt'] = '斗牛';
                $return['input_nameTxt'] = $tmp_str;
                return $return;
            }
        }
        //六合彩 合肖 生肖连
        if (in_array($tmp_bet_info[0], ['SumAnimal', 'AnimalEven'])) {
            $tmp_arr = explode(',', $tmp_bet_info[1]);
            $tmp_str = '';
            foreach ($tmp_arr as $val) {
                $tmp_str .= $ch_arr[$val] . ',';
            }
            $return['gameplayTxt'] = $ch_arr[$tmp_bet_info[0]];
            $return['input_nameTxt'] = rtrim($tmp_str, ',');
            return $return;
        } elseif ($tmp_bet_info[0] == 'PassTest') {//过关
            $arr1 = explode(',', $tmp_bet_info[1]);
            $arr2 = explode(',', $tmp_bet_info[2]);
            $str = isset($ch_arr['PassTest']) ? $ch_arr['PassTest'] : '';
            $str1 = '';
            foreach ($arr1 as $key => $val) {
                if (isset($ch_arr[$val]) && isset($ch_arr[$arr2[$key]])) {
                    $str1 .= $ch_arr[$arr2[$key]] . ' ' . $ch_arr[$val] . ',';
                }
            }
            $return['gameplayTxt'] = $str;
            $return['input_nameTxt'] = rtrim($str1, ',');
            return $return;
        }

        $gameplay = isset($tmp_bet_info[0]) ? $tmp_bet_info[0] : '';
        foreach ($tmp_bet_info as $key => $val) {
            if ($val) {
                if (array_key_exists($val, $ch_arr)) {
                    $tmp_bet_info[$key] = $ch_arr[$val];
                } else {
                    continue;
                }
            } else {
                continue;
            }
        }
        $mingxi = isset($tmp_bet_info[2]) ? $tmp_bet_info[2] : '';
        //以下玩法翻译时不需要明细
        $not = ['All_Animal', 'Seven_code', 'Te_First_num', 'Te_Animal', 'random_choose_two', 'random_choose_two_group', 'random_choose_three', 'random_choose_four', 'random_choose_five'];
        if(in_array($gameplay, $not)) $mingxi = '';

        $return['gameplayTxt'] = $tmp_bet_info[0];
        $return['input_nameTxt'] = trim($tmp_bet_info[1], ',') . ' ' . trim($mingxi, ',');
        return $return;
    }

    /**
     * 获取维护状态
     * @mtype 1.全网维护 2.一般维护
     * @ptype 终端 1:pc,2:wap,agent,spider,api
     * @line_id 线路ID
     * @fc_type 彩种 一般维护类型的pc和wap端 才需要传
     * @author Frank
     */
    public function game_is_maintain($mtype, $ptype, $line_id, $fc_type = '') {
        $result['return'] = 1;
        $result['remark'] = '';
        $result['starttime'] = '';
        $result['endtime'] = '';

        $ptypes = [1 => 'pc', 2 => 'wap', 3 => 'agent', 4 => 'spider', 5 => 'api'];
        $ptype = isset($ptypes[$ptype]) ? $ptypes[$ptype] : $ptype; // 如果传的终端类型是数字，则转换

        if ($mtype == 2) { // 如果是一般维护，则先判断全网
            $data = Yii::$app->redis->get("maintain_{$ptype}_all_line_ids");
            $data = json_decode($data, true);
            if ($data) {
                if (in_array($line_id, $data['line_id'])) { // 如果真，则直接返回维护
                    $result['return'] = 2;
                    //如果设置了开始时间和结束时间 验证是否过期
                     if(isset($data['endtime']) && time() >= $data['endtime']){
                         $result['return'] = 1;
                    }
                    $result['remark'] = $data['remark'];
                    $result['starttime'] = $data['starttime'];
                    $result['endtime'] = $data['endtime'];
                    return $result;
                }
            }
        }

        $judgment_fc_type = $mtype == 2 && in_array($ptype, ['pc', 'wap']);
        if ($judgment_fc_type) {
            $data = Yii::$app->redis->hget("maintain_{$ptype}_one_line_ids", $line_id);
        } else {
            if (in_array($ptype, ['api', 'spider'])) {
                $data = Yii::$app->redis->get("maintain_{$ptype}_all_line_ids", false);
            } else {
                $data = Yii::$app->redis->get("maintain_{$ptype}_all_line_ids");
            }
        }

        $data = json_decode($data, true);
        if ($data) {
            if ($judgment_fc_type) {
                $return = in_array($fc_type, $data['fc_type']) ? 2 : 1;
            } else {
                $return = in_array($line_id, $data['line_id']) ? 2 : 1;
            }
            $result['return'] = $return;
            //如果设置了开始时间和结束时间 验证是否过期
            if(isset($data['endtime']) && time() >= $data['endtime']){
                 $result['return'] = 1;
            }
            $result['remark'] = $data['remark'];
            $result['starttime'] = $data['starttime'];
            $result['endtime'] = $data['endtime'];
        }
        return $result;
    }
    /**
          ***********************************************************
          *  查看所有彩种的维护状态      @author ruizuo qiyongsheng    *
          ***********************************************************
    */
    public function getAllTypeMaintain($mtype, $ptype, $line_id){
        $ptypes = [1 => 'pc', 2 => 'wap', 3 => 'agent', 4 => 'spider', 5 => 'api'];
        $ptype = isset($ptypes[$ptype]) ? $ptypes[$ptype] : $ptype; 
        $data = Yii::$app->redis->hget("maintain_{$ptype}_one_line_ids", $line_id);
        if($data){
            $data = json_decode($data, true);
        }else{
            $data = array();
        }
        $wh_arr = isset($data['fc_type']) ? $data['fc_type'] : array();
        $starttime = isset($data['starttime']) ? $data['starttime'] : 0;
        $endtime = isset($data['endtime']) ? $data['endtime'] : 0;
        $remark = isset($data['remark']) ? $data['remark'] : '';
        $all_data = $this->getAllFcTypes();
        $type_arr = array();
        $return = array();
        foreach($all_data as $val){
            $type_arr[] = $val['type'];
        }


        foreach($type_arr as $val){
            $return[$val]['starttime'] = '';
            $return[$val]['endtime'] = '';
            $return[$val]['remark'] = '';
            $return[$val]['return'] = 1;
            if( in_array($val, $wh_arr) && $endtime > time() ){
                $return[$val]['starttime'] = isset($starttime) ? $starttime : 0;
                $return[$val]['endtime'] = isset($endtime) ? $endtime : 0;
                $return[$val]['remark'] = isset($remark) ? $remark : '';
                $return[$val]['return'] = 2;
            }
        }

        return $return;
    }
        
        

    /**
     * **********************************************************
     *  清除指定前缀或后缀的redis    @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public function batchDelRedis($type = '', $pix = '') {
        //type: 前缀before,后缀after
        //pix :前后缀
        $redis = Yii::$app->redis;
        $result['ErrorCode'] = 2;
        if (empty($pix)) {
            $result['ErrorMsg'] = '请输入要清除的Key';
            return json_encode($result);
        }
        // 获取Redis   Key
        if ($type == 'before' && $pix) {
            $keys = $redis->keys($pix . '*');
        } elseif ($type == 'after' && $pix) {
            $keys = $redis->keys('*' . $pix);
        } else {
            $keys = $pix;
            $is_exists = $redis->exists($keys);
        }
        if (!isset($is_exists) && !is_array($keys)) {
            $result['ErrorMsg'] = '键名不存在';
            return json_encode($result);
        }
        $res = false;
        if (is_array($keys)) {
            foreach ($keys as $val) {
                $res = $redis->del($val);
            }
        } else {
            $res = $redis->del($keys);
        }
        if ($res) {
            $result['ErrorCode'] = 1;
        } else {
            $result['ErrorMsg'] = '清除失败！';
        }
        return json_encode($result);
    }

}
