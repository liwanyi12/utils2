<?php
declare(strict_types=1);

namespace Liwanyi\Utils2;

class Json
{
    // 保存json 数据类型
    public function saveJsonValue($value)
    {
        return html_entity_decode($value);
    }


}
