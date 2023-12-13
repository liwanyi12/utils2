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

}