<?php
/**
 * php常规引入文件方式
 */
echo '<h1>__DRI__ + 相对路径引入文件</h1>';
echo '<h2>这种PHP引入文件方式好处</h2>';
echo '<h2>1.动态（即只要文件内部关系不变，迁移整个文件不用改路径）</h2>';
echo '<h2>2.减少考虑，一切文件引入都从a出发去找，包括在b中引入c（这个b是a引入的），即不用考虑b与c的相对路径。</h2>';
echo '<hr />';
define('ROOT', __DIR__);

echo 'a2所在：' . __DIR__;
$str = <<<EOT
<h3>在a2里“定义”了根路径（即以a所在目录为根目录，接下来一切文件引入都从a出发找）：</h3>
<h3>define('ROOT', __DIR__);</h3>
<h3>我是a2，我这样引入b2：</h3>
<h3>require ROOT . '/two/b2.php';</h3>
EOT;
echo $str;

require ROOT . '/two/b2.php';
