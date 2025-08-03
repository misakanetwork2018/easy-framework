<?php

// 这里可以放置全局助手函数

use EasyFrameworkCore\App;

if (!function_exists('route')) {
    /**
     * 获取路由链接
     *
     * @param $module
     * @param $action
     * @param array $queries
     * @return string
     * @throws \EasyFrameworkCore\Exception\ClassNotExistException
     */
    function route($module, $action, array $queries = []): string
    {
        $useQueries = false;

        if (App::config('rewrite')) {
            $url = "/$module/$action";
        } else {
            $url = "?m=$module&a=$action";
            $useQueries = true;
        }

        foreach ($queries as $k => $v) {
            if ($useQueries) {
                $url .= "&$k=$v";
            } else {
                $url .= "?$k=$v";
                $useQueries = true;
            }
        }

        return $url;
    }
}