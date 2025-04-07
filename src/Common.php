<?php
declare(strict_types=1);

namespace Liwanyi\Utils2;
class Common
{
    /**
     *
     * @param $url
     * @return mixed
     */
    public function httpCurl($url)
    {
        $stream_opts = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ]
        ];
        return json_decode(file_get_contents($url, false, stream_context_create($stream_opts)));
    }

    /**
     * 校验身份证号是否合法
     * @param string $num 待校验的身份证号
     * @return bool
     */
    public static function isValid(string $num)
    {
        //老身份证长度15位，新身份证长度18位
        $length = strlen($num);
        if ($length == 15) { //如果是15位身份证

            //15位身份证没有字母
            if (!is_numeric($num)) {
                return false;
            }
            // 省市县（6位）
            $areaNum = substr($num, 0, 6);
            // 出生年月（6位）
            $dateNum = substr($num, 6, 6);

        } else if ($length == 18) { //如果是18位身份证

            //基本格式校验
            if (!preg_match('/^\d{17}[0-9xX]$/', $num)) {
                return false;
            }
            // 省市县（6位）
            $areaNum = substr($num, 0, 6);
            // 出生年月日（8位）
            $dateNum = substr($num, 6, 8);

        } else { //假身份证
            return false;
        }

        //验证地区
        if (!self::isAreaCodeValid($areaNum)) {
            return false;
        }

        //验证日期
        if (!self::isDateValid($dateNum)) {
            return false;
        }

        //验证最后一位
        if (!self::isVerifyCodeValid($num)) {
            return false;
        }

        return true;
    }

    /**
     * 省市自治区校验
     * @param string $area 省、直辖市代码
     * @return bool
     */
    private static function isAreaCodeValid(string $area)
    {
        $provinceCode = substr($area, 0, 2);

        // 根据GB/T2260—999，省市代码11到65
        if (11 <= $provinceCode && $provinceCode <= 65) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证出生日期合法性
     * @param string $date 日期
     * @return bool
     */
    private static function isDateValid(string $date)
    {
        if (strlen($date) == 6) { //15位身份证号没有年份，这里拼上年份
            $date = '19' . $date;
        }
        $year = intval(substr($date, 0, 4));
        $month = intval(substr($date, 4, 2));
        $day = intval(substr($date, 6, 2));

        //日期基本格式校验
        if (!checkdate($month, $day, $year)) {
            return false;
        }

        //日期格式正确，但是逻辑存在问题(如:年份大于当前年)
        $currYear = date('Y');
        if ($year > $currYear) {
            return false;
        }
        return true;
    }

    /**
     * 验证18位身份证最后一位
     * @param string $num 待校验的身份证号
     * @return bool
     */
    private static function isVerifyCodeValid(string $num)
    {
        if (strlen($num) == 18) {
            $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
            $tokens = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];

            $checkSum = 0;
            for ($i = 0; $i < 17; $i++) {
                $checkSum += intval($num[$i]) * $factor[$i];
            }

            $mod = $checkSum % 11;
            $token = $tokens[$mod];

            $lastChar = strtoupper($num[17]);

            if ($lastChar != $token) {
                return false;
            }
        }
        return true;
    }

    /**
     * 生成订单号 (高并发情况下,不保证全局唯一)
     * @return string
     */
    public static function createOrderNo()
    {
        $datePart = date('Ymd');

        $microtime = microtime(true); // 获取当前时间戳（带微秒）
        $randomPart = mt_rand(1000, 9999); // 生成一个 4 位随机数

        $uniquePart = substr(str_replace('.', '', (string)$microtime), -6) . $randomPart;
        return $datePart . $uniquePart;
    }

    /**
     * 根据经纬度计算两个距离
     * @param $first_lng
     * @param $first_lat
     * @param $lng
     * @param $lat
     * @return string
     */
    public static function distance($first_lng, $first_lat, $lng, $lat)
    {
        $earth_radius = 6371; // 地球半径，单位为公里
        $delta_lng = deg2rad($lng - $first_lng);
        $delta_lat = deg2rad($lat - $first_lat);
        $a = sin($delta_lat / 2) * sin($delta_lat / 2) + cos(deg2rad($first_lng)) * cos(deg2rad($lat)) * sin($delta_lng / 2) * sin($delta_lng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return number_format($earth_radius * $c, 2, '.', '');
    }


    // 腾讯地图获取 两个经纬度之间的距离

    /**
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @param $unit
     * @return float|int
     */
    public function getDistanceBetweenPoints($lat1, $lon1, $lat2, $lon2, $unit = 'km')
    {
        // 将经纬度从度转换为弧度
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // 计算差值
        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        // Haversine 公式
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($lat1) * cos($lat2) * sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // 地球半径（单位：公里）
        $earthRadius = 6371;

        // 计算距离（单位：公里）
        $distance = $earthRadius * $c;

        // 根据单位转换距离
        switch (strtolower($unit)) {
            case 'm': // 米
                $distance *= 1000;
                break;
            case 'km': // 公里
                // 默认单位，无需转换
                break;
            case 'mi': // 英里
                $distance *= 0.621371;
                break;
            case 'ft': // 英尺
                $distance *= 3280.84;
                break;
            default:
                throw new \InvalidArgumentException("不支持的单位: {$unit}");
        }

        return $distance;
    }


    /**
     * 腾讯 根据地址获取经纬度
     * @param $address
     * @param $apiKey
     * @return array|null
     */
    function getLatLngFromAddressByTencent($address, $apiKey)
    {
        // 构建请求 URL
        $url = "https://apis.map.qq.com/ws/geocoder/v1/?address=" . urlencode($address) . "&key=" . $apiKey;

        // 发起请求
        $response = file_get_contents($url);

        // 解析 JSON 数据
        $data = json_decode($response, true);

        // 检查是否成功
        if ($data && $data['status'] === 0) {
            $latitude = $data['result']['location']['lat'];  // 纬度
            $longitude = $data['result']['location']['lng']; // 经度
            return [
                'latitude' => $latitude,
                'longitude' => $longitude
            ];
        } else {
            // 记录错误日志
            error_log("腾讯地图 Geocoding API 请求失败: " . json_encode($data));
            return null;
        }
    }

    /**
     * 判断是否是AJAX请求
     * @return bool
     *
     * 使用案例：
     * if (is_ajax()) { ... }
     */
    public static function is_ajax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * 获取客户端IP
     * @return string
     *
     * 使用案例：
     * $ip = get_client_ip();
     */
    public static function get_client_ip(): string
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ??
            $_SERVER['HTTP_CLIENT_IP'] ??
            $_SERVER['REMOTE_ADDR'] ?? '';
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
    }
}