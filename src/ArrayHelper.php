<?php
declare(strict_types=1);

namespace Liwanyi\Utils2;


class ArrayHelper
{

    /**
     * 获取数组第一个值  reset() array_values($arr)[0] Arr::first() 都可以获取到第一个数组的值
     */
    public static function getFirstValue($data)
    {
        if (!is_array($data) && !is_object($data)) {
            throw new \InvalidArgumentException('Input must be an array or an object.');
        }

        return reset($data);
    }


    /**
     * 把字符串打散为数组
     * @param string $separator
     * @param string $string
     * @return string|string[]
     */
    public static function explodeValue(string $separator, string $string): array
    {
        if (empty($data)) {
            return $string;
        }

        return explode($separator, $string);
    }

    public static function implodeValue($array, $separator = "")
    {
        if (empty($array)) {
            return $array;
        }

        // 检查 $array 是否为数组
        if (!is_array($array)) {
            throw new \InvalidArgumentException('The first argument must be an array or null.');
        }

        // 检查 $separator 是否为字符串或数组
        if (!is_string($separator) && !is_array($separator)) {
            throw new \InvalidArgumentException('The separator must be a string or an array.');
        }

        // 如果 $separator 是数组，将其转换为字符串
        if (is_array($separator)) {
            $separator = implode('', $separator);
        }

        return implode($separator, $array);
    }


    /**
     * 清理数组中重复的值
     * @param array $array
     * @param array $value
     * @return array
     */
    public function deleteArrayRepeatData(array $array, array $value): array
    {
        return array_values(array_diff($array, $value));
    }


    /**
     * 根据二维数组中的某个字段进行排序
     * @param array $array 二维数组
     * @param string $key 排序依据的字段名
     * @param string $type 排序类型，'asc' 为升序，'desc' 为降序
     * @return array 排序后的数组
     */
    public static function arraySortByKey(array $array, string $key, string $type = 'asc'): array
    {
        usort($array, function ($a, $b) use ($key, $type) {
            if ($a[$key] == $b[$key]) {
                return 0;
            }
            return ($type === 'asc') ? ($a[$key] < $b[$key] ? -1 : 1) : ($a[$key] > $b[$key] ? -1 : 1);
        });

        return $array;
    }


    /**
     * 根据二维数组中的某个字段进行分组
     * @param array $results 二维数组数据
     * @param string $field 分组依据的字段
     * @return array 分组后的结果
     */
    public function groupByField(array $results, string $field): array
    {
        $groupedData = [];
        foreach ($results as $record) {
            $key = $record[$field];
            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [];
            }
            $groupedData[$key][] = $record;
        }
        return $groupedData;
    }

    /**
     * 从二维数组中获取某个字段最小值
     * @param array $array
     * @param string $field
     * @return mixed
     */

    public function getMinValueFromArray(array $array, string $field) {
        if (empty($array)) {
            return [];
        }

        $array = array_column($array, $field);

        if (empty($array)) {
            return [];
        }

        return min($array);
    }

}










