<?php
declare(strict_types=1);

namespace Liwanyi\Utils2;

class Math
{
    /**
     * 高精度加法
     * @param string $a
     * @param string $b
     * @param int $scale 保留小数位数
     * @return string
     */
    public static function add(string $a, string $b, int $scale = 2): string
    {
        return bcadd($a, $b, $scale);
    }

    /**
     * 高精度减法
     * @param string $a
     * @param string $b
     * @param int $scale
     * @return string
     */
    public static function sub(string $a, string $b, int $scale = 2): string
    {
        return bcsub($a, $b, $scale);
    }

    /**
     * 高精度乘法
     * @param string $a
     * @param string $b
     * @param int $scale
     * @return string
     */
    public static function mul(string $a, string $b, int $scale = 2): string
    {
        return bcmul($a, $b, $scale);
    }

    /**
     * 高精度除法
     * @param string $a
     * @param string $b
     * @param int $scale
     * @return string
     * @throws InvalidArgumentException 除数为0时抛出异常
     */
    public static function div(string $a, string $b, int $scale = 2): string
    {
        if (bccomp($b, '0', $scale) === 0) {
            throw new InvalidArgumentException('Division by zero');
        }
        return bcdiv($a, $b, $scale);
    }
}