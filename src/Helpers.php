<?php
declare(strict_types=1);

namespace Liwanyi\Utils2;

use think\facade\Cache;
use think\facade\Log;

if (!function_exists('env')) {
    /**
     * 获取环境变量值
     * @param string $key 键名
     * @param mixed $default 默认值
     * @return mixed
     *
     * 使用案例：
     * // .env 文件内容: DB_HOST=127.0.0.1
     * $host = env('DB_HOST', 'localhost');
     * // 返回 '127.0.0.1'
     *
     * $debug = env('APP_DEBUG', false);
     * // 自动转换字符串 'true' 为布尔值 true
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return is_callable($default) ? $default() : $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        return $value;
    }
}

if (!function_exists('remember')) {
    /**
     * 缓存记住结果
     * @param string $key 缓存键
     * @param callable $callback 回调函数
     * @param int $ttl 缓存时间(秒)
     * @return mixed
     *
     * 使用案例：
     * $products = remember('popular_products', function() {
     *     return Db::table('products')->where('views', '>', 1000)->select();
     * }, 3600);
     * // 首次查询数据库并缓存，后续直接从缓存读取
     */
    function remember(string $key, callable $callback, int $ttl = 3600)
    {
        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $value = $callback();
        Cache::set($key, $value, $ttl);
        return $value;
    }
}

if (!function_exists('str_rand')) {
    /**
     * 生成随机字符串
     * @param int $length 长度
     * @param string $type 类型(number/letter/mix/special)
     * @return string
     *
     * 使用案例：
     * $token = str_rand(32); // 默认混合类型
     * // 可能结果: "x7F3jK9pL2qR5tY8vW1sB4nM6cV9zX0"
     *
     * $code = str_rand(6, 'number');
     * // 可能结果: "384729"
     */
    function str_rand(int $length = 16, string $type = 'mix'): string
    {
        $numbers = '0123456789';
        $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $specials = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        if ($type === 'number') {
            $chars = $numbers;
        } elseif ($type === 'letter') {
            $chars = $letters;
        } elseif ($type === 'special') {
            $chars = $specials;
        } else {
            $chars = $numbers . $letters;
        }

        $result = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[mt_rand(0, $max)];
        }

        return $result;
    }
}

if (!function_exists('safe_json_decode')) {
    /**
     * 安全JSON解码
     * @param string $json JSON字符串
     * @param bool $assoc 是否返回数组
     * @param mixed $default 解码失败默认值
     * @return mixed
     *
     * 使用案例：
     * $data = safe_json_decode('{"name":"John","age":30}', true);
     * // 返回: ['name' => 'John', 'age' => 30]
     *
     * $invalid = safe_json_decode('invalid json', true, []);
     * // 返回: [] (因为解码失败返回默认值)
     */
    function safe_json_decode(string $json, bool $assoc = true, $default = [])
    {
        $decoded = json_decode($json, $assoc);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : (is_callable($default) ? $default() : $default);
    }
}

if (!function_exists('throw_if')) {
    /**
     * 条件抛出异常
     * @param mixed $condition 条件
     * @param Throwable|string $exception 异常类或消息
     * @param array ...$parameters 异常参数
     * @throws Throwable
     *
     * 使用案例：
     * throw_if(!$user, '用户不存在');
     * // 等同于: if (!$user) throw new RuntimeException('用户不存在');
     *
     * throw_if($count > 100, new CustomException('数量超标', 4001));
     */
    function throw_if($condition, $exception, ...$parameters)
    {
        if ($condition) {
            if (is_string($exception)) {
                throw new \RuntimeException($exception);
            }

            throw $exception;
        }
    }
}

if (!function_exists('data_get')) {
    /**
     * 安全获取数组/对象值
     * @param mixed $target 目标
     * @param string|array $key 键名
     * @param mixed $default 默认值
     * @return mixed
     *
     * 使用案例：
     * $value = data_get($user, 'profile.address.street', '未知');
     * // 获取嵌套值，不存在则返回'未知'
     *
     * $data = ['users' => ['name' => 'John']];
     * $name = data_get($data, 'users.name');
     * // 返回 'John'
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $segment) {
            if (is_array($target) && array_key_exists($segment, $target)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return is_callable($default) ? $default() : $default;
            }
        }

        return $target;
    }
}

if (!function_exists('optional')) {
    /**
     * 空对象安全调用 (PHP 7.4 兼容版)
     * @param mixed $value 可能为null的值
     * @return object 代理对象
     *
     * 使用案例：
     * // 安全访问可能为null的对象属性
     * $street = optional($user->address)->street;
     * // 如果$user或address为null不会报错，返回null
     *
     * // 安全调用可能为null的对象方法
     * optional($order)->update(['status' => 1]);
     * // 只有$order存在时才执行update
     *
     * // 链式调用
     * $phone = optional($user->contact)->phone('home');
     * // 安全调用contact对象的phone方法
     */
    function optional($value = null)
    {
        return new class($value) {
            /** @var mixed 存储原始值 */
            private $value;

            /**
             * 构造函数
             * @param mixed $value
             */
            public function __construct($value)
            {
                $this->value = $value;
            }

            /**
             * 方法调用拦截
             * @param string $method 方法名
             * @param array $args 参数
             * @return mixed|null
             */
            public function __call($method, $args)
            {
                if (is_object($this->value) && method_exists($this->value, $method)) {
                    return $this->value->{$method}(...$args);
                }
                return null;
            }

            /**
             * 属性访问拦截
             * @param string $property 属性名
             * @return mixed|null
             */
            public function __get($property)
            {
                return (is_object($this->value) && property_exists($this->value, $property)) ? $this->value->{$property} : null;
            }

            /**
             * 属性设置拦截
             * @param string $property 属性名
             * @param mixed $value 属性值
             */
            public function __set($property, $value)
            {
                if (is_object($this->value)) {
                    $this->value->{$property} = $value;
                }
            }
        };
    }
}

if (!function_exists('retry')) {
    /**
     * 重试机制 (PHP 7.4+ 兼容版)
     * @param int $times 重试次数
     * @param callable $callback 回调函数
     * @param int $sleep 重试间隔(毫秒)
     * @param callable|null $when 条件判断
     * @return mixed
     * @throws Exception
     *
     * 使用案例：
     * $result = retry(3, function() {
     *     return Http::get('https://api.example.com');
     * }, 500);
     *
     * // 带条件重试
     * retry(2, $callback, 0, function($e) {
     *     return $e instanceof ConnectionException;
     * });
     */
    function retry(int $times, callable $callback, int $sleep = 0, callable $when = null)
    {
        $attempts = 0;
        $maxAttempts = $times + 1; // 总尝试次数 = 重试次数 + 初始尝试

        while ($attempts < $maxAttempts) {
            try {
                return $callback();
            } catch (\Exception $e) {
                $attempts++;

                // 检查是否应该继续重试
                if ($attempts >= $maxAttempts || ($when && !$when($e))) {
                    throw $e;
                }

                if ($sleep > 0) {
                    usleep($sleep * 1000);
                }
            }
        }

        // 理论上不会执行到这里
        throw new \RuntimeException('Retry failed after ' . $times . ' attempts');
    }
}

if (!function_exists('tap')) {
    /**
     * 管道操作
     * @param mixed $value 传入值
     * @param callable|null $callback 回调函数
     * @return mixed
     *
     * 使用案例：
     * $user = tap(new User(), function($user) {
     *     $user->name = 'John';
     *     $user->save();
     * });
     * // 创建用户并设置属性后返回用户对象
     *
     * $result = tap($value, 'trim');
     * // 对值处理后返回原值
     */
    function tap($value, callable $callback = null)
    {
        if (!is_null($callback)) {
            $callback($value);
        }

        return $value;
    }
}

if (!function_exists('str_mask')) {
    /**
     * 字符串掩码处理
     * @param string $str
     * @param int $start
     * @param int $length
     * @param string $mask
     * @return string
     *
     * 使用案例：
     * str_mask('13800138000', 3, 4); // 138****8000
     */
    function str_mask(string $str, int $start = 0, int $length = null, string $mask = '*'): string
    {
        $length = $length ?? strlen($str) - $start;
        return substr_replace($str, str_repeat($mask, $length), $start, $length);
    }
}

if (!function_exists('str_wrap')) {
    /**
     * 用指定字符包裹字符串
     * @param string $str
     * @param string $wrapper
     * @return string
     *
     * 使用案例：
     * str_wrap('hello', '"'); // "hello"
     */
    function str_wrap(string $str, string $wrapper = '"'): string
    {
        return $wrapper . $str . $wrapper;
    }
}


if (!function_exists('dd')) {
    /**
     * 格式化打印变量并终止程序
     * @param mixed ...$vars
     *
     * 使用案例：
     * dd($user, $orders);
     */
    function dd(...$vars)
    {
        echo '<pre>';
        foreach ($vars as $var) {
            print_r($var);
        }
        echo '</pre>';
        exit(1);
    }
}

if (!function_exists('log_debug')) {
    /**
     * 快速记录调试日志
     * @param string $message
     * @param array $context
     *
     * 使用案例：
     * log_debug('用户登录', ['user_id' => 123]);
     */
    function log_debug(string $message, array $context = [])
    {
        Log::debug($message, $context);
    }
}

if (!function_exists('benchmark')) {
    /**
     * 代码执行时间测量
     * @param callable $callback
     * @param int $round
     * @return array [result, time]
     *
     * 使用案例：
     * [$result, $time] = benchmark(fn() => heavy_operation());
     */
    function benchmark(callable $callback, int $round = 4): array
    {
        $start = microtime(true);
        $result = $callback();
        return [$result, round(microtime(true) - $start, $round)];
    }
}
