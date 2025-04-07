<?php

namespace Liwanyi\Utils2\models;

use think\Model;

/**
 * 基础模型类
 * 实现 ModelInterface 接口并提供 ThinkPHP 的模型功能
 */
class BModel extends Model implements ModelInterface
{
    /**
     * 实例缓存数组
     * @var array
     */
    private static $instances = [];

    /**
     * 获取单例实例
     * @return self
     */
    public static function getInstance(): self
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }
        return self::$instances[$class];
    }

    /**
     * 创建新记录
     * @param array $data 要插入的数据
     * @param array $where 条件数组(用于防重复检查等场景)
     * @return mixed 返回创建的结果
     */
    public function createData(array $data, array $where = [])
    {
        if (!empty($where)) {
            $exists = $this->where($where)->find();
            if ($exists) {
                return false;
            }
        }
        return $this->save($data);
    }

    /**
     * 读取单条数据
     * @param array $where 查询条件
     * @param array $with 关联预加载
     * @param array $field 查询字段
     * @return mixed
     */
    public function readData(array $where = [], array $with = [], $field = ['*'])
    {
        return $this->with($with)
            ->where($where)
            ->field($field)
            ->find();
    }

    /**
     * 查询多条数据
     * @param array $where 查询条件
     * @param array $with 关联预加载
     * @param array $field 查询字段
     * @param array $options 其他选项 (like/page/list_rows/order)
     * @return mixed
     */
    public function selectData(array $where = [], array $with = [], $field = ['*'], array $options = [])
    {
        $query = $this->with($with)->where($where)->field($field);

        // 模糊查询处理
        if (isset($options['like']) && is_array($options['like'])) {
            foreach ($options['like'] as $field => $keyword) {
                $query->whereLike($field, "%{$keyword}%");
            }
        }

        // 排序处理
        if (isset($options['order']) && is_array($options['order'])) {
            foreach ($options['order'] as $field => $direction) {
                $query->order($field, $direction);
            }
        }

        // 分页处理
        if (isset($options['page']) && $options['page'] && isset($options['list_rows']) && $options['list_rows']) {
            return $query->paginate([
                'page' => $options['page'],
                'list_rows' => $options['list_rows'],
            ]);
        }

        return $query->select();
    }

    /**
     * 更新数据
     * @param array $where 更新条件
     * @param array $data 更新数据
     * @return int 影响的行数
     */
    public function updateData(array $where = [], array $data = [])
    {
        return $this->where($where)->update($data);
    }

    /**
     * 删除数据(物理删除)
     * @param array $where 删除条件
     * @return int 影响的行数
     */
    public function deleteData(array $where = [])
    {
        return $this->where($where)->delete();
    }

    /**
     * 软删除数据
     * @param array $where 删除条件
     * @return int 影响的行数
     */
    public function softDeleteData(array $where = [])
    {
        return $this->where($where)->update(['deleted_time' => time()]);
    }

    /**
     * 字段值递减
     * @param array $where 条件
     * @param string $field 字段名
     * @param int|float $value 递减值
     * @return int 影响的行数
     */
    public function setDecFieldValue(array $where, string $field, $value)
    {
        return $this->where($where)->setDec($field, $value);
    }

    /**
     * 字段值递增
     * @param array $where 条件
     * @param string $field 字段名
     * @param int|float $value 递增值
     * @return int 影响的行数
     */
    public function setIncFieldValue(array $where, string $field, $value)
    {
        return $this->where($where)->setInc($field, $value);
    }
}