<?php

namespace fool;

class Loader
{
    /*
     * 改变1：
     * PSR-4命名空间规范: 非类应该以\结尾，如fool\ ; app\
     * 记录的目录 /data/autoload/p3/fool,最后没下划线
     * 改变2：
     * findFile方式添加了$prefixLengthsPsr4
     */ 

    /*
     * @var array PSR-4 加载目录
     * */
    private static $prefixDirsPsr4 = [];

    /*
     * @var array PSR-4 命名空间前缀长度映射
     * */
    private static $prefixLengthsPsr4 = [];

    /*
     * 注册自动加载处理函数
     * @return void
     * */
    public static function register() 
    {
        spl_autoload_register("fool\\Loader::autoload", true, true);

        // 添加命名空间 对应目录
        self::addNamespace([
            'app' => APP_PATH,
            'fool' => FOOL_PATH,
        ]);
    }

    /*
     * 自动加载处理函数
     * @param  string  $class 类名
     * @return bool
     * */
    public static function autoload($class) 
    {
        if ($file = self::findFile($class)) {
            return require $file;
        }

        return false;
    }

    /*
     * 注册命名空间。即添加命名空间对应目录 进 变量里，使findFile时能找到
     * 这个方法是再封装了一层，目的有：
     *  1. 提供对外注册命名空间接口。
     *  2. 使调用传参更方便。
     * @param string|array $namespace 命名空间
     * @param string       $paths     路径
     * @return void
     * */
    public static function addNamespace($namespace, $path = '') 
    {
        if (is_array($namespace)) {
            foreach ($namespace as $prefix => $paths) {
                self::addPsr4($prefix . '\\', rtrim($paths, '/'), true);
            }
        } else {
            // PSR-4命名空间规范: 非类应该以\结尾，如fool\ ; app\
            self::addPsr4($namespace . '\\', rtrim($path, '/'), true);
        }
    }

    /*
     * 查找文件
     * @param  string $class 类名
     * @return bool|string    
     * */
    private static function findFile($class) 
    {
        // 先直接 命名空间 转成 路径
        $logicalPathPsr4 = strtr($class, '\\', DS) . EXT;

        // 根据(命名空间 与 目录)映射 替换前缀
        $first = $class[0];
        if (isset(self::$prefixLengthsPsr4[$first])) {
            foreach (self::$prefixLengthsPsr4[$first] as $prefix => $length) {
                if (0 === strpos($class, $prefix)) {
                    foreach (self::$prefixDirsPsr4[$prefix] as $dir) {
                        if (is_file($file = $dir . DS . substr($logicalPathPsr4, $length))) {
                            return $file;
                        }
                    }
                }
            } 
        }

        return false;
    }

    /*
     * 添加PSR-4 空间
     * @param  string $prefix  命名空间前缀
     * @param  string $paths   路径
     * @param  bool   $prepend 该路径的优先级更高
     * @return void
     * */
    private static function addPsr4($prefix, $paths, $prepend = false) 
    {
        if (!isset(self::$prefixDirsPsr4[$prefix])) {
            // 注册新的命名空间
            self::$prefixDirsPsr4[$prefix] = (array) $paths;
            // 记录前缀长度
            $length = strlen($prefix);
            if ('\\' !== $prefix[$length - 1]) {
                // PSR-4规范，非类的命名空间应该以\结尾
                echo 'A non-empty PSR-4 prefix must end with a namespace separator.';
            }
            self::$prefixLengthsPsr4[$prefix[0]][$prefix] = $length;
        } else {
            // 为已有命名空间添加对应目录
            self::$prefixDirsPsr4[$prefix] = $prepend ?
                array_merge((array) $paths, self::$prefixDirsPsr4[$prefix]) :
                array_merge(self::$prefixDirsPsr4[$prefix], (array) $paths);
        } 
    }
}
