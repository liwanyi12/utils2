<?php

namespace Liwanyi\Utils2\models;

interface ModelInterface
{
    /**
     * 创建新记录
     * @param array $data 要插入的数据
     * @param array $where 条件数组(用于防重复检查等场景)
     * @return mixed 返回创建的结果(通常是插入ID或模型对象)
     */
    public function createData(array $data, array $where = []);

    /**
     * 读取数据
     * @param array $where 查询条件
     * @param array $with 关联预加载
     * @return mixed 返回查询结果(通常是数组或集合)
     */
    public function readData(array $where = [], array $with = []);

    /**
     * 更新数据
     * @param array $where 更新条件
     * @param array $data 要更新的数据
     * @return int 返回受影响的行数
     */
    public function updateData(array $where = [], array $data = []);

    /**
     * 删除数据(物理删除)
     * @param array $where 删除条件
     * @return int 返回受影响的行数
     */
    public function deleteData(array $where = []);

    /**
     * 软删除数据
     * @param array $where 删除条件
     * @return bool 返回是否删除成功
     */
    public function softDeleteData(array $where = []);

    /**
     * 字段值递减
     * @param array $where 条件
     * @param string $field 字段名
     * @param int|float $value 递减值
     * @return int 返回受影响的行数
     */
    public function setDecFieldValue(array $where, string $field, $value);

    /**
     * 字段值递增
     * @param array $where 条件
     * @param string $field 字段名
     * @param int|float $value 递增值
     * @return int 返回受影响的行数
     */
    public function setIncFieldValue(array $where, string $field, $value);
}