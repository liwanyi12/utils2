<?php
declare(strict_types=1);

namespace Liwanyi\Utils2;
class Redis
{
    public function __construct($config = [])
    {
        $this->config = $config ? $config : ['host' => '127.0.0.1', 'port' => 6379,];
        $this->redis = $this->connect();
    }

    public function connect()
    {
        $redis = new \Redis();
        $redis->connect($this->config['host'], (int)$this->config['port']);
        $redis->auth($this->config['auth']);
        return $redis;
    }

    // 设置key
    public function setValue(string $key, mixed $value, $expire = 0)
    {
        if ($expire == 0) {
            return $this->redis->set($key, $value);
        } else {
            return $this->redis->setex($key, $expire, $value);
        }
    }

    /**
     *  判断key 是否存在
     * @param string $key
     * @return bool|int
     */
    public function isExist(string $key)
    {
        return $this->redis->exists($key);
    }


    public function getValue(string $key)
    {
        if (empty($key)) {
            return '';
        }
        if (is_array($key)) {
            return $this->redis->mGet($key);
        }
        return $this->redis->get($key);
    }


    // 当key 不存在的时候设置 否则设置失败
    public function setnx(string $key, mixed $value)
    {
        if (empty($key)) {
            return '';
        }
        return $this->redis->setnx($key, $value);
    }

    // 删除缓存
    public function remove(string $key)
    {
        // $key => "key1" || array('key1','key2')
        if (empty($key)) {
            return '';
        }

        return $this->redis->del($key);
    }

// redis 列表 集合 有序集合 redis 限流

// 列表********************************************************************************
    /**
     * 从左边（头部） 添加数据
     * @param $key
     * @param ...$args
     * @return false|int|\Redis
     * @throws \RedisException
     */
    public function lPushValue(string $key, mixed ...$args)
    {
        if (empty($key)) {
            return '';
        }

        return $this->redis->lPush($key, ...$args);
    }

    /**
     * 移除元素
     * @param $key
     * @param $count
     * @return bool|mixed|\Redis
     * @throws \RedisException
     */
    public function lPopValue(string $key)
    {
        if (empty($key)) {
            return '';
        }
        return $this->redis->lPop($key);
    }

    /**
     * 从右边（尾部） 添加数据
     * @param $key
     * @param ...$args
     * @return false|int|\Redis
     * @throws \RedisException
     */
    public function rPushValue(string $key, mixed ...$args)
    {
        if (empty($key)) {
            return '';
        }
        return $this->redis->rPush($key, ...$args);
    }

    /**
     * 移除元素（右边）
     * @param $key
     * @param $count
     * @return bool|mixed|\Redis
     * @throws \RedisException
     */
    public function rPopValue(string $key)
    {
        if (empty($key)) {
            return '';
        }
        return $this->redis->rPop($key);
    }


    /**
     * 返回列表长度
     * @param $list_name
     * @return bool|int|\Redis
     * @throws \RedisException
     */
    public function lLenList(string $list_name)
    {
        if (empty($list_name)) {
            return '';
        }

        return $this->redis->lLen($list_name);
    }

    /**
     * 返回指定范围内的长度( 默认返回全部数据)
     * @param $list_name
     * @param $start
     * @param $end
     * @return array|\Redis|string
     * @throws \RedisException
     */
    public function lRangeValue(string $list_name, int $start = 0, int $end = -1)
    {
        if (empty($list_name)) {
            return '';
        }
        return $this->redis->lRange($list_name, $start, $end);
    }

    /**
     * 删除指定数量 value 的值
     *  LREM list -2 “hello” 会从列表key中删除最后两个出现的 “hello”
     * @param $key
     * @param $value
     * @param $count 可以大于0 可以小于0 可以等于0 默认是0
     * @return bool|int|\Redis
     * @throws \RedisException
     */
    public function lRemValue(string $key, string $value, int $count = 0)
    {
        if (!$key || empty($value)) {
            return '';
        }
        return $this->redis->lrem($key, $value, $count);
    }


    /**
     * 用于设置列表 key 中 index 位置的元素值为 element
     * 默认设置的是第一个
     * @param $key
     * @param $value
     * @param $index
     * @return bool|\Redis|string
     * @throws \RedisException
     */
    public function lSetValue(string $key, string $value, int $index = 0)
    {
        if (!$key || empty($value)) {
            return '';
        }
        return $this->redis->lSet($key, $index, $value);
    }

    /**
     * 保证列表的长度
     * @param $list_name
     * @param $start
     * @param $end
     * @return array|false|\Redis
     * @throws \RedisException
     */
    public function lLTrim(string $list_name, int $start, int $end)
    {
        if (empty($list_name) || empty($start) || empty($end)) {
            return '';
        }
        return $this->redis->lTrim($list_name, $start, $end);
    }


    /**
     * 在指定位置 插入数据
     * @param $key
     * @param $position
     * @param $pivot
     * @param $value
     * @return false|int|\Redis
     * @throws \RedisException
     */
    public function lInsertValue(string $key, int $position, string $pivot, mixed $value)
    {
        return $this->redis->lInsert($key, $position, $pivot, $value);
    }

    /**
     * 有序集合++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     */

    /**
     * 向有序集合添加数值
     * @param $key
     * @param $score_or_options
     * @param ...$more_scores_and_mems
     * @return false|int|\Redis
     * @throws \RedisException
     */
    public function zAddValue(string $key, $score_or_options, ...$more_scores_and_mems)
    {
        return $this->redis->zAdd($key, $score_or_options, ...$more_scores_and_mems);
    }

    /**
     * 给集合成员增加固定的数值
     * @param $key
     * @param $value
     * @param $member
     * @return float|\Redis
     * @throws \RedisException
     */
    public function zIncrByValue(string $key, float $member, string $value)
    {
        return $this->redis->zIncrBy($key, (float)$value, $member);
    }

    /**
     * 计算给定的一个或多个有序集的交集并将结果集存储在新的有序集合 key 中
     *
     * @param string $Output 存储结果的有序集合的 key
     * @param array $ZSetKeys 需要计算交集的有序集合的 key 数组
     * @param array|null $Weights 可选，权重数组，用于对每个有序集合进行加权
     * @param string $aggregateFunction 可选，聚合函数，默认为 'SUM'
     * @return int|null 返回结果集合中的元素数量，失败时返回 null
     */
    public function zInter(string $Output, array $ZSetKeys, ?array $Weights = null, string $aggregateFunction = 'SUM'): ?int
    {
        try {
            $weights = $Weights ?? [];

            $result = $this->redis->zInterStore($Output, $ZSetKeys, $weights, $aggregateFunction);

            return $result !== false ? $result : null;
        } catch (\Exception $e) {
            // 捕获异常并记录错误日志
            error_log("Redis zInterStore failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 命令用于返回有序集的成员个数。
     * @param $key
     * @return false|int|\Redis
     * @throws \RedisException
     */
    public function zCardValue(string $key)
    {
        return $this->redis->zCard($key);
    }

    /**
     * 集合的合并
     * @param string $key
     * @param ...$otherKeys
     * @return array
     */
    public function sUnionKeys(string $key, ...$otherKeys)
    {
        return $this->redis->sUnion($key, $otherKeys);
    }

    /**
     * 所有给定集合的并集存储在 destination 集合中
     * @param string $dstKey
     * @param string $key1
     * @param string ...$otherKeys
     * @return int
     */
    public function sUnionStore(string $dstKey, string $key1,string ...$otherKeys)
    {
        return $this->redis->sUnionStore($dstKey, $key1, $otherKeys);
    }

    /**
     * 迭代集合中的元素
     * @param $key
     * @param $iterator
     * @param $pattern
     * @param $count
     * @return array|bool
     */
    public function SSCAN($key, &$iterator, $pattern = null, $count = 0)
    {
        return $this->redis->SSCAN($key, $iterator, $pattern, $count);
    }

    /**
     * 从大到小排列
     * @param $key
     * @param $start
     * @param $end
     * @param $withscores
     * @return array|\Redis
     * @throws \RedisException
     */
    public function zRevRangeValue(string $key, int $start, int $end, bool $withscores = null)
    {
        return $this->redis->zRevRange($key, (int)$start, (int)$end, $withscores);
    }

    /**
     * 返回指定分数成员之间的数量(查询指定范围数量）
     * @param $key
     * @param $start
     * @param $end
     * @return false|int|\Redis
     * @throws \RedisException
     */
    public function zCountValue(string $key, int $start, int $end)
    {
        return $this->redis->zCount($key, $start, $end);
    }


    /**
     * 获取有序集数据 （从小到大排序）
     * @param $key
     * @param $start
     * @param $end
     * @param null $withscores （是否具有得分）
     * @return array
     */
    public function zRangeValue(string $key, int $start, int $end, bool $withscores = null)
    {
        return $this->redis->zRange($key, (int)$start, (int)$end, $withscores);
    }

    /**
     * 通过分数获取指定数量 (表示的分值区间 就是说 0-9  就是说 在0-9 分之间的人）
     * @param $key
     * @param $start
     * @param $end
     * @param array $options
     * @return array|false|\Redis
     * @throws \RedisException
     */
    public function zRangeValueByScore(string $key, int $start, int $end, array $options = [])
    {
        return $this->redis->zRangeByScore($key, $start, $end, $options);
    }

    /**
     * 删除有序集数据
     * @param $key
     * @param $member
     * @return int
     */
    public function zRemValue(string $key, mixed $member)
    {
        return $this->redis->zRem($key, $member);
    }


    /**
     *阻塞的方式 获取队列数据
     * @param $key_or_keys
     * @param $timeout_or_key
     * @param ...$extra_args
     * @return array|\Redis|true|null
     * @throws \RedisException
     */
    public function brPop(array $key_or_keys, $timeout_or_key, ...$extra_args)
    {
        return $this->redis->brPop($key_or_keys, $timeout_or_key, ...$extra_args);
    }


    /**
     * 阻塞的方式 获取队列数据 (右)
     * @param $key_or_keys
     * @param $timeout_or_key
     * @param ...$extra_args
     * @return array|\Redis|true|null
     * @throws \RedisException
     */
    public function blPop(array $key_or_keys, $timeout_or_key, ...$extra_args)
    {
        return $this->redis->blPop($key_or_keys, $timeout_or_key, ...$extra_args);
    }

    /**
     * 从列表中弹出一个值，并将该值插入到另外一个列表中并返回它
     * @param string $srcKey
     * @param string $dstKey
     * @param int $timeout
     * @return bool|mixed|string
     */
    public function brpoplpush(string $srcKey, string $dstKey, int $timeout)
    {
        return $this->redis->brpoplpush($srcKey, $dstKey, $timeout);
    }

    /**
     * 通过索引获取列表中的元素
     * @param string $key
     * @param int $index
     * @return bool|mixed
     */
    public function lIndex(string $key, int $index)
    {
        return $this->redis->lIndex($key, $index);
    }

    /**
     * 在列表的元素前或者后插入元素
     * @param string $key
     * @param int $position Redis::BEFORE | Redis::AFTER
     * @param string $pivot
     * @param mixed $value
     * @return int
     */
    public function lInsert(string $key, int $position , string $pivot, mixed $value)
    {
        return $this->redis->lInsert($key, $position, $pivot, $value);
    }

    /**
     * 对一个列表进行修剪(trim)
     * @param string $key
     * @param int $start
     * @param int $stop
     * @return array|bool
     */
    public function lTrim(string $key, int $start,int $stop)
    {
        return $this->redis->lTrim($key, $start, $stop);
    }

    /**
     * 为已存在的列表添加值
     * @param $key
     * @return bool|mixed
     */
    public function rPushx(string $key,mixed $value)
    {
        return $this->redis->rPushx($key, $value);
    }
    //**********************
    //集合内部使用的hash 所以 增加 删除 查找 的复杂度都是 （O1）
    //******************************************************集合


    /**
     * 向集合中添加数据 （朋友圈点赞）
     * @param $key
     * @param $value
     * @return bool|int
     */
    public function sAdd(string $key, mixed $value)
    {
        return $this->redis->sAdd($key, ...$value);
    }

    /**
     * 向集合中删除数据 (取消朋友圈点赞)
     * @param $key
     * @param $value
     * @return bool|int
     */
    public function sRem(string $key, mixed $value)
    {
        return $this->redis->sRem($key, ...$value);
    }

    /**
     * 判断是否在集合中 （可用于是否点赞了该条朋友圈）
     * @param $key
     * @param $value
     * @return bool
     */
    public function sIsMember(string $key, mixed $value)
    {
        return $this->redis->sIsMember($key, $value);
    }


    /**
     * 用于统计集合中数据的数量 （点赞统计）
     * @param $key
     * @return int
     */
    public function scard(string $key)
    {
        return $this->redis->scard($key);
    }

    /**
     * 返回给定所有集合的差集
     * @param string $key
     * @param string ...$otherKeys
     * @return array
     */
    public function sdiff(string $key,string ...$otherKeys)
    {
        return $this->redis->sDiff($key,$otherKeys);
    }

    /**
     * 返回给定所有集合的差集并存储在 新的集合 中
     * @param string $dstKey
     * @param string $key1
     * @param string ...$otherKeys
     * @return bool|int
     */
    public function sdiffStore(string $dstKey, string $key1,string  ...$otherKeys)
    {
        return $this->redis->sDiffStore($dstKey, $key1, $otherKeys);
    }

    /**
     * 返回给定所有集合的交集
     * @param $key1
     * @param ...$otherKeys
     * @return array
     */
    public function sinter($key1, ...$otherKeys)
    {
        return $this->redis->sInter($key1, $otherKeys);
    }

    /*
     * 返回给定所有集合的交集并存储在 新的集合 中
     */
    public function sinterStore(string $dstKey,string $key1,string ...$otherKeys)
    {
        return $this->redis->sinterStore($dstKey,$key1, $otherKeys);
    }

    /**
     * 将 member 元素从 source 集合移动到 destination 集合
     * @param string $srcKey
     * @param string $dstKey
     * @param string $member
     * @return bool
     */
    public function sMove(string $srcKey, string $dstKey, mixed $member)
    {
        return $this->redis->sMove($srcKey, $dstKey, $member);
    }

    /**
     * 移除并返回集合中的一个随机元素
     * @param string $key
     * @param int $count
     * @return array|bool|mixed|string
     */
    public function sPop(string $key, int $count=1)
    {
        return $this->redis->sPop($key, $count);
    }

    /**
     * 返回集合中一个或多个随机数
     * @param string $key
     * @param int $count
     * @return array|bool|mixed|string
     */
    public function sRandMember(string $key, int $count = 1)
    {
        return $this->redis->sRandMember($key, $count);
    }

    /**
     * 获取集合中的所有数据
     * @param $key
     * @return array
     */
    public function sMembers(string $key)
    {
        return $this->redis->sMembers($key);
    }

    /**
     * 增加地理位置
     * @param $key
     * @param $log
     * @param $lat
     * @param $name
     * @return false|int
     */
    public function addLocation(string $key, string $log, string $lat, string $name)
    {
        if (!$key || !$log || !$lat || !$name) return false;
        return $this->redis->geoadd($key, $log, $lat, $name);
    }

    /**
     * 获取两个成员之间的距离
     * @param string $key Redis 的 key
     * @param string $member1 第一个成员名称
     * @param string $member2 第二个成员名称
     * @param string $unit 距离单位（m: 米, km: 千米, mi: 英里, ft: 英尺）
     * @return false|float 返回距离，失败时返回 false
     */
    public function getDistance(string $key, string $member1, string $member2, string $unit = 'm')
    {
        if (!$key || !$member1 || !$member2) {
            return false;
        }
        $distance = $this->redis->geodist($key, $member1, $member2, $unit);
        return $distance !== false ? (float)$distance : false;
    }


    /**
     * 获取方圆内的地理位置
     * @param $key    key
     * @param $radius 方圆距离
     * @param $member 指定用户
     * @param $units  单位 (miles:英里  km:公里  m:米)
     * @param $options
     * @return array|false
     */
    public function geoRadiusByMembers(string $key, string $member, string $radius, string $units, $options = [])
    {
        if (!$key || !$units || !$radius || !$member) return false;
        return $this->redis->georadiusbymember($key, $member, $radius, $units, $options);
    }

    /**
     * 哈希
     */
    public function hset(string $key, string $field, string $value)
    {
        return $this->redis->hset($key, $field, $value);
    }


    /**
     * 添加或更新成员的分数
     *
     * @param string $member 成员名称
     * @param float $score 分数
     * @param int|null $expireDay 日榜过期时间（秒）
     * @param int|null $expireWeek 周榜过期时间（秒）
     * @param int|null $expireMonth 月榜过期时间（秒）
     * @return bool 是否成功
     */
    public function addScore(
        string $member,
        float $score,
        ?int $expireDay = 604800,    // 默认 7 天
        ?int $expireWeek = 2592000,  // 默认 30 天
        ?int $expireMonth = 31536000 // 默认 365 天
    ): bool {
        if (!$member) {
            return false;
        }

        try {
            // 总榜 key
            $totalKey = 'total_rank';

            // 日榜 key（如 "day_rank_2023_10_05"）
            $dayKey = 'day_rank_' . date('Y_m_d');

            // 周榜 key（如 "week_rank_2023_W40"）
            $weekKey = 'week_rank_' . date('Y_W');

            // 月榜 key（如 "month_rank_2023_10"）
            $monthKey = 'month_rank_' . date('Y_m');

            // 更新总榜、日榜、周榜和月榜的分数
            $this->redis->zadd($totalKey, $score, $member);
            $this->redis->zadd($dayKey, $score, $member);
            $this->redis->zadd($weekKey, $score, $member);
            $this->redis->zadd($monthKey, $score, $member);

            // 设置过期时间
            if ($expireDay !== null) {
                $this->redis->expire($dayKey, $expireDay);
            }
            if ($expireWeek !== null) {
                $this->redis->expire($weekKey, $expireWeek);
            }
            if ($expireMonth !== null) {
                $this->redis->expire($monthKey, $expireMonth);
            }

            return true;
        } catch (\Exception $e) {
            error_log("Redis ZADD failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 获取排行榜
     *
     * @param string $type 榜单类型（total: 总榜, month: 月榜, week: 周榜, day: 日榜）
     * @param int $topN 返回前 N 名
     * @return array 返回排行榜数据
     */
    public function getRank(string $type, int $topN = 10): array
    {
        try {
            // 根据类型生成 key
            switch ($type) {
                case 'total':
                    $key = 'total_rank';
                    break;
                case 'month':
                    $key = 'month_rank_' . date('Y_m');
                    break;
                case 'week':
                    $key = 'week_rank_' . date('Y_W');
                    break;
                case 'day':
                    $key = 'day_rank_' . date('Y_m_d');
                    break;
                default:
                    return [];
            }

            // 使用 ZREVRANGE 获取前 N 名的成员及其分数
            return $this->redis->zrevrange($key, 0, $topN - 1, true);
        } catch (\Exception $e) {
            error_log("Redis ZREVRANGE failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 获取成员的排名
     *
     * @param string $type 榜单类型（total: 总榜, month: 月榜, week: 周榜, day: 日榜）
     * @param string $member 成员名称
     * @return int|false 返回排名（从 0 开始），失败时返回 false
     */
    public function getMemberRank(string $type, string $member)
    {
        if (!$member) {
            return false;
        }

        try {
            // 根据类型生成 key
            switch ($type) {
                case 'total':
                    $key = 'total_rank';
                    break;
                case 'month':
                    $key = 'month_rank_' . date('Y_m');
                    break;
                case 'week':
                    $key = 'week_rank_' . date('Y_W');
                    break;
                case 'day':
                    $key = 'day_rank_' . date('Y_m_d');
                    break;
                default:
                    return false;
            }

            // 使用 ZREVRANK 获取成员的排名
            return $this->redis->zrevrank($key, $member);
        } catch (\Exception $e) {
            error_log("Redis ZREVRANK failed: " . $e->getMessage());
            return false;
        }
    }


    /**
     * 获取排行榜及用户排名
     *
     * @param string $key 有序集合的 key
     * @param string $member 成员名称
     * @param int $topN 返回前 N 名
     * @return array 返回排行榜数据及用户排名
     */
    public function getRankWithUserRank(string $key, string $member, int $topN = 10): array
    {
        // 获取排行榜数据
        $rankData = $this->getRank($key, $topN);

        // 获取用户排名
        $userRank = $this->getMemberRank($key, $member);

        return [
            'data' => $rankData,
            'user_rank' => $userRank !== false ? $userRank + 1 : null // 排名从 1 开始
        ];
    }
}