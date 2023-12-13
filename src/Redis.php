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
        $redis->connect($this->config['host'], $this->config['port']);
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
        if (is_array($key)) {
            return $this->redis->mGet($key);
        }
        return $this->redis->get($key);
    }


    // 当key 不存在的时候设置 否则设置失败
    public function setnx($key, $value)
    {
        return $this->redis->setnx($key, $value);
    }

    // 删除缓存
    public function remove($key)
    {
        // $key => "key1" || array('key1','key2')
        return $this->redis->del($key);
    }

// redis 列表 集合 有序集合 redis 限流

    /**
     * 从左边（头部） 添加数据
     * @param $key
     * @param ...$args
     * @return false|int|\Redis
     * @throws \RedisException
     */
    public function lPushValue($key, ...$args)
    {
        return $this->redis->lPush($key, ...$args);
    }

    /**
     * 移除元素
     * @param $key
     * @param $count
     * @return bool|mixed|\Redis
     * @throws \RedisException
     */
    public function lPopValue($key, $count = 0)
    {
        return $this->redis->lPop($key, $count);
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
        return $this->redis->rPush($key, ...$args);
    }

    /**
     * 移除元素（右边）
     * @param $key
     * @param $count
     * @return bool|mixed|\Redis
     * @throws \RedisException
     */
    public function rPopValue($key, $count = 0)
    {
        return $this->redis->rPop($key, $count);
    }


    /**
     * 返回列表长度
     * @param $list_name
     * @return bool|int|\Redis
     * @throws \RedisException
     */
    public function lLenList($list_name)
    {
        return $this->redis->lLen($list_name);
    }

    /**
     * 返回指定范围内的长度( 默认返回全部数据)
     * @param $list_name
     * @param $start
     * @param $end
     * @return array|\Redis
     * @throws \RedisException
     */
    public function lRangeValue($list_name, $start = 0, $end = -1)
    {
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
    public function lRemValue($key, $value, $count =0)
    {
        if (!$key || empty($value)) {
            return '';
        }
        return $this->redis->lrem($key, $value, $count );
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
    public function lSetValue($key, $value, $index = 0){
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
    public function lLTrim($list_name,$start,$end){
        if(empty($list_name) || empty($start) || empty($end)){
            return '';
        }
        return $this->redis->lTrim($list_name,$start,$end);
    }

}