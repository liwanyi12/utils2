<?php

namespace Liwanyi\Utils2\models;
interface ModelInterface
{
    public function createData(array $data, array $where = []);

    public function readData(array $where = [], array $with = []);

    public function updateData(array $where = [], array $data = []);

    public function deleteData(array $where = []);

    public function softDeleteData(array $where = []);
}