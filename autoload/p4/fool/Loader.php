<?php

namespace fool;

class Loader
{
    /*
     * 改变1：
     *  添加了回退目录
     * 改变2：
     *  添加了类名映射
     * 改变3：
     *  添加类别名
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
     * @var array PSR-4 加载失败的回退目录
     * */
    private static $fallbackDirsPsr4 = [];

    /*
     * @var array 类名映射
     * 用处：如缓存文件，会多次被调用的，代码里直接设置映射关系，就不用遍历找了
     * */
    private static $classMap = [];

    /*
     * @var array 命名空间别名
     * 与classMap区别是，classMap对应具体类，这个对应目录
     * */
    private static $namespaceAlias = [];

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

        // 自动加载extend目录
        self::$fallbackDirsPsr4[] = rtrim(EXTEND, DS);
    }

    /*
     * 自动加载处理函数
     * @param  string  $class 类名
     * @return bool
     * */
    public static function autoload($class) 
    {
        // 检测命名空间别名，若匹配，则返回（并不加载）后面再进来findFile后加载
        if (!empty(self::$namespaceAlias)) {
            $length = strpos($class, '\\');
            $prefix = substr($class, 0, $length);
            if (isset(self::$namespaceAlias[$prefix])) {
                $original = self::$namespaceAlias[$prefix] . substr($class, $length);
                if (class_exists($original)) {
                    return class_alias($original, $class, false);
                }
            }
        }

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
     * 注册类库映射。 即直接设置 类名 对应 文件路径
     * @param string|string $class 类名
     * @param string        $map   映射。即具体文件名
     * @return void
     * */
    public static function addClassMap($class, $map = '') 
    {
        if (is_array($class)) {
            self::$classMap = array_merge(self::$classMap, $class);
        } else {
            self::$classMap[$class] = $map;
        }
    }

    /*
     * 注册命名空间别名
     * @param string|array $namespace 命名空间
     * @param string       $original  源文件
     * @return void
     * */
    public static function addNamespaceAlias($namespace, $original = '') 
    {
        if (is_array($namespace)) {
            self::$namespaceAlias = array_merge(self::$namespaceAlias, $namespace);
        } else {
            self::$namespaceAlias[$namespace] = $original;
        }
    }

    /*
     * 查找文件
     * @param  string $class 类名
     * @return bool|string    
     * */
    private static function findFile($class) 
    {
        // 类库映射 具体指定的，优先级最高
        if (isset(self::$classMap[$class])) {
            return self::$classMap[$class];
        }

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

        // 指定目录找不到 从PSR-4回退目录(也可理解为默认目录)找
        foreach (self::$fallbackDirsPsr4 as $dir) {
            if (is_file($file = $dir . DS . $logicalPathPsr4)) {
                return $file;
            }
        }

        // 找不到记录一下映射为false, 并返回false
        return self::$classMap[$class] = false;
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
