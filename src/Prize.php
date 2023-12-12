<?php
declare(strict_types=1);

namespace Liwanyi\Utils2;

class Prize
{
    /**
     * 抽奖算法
     * @param $proArr
     * @return int|string
     * $arr = array(
            array('id' => 1, 'name' => '特等奖', 'v' => 1),
            array('id' => 2, 'name' => '一等奖', 'v' => 5),
            array('id' => 3, 'name' => '二等奖', 'v' => 10),
            array('id' => 4, 'name' => '三等奖', 'v' => 12),
            array('id' => 5, 'name' => '四等奖', 'v' => 22),
            array('id' => 6, 'name' => '没中奖', 'v' => 50)
           );
    $proArr = array();
    foreach ($arr as $key => $val)
    {
    $proArr[$key] = $val['v'];
    }
    $rs = getRand($proArr);
     * */
    public static function getRand($proArr) { //计算中奖概率
        $rs = ''; //z中奖结果
        $proSum = array_sum($proArr); //概率数组的总概率精度
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $rs = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset($proArr);
        return $rs;
    }








}
