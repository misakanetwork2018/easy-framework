<?php

// 这里可以放置全局助手函数

use EasyFrameworkCore\App;

if (!function_exists('route')) {
    /**
     * 获取路由链接
     *
     * @param string $module
     * @param string $action
     * @param array $queries
     */
    function route(string $module = 'Index', string $action = 'index', array $queries = []): void
    {
        echo App::generateUrl($module, $action, $queries);
    }
}