<?php

namespace Liwanyi\Utils2;
class Redis
{
    private $redis;

    //当前数据库ID号
    protected $dbId = 0;

    //当前权限认证码
    protected $auth;

    /**
     * 实例化的对象,单例模式.
     */
    static private $_instance = array();

    private $k;

    //连接属性数组
    protected $attr = array(
        //连接超时时间，redis配置文件中默认为300秒
        'timeout' => 30,
        //选择的数据库。
        'db_id' => 0,
    );

    //什么时候重新建立连接
    protected $expireTime;

    protected $host;

    protected $port;


    private function __construct($config, $attr = array())
    {
        $this->attr  = array_merge($this->attr, $attr);
        $this->redis = new Redis();
        $this->port  = $config['port'] ? $config['port'] : 6379;
        $this->host  = $config['host'];
        $this->redis->connect($this->host, $this->port, $this->attr['timeout']);
        if ($config['auth']) {
            $this->auth($config['auth']);
            $this->auth = $config['auth'];
        }
        $this->expireTime = time() + $this->attr['timeout'];
    }


    private function __clone(){}



}