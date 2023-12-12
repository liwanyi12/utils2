<?php
declare(strict_types=1);
namespace Liwanyi\Utils2;

class Lock
{

    private $config;
    private $redis;

    protected $db;
    public function __construct($config = [])
    {
        $this->config = $config ? $config : ['host' => '127.0.0.1', 'port' => 6379,];
        $this->redis = $this->connect();
        $db = $this->config['db']??0;
        $this->redis->select($db);
    }

    public function connect()
    {
        $redis = new \Redis();
        $redis->connect($this->config['host'], $this->config['port']);
        $redis->auth($this->config['auth']);
        return $redis;
    }

    /**
     * @param $scene 锁场景
     * @param $expire 锁有效期
     * @return bool
     */
    public function lock($scene = null, $expire = 10)
    {
        if (!$scene || !$expire) {
            return false;
        }

        // 生成随机值，锁标识
        $lockId = md5(uniqid(md5(microtime(true)), true));
        $result = $this->redis->set($scene, $lockId, ['NX', 'EX' => $expire]);
        if ($result)
            return $lockId;
        else
            return $result;
    }

    /**
     * 解锁
     */
    public function unLock($scene, $lockId)
    {
        $lua = <<<SCRIPT
            local key=KEYS[1]
            local value=ARGV[1]
            if(redis.call('get', key) == value)
            then
            return redis.call('del', key)
            end
SCRIPT;
        return $this->redis->eval($lua, [$scene, $lockId], 1);
    }
}
