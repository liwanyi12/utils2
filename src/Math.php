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
     * @throws \InvalidArgumentException 除数为0时抛出异常
     */
    public static function div(string $a, string $b, int $scale = 2): string
    {
        if (bccomp($b, '0', $scale) === 0) {
            throw new \InvalidArgumentException('Division by zero');
        }
        return bcdiv($a, $b, $scale);
    }


    /**
     * 比较两个数字（支持高精度）
     * @param string|int|float $num1
     * @param string|int|float $num2
     * @param int $scale 比较的小数位数（默认2位）
     * @return int 返回 1（$num1 > $num2）、0（相等）、-1（$num1 < $num2）
     * @throws \InvalidArgumentException 如果参数不是数字
     * $balance = "100.50";
     * $withdraw = 50.25;
     * if (compareNumbers($balance, $withdraw) >= 0) {
     *      echo "允许提款";
     * }
     */

    public function compareNumbers($num1, $num2, int $scale = 2): int
    {
        // 验证输入是否为有效数字
        if (!is_numeric($num1) || !is_numeric($num2)) {
            throw new \InvalidArgumentException("参数必须是数字");
        }

        // 统一转为字符串，避免浮点精度问题
        $num1 = is_string($num1) ? $num1 : number_format((float)$num1, $scale, '.', '');
        $num2 = is_string($num2) ? $num2 : number_format((float)$num2, $scale, '.', '');

        return bccomp($num1, $num2, $scale);
    }
}