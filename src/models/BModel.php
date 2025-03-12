<?php

namespace Liwanyi\Utils2\models;
// 使用thinkphp 框架即可 composer dump-autoload
use think\Model;
class BModel extends Model implements ModelInterface
{
    private static $instances = [];

    public static function getInstance(): self
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }
        return self::$instances[$class];
    }

    public function createData(array $data, array $where = [])
    {
        return $this->save($data);
    }

    public function readData(array $where = [], array $with = [])
    {
        return $this->with($with)->where($where)->find();
    }

    public function updateData(array $where = [], array $data = [])
    {
        return $this->where($where)->update($data);
    }

    public function deleteData(array $where = [])
    {
        return $this->where($where)->destroy();
    }

    public function softDeleteData(array $where = [])
    {
        return $this->where($where)->update(['deleted_at' => date('Y-m-d H:i:s')]);
    }
}