<?php

namespace Liwanyi\Utils2\models;

use think\Model;

class ThinkORMDecorator implements ModelInterface
{
    /**
     * @var Model
     */
    private $model;

    /**
     * 实例缓存数组
     * @var array
     */
    private static $instances = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * 获取单例实例
     * @return self
     */
    public static function getInstance(): self
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            $modelClass = str_replace('Decorator', '', substr($class, strrpos($class, '\\') + 1));
            self::$instances[$class] = new static(new $modelClass());
        }
        return self::$instances[$class];
    }

    public function createData(array $data, array $where = [])
    {
        if (!empty($where)) {
            $exists = $this->model->where($where)->find();
            if ($exists) {
                return false;
            }
        }
        return $this->model->save($data);
    }

    public function readData(array $where = [], array $with = [], $field = ['*'])
    {
        return $this->model->with($with)
            ->where($where)
            ->field($field)
            ->find();
    }

    public function selectData(array $where = [], array $with = [], $field = ['*'], array $options = [])
    {
        $query = $this->model->with($with)->where($where)->field($field);

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

    public function updateData(array $where = [], array $data = [])
    {
        return $this->model->where($where)->update($data);
    }

    public function deleteData(array $where = [])
    {
        return $this->model->where($where)->delete();
    }

    public function softDeleteData(array $where = [])
    {
        return $this->model->where($where)->update(['deleted_time' => time()]);
    }

    public function setDecFieldValue(array $where, string $field, $value)
    {
        return $this->model->where($where)->setDec($field, $value);
    }

    public function setIncFieldValue(array $where, string $field, $value)
    {
        return $this->model->where($where)->setInc($field, $value);
    }
}