<?php

namespace Liwanyi\Utils2;

class Common
{
    /**
     * @text 返回随机字符串
     * @return string
     */
    public static function getRandUniqueString(): string
    {
        return md5(uniqid(md5(microtime(true)), true));
    }





}