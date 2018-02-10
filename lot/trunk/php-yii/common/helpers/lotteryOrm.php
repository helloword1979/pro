<?php

namespace common\helpers;

use yii\helpers\ArrayHelper;

/*
 * 明细 中英文对照
 *
 */

/*
  fc_3d(福彩3D)       pl_3(排列三)
  dm_28(丹麦28)      jnd_28(加拿大28)       bj_28(北京28)
  dm_klc(丹麦快乐彩) bj_kl8(北京快乐8)  jnd_bs(加拿大卑斯)
  tj_ssc(天津时时彩) cq_ssc(重庆时时彩) xj_ssc(新疆时时彩)
  js_k3(江苏快三)    jx_k3(江西快三)    ah_k3(安徽快三)
  pc_dd(PC蛋蛋)
  gd_11(广东11选5) sd_11(山东11选5) jx_11(江西11选5)
  cq_kl10(重庆快乐十分)  gd_kl10(广东快乐十分)
  bj_pk10(北京pk拾)
 */

class lotteryOrm {

    public function getCH($fc_type){
        $method_name = null;
        switch ($fc_type) {
            case 'fc_3d'://福彩3D
                $method_name = 'three_lot';
                break;
            case 'pl_3'://排列三
                $method_name = 'three_lot';
                break;
            case 'dm_28'://丹麦28
                $method_name = 'two_eight_lot';
                break;
            case 'jnd_28'://加拿大28
                $method_name = 'two_eight_lot';
                break;
            case 'bj_28'://北京28
                $method_name = 'two_eight_lot';
                break;
            case 'dm_klc'://丹麦快乐彩
                $method_name = 'happy_lot';
                break;
            case 'bj_kl8'://北京快乐8
                $method_name = 'happy_lot';
                break;
            case 'jnd_bs'://加拿大卑斯
                $method_name = 'happy_lot';
                break;
            case 'tj_ssc'://天津时时彩
                $method_name = 'ssc_lot';
                break;
            case 'cq_ssc'://重庆时时彩
                $method_name = 'ssc_lot';
                break;
            case 'xj_ssc'://新疆时时彩
                $method_name = 'ssc_lot';
                break;
            case 'gx_k3'://广西快三
            case 'jx_k3'://江西快三
                $method_name = 'quickThree_lot';
                break;
            case 'js_k3'://江苏快三
                $method_name = 'quickThree_lot';
                break;
            case 'ah_k3'://安徽快三
                $method_name = 'quickThree_lot';
                break;
            case 'pc_28'://PC蛋蛋
            case 'pc_dd':
                $method_name = 'pc_dd';
                break;
            case 'gd_11'://广东11选5
                $method_name = 'ten_five_lot';
                break;
            case 'sd_11'://山东11选5
                $method_name = 'ten_five_lot';
                break;
            case 'jx_11'://江西11选5
                $method_name = 'ten_five_lot';
                break;
            case 'cq_ten'://重庆快乐十分
                $method_name = 'happy_ten_lot';
                break;
            case 'gd_ten'://广东快乐十分
                $method_name = 'happy_ten_lot';
                break;
            case 'bj_10'://北京pk拾
            case 'jsfc'://极速飞车
            case 'xdl_10'://新德里pk拾
                $method_name = 'pk_10';
                break;
            case 'ffc_o'://分分彩
            case 'lfc_o'://两分彩
            case 'els_o'://俄罗斯1.5分彩
            case 'dj_o' ://东京1.5分彩
            case 'mnl_o'://马尼拉45秒彩
            case 'mg_o': //美国45秒彩
                $method_name = 'ffc_o';
                break;
            case 'liuhecai':
            case 'jsliuhecai':
                $method_name = 'liuhecai';
                break;
            default:
                $method_name = false;
                break;
        }

        if($method_name){
            return $this->$method_name(); //返回fc_type相对应的方法名
        }else{
            return false;
        }
    }
    //获取每个彩种开奖结果球的个数
    public  function getBallNum($type) {
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
            case 'bj_kl8':
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

    public function returnOrm($type) {
        return $this->$type();
    }

    //福彩3D,排列3
    public function three_lot() {
        return [
            'first_ball' => '第一球',
            'second_ball' => '第二球',
            'third_ball' => '第三球',
            'span_ball' => '跨度',
            'triple_ball' => '3连',
            'gallbladder_ball' => '独胆',
            'tiger_ball' => '總和,龍虎',
            '0' => '0',
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            '7' => '7',
            '8' => '8',
            '9' => '9',
            'big' => '大',
            'small' => '小',
            'single' => '单',
            'double' => '双',
            'leopard' => '豹子',
            'straight' => '顺子',
            'pairs' => '对子',
            'Half_suitable' => '半顺',
            'Miscellaneous_six' => '杂六',
            'total_sum_big' => '总和大',
            'total_sum_small' => '总和小',
            'total_sum_single' => '总和单',
            'total_sum_double' => '总和双',
            'dragon' => '龙',
            'tiger' => '虎',
            'sum' => '和',
        ];
    }

    //六合彩
    public function liuhecai() {
        return [
            'Tema' => '特码',
            'JustCode' => '正码',
            'JustCode_Te' => '正特',
            'JustCode_one_six' => '正码1-6',
            'PassTest' => '过关',
            'JointMark' => '连码',
            'HalfWave' => '半波',
            'Ashor_EndNum' => '一肖/尾数',
            'Animal' => '生肖',
            'Tema_Animal' => '特码生肖',
            'SumAnimal' => '合肖',
            'AnimalEven' => '生肖连',
            'EndNum_Even' => '尾数连',
            'AllMiss' => '全不中',
            //特码
            'Te_A' => '特A',
            'Te_B' => '特B',
            '1' => '1', '2' => '2', '3' => '3',
            '4' => '4', '5' => '5', '6' => '6',
            '7' => '7', '8' => '8', '9' => '9',
            '10' => '10', '11' => '11', '12' => '12',
            '13' => '13', '14' => '14', '15' => '15',
            '16' => '16', '17' => '17', '18' => '18',
            '19' => '19', '20' => '20', '21' => '21',
            '22' => '22', '23' => '23', '24' => '24',
            '25' => '25', '26' => '26', '27' => '27',
            '28' => '28', '29' => '29', '30' => '30',
            '31' => '31', '32' => '32', '33' => '33',
            '34' => '34', '35' => '35', '36' => '36',
            '37' => '37', '38' => '38', '39' => '39',
            '40' => '40', '41' => '41', '42' => '42',
            '43' => '43', '44' => '44', '45' => '45',
            '46' => '46', '47' => '47', '48' => '48',
            '49' => '49',
            '1-10' => '1-10', '11-20' => '11-20', '21-30' => '21-30',
            '31-40' => '31-40', '41-49' => '41-49',
            'big_single' => '大单',
            'small_single' => '小单',
            'big_double' => '大双',
            'small_double' => '小双',
            'Poultry' => '家禽',
            'wild_animals' => '野兽',
            'in_two'=>'中二',
            'in_three'=>'中三',
            'in_te'=>'中特',
            //正码特,正码1-6
            'total_single' => '总单',
            'total_double' => '总双',
            'total_big' => '总大',
            'total_small' => '总小',
            'total_end_big' => '总尾大',
            'total_end_small' => '总尾小',
            'JustCode_Te_one' => '正1特',
            'JustCode_Te_two' => '正2特',
            'JustCode_Te_three' => '正3特',
            'JustCode_Te_four' => '正4特',
            'JustCode_Te_five' => '正5特',
            'JustCode_Te_six' => '正6特',
            'JustCode_one' => '正码1',
            'JustCode_two' => '正码2',
            'JustCode_three' => '正码3',
            'JustCode_four' => '正码4',
            'JustCode_five' => '正码5',
            'JustCode_six' => '正码6',
            'big' => '大',
            'small' => '小',
            'single' => '单',
            'double' => '双',
            'red_wave' => '红波',
            'green_wave' => '绿波',
            'blue_wave' => '蓝波',
            'sum_big' => '合大',
            'sum_small' => '合小',
            'sum_single' => '合单',
            'sum_double' => '合双',
            'end_big' => '尾大',
            'end_small' => '尾小',
            //连码
            'second_full' => '二全中',
            'second_in_te' => '二中特',
            'Special_series' => '特串',
            'third_full' => '三全中',
            'third_in_second' => '三中二',
            'fourth_full' => '四全中',
            //半波
            'red_single' => '红单',
            'red_double' => '红双',
            'red_big' => '红大',
            'red_small' => '红小',
            'red_sum_single' => '红合单',
            'red_sum_double' => '红合双',
            'green_single' => '绿单',
            'green_double' => '绿双',
            'green_big' => '绿大',
            'green_small' => '绿小',
            'green_sum_single' => '绿合单',
            'green_sum_double' => '绿合双',
            'blue_single' => '蓝单',
            'blue_double' => '蓝双',
            'blue_big' => '蓝大',
            'blue_small' => '蓝小',
            'blue_sum_single' => '蓝合单',
            'blue_sum_double' => '蓝合双',
            //一肖/尾数
            'Ashor' => '一肖',
            'EndNum' => '尾数',
            'mouse' => '鼠',
            'cattle' => '牛',
            'tiger' => '虎',
            'rabbit' => '兔',
            'dragon' => '龙',
            'snake' => '蛇',
            'horse' => '马',
            'sheep' => '羊',
            'monkey' => '猴',
            'chicken' => '鸡',
            'dog' => '狗',
            'pig' => '猪',
            'zero_end' => '0尾',
            'one_end' => '1尾',
            'two_end' => '2尾',
            'three_end' => '3尾',
            'four_end' => '4尾',
            'five_end' => '5尾',
            'six_end' => '6尾',
            'seven_end' => '7尾',
            'eight_end' => '8尾',
            'nine_end' => '9尾',
            //特码生肖
            'Te_Animal' => '特肖',
            //合肖
            'two_Animal' => '二肖',
            'three_Animal' => '三肖',
            'four_Animal' => '四肖',
            'five_Animal' => '五肖',
            'six_Animal' => '六肖',
            'seven_Animal' => '七肖',
            'eight_Animal' => '八肖',
            'nine_Animal' => '九肖',
            'ten_Animal' => '十肖',
            'elven_Animal' => '十一肖',
            //生肖连
            'two_Animal_in' => '二肖连中',
            'three_Animal_in' => '三肖连中',
            'four_Animal_in' => '四肖连中',
            'five_Animal_in' => '五肖连中',
            'two_Animal_not_in' => '二肖连不中',
            'three_Animal_not_in' => '三肖连不中',
            'four_Animal_not_in' => '四肖连不中',
            //尾数连
            'two_end_in' => '二尾连中',
            'three_end_in' => '三尾连中',
            'four_end_in' => '四尾连中',
            'two_end_not_in' => '二尾连不中',
            'three_end_not_in' => '三尾连不中',
            'four_end_not_in' => '四尾连不中',
            //全不中
            'five_not_in' => '五不中',
            'six_not_in' => '六不中',
            'seven_not_in' => '七不中',
            'eight_not_in' => '八不中',
            'nine_not_in' => '九不中',
            'ten_not_in' => '十不中',
            'elven_not_in' => '十一不中',
            'twelve_not_in' => '十二不中',
            //五行(下面是新增玩法)
            'Five_elements' => '五行',
            'metal' => '金',
            'wood'  => '木',
            'water' => '水',
            'fire'  => '火',
            'earth' => '土',
            //七码 （明细二为数字，比如0,7,中间用半角逗号隔开）
            'Seven_code'    => '七码', //明细一
            'single_double' => '单双', //明细三
            'seven_double'  => '单0 . 双7',
            'six_double'    => '单1 . 双6',
            'five_double'   => '单2 . 双5',
            'four_double'   => '单3 . 双4',
            'three_double'  => '单4 . 双3',
            'two_double'    => '单5 . 双2',
            'one_double'    => '单6 . 双1',
            'seven_single'  => '单7 . 双0',
            'big_small'     => '大小', //明细三
            'seven_small'  => '大0 . 小7',
            'six_small'    => '大1 . 小6',
            'five_small'   => '大2 . 小5',
            'four_small'   => '大3 . 小4',
            'three_small'  => '大4 . 小3',
            'two_small'    => '大5 . 小2',
            'one_small'    => '大6 . 小1',
            'seven_big'    => '大7 . 小0',
            //正肖
            'Just_Animal' => '正肖',
            'just_Animal' => '正肖',
            //总肖
            'All_Animal' => '总肖',
            'all_Animal_sd' => '总肖单双',
            'all_Animal_single' => '总肖单',
            'all_Animal_double' => '总肖双',
            'all_Animal_shunzi' => '234肖',
            'all_Animal_six' => '6肖',
            'all_Animal_five' => '5肖',
            'all_Animal_seven' => '7肖',
            //特码头尾数
            'Te_First_num' => '特码头数',
            'First_num_zero' => '头0',
            'First_num_one'  => '头1',
            'First_num_two'  => '头2',
            'First_num_three'=> '头3',
            'First_num_four' => '头4',
            'Te_Last_num'    => '特码尾数',
            'Last_num_zero'   => '尾0',
            'Last_num_one'   => '尾1',
            'Last_num_two'   => '尾2',
            'Last_num_three' => '尾3',
            'Last_num_four'  => '尾4',
            'Last_num_five'  => '尾5',
            'Last_num_six'   => '尾6',
            'Last_num_seven' => '尾7',
            'Last_num_eight' => '尾8',
            'Last_num_nine'  => '尾9',
        ];
    }

    //北京快乐8,丹麦快乐彩,加拿大卑斯
    public function happy_lot() {
        return [
            'choose_one' => '选一',
            'choose_two' => '选二',
            'choose_three' => '选三',
            'choose_four' => '选四',
            'choose_five' => '选五',
            'sum' => '和值',
            'up_middle_down' => '上中下',
            'odd_and_even' => '奇偶',
            '1' => '1', '2' => '2', '3' => '3',
            '4' => '4', '5' => '5', '6' => '6',
            '7' => '7', '8' => '8', '9' => '9',
            '10' => '10', '11' => '11', '12' => '12',
            '13' => '13', '14' => '14', '15' => '15',
            '16' => '16', '17' => '17', '18' => '18',
            '19' => '19', '20' => '20', '21' => '21',
            '22' => '22', '23' => '23', '24' => '24',
            '25' => '25', '26' => '26', '27' => '27',
            '28' => '28', '29' => '29', '30' => '30',
            '31' => '31', '32' => '32', '33' => '33',
            '34' => '34', '35' => '35', '36' => '36',
            '37' => '37', '38' => '38', '39' => '39',
            '40' => '40', '41' => '41', '42' => '42',
            '43' => '43', '44' => '44', '45' => '45',
            '46' => '46', '47' => '47', '48' => '48',
            '49' => '49', '50' => '50', '51' => '51',
            '52' => '52', '53' => '53', '54' => '54',
            '55' => '55', '56' => '56', '57' => '57',
            '58' => '58', '59' => '59', '60' => '60',
            '61' => '61', '62' => '62', '63' => '63',
            '64' => '64', '65' => '65', '66' => '66',
            '67' => '67', '68' => '68', '69' => '69',
            '70' => '70', '71' => '71', '72' => '72',
            '73' => '73', '74' => '74', '75' => '75',
            '76' => '76', '77' => '77', '78' => '78',
            '79' => '79', '80' => '80',
            'total_sum_big' => '总和大',
            'total_sum_small' => '总和小',
            'total_sum_single' => '总和单',
            'total_sum_double' => '总和双',
            'total_sum_810' => '总和810',
            'up_disc' => '上盘',
            'middle_disc' => '中盘',
            'down_disc' => '下盘',
            'odd_disc' => '奇盘',
            'sum_disc' => '和盘',
            'even_disc' => '偶盘',
            'one_in_one' => '一中一',
            'two_in_two' => '二中二',
            'three_in_three' => '三中三',
            'three_in_two' => '三中二',
            'four_in_four' => '四中三',
            'four_in_three' => '四中四',
            'four_in_two' => '四中二',
            'five_in_five' => '五中四',
            'five_in_four' => '五中三',
            'five_in_three' => '五中四',
        ];
    }

    //dm_28(丹麦28) jnd_28(加拿大28)  bj_28(pk北京28)
    public function two_eight_lot() {
        return [
            'dm_28' => '丹麦28',
            'jnd_28' => '加拿大28',
            'bj_28' => '北京28',
            '0' => '0', '1' => '1', '2' => '2', '3' => '3',
            '4' => '4', '5' => '5', '6' => '6',
            '7' => '7', '8' => '8', '9' => '9',
            '10' => '10', '11' => '11', '12' => '12',
            '13' => '13', '14' => '14', '15' => '15',
            '16' => '16', '17' => '17', '18' => '18',
            '19' => '19', '20' => '20', '21' => '21',
            '22' => '22', '23' => '23', '24' => '24',
            '25' => '25', '26' => '26', '27' => '27',
            'big_num' => '大数',
            'small_num' => '小数',
            'single_num' => '单数',
            'double_num' => '双数',
            'big_single' => '大单',
            'big_double' => '大双',
            'small_single' => '小单',
            'small_double' => '小双',
            'max_small' => '极小',
            'max_big' => '极大',
            'total_sum_big' => '总和大',
            'total_sum_small' => '总和小',
            'total_sum_single' => '总和单',
            'total_sum_double' => '总和双'
        ];
    }

    //tj_ssc(天津时时彩) cq_ssc(重庆时时彩) xj_ssc(新疆时时彩)
    public function ssc_lot() {
        return [
            'first_ball' => '第一球',
            'second_ball' => '第二球',
            'third_ball' => '第三球',
            'fourth_ball' => '第四球',
            'fifth_ball' => '第五球',
            'tiger_ball' => '總和,龍虎',
            'before_three_ball' => '前三球',
            'middle_three_ball' => '中三球',
            'after_three_ball' => '后三球',
            'Bullfighting' => '斗牛',
            'poker' => '梭哈',
            'big' => '大',
            'small' => '小',
            'single' => '单',
            'double' => '双',
            'total_sum_big' => '总和大',
            'total_sum_small' => '总和小',
            'total_sum_single' => '总和单',
            'total_sum_double' => '总和双',
            'dragon' => '龙',
            'tiger' => '虎',
            'sum' => '和',
            'leopard' => '豹子',
            'straight' => '顺子',
            'pairs' => '对子',
            'Half_suitable' => '半顺',
            'Miscellaneous_six' => '杂六',
            'not_cow' => '没牛',
            'cow_one' => '牛1',
            'cow_two' => '牛2',
            'cow_three' => '牛3',
            'cow_four' => '牛4',
            'cow_five' => '牛5',
            'cow_six' => '牛6',
            'cow_seven' => '牛7',
            'cow_eight' => '牛8',
            'cow_nine' => '牛9',
            'cow_cow' => '牛牛',
            'cow_big' => '牛大',
            'cow_small' => '牛小',
            'cow_single' => '牛单',
            'cow_double' => '牛双',
            'cow_five' => ' 五条',
            'cow_four' => '四条',
            'cow_cow' => '葫芦',
            'cow_big' => '顺子',
            'cow_small' => '三条',
            'cow_single' => '一对',
            'cow_double' => '两对',
            'cow_powder' => '散号'
        ];
    }

    //js_k3(江苏快三)    jx_k3(江西快三)    ah_k3(安徽快三)
    public function quickThree_lot() {
        return [
            'sum' => '和值',
            'gallbladder_ball' => '独胆',
            'leopard' => '豹子',
            'two_even' => '两连',
            'pairs' => '对子',
            '1' => '1', '2' => '2', '3' => '3',
            '4' => '4', '5' => '5', '6' => '6',
            '7' => '7', '8' => '8', '9' => '9',
            '10' => '10', '11' => '11', '12' => '12',
            '13' => '13', '14' => '14', '15' => '15',
            '16' => '16', '17' => '17', '18' => '18',
            'big' => '大',
            'small' => '小',
            'single' => '单',
            'double' => '双',
            '1,1,1' => '1,1,1',
            '2,2,2' => '2,2,2',
            '3,3,3' => '3,3,3',
            '4,4,4' => '4,4,4',
            '5,5,5' => '5,5,5',
            '6,6,6' => '6,6,6',
            'random_leopard' => '任意豹子',
            '1,2' => '1,2',
            '1,3' => '1,3',
            '1,4' => '1,4',
            '1,5' => '1,5',
            '1,6' => '1,6',
            '2,3' => '2,3',
            '2,4' => '2,4',
            '2,5' => '2,5',
            '2,6' => '2,6',
            '3,4' => '3,4',
            '3,5' => '3,5',
            '3,6' => '3,6',
            '4,5' => '4,5',
            '4,6' => '4,6',
            '5,6' => '5,6',
            '1,1' => '1,1',
            '2,2' => '2,2',
            '3,3' => '3,3',
            '4,4' => '4,4',
            '5,5' => '5,5',
            '6,6' => '6,6'
        ];
    }

    //pc_dd(PC蛋蛋)
    public function pc_dd() {
        return [
            'pc_28' => 'PC蛋蛋',
            '0' => '0', '1' => '1', '2' => '2', '3' => '3',
            '4' => '4', '5' => '5', '6' => '6',
            '7' => '7', '8' => '8', '9' => '9',
            '10' => '10', '11' => '11', '12' => '12',
            '13' => '13', '14' => '14', '15' => '15',
            '16' => '16', '17' => '17', '18' => '18',
            '19' => '19', '20' => '20', '21' => '21',
            '22' => '22', '23' => '23', '24' => '24',
            '25' => '25', '26' => '26', '27' => '27',
            'total_sum_big' => '总和大',
            'total_sum_small' => '总和小',
            'total_sum_single' => '总和单',
            'total_sum_double' => '总和双',
            'big_single' => '大单',
            'big_double' => '大双',
            'small_single' => '小单',
            'small_double' => '小双',
            'max_small' => '极小',
            'max_big' => '极大',
            'red_wave' => '红波',
            'green_wave' => '绿波',
            'blue_wave' => '蓝波',
            'leopard' => '豹子',
            'Tema_in_Three' => '特码包三'
        ];
    }

    //gd_11(广东11选5) sd_11(山东11选5) jx_11(江西11选5)
    public function ten_five_lot() {
        return [
            'first_ball' => '第一球',
            'second_ball' => '第二球',
            'third_ball' => '第三球',
            'fourth_ball' => '第四球',
            'fifth_ball' => '第五球',
            'total_sum' => '总和',
            'random_choose' => '任选',
            'group_choose' => '组选',
            'vertical_choose' => '直选',
            '1' => '1', '2' => '2', '3' => '3',
            '4' => '4', '5' => '5', '6' => '6',
            '7' => '7', '8' => '8', '9' => '9',
            '10' => '10', '11' => '11',
            'big' => '大',
            'small' => '小',
            'single' => '单',
            'double' => '双',
            'sum_big' => '和大',
            'sum_small' => '和小',
            'sum_single' => '和单',
            'sum_double' => '和双',
            'end_big' => '尾大',
            'end_small' => '尾小',
            'dragon' => '龙',
            'tiger' => '虎',
            'one_in_one' => '一中一',
            'two_in_two' => '二中二',
            'three_in_three' => '三中三',
            'four_in_four' => '四中四',
            'five_in_five' => '五中五',
            'six_in_five' => '六中五',
            'seven_in_five' => '七中五',
            'eight_in_five' => '八中五',
            'before_two' => '前二',
            'before_three' => '前三',
        ];
    }

    //cq_kl10(重庆快乐十分)  gd_kl10(广东快乐十分)
    public function happy_ten_lot() {
        return[
            'first_ball' => '第一球',
            'second_ball' => '第二球',
            'third_ball' => '第三球',
            'fourth_ball' => '第四球',
            'fifth_ball' => '第五球',
            'six_ball' => '第六球',
            'seven_ball' => '第七球',
            'eight_ball' => '第八球',
            'tiger_ball' => '總和,龍虎',
            'JointMark' => '连码',
            '1' => '1', '2' => '2', '3' => '3',
            '4' => '4', '5' => '5', '6' => '6',
            '7' => '7', '8' => '8', '9' => '9',
            '10' => '10', '11' => '11', '12' => '12',
            '13' => '13', '14' => '14', '15' => '15',
            '16' => '16', '17' => '17', '18' => '18',
            '19' => '19', '20' => '20',
            'big' => '大',
            'small' => '小',
            'single' => '单',
            'double' => '双',
            'sum_single' => '合单',
            'sum_double' => '合双',
            'end_big' => '尾大',
            'end_small' => '尾小',
            'east' => '东',
            'south' => '南',
            'west' => '西',
            'north' => '北',
            'middle' => '中',
            'hair' => '发',
            'white' => '白',
            'total_sum' => '总和',
            'total_sum_big' => '总和大',
            'total_sum_small' => '总和小',
            'total_sum_single' => '总和单',
            'total_sum_double' => '总和双',
            'total_sum_end_big' => '总和尾大',
            'total_sum_end_small' => '总和尾小',
            'dragon' => '龙',
            'tiger' => '虎',
            'random_choose_two' => '任选二',
            'random_choose_two_group' => '任选二组',
            'random_choose_three' => '任选三',
            'random_choose_four' => '任选四',
            'random_choose_five' => '任选五',
        ];
    }

    //bj_pk10(北京pk拾)
    public function pk_10() {
        return [
            'first_second_sum' => '冠亚军和',
            'first' => '冠军',
            'second' => '亚军',
            'third' => '第三名',
            'fourth' => '第四名',
            'fifth' => '第五名',
            'sixth' => '第六名',
            'seventh' => '第七名',
            'eighth' => '第八名',
            'ninth' => '第九名',
            'tenth' => '第十名',
            'first_second_big' => '冠亚大',
            'first_second_small' => '冠亚小',
            'first_second_single' => '冠亚单',
            'first_second_double' => '冠亚双',
            '3' => '3', '4' => '4', '5' => '5',
            '6' => '6', '7' => '7', '8' => '8',
            '9' => '9', '10' => '10', '11' => '11',
            '12' => '12', '13' => '13', '14' => '14',
            '15' => '15', '16' => '16', '17' => '17',
            '18' => '18', '19' => '19',
            'big' => '大',
            'small' => '小',
            'single' => '单',
            'double' => '双',
            'dragon' => '龙',
            'tiger' => '虎',
            'dragon_tiger' => '龍虎'
        ];
    }

    //分分彩
    public function ffc_o() {
        return [
            'first_ball' => '第一球',
            'second_ball' => '第二球',
            'third_ball' => '第三球',
            'fourth_ball' => '第四球',
            'fifth_ball' => '第五球',
            '1' => '1', '2' => '2', '3' => '3',
            '4' => '4', '5' => '5', '6' => '6',
            '7' => '7', '8' => '8', '9' => '9',
            '0' => '0',
            'big' => '大',
            'small' => '小',
            'single' => '单',
            'double' => '双',
            'total_sum' => '总和',
            'total_sum_big' => '总和大',
            'total_sum_small' => '总和小',
            'total_sum_single' => '总和单',
            'total_sum_double' => '总和双',
            'dragon' => '龙',
            'tiger' => '虎',
        ];
    }

}

?>
