<?php
echo '<hr />';
echo 'b2所在：'. __DIR__;
$str=<<<EOT
<h3>我是b2， 我这样引入c2(像a2一样)：</h3>
<h3>require ROOT . '/two/c2.php';</h3>
EOT;
echo $str;
require(ROOT . '/two/c2.php');