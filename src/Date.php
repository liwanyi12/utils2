<?php
declare(strict_types=1);

namespace Liwanyi\Utils2;

class Date
{
    /**
     * 获取两个日期相差多少天
     *
     * @param string $day1 日期1
     * @param string $day2 日期2
     * @return int
     */
    public static function diff(string $day1, string $day2 = ''): int
    {
        if (!$day2) {
            $day2 = date('Y-m-d');
        }

        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
            [$second1, $second2] = [$second2, $second1];
        }

        return intval(ceil(($second1 - $second2) / 86400));
    }

    /**
     * 返回截止到今天晚上零点之前的秒数
     * @return int 秒数
     */
    public static function secondEndToday(): int
    {
        list($y, $m, $d) = explode('-', date('Y-m-d'));
        return mktime(23, 59, 59, intval($m), intval($d), intval($y)) - time();
    }

    /**
     * 计算多少天前的日期
     * @param int $days
     * @param string $format
     * @return string
     *
     * 使用案例：
     * days_ago(7); // 7天前的日期
     */
    public static function days_ago(int $days, string $format = 'Y-m-d'): string
    {
        return date($format, strtotime("-$days days"));
    }

    /**
     * 判断是否是周末
     * @param string|null $date
     * @return bool
     *
     * 使用案例：
     * is_weekend('2023-05-20'); // true
     */
    public static function is_weekend(?string $date = null): bool
    {
        $day = date('N', $date ? strtotime($date) : time());
        return $day >= 6;
    }
}