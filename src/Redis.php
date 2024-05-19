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
    public function setValue($key, $value, $expire = 0)
    {
        if ($expire == 0) {
            return $this->redis->set($key, $value);
        } else {
            return $this->redis->setex($key, $expire, $value);
        }
    }

    public function getValue($key)
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
    public function setnx($key, $value)
    {
        if (empty($key)) {
            return '';
        }
        return $this->redis->setnx($key, $value);
    }

    // 删除缓存
    public function remove($key)
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
    public function lPushValue($key, ...$args)
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
    public function lPopValue($key)
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
    public function rPushValue($key, ...$args)
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
    public function rPopValue($key)
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
    public function lLenList($list_name)
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
    public function lRangeValue($list_name, $start = 0, $end = -1)
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
    public function lRemValue($key, $value, $count = 0)
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
    public function lSetValue($key, $value, $index = 0)
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
    public function lLTrim($list_name, $start, $end)
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
    public function lInsertValue($key, $position, $pivot, $value)
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
    public function zAddValue($key, $score_or_options, ...$more_scores_and_mems)
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
    public function zIncrByValue($key, $member, $value)
    {
        return $this->redis->zIncrBy($key, (float)$value, $member);
    }


    /**
     * 命令用于返回有序集的成员个数。
     * @param $key
     * @return false|int|\Redis
     * @throws \RedisException
     */
    public function zCardValue($key)
    {
        return $this->redis->zCard($key);
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
    public function zRevRangeValue($key, $start, $end, $withscores = null)
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
    public function zCountValue($key, $start, $end)
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
    public function zRangeValue($key, $start, $end, $withscores = null)
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
    public function zRangeValueByScore($key, $start, $end, array $options = [])
    {
        return $this->redis->zRangeByScore($key, $start, $end, $options);
    }

    /**
     * 删除有序集数据
     * @param $key
     * @param $member
     * @return int
     */
    public function zRemValue($key, $member)
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
    public function brPop($key_or_keys, $timeout_or_key, ...$extra_args)
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
    public function blPop($key_or_keys, $timeout_or_key, ...$extra_args)
    {
        return $this->redis->brPop($key_or_keys, $timeout_or_key, ...$extra_args);
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
    public function sAdd($key, $value)
    {
        return $this->redis->sAdd($key, ...$value);
    }

    /**
     * 向集合中删除数据 (取消朋友圈点赞)
     * @param $key
     * @param $value
     * @return bool|int
     */
    public function sRem($key, $value)
    {
        return $this->redis->sRem($key, ...$value);
    }

    /**
     * 判断是否在集合中 （可用于是否点赞了该条朋友圈）
     * @param $key
     * @param $value
     * @return bool
     */
    public function sIsMember($key, $value)
    {
        return $this->redis->sIsMember($key, $value);
    }


    /**
     * 用于统计集合中数据的数量 （点赞统计）
     * @param $key
     * @return int
     */
    public function scard($key)
    {
        return $this->redis->scard($key);
    }


    /**
     * 获取集合中的所有数据
     * @param $key
     * @return array
     */
    public function sMembers($key)
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
    public function addLocation($key, $log, $lat, $name)
    {
        if (!$key || !$log || !$lat || !$name) return false;
        return $this->redis->geoadd($key, $log, $lat, $name);
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
    public function geoRadiusByMembers($key, $member, $radius, $units, $options = [])
    {
        if (!$key || !$units || !$radius || !$member) return false;
        return $this->redis->georadiusbymember($key, $member, $radius, $units, $options);
    }

}