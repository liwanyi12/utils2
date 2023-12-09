<?php

namespace Liwanyi\Utils2;

class Prize
{
    // 简单抽奖方法
    public static function computeRand($count, $rand, $result)
    {
        $step = 0;
        foreach ($result as $key => $item) {
            $current = Arr::first($item);
            $current = $current * ($count / 100);
            $current = intval(floor($current));
            if ($rand >= $step && $rand < ($current + $step)) {
                return Arr::last($item);
            }
            $step += $current;
        }
        return Arr::last(Arr::last($result));
    }

}