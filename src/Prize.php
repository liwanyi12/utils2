<?php
declare(strict_types=1);
namespace Liwanyi\Utils2;

class Prize
{
    // 简单抽奖方法
    public static function computeRand($count, $rand, $result)
    {
        $step = 0;
        foreach ($result as $key => $item) {
            $current = array_values($item)[0];
            $current = $current * ($count / 100);
            $current = intval(floor($current));
            if ($rand >= $step && $rand < ($current + $step)) {
                return end($item);
            }
            $step += $current;
        }
        $new_data = end($result);
        return end($new_data);
    }

}