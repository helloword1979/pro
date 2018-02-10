<?php

namespace our_backend\controllers;

use Yii;
use our_backend\controllers\Controller;
use our_backend\models\TaocanModel;
use common\models\StatModel as selfModel;
use common\models\AgentModel;
use common\models\UserModel;
use common\models\BetModel;
use common\models\LineModel;
use yii\helpers\ArrayHelper;
use common\helpers\Curl;

class StatController extends Controller {

    public function actionIndex() {
        $data['show'] = $this->showData();

        $request = Yii::$app->request->get();
        $result = $this->search($request, $data['show']);

        $render = [
            'data' => $data,
            'result' => $result
        ];
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('index.html', $render);
        } else {
            return $this->render('index.html', $render);
        }
    }

    public function actionLine() {
        $data['show'] = $this->showData();

        $request = Yii::$app->request->get();
        $request['tab'] = 'line';
        $result = $this->search($request, $data['show']);

        $render = [
            'data' => $data,
            'result' => $result
        ];
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('index.html', $render);
        } else {
            return $this->render('index.html', $render);
        }
    }

    public function actionCommission() {
        $request = Yii::$app->request->get();
        $result = $this->commission($request);

        $render = [
            'result' => $result
        ];
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('commission.html', $render);
        } else {
            return $this->render('commission.html', $render);
        }
    }

    public function actionRestat() {
        $request = Yii::$app->request->post();
        $result = $this->restat($request);

        return json_encode($result);
    }

    public function actionTask() {
        if ($this->task_is_running()) {
            $result['ErrorMsg'] = '任务正在运行中，请稍后再试';
            $result['ErrorCode'] = 2;
        } else {
            $result['ErrorMsg'] = 'OK';
            $result['ErrorCode'] = 1;
        }

        return json_encode($result);
    }

    public function actionExport() {
        $request = Yii::$app->request->get();
        $act = isset($request['act']) ? trim($request['act']) : '';

        switch ($act) {
            case 'commission':
                $line_id = isset($request['line_id']) ? trim($request['line_id']) : '';
                if (empty($line_id)) {
                    echo '线路不能为空';
                    return;
                }

                $request['display'] = 5;
                $data = $this->getStat($request, $this->showData());
                sort($data);
                if (empty($data)) {
                    echo '数据为空';
                    return;
                }

                $filename = '站点提成报表-' . $line_id . '-' . date('Y_m_d-H_i_s');

                $line = LineModel::getOne(['line_id'=>$line_id], 'id');
                $line_set = TaocanModel::get_set_one(['line_id'=>$line['id']]);
                $set = TaocanModel::get_one(['id'=>$line_set['tid']]);
                if (empty($set)) {
                    $set = TaocanModel::get_one(['id'=>1]);
                }

                $total_win = 0;
                foreach ($data as $key => &$val) {
                    $val['line_id'] = $line_id;
                    $val['taocan'] = $tname = $set['tname'];
                    $ttc = $set['pktc'];
                    $val['yingli'] = $val['bet'] - $val['win'];
                    $val['comm'] = $comm = $set['pktc'] . '%';
                    $val['paidup'] = round($val['yingli'] * $set['pktc'] / 100, 2);
                    $total_win += $val['yingli'];
                }unset($val);
                //合计
                $total = array();
                $total['line_id'] = '合计：';
                $total['yingli'] = $total_win;
                $total['agentname'] = '';
                $total['taocan'] = $tname;
                $total['comm'] = $comm;
                $total['paidup'] = round($total_win * $ttc / 100, 2);;
                $data[] = $total;

                $fields = [
                    'line_id' => '线路ID',
                    'agentname' => '代理账号',
                    'yingli' => '盈利',
                    'taocan' => '套餐名称',
                    'comm' => '抽成比例',
                    'paidup' => '应收金额'
                ];
                break;
            default:
                $result = $this->search($request, $this->showData());
                $data = $result['data'];

                if (empty($data)) {
                    echo '数据为空';
                    return;
                }

                $tab = isset($request['tab']) ? trim($request['tab']) : '';
                switch ($tab) {
                    case 'line':
                        $filename = '线路注单统计报表' . '-' . date('Y_m_d-H_i_s');
                        $fields = [
                            'id' => 'ID',
                            'fc_typeTxt' => '彩种',
                            'line_id' => '线路',
                            // 'sh_id'=>'股东ID',
                            // 'ua_id'=>'总代ID',
                            // 'at_id' => '代理ID',
                            // 'agentname' => '代理账号',
                            // 'uid' => '会员ID',
                            // 'username' => '会员账号',
                            'bet' => '总注单',
                            'bet_count' => '总笔数',
                            'valid_bet' => '有效注单',
                            'valid_bet_count' => '有效笔数',
                            'win' => '赢金额',
                            'win_count' => '赢笔数',
                            'addday' => '注单日期',
                            'updatedate' => '最近更新时间'
                        ];
                        break;
                    default:
                        $filename = '会员注单统计报表' . '-' . date('Y_m_d-H_i_s');
                        $fields = [
                            'id' => 'ID',
                            'fc_typeTxt' => '彩种',
                            'line_id' => '线路',
                            // 'sh_id'=>'股东ID',
                            // 'ua_id'=>'总代ID',
                            'at_id' => '代理ID',
                            // 'agentname' => '代理账号',
                            'uid' => '会员ID',
                            // 'username' => '会员账号',
                            'bet' => '总注单',
                            'bet_count' => '总笔数',
                            'valid_bet' => '有效注单',
                            'valid_bet_count' => '有效笔数',
                            'win' => '赢金额',
                            'win_count' => '赢笔数',
                            'addday' => '注单日期',
                            'updatedate' => '最近更新时间'
                        ];
                        break;
                }

                break;
        }

        $this->csv_export($data, $fields, $filename);
        // $HTML = $this->getExportContent($data, $fields);

        // header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        // header("Content-Disposition:attachment;filename=" . $filename . ".xls");
        // echo $HTML;
        return;
    }

    public function actionChart() {
        $data['show'] = $this->showData();

        $request = Yii::$app->request->get();
        $stat = $this->getStat($request, $data['show']);

        $chart = [];
        if (!empty($stat)) {
            $chart['xaxis_categories'] = array_keys($stat);
            $chart['series_type'] = 'column'; // line/spline/column 直线图/曲线图/柱状图
            $chart['title'] = '注单走势图';
            $chart['subtitle'] = date('Y-m-d H:i');
            $chart['yaxis_title'] = '注单量 (元)';
            $chart['tooltip_valuesuffix'] = '元';
            $chart['tooltip_valuesuffix2'] = '笔';

            $chart_type = isset($request['chart_type']) ? trim($request['chart_type']) : '';
            switch ($chart_type) {
                case 'bar':
                    $chart['height'] = count($chart['xaxis_categories']) * 120;
                    break;
                case 'column':
                    $chart['width'] = count($chart['xaxis_categories']) * 120;
                    break;
                default:
                    $chart['width'] = count($chart['xaxis_categories']) * 120;
                    break;
            }

            $col = [
                'bet' => '总注单',
                'bet_count' => '总笔数',
                'valid_bet' => '有效注单',
                'valid_bet_count' => '有效笔数',
                'win' => '赢金额',
                'win_count' => '赢笔数'
            ];
            foreach ($col as $key => $val) {
                $chart['series'][$key]['name'] = $val;
                $chart['series'][$key]['data'] = array_column($stat, $key, 'index');
            }
        }

        $render = [
            'data' => $data,
            'chart' => $chart
        ];
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('chart.html', $render);
        } else {
            return $this->render('chart.html', $render);
        }
    }

    /*
     * *************************************************************************
     */

    public function search($request, $showData) {
        $query = $request;
        unset($query['_pjax']);
        unset($query['tab']);
        if (empty($query)) {
            return;
        }

        $tab = isset($request['tab']) ? trim($request['tab']) : '';
        $line_id = isset($request['line_id']) ? trim($request['line_id']) : '';
        $agentname = isset($request['agentname']) ? trim($request['agentname']) : '';
        $username = isset($request['username']) ? trim($request['username']) : '';
        $fc_type = isset($request['fc_type']) ? trim($request['fc_type']) : '';
        $order_sort = isset($request['order_sort']) ? trim($request['order_sort']) : '';
        $starttime = isset($request['starttime']) ? trim($request['starttime']) : '';
        $endtime = isset($request['endtime']) ? trim($request['endtime']) : '';
        $page = isset($request['page']) ? intval($request['page']) : 1;
        $pagenum = isset($request['pagenum']) ? intval($request['pagenum']) : 100;

        // 时间 处理
        $starttime = strtotime($starttime) ? strtotime($starttime) : $starttime;
        $endtime = strtotime($endtime) ? strtotime($endtime) : $endtime;

        // 查询条件
        $where[] = 'and';
        if ($line_id) {
            $where[] = ['=', 'line_id', $line_id];
        }
        if ($agentname) {
            $where[] = ['=', 'at_username', $agentname];
        }
        if ($username) {
            $where[] = ['=', 'uname', $username];
        }
        if ($fc_type) {
            $where[] = ['=', 'fc_type', $fc_type];
        }
        $orderby = ''; // 排序
        if ($order_sort) {
            list($order, $sort) = explode('-', $order_sort);
            $orderby = "$order $sort";
        } else {
            $orderby = "addday desc";
        }
        // 时间筛选
        if ($starttime) {
            $startdate = date('Ymd', $starttime);
            $where[] = ['>=', 'addday', $startdate];
        }
        if ($endtime) {
            $enddate = date('Ymd', $endtime);
            $where[] = ['<=', 'addday', $enddate];
        }

        // 分页
        if ($tab == 'line') {
            $recordcount = selfModel::get_count_line($where);
        } else {
            $recordcount = selfModel::get_count($where);
        }
        $pagecount = ceil($recordcount / $pagenum);
        if ($page > $pagecount && $pagecount != 0)
            $page = $pagecount;
        if ($page <= 0)
            $page = 1;
        $offset = ($page - 1) * $pagenum;
        $limit = $pagenum;

        if ($tab == 'line') {
            $data = selfModel::get_list_line($where, $offset, $limit, $orderby);
        } else {
            $data = selfModel::get_list($where, $offset, $limit, $orderby);
        }
        $data = $this->trans($data, $showData, $tab); // 翻译
        $total = $this->getTotalData($where,$data, $tab); //统计
        $result['data'] = $data;
        $result['page'] = $page;
        $result['pagenum'] = $pagenum;
        $result['pagecount'] = $pagecount;
        $result['export_query'] = $request;
        $result['total'] = $total;
        return $result;
    }

    public function commission($request) {
        $query = $request;
        unset($query['_pjax']);
        if (empty($query)) {
            return;
        }

        $starttime = isset($request['starttime']) ? $request['starttime'] : '';
        $endtime = isset($request['endtime']) ? $request['endtime'] : '';

        $starttime = strtotime($starttime) ? strtotime($starttime) : $starttime;
        $endtime = strtotime($endtime) ? strtotime($endtime) : $endtime;

        //获取所有线路
        $redis = Yii::$app->redis;
        $lines = $redis->get('linelist_get_money');
        if (!$lines) {
            $lines = parent::getLines();
            $redis->setex('linelist_get_money', 3000, json_encode($lines));
        } else {
            $lines = json_decode($lines, true);
        }
        if (empty($lines)) {
            return;
        }
        $lines = array_column($lines, 'line_id', 'id');

        //根据日期查询所有的线路统计数据
        $where = ['and'];
        if (!empty($starttime)) {
            $starttime = date('Ymd', $starttime);
            $where[] = ['>=', 'addday', $starttime];
        }
        if (!empty($endtime)) {
            $endtime = date('Ymd', $endtime);
            $where[] = ['<=', 'addday', $endtime];
        }
        $stat_list = selfModel::get_items($where);
        if (empty($stat_list)) {
            return;
        }

        //计算出每个代理的盈利金额总和
        $stat = [];
        foreach ($stat_list as $key => $val) {
            @$stat[$val['line_id']][$val['at_id']] += ($val['bet'] - $val['win'] );
        }

        //查询出所有的套餐及关联信息
        $relation_arr = TaocanModel::get_set_all(['>', 'id', 0]);
        $taocan_arr = $redis->get('line_taocan');
        if (!$taocan_arr) {
            $taocan_arr = TaocanModel::get_all(['>', 'id', 0]);
            $redis->setex('line_taocan', 60, json_encode($taocan_arr));
        } else {
            $taocan_arr = json_decode($taocan_arr, true);
        }
        if (empty($taocan_arr)) {
            return;
        }
        //处理套餐数组
        $new_taocan = array();
        foreach ($taocan_arr as $val) {
            $new_taocan[$val['id']] = $val;
        }
        // 获取每个线路所对应的套餐
        $data = array();
        foreach ($lines as $id => $line_id) {
            //如果没有套餐，取套餐一为默认套餐
            $data[$line_id] = $new_taocan[1];
            foreach ($relation_arr as $key => $relation) {
                if ($relation['line_id'] == $id) {
                    $data[$line_id] = $new_taocan[$relation['tid']];
                    continue;
                }
            }
        }//$data最终结构 [线路id]=>对应套餐祥细信息
        //处理返回数据
        $new_data = array();
        foreach ($stat as $line_id => $val) {
            //取出当前线路的套餐，并计算出代理的提成
            $tmp_arr = array();
            $tmp_tname = $tmp_tic = '';
            $tmp_arr = $data[$line_id];
            $tmp_tname = $tmp_arr['tname']; //套餐名称
            $tmp_tic = $tmp_arr['pktc']; //提成比例
            $line_money = 0;
            foreach ($val as $agent_id => $win) {
                $tmp_money = 0;
                $line_money += $win;
                $tmp_money = round($win * $tmp_tic / 100, 2);
                $new_data[$line_id]['agent'][$agent_id]['allmoney'] = $win; //总金额
                $new_data[$line_id]['agent'][$agent_id]['tc_money'] = $tmp_money; //提成金额
                $agent = AgentModel::getOneAgentByCondition(Yii::$app->db, 'my_user_agent', ['id' => $agent_id]);
                $new_data[$line_id]['agent'][$agent_id]['agent_name'] = $agent['login_name'];
            }
            $new_data[$line_id]['tname'] = $tmp_tname;
            $new_data[$line_id]['proportion'] = $tmp_tic . '%';
            $new_data[$line_id]['line_money'] = $line_money;
            $new_data[$line_id]['line_tc_money'] = round($line_money * $tmp_tic / 100, 2);
        }
    
        $result['data'] = $new_data;
        return $result;
    }

    public function task_is_running() {
        return Yii::$app->redis->get('bet_restat_task_is_running');
    }

    public function restat($request) {
        if ($this->task_is_running()) {
            $result['ErrorMsg'] = '任务正在运行中，请稍后再试';
            $result['ErrorCode'] = 2;
            return $result;
        }

        $datetype = isset($request['datetype']) ? trim($request['datetype']) : 'day';
        $line_id = isset($request['line_id']) ? trim($request['line_id']) : '';
        $fc_type = isset($request['fc_type']) ? trim($request['fc_type']) : '';
        $day = isset($request['day']) ? trim($request['day']) : '';
        $starttime = isset($request['starttime']) ? trim($request['starttime']) : '';
        $endtime = isset($request['endtime']) ? trim($request['endtime']) : '';

        if ($datetype == 'day') {
            if (empty($day)) {
                $result['ErrorCode'] = 2;
                $result['ErrorMsg'] = '请输入日期';
                return $result;
            }
        } elseif ($datetype == 'interval') {
            if (empty($starttime) || empty($endtime)) {
                $result['ErrorCode'] = 2;
                $result['ErrorMsg'] = '请输入日期';
                return $result;
            }
        } else {
            $result['ErrorCode'] = 2;
            $result['ErrorMsg'] = '日期类型不明确';
            return $result;
        }

        $starttime = strtotime($starttime) ? strtotime($starttime) : $starttime;
        $endtime = strtotime($endtime) ? strtotime($endtime) : $endtime;

        $data['datetype'] = $datetype;
        $data['line_id'] = $line_id;
        $data['fc_type'] = $fc_type;
        $data['day'] = $day;
        $data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
        $data['timezone'] = date_default_timezone_get();

        if (empty($host = Yii::$app->params['app_stat_host_http'])) {
            $result['ErrorCode'] = 2;
            $result['ErrorMsg'] = '未配置 : app_stat_host_http';
            return $result;
        }
        $return = Curl::https_request($host, true, $data);

        $return = json_decode($return, true);
        if (!isset($return['connect'])) {
            $result['ErrorCode'] = 2;
            $result['ErrorMsg'] = '请求服务失败';
            return $result;
        }

        $result['ErrorCode'] = 1;
        $result['ErrorMsg'] = '操作成功';
        return $result;
    }

    public function getExportContent($data, $fields) {
        list($first) = $data;
        foreach ($fields as $fieldkey => $fieldname) {
            if (!key_exists($fieldkey, $first))
                unset($fields[$fieldkey]);
        }
        $tab = 'style="width:100%;font-size:14px;"';
        $tab_tr = 'style="height:36px;border-bottom:1px solid;"';
        $tab_tr_th = 'style="text-align:center;"';
        $tab_tr_td = 'style="text-align:center;"';
        $HTML = '<meta http-equiv=Content-Type content="text/html; charset=utf-8">' . PHP_EOL;
        $HTML .= '<table ' . $tab . '>' . PHP_EOL;
        $HTML .= '<tr ' . $tab_tr . '>';
        foreach ($fields as $fieldkey => $fieldname) {
            $HTML .= '<th ' . $tab_tr_th . '>' . $fieldname . '</th>';
        }
        $HTML .= '</tr>' . PHP_EOL;

        foreach ($data as $v) {
            $HTML .= '<tr ' . $tab_tr . '>';
            foreach ($fields as $fieldkey => $fieldname) {
                $tab_tr_td_win = ($fieldkey == 'win') ? ($v[$fieldkey] > 0) ? 'style="color:red;"' : 'style="color:green;"' : '';
                $HTML .= '<td ' . $tab_tr_td . $tab_tr_td_win . '>' . $v[$fieldkey] . '</td>';
            }
            $HTML .= '</tr>' . PHP_EOL;
        }
        $HTML .= '</table>';

        return $HTML;
    }

    public function csv_export($data, $fields, $fileName) {

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
        header('Cache-Control: max-age=0');

        //打开PHP文件句柄,php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');

        //输出Excel列名信息
        foreach ($fields as $key => $value) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $fields[$key] = iconv('utf-8', 'gbk', $value);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $fields);

        //计数器
        $num = 0;

        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;

        //逐行取出数据，不浪费内存
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {

            $num++;

            //刷新一下输出buffer，防止由于数据过多造成问题
            if ($limit == $num) { 
                ob_flush();
                flush();
                $num = 0;
            }

            $row = $data[$i];
            foreach ($fields as $key => $value) {
                $tmp[$key] = iconv('utf-8', 'gbk', $row[$key]);
            }

            fputcsv($fp, $tmp);
        }
    }

    public function getStat($request, $showData) {

        $display = isset($request['display']) ? trim($request['display']) : '';
        if (in_array($display, [1, 2]))
            $request['tab'] = 'line'; // 显示类型为线路/彩种 则查线路表
        $result = $this->search($request, $showData);

        $stat = [];
        if ($result['data']) {
            foreach ($result['data'] as $key => $val) {
                switch ($display) {
                    case '1':
                        $ckey = $val['line_id'];
                        break;
                    case '2':
                        $ckey = key_exists($val['fc_type'], $showData['games']) ? $showData['games'][$val['fc_type']]['name'] : $val['fc_type'];
                        break;
                    case '3':
                        $ckey = key_exists($val['at_id'], $showData['agents']) ? $showData['agents'][$val['at_id']]['login_name'] : $val['at_id'];
                        break;
                    case '4':
                        $ckey = key_exists($val['uid'], $showData['users']) ? $showData['users'][$val['uid']]['uname'] : $val['uid'];
                        break;
                    default:
                        $ckey = $val['line_id'];
                        break;
                }
                if ($display == 5) {
                    $ckey = $val['at_id'];
                    @$stat[$ckey]['at_id'] = $val['at_id'];
                    @$stat[$ckey]['agentname'] = $val['at_username'];
                }
                @$stat[$ckey]['index'] = $ckey;
                @$stat[$ckey]['bet'] += $val['bet'];
                @$stat[$ckey]['bet_count'] += $val['bet_count'];
                @$stat[$ckey]['valid_bet'] += $val['valid_bet'];
                @$stat[$ckey]['valid_bet_count'] += $val['valid_bet_count'];
                @$stat[$ckey]['win'] += $val['win'];
                @$stat[$ckey]['win_count'] += $val['win_count'];
            }
        }
        return $stat;
    }

    public function showData($type = '') {

        switch ($type) {
            case 'lines':
                $lines = parent::getLines();
                $data = ArrayHelper::index($lines, 'line_id');
                break;
            case 'games':
                $games = parent::getAllFcTypes();
                $data = ArrayHelper::index($games, 'type');
                break;
            case 'agents':
                $agents = AgentModel::getAgent('at');
                $data = ArrayHelper::index($agents, 'id');
                break;
            case 'users':
                $users = UserModel::get_items([]);
                $data = ArrayHelper::index($users, 'uid');
                break;
            default:
                $lines = parent::getLines();
                $games = parent::getAllFcTypes();

                $lines = ArrayHelper::index($lines, 'line_id');
                $games = ArrayHelper::index($games, 'type');

                $data['lines'] = $lines;
                $data['games'] = $games;
                break;
        }

        return $data;
    }

    public function trans($data, $showData, $tab) {
        // $showData = $this->showData();

        foreach ($data as $k => &$v) {
            if (isset($v['fc_type']))
                $v['fc_typeTxt'] = key_exists($v['fc_type'], $showData['games']) ? $showData['games'][$v['fc_type']]['name'] : $v['fc_type'];
            if (isset($v['addtime']))
                $v['adddate'] = date('Y-m-d H:i:s', $v['addtime']);
            if (isset($v['updatetime']))
                $v['updatedate'] = date('Y-m-d H:i:s', $v['updatetime']);
            if($tab == 'line'){
                $data[$k]['money'] = number_format($v['valid_bet'] - $v['win'], 2, '.', ',');
            }else{
                $data[$k]['money'] = number_format($v['win'] - $v['valid_bet'], 2, '.', ',');
            }
        }unset($v);

        return $data;
    }
/**
      ***********************************************************
      *  统计当前页与所有符合条件的数据 @author ruizuo qiyongsheng    *
      ***********************************************************
*/
    public function getTotalData($where, $data, $tab = ''){
       $bet = $bet_count = $valid_bet = $valid_bet_count = $win = $win_count = 0;
       $all_bet = $all_bet_count = $all_valid = $all_valid_count = $all_win = $all_win_count = 0;
       if(!empty($data)){
           foreach($data as $key=>$val){
                $bet += $val['bet'];
                $bet_count += $val['bet_count'];
                $valid_bet += $val['valid_bet'];
                $valid_bet_count += $val['valid_bet_count'];
                $win += $val['win'];
                $win_count += $val['win_count'];
           }
       }

       $all_bet = selfModel::getSum('SUM(bet) ', $where, $tab);
       $all_bet_count = selfModel::getSum('SUM(bet_count)', $where, $tab);
       $all_valid = selfModel::getSum('SUM(valid_bet)', $where, $tab);
       $all_valid_count = selfModel::getSum('SUM(valid_bet_count)', $where, $tab);
       $all_win = selfModel::getSum('SUM(win)', $where, $tab);
       $all_win_count = selfModel::getSum('SUM(win_count)', $where, $tab);

       $return = array();
       $return['bet'] = number_format($bet, '2', '.', ',');
       $return['bet_count'] = number_format($bet_count, '0', '.', ',');
       $return['valid_bet'] = number_format($valid_bet, '2', '.', ',');
       $return['valid_bet_count'] = number_format($valid_bet_count, '0', '.', ',');
       $return['win'] = number_format($win, '2', '.', ',');
       $return['win_count'] = number_format($win_count, '0', '.', ',');
       $return['all_bet'] = number_format($all_bet, '2', '.', ',');
       $return['all_bet_count'] = number_format($all_bet_count, '0', '.', ',');
       $return['all_valid'] = number_format($all_valid, '2', '.', ',');
       $return['all_valid_count'] = number_format($all_valid_count, '0', '.', ',');
       $return['all_win'] = number_format($all_win, '2', '.', ',');
       $return['all_win_count'] = number_format($all_win_count, '0', '.', ',');
       if($tab == 'line'){
           $return['money'] = number_format($valid_bet - $win, '0', '.', ',');
           $return['all_money'] = number_format($all_valid - $all_win, '0', '.', ',');
       }else{
           $return['money'] = number_format($win - $valid_bet, '0', '.', ',');
           $return['all_money'] = number_format($all_win - $all_valid, '0', '.', ',');
       }
       return $return;
    }

}
