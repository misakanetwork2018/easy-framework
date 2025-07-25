<html lang="zh-Hans">
<head>
    <meta charset="UTF-8">
    <title>系统发生错误</title>
    <style>
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
<?php if ($this->show): ?>
<p>
    系统发生错误：<?php echo $this->e->getMessage() ?>
    在文件 <code><?php echo $this->e->getFile() ?>:<?php echo $this->e->getLine() ?></code>
</p>
<pre>
<?php echo $this->e->getTraceAsString(); ?>
</pre>
<?php else: ?>
<p>系统发生错误，请联系网站管理员</p>
<?php endif; ?>
</body>
</html>
