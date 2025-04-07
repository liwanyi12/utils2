<?php

class Deal implements ArrayAccess
{
    public function offsetExists($offset)
    {
        echo "这里是 offsetExists() 方法 你输入的参数是 {$offset}";
    }

    public function offsetGet($offset)
    {
        echo "这里是 offsetGet() 方法 你输入的参数是 $offset";
    }

    public function offsetSet($offset, $value)
    {
        echo "这里是 offsetSet() 方法 你输入的 {$offset}={$value}";
    }

    public function offsetUnset($offset)
    {
        echo "这里是 offsetUnset() 方法 你输入的参数是 {$offset}";
    }
}

$data = (new Deal());
unset($data['wow']);
$data['when'] = 'today';
//$t = isset($data['how']);// 输出: 这里是 offsetExists() 方法 你输入的参数是 how
//var_dump($t);// 输出: boolean false

