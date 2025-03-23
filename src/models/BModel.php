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

    public function readData(array $where = [], array $with = [],$field=['*'])
    {
        return $this->with($with)->where($where)->field($field)->find();
    }

    //$data = [
    //    'like' => [
    //        'name' => 'John', // 模糊查询 name 字段包含 'John' 的记录
    //    ],
    //    'page' => 1, // 查询第1页
    //    'list_rows' => 10, // 每页显示10条记录
    //    'order' => [
    //        'created_at' => 'desc', // 按创建时间降序排列
    //    ],
    //];
    //
    //$result = $this->selectData(
    //    ['status' => 1], // 精确查询 status = 1
    //    ['profile'], // 关联查询 profile
    //    ['id', 'name', 'created_at'], // 查询的字段
    //    $data // 其他查询参数
    //);
    public function selectData(array $where = [], array $with = [], $field = ['*'], array $data = [])
    {
        // 初始化查询构建器
        $query = $this->with($with)->where($where)->field($field);

        // 模糊查询处理
        if (isset($data['like']) && is_array($data['like'])) {
            foreach ($data['like'] as $field => $keyword) {
                $query->where($field, 'like', "%{$keyword}%");
            }
        }

        // 分页处理
        if (isset($data['page']) && $data['page'] && isset($data['list_rows']) && $data['list_rows']) {
            $paginate = [
                'page' => $data['page'],
                'list_rows' => $data['list_rows'],
            ];
            return $query->paginate($paginate);
        }

        // 排序处理
        if (isset($data['order']) && is_array($data['order'])) {
            foreach ($data['order'] as $field => $direction) {
                $query->order($field, $direction);
            }
        }

        // 返回查询结果
        return $query->select();
    }

    public function updateData(array $where = [], array $data = [])
    {
        return $this->where($where)->update($data);
    }

    public function deleteData(array $where = [])
    {
        return $this->where($where)->delete();
    }

    public function softDeleteData(array $where = [])
    {
        return $this->where($where)->update(['deleted_time' => date('Y-m-d H:i:s')]);
    }
}