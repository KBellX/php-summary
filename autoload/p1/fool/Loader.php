<?php

namespace fool;

class Loader
{
    /*
     * 注册自动加载处理函数
     * @return void
     * */
    public static function register() 
    {
        spl_autoload_register("fool\\Loader::autoload", true, true);
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
     * 查找文件
     * @param  string $class 类名
     * @return bool|string    
     * */
    private static function findFile($class) 
    {
        return ROOT_PATH . strtr($class, '\\', DS) . EXT;
    }
}
