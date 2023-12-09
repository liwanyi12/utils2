<?php

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
    public function setValue($key, $value, $expire=0){
        if($expire == 0){
            $ret =  $this->redis->set($key, $value);
        }else{
            $ret =  $this->redis->setex($key, $expire, $value);
        }
        return $ret;
    }


    // 当key 不存在的时候设置 否则设置失败
    public function setnx($key, $value){
        return $this->redis->setnx($key, $value);
    }

    // 删除缓存
    public function remove($key){
        // $key => "key1" || array('key1','key2')
        return $this->redis->del($key);
    }

// redis 获取字符串  队列 集合 有序集合
}