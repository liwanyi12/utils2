<?php

namespace Liwanyi\Utils2\models;

/**
 * 基础模型类
 */
abstract class BModel extends ThinkORMDecorator
{
    // 现在BModel只是一个抽象基类，具体实现由装饰器处理
    // 你可以在这里添加一些所有模型共用的方法
}