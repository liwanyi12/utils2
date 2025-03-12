<?php
declare(strict_types=1);

namespace Liwanyi\Utils2;

class StringHelper
{
    /**
     * @text 返回随机字符串
     * @return string
     */
    public static function getRandUniqueString(): string
    {
        return md5(uniqid(md5(microtime(true)), true));
    }

    /**
     * 获取指定长度随机字符串
     * @param int $length
     * @return string
     */
    public static function getLengthStr(int $length = 6): string // 修改为 int 类型
    {
        $strs = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        return substr(str_shuffle($strs), mt_rand(0, strlen($strs) - 11), $length);
    }

    // 判断是否包含字符串
    public static function isContainStrings(string $string, $contained): bool // 添加返回类型
    {
        return strpos($string, strval($contained)) !== false; // 简化返回
    }

    /**
     * 字符串切割为数组
     * @param string $string
     * @return array
     */
    public static function explodeString(string $string): array // 修改为 static
    {
        return preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
     * @param string $user_name 姓名
     * @param string $repeatStr 替换的字符
     * @param string $encode 字符编码
     * @return string
     */
    public static function hiddenString(string $user_name, string $repeatStr = '*', string $encode = 'utf-8'): string // 修改为 static
    {
        if (empty($user_name)) {
            return '***';
        }
        $length = mb_strlen($user_name, $encode);
        $firstStr = mb_substr($user_name, 0, 1, $encode);
        $lastStr = mb_substr($user_name, -1, 1, $encode);
        return $length == 2 ? $firstStr . str_repeat($repeatStr, $length - 1) : $firstStr . str_repeat($repeatStr, $length - 2) . $lastStr;
    }
}