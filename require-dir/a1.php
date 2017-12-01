<?php
echo '<h1>直接相对路径引入文件</h1>';
echo '<h2>这种方式：相对慢且比较乱</h2>';
echo '<hr />';

echo 'a1所在：' . __DIR__;

$str = <<<A
<h3>我是a1，我这样引入b1：</h3>
require('two/b1.php');
A;
echo $str;

require('two/b1.php');