<?php

namespace fool;

class Loader
{
    /*
     * 改变：
     * 添加注册命名空间,查找时也根据注册的命名空间找
     * */

    /*
     * @var array PSR-4 加载目录
     * */
    private static $prefixDirsPsr4 = [];

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
                self::addPsr4($prefix, $paths, true);
            }
        } else {
            self::addPsr4($namespace, $path, true);
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
        $len = strpos($logicalPathPsr4, '/');
        $cPrefix = substr($logicalPathPsr4, 0, $len);
        $follow = substr($logicalPathPsr4, $len+1);
        
        foreach (self::$prefixDirsPsr4 as $prefix => $dirs) {
            if ($prefix == $cPrefix) {
                foreach ($dirs as $dir) {
                    if (is_file($file = $dir . $follow)) {
                        return $file;
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
        } else {
            // 为已有命名空间添加对应目录
            self::$prefixDirsPsr4[$prefix] = $prepend ?
                array_merge((array) $paths, self::$prefixDirsPsr4[$prefix]) :
                array_merge(self::$prefixDirsPsr4[$prefix], (array) $paths);
        } 
    }
}
