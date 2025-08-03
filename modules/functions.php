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
        $useQueries = false;
        $url = '/';

        if (App::config('rewrite')) {
            if ($module != 'Index')
                $url .= $module;

            if ($action != 'index')
                $url = "/$module/$action";
        } else {
            if ($module != 'Index') {
                $useQueries = true;
                $url .= "?m=$module";
            }

            if ($action != 'index') {
                if ($useQueries) {
                    $url .= "&m=$module";
                } else {
                    $useQueries = true;
                    $url .= "?m=$module";
                }
            }
        }

        foreach ($queries as $k => $v) {
            if ($useQueries) {
                $url .= "&$k=$v";
            } else {
                $url .= "?$k=$v";
                $useQueries = true;
            }
        }

        echo $url;
    }
}