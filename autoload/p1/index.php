<?php
define("DS", DIRECTORY_SEPARATOR);
define("EXT", '.php');
define("ROOT_PATH", __DIR__ . DS);

// 注册自动加载机制
require ROOT_PATH . 'fool/Loader.php';
\fool\Loader::register();


// new与目录对应的命名空间，成功
use app\controller\User;

$user = new User();
$user->index();


// new目录与命名空间不对应的 (项目目录/vendor/pack1/A.php, 命名空间pack1\A) 失败
use pack1\A;

$p1 = new A();
$p1->work();
