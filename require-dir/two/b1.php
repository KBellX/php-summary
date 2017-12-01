<?php
echo '<hr />';
echo 'b1所在：' . __DIR__;

$str = <<<A
<h3>我是b1，我这样引入c1：</h3>
require('c1.php');
A;
echo $str;

require('c1.php');