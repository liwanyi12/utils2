<?php

namespace Liwanyi\Utils2\models;

use think\Model;

class BModel extends Model implements ModelInterface
{
    private static $instances = [];

    // 实例化
    public static function getInstance(): self
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }
        return self::$instances[$class];
    }

    // 创建数据
    public function createData(array $data, array $where = [])
    {
        return $this->save($data);
    }

    //数据查询
    public function readData(array $where = [], array $with = [])
    {
        return $this->with($with)->where($where)->find();
    }

    // 数据更新
    public function updateData(array $where = [], array $data = [])
    {
        return $this->where($where)->update($data);
    }

    //数据删除
    public function deleteData(array $where = [])
    {
        return $this->where($where)->destroy();
    }

    //软删除
    public function softDeleteData(array $where = [])
    {
        return $this->where($where)->update(['deleted_at' => date('Y-m-d H:i:s')]);
    }

    // 字段自减
    public function decFieldData(array $where, string $field, $value): int
    {
        return $this->where($where)->setDec($field, $value);
    }

    // 字段自增
    public function incFieldData(array $where, string $field, $value): int
    {
        return $this->where($where)->setInc($field, $value);
    }

    // 筛选数据
    public function selectData(array $where = [], array $with = [], $field = ['*'], array $options = [])
    {
        $query = $this->with($with)->where($where)->field($field);

        if (isset($options['like']) && is_array($options['like'])) {
            foreach ($options['like'] as $field => $keyword) {
                $query->whereLike($field, "%{$keyword}%");
            }
        }

        if (isset($options['order']) && is_array($options['order'])) {
            foreach ($options['order'] as $field => $direction) {
                $query->order($field, $direction);
            }
        }

        if (isset($options['page']) && $options['page'] && isset($options['list_rows']) && $options['list_rows']) {
            return $query->paginate([
                'page' => $options['page'],
                'list_rows' => $options['list_rows'],
            ]);
        }

        return $query->select();
    }
}