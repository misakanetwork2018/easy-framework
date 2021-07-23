<html>
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
<p>系统发生错误<?php if ($this->show): ?>：<?php echo $this->e->getMessage() ?></p>
<pre>
<?php debug_print_backtrace(); ?>
</pre>
<?php else: ?>
    </p>
<?php endif; ?>
</body>
</html>
