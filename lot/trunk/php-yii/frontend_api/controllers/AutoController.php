<?php

namespace frontend_api\controllers;

use Yii;
use frontend_api\controllers\Controller;
use frontend_api\models\GamesModel;
use common\models\AutoModel;
use yii\helpers\ArrayHelper;
use frontend_api\models\AutoModel as Auto;
use common\helpers\lotteryOrm;

class AutoController extends Controller {

    //开奖结果页(显示彩种分类)
    public function actionIndex() {
        $fc_cates = $this->getAllFcCates();
        $data = [];
        foreach ($fc_cates as $k => $v) {
            $data[] = [
                'type' => $v['type'],
                'name' => $v['name'],
                'img_path' => $v['img_path']
            ];
        }

        $echo = [
            'Data' => $data,
            'ErrorCode' => 1,
            'ErrorMsg' => '获取成功'
        ];
        echo json_encode($echo, true);
        die;
    }

    /**
     * **********************************************************
     *  开奖结果列表           @author ruizuo qiyongsheng    *
     * **********************************************************
     */
    public function actionAuto_list() {
        $get = Yii::$app->request->get();
        $result = array();
        $result['ErrorCode'] = 2;

        $type = isset($get['type']) ? $get['type'] : null;
        if (empty($type)) {
            $result['ErrorMsg'] = '参数不正确';
            return json_encode($result);
        }

        //查询指定大分类下的所有彩种
        $games_arr = $this->getAllFcTypes();
        $games = array();
        foreach ($games_arr as $key => $val) {
            if ($type == $val['ltype']) {
                $games[$key]['type'] = $val['type'];
                $games[$key]['name'] = $val['name'];
                $games[$key]['img_path'] = $val['img_path'];
            }
        }
        if (empty($games)) {
            $result['ErrorMsg'] = '获取彩种数据失败';
            return json_encode($result);
        }

        $new_arr = array();
        foreach ($games as $key => $val) {
            $auto_list = parent::getLastAutoByType($val['type']);
            if (!$auto_list)
                continue;
            $new_arr[$key][] = array_merge($val, $auto_list);
        }
        $result['ErrorCode'] = 1;
        $result['ErrorMsg'] = '获取成功';
        $result['Data'] =  $new_arr;
        return json_encode($result);
    }

    // 开奖结果
    public function actionList() {
        $request = Yii::$app->request->post();
        $fc_type = isset($request['fc_type']) ? trim($request['fc_type']) : '';
        $page = isset($request['page']) ? trim($request['page']) : 1;
        $pagenum = isset($request['pagenum']) ? trim($request['pagenum']) : 50;

        if (empty($fc_type)) {
            $result['ErrorCode'] = 2;
            $result['ErrorMsg'] = '参数不正确';
            return json_encode($result);
        }

        // 分页
        $recordcount = AutoModel::get_count($fc_type, []);
        $pagecount = ceil($recordcount / $pagenum);
        if ($page > $pagecount && $pagecount > 0)
            $page = $pagecount;
        $offset = ($page - 1) * $pagenum;
        $limit = $pagenum;

        $auto_liat = AutoModel::get_list($fc_type, [], $offset, $limit);

        $lotteryOrm = new lotteryOrm();
        $ball_num = $lotteryOrm->getBallNum($fc_type);

        $data = [];
        foreach ($auto_liat as $key => $val) {
            $data[$key]['qishu'] = $val['qishu'];
            for ($i=1; $i <= $ball_num; $i++) {
                $data[$key]['ball'][] = intval($val['ball_' . $i]);
            }
            $data[$key]['ball'] = implode(',', $data[$key]['ball']);
        }

        $result['ErrorCode'] = 1;
        $result['ErrorMsg'] = 'OK';
        // $result['Page'] =  $page;
        // $result['Pagenum'] =  $pagenum;
        $result['Data'] =  $data;
        return json_encode($result);
    }

  /**
      ***********************************************************
      *  开奖结果走势图           @author ruizuo qiyongsheng    *
      ***********************************************************
    */
    public function actionTrend(){
        $get = Yii::$app->request->post();
        $result = array();
        $result['ErrorCode'] = 2;
        $type = isset($get['type']) ? $get['type'] : null;
        $num = isset($get['num']) ? intval($get['num']) : 30;
        if (empty($type)) {
            $result['ErrorMsg'] = '参数不正确';
            return json_encode($result);
        }
        $games_arr = $this->getAllFcTypes();
        $tmp_arr = array();
        foreach($games_arr as $val){
            $tmp_arr[] = $val['type'];
        }
        if(!in_array($type, $tmp_arr)){
            $result['ErrorMsg'] = '参数不正确';
            return json_encode($result);
        }

        $data = Auto::getTrend($type, array(), 0, $num);
        if(!$data || empty($data)){
            $result['ErrorMsg'] = '获取数据失败';
            return json_encode($result);
        }
        $data = $this->getBall($type, $data);
        $return  = array();
        foreach($data as $key=>$val){
            $return[$key]['qishu'] = $val['qishu'];
            $return[$key]['ball'] = $val['ball'];
        }
        $result['ErrorCode'] = 1;
        $result['ErrorMsg'] = '获取成功';
        $result['Data'] = $return; 
        return json_encode($result);
    }
    /**
          ***********************************************************
          *  pc端走势图           @author ruizuo qiyongsheng    *
          ***********************************************************
    */
    public function actionPc_trend(){
        $get = Yii::$app->request->post();
        $result = array();
        $result['ErrorCode'] = 2;
        $type = isset($get['type']) ? $get['type'] : null;
        $num = isset($get['num']) ? intval($get['num']) : 30;
        $date = isset($get['startT']) ? $get['startT'] : '';
        if($date == 't0'){
            $date = date('Y-m-d');
        }elseif($date == 't1'){
            $date = date('Y-m-d', strtotime("-1 days"));
        }elseif($date == 't2'){
            $date = date('Y-m-d', strtotime("-2 days"));
        }else{
            // $date = date('Y-m-d');
        }
        if (empty($type)) {
            $result['ErrorMsg'] = '参数不正确';
            return json_encode($result);
        }
        $games_arr = $this->getAllFcTypes();
        $cat_data = $this->getAllFcCates();
        //处理数据获得图标
        $new_type = array();
        foreach($cat_data as $key=>$val){
            $new_type[$key]['type'] = $val['type'];
            $new_type[$key]['name'] = $val['name'];
            $new_type[$key]['img'] = $val['img_path'];
        }

        $tmp_arr = array();
        $type_arr = array();
        $img_arr = array();
        foreach($games_arr as $val){
            $tmp_arr[] = $val['type'];
            $type_arr[$val['type']] = $val['name'];
            $img_arr[$val['type']] = $val['img_path'];
        }
        if(!in_array($type, $tmp_arr)){
            $result['ErrorMsg'] = '参数不正确';
            return json_encode($result);
        }
        
        $type_name = $type_arr[$type];
        $type_img = isset($img_arr[$type]) ? $img_arr[$type] : '';
        //取日期的开始和结束
        $where = array();
        if($date){
            $starttime = strtotime($date . ' 00:00:00');
            $endtime = strtotime($date . ' 23:59:59');
            $where = array('between', 'addtime', $starttime, $endtime);
        }
        $data = Auto::getTrend($type, $where , 0, $num);
        if(!$data || empty($data)){
            $result['ErrorMsg'] = '获取数据失败';
            return json_encode($result);
        }
        $data = $this->getBall($type, $data);
        $return = array();
        //处理数据
        $weekarray=array('日','一','二','三','四','五','六');
        foreach($data as $key=>$val){
            $return[$key]['playGroupName'] = $type_name;
            $return[$key]['date'] = date('Y-m-d H:i:s',$val['addtime']);
            $return[$key]['number'] = $val['qishu'];
            $return[$key]['openCode'] = implode(',', $val['ball']);
            $return[$key]['opentime'] = $val['datetime'];
            $return[$key]['playGroupId'] = $type;
            $return[$key]['ktime'] = '星期' . $weekarray[date('w', $val['datetime'])] . ' ' . date('m/d H:i', $val['datetime']);
        }

        $result['ErrorCode'] = 1;
        $result['ErrorMsg'] = '获取成功';
        $result['Data'] = $return;
        $result['img'] = $type_img;
        $result['type_list'] = $new_type;
        return json_encode($result);
    }
        
    /**
          ***********************************************************
          *  取出开奖结果的基本球         @author ruizuo qiyongsheng    *
          ***********************************************************
    */
    private function getBall($type, $data){
        $lotteryOrm = new lotteryOrm();
        $ball_num = $lotteryOrm->getBallNum($type);
        //取出开奖结果
        $ball_arr = array();
        for ($i = 1; $i <= $ball_num; $i++) {
            $ball_arr[] = 'ball_' . $i;
        }
        foreach($data as $key => $auto){
            foreach($ball_arr as $val){
                $tmp = array();
                $tmp = explode(',', $auto[$val]);
                if(count($tmp) < 1 ){
                    $result['ErrorMsg'] = '处理数据失败';
                    return json_encode($result);
                }
                $data[$key]['ball'][] = intval($tmp[0]);
            }
        }

        return $data;
    }
        

    /**
     * **********************************************************
     *  开奖历史记录           @author ruizuo qiyongsheng    *
     * **********************************************************
     */

    public function actionHistory() {
        $request = Yii::$app->request->post();
        $fc_type = isset($request['fc_type']) ? $request['fc_type'] : 'liuhecai';
        $qishu = isset($request['qishu']) ? $request['qishu'] : '';
        $starttime = isset($request['starttime']) ? $request['starttime'] : '';
        $endtime = isset($request['endtime']) ? $request['endtime'] : '';
        $page = isset($request['page']) ? intval($request['page']) : 1;
        $pagenum = isset($request['pagenum']) ? intval($request['pagenum']) : 50;

        $starttime = strtotime($starttime) ? strtotime($starttime) : $starttime;
        $endtime = strtotime($endtime) ? strtotime($endtime) : $endtime;

        if (empty($fc_type)) {
            $result['ErrorCode'] = 2;
            $result['ErrorMsg'] = '请选择彩种';
            return json_encode($result);
        }

        $where[] = 'and';
        if ($qishu) {
            $where[] = ['=', 'qishu', $qishu];
        }
        if ($starttime) {
            $starttime = strtotime(date('Y-m-d 00:00:00', $starttime));
            $where[] = ['>=', 'addtime', $starttime];
        }
        if ($endtime) {
            $endtime = strtotime(date('Y-m-d 23:59:59', $endtime));
            $where[] = ['<=', 'addtime', $endtime];
        }

        $recordcount = AutoModel::get_count($fc_type, $where);
        $pagecount = ceil($recordcount / $pagenum);
        if ($page > $pagecount && $pagecount > 0) $page = $pagecount;
        $offset = ($page - 1) * $pagenum;
        $limit = $pagenum;

        $lotteryOrm = new \common\helpers\lotteryOrm();
        $ball_num = $lotteryOrm->getBallNum($fc_type);

        $games = $this->getAllFcTypes();
        $games = ArrayHelper::index($games, 'type');

        $data = AutoModel::get_list($fc_type, $where, $offset, $limit);
        $tmp = [];
        foreach($data as $k => $v){
            $tmp[$k]['type'] = $fc_type;
            $tmp[$k]['name'] = key_exists($fc_type, $games) ? $games[$fc_type]['name'] : $fc_type;
            $tmp[$k]['img_path'] = key_exists($fc_type, $games) ? $games[$fc_type]['img_path'] : $fc_type;
            $tmp[$k]['qishu'] = $v['qishu'];
            $tmp[$k]['adddate'] = date('Y-m-d H:i:s', $v['addtime']);
            for ($i=1; $i <= $ball_num; $i++) {
                list($tmp[$k]['balls'][]) = explode(',', $v['ball_' . $i]);
            }
        }$data = $tmp;

        if($fc_type == 'liuhecai' || $fc_type == 'bj_10'){//加入色波
           foreach($data as $key=>$val){
                if(isset($val['balls']) && is_array($val['balls'])){
                    $tmp_arr = array();
                    $tmp_arr = $val['balls'];
                    unset($data[$key]['balls']);
                    if($fc_type == 'liuhecai'){
                        $data[$key]['balls'] = $this->getSixWave($tmp_arr);
                    }elseif($fc_type == 'bj_10'){
                        $data[$key]['balls'] = $this->getBjColor($tmp_arr);
                    }
                }
           }
        }

        $games = $this->getAllFcTypes();
        $tmp = [];
        foreach($games as $key=>$val){
            $tmp[$key]['label'] = $val['name'];
            $tmp[$key]['value'] = $val['type'];
        }
        $games = $tmp;

        $result['ErrorCode'] = 1;
        $result['ErrorMsg'] = 'OK';
        $result['Page'] = $page;
        $result['Pagenum'] = $pagenum;
        $result['Pagecount'] = $pagecount;
        $result['Recordcount'] = $recordcount;
        $result['type_list'] = $games;
        $result['Data'] = $data;
        return json_encode($result);
    }

/**
      ***********************************************************
      *  获取六合彩色波           @author ruizuo qiyongsheng    *
      ***********************************************************
*/
    private function getSixWave($balls){
        $ball['six-red'] = [1, 2, 7, 8, 12, 13, 18, 19, 23, 24, 29, 30, 34, 35, 40, 45, 46];
        $ball['six-blue'] = [3, 4, 9, 10, 14, 15, 20, 25, 26, 31, 36, 37, 41, 42, 47, 48];
        $ball['six-green'] = [5, 6, 11, 16, 17, 21, 22, 27, 28, 32, 33, 38, 39, 43, 44, 49];
        $return  = array();
        foreach($ball as $color=>$num_arr){
            foreach($balls as $key=>$val){
                if(in_array($val, $num_arr)){
                    $return[$key]['num'] = $val;
                    $return[$key]['color'] = $color;
                    continue;
                }
            }
        }
        ksort($return);
        return $return;
    }
    
/**
      ***********************************************************
      *  获取北京PK10颜色           @author ruizuo qiyongsheng    *
      ***********************************************************
*/
    private function getBjColor($balls){
        $bj_color_arr = ['','bj-yellow','bj-blue','bj-black','bj-orange','bj-azure','bj-deepblue','bj-silver','bj-red','bj-brown','bj-green'];
        $return  = array();
        foreach($balls as $key=>$val){
            $ball = intval($val);
            $return[$key]['color'] = isset($bj_color_arr[$ball]) ? $bj_color_arr[$ball] : '';
            $return[$key]['num'] = $val;
        }
        return $return;
    }
}
