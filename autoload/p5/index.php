<?php
define("DS", DIRECTORY_SEPARATOR);
define("EXT", '.php');
define("ROOT_PATH", __DIR__ . DS);
define("FOOL_PATH", ROOT_PATH . 'fool' . DS);
define("APP_PATH", ROOT_PATH . 'app' . DS);

define("EXTEND", ROOT_PATH . 'extend' . DS);

// 注册自动加载机制
require ROOT_PATH . 'fool/Loader.php';
use \fool\Loader;

Loader::register();


// new与目录对应的命名空间，成功
use app\controller\User;

$user = new User();
$user->index();


// 设置命名空间pack1 对应 目录 vendow/pack1
Loader::addNamespace('pack1', ROOT_PATH . 'vendor/pack1'. DS);

// new目录与命名空间不对应的 (项目目录/vendor/pack1/A.php, 命名空间pack1\A) 也成功
use pack1\A;

$p1 = new A();
$p1->work();


// new扩展目录的类
use e1\A as eA;
$e = new eA();
$e->work();


// 注册类库映射
Loader::addClassMap('TestClassMap', ROOT_PATH . 'classMap/TestClassMap.php');

$tcp = new TestClassMap();
$tcp->work();


// 添加别名
Loader::addNamespaceAlias('model', 'app\model');
use model\Goods;    // 使用了别名找类
$goods = new Goods();
$goods->getList();


