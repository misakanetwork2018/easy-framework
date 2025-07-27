<?php

namespace EasyFrameworkCore;

use EasyFrameworkCore\Exception\RouteNotFoundException;
use EasyFrameworkCore\Helper\Str;
use EasyFrameworkCore\Http\Request;
use ReflectionClass;
use ReflectionMethod;
use ReflectionObject;

class Module
{
    private function doMiddleware(Request $request, ReflectionMethod|ReflectionObject $reflection): mixed
    {
        $middlewares = $reflection->getAttributes(\EasyFrameworkCore\Attribute\Middleware::class);
        $resp = null;
        foreach ($middlewares as $attr) {
            $inst = $attr->newInstance();
            $class_name = $inst->className;
            if (!class_exists($class_name))
                continue;

            $classReflection = new ReflectionClass($class_name);
            if (!$classReflection->implementsInterface(Middleware::class))
                continue;

            $m_obj = new $class_name();

            $resp = $m_obj->handle($request);

            if ($resp !== null) {
                return $resp;
            }
        }

        return $resp;
    }

    /**
     * 禁止从外部直接创建，只能通过route方法
     */
    private function __construct()
    {

    }

    /**
     * 路由到Module->Action
     *
     * @param string $ns
     * @param bool $returnFalseWhenRouteFailed
     * @return mixed
     * @throws \EasyFrameworkCore\Exception\RouteNotFoundException
     * @throws \ReflectionException
     * @throws \EasyFrameworkCore\Exception\ClassNotExistException
     */
    public static function route(string $ns, bool $returnFalseWhenRouteFailed = false): mixed
    {
        $request = App::make(Request::class);
        $module = $request->get('m', 'index');
        $action = $request->get('a', 'index');

        $class = "\\" . $ns . "\\" . Str::toPascal($module);

        if (!class_exists($class)) {
            if ($returnFalseWhenRouteFailed)
                return false;
            else
                throw new RouteNotFoundException();
        }

        $obj = new $class(); /* @var \EasyFrameworkCore\Module $obj */

        $method = Str::toCamel($action);

        if (!method_exists($obj, $method)) {
            if ($returnFalseWhenRouteFailed)
                return false;
            else
                throw new RouteNotFoundException();
        }

        // 处理中间件
        $reflection = new ReflectionObject($obj);

        $resp = $obj->doMiddleware($request, $reflection);

        if ($resp !== null)
            return $resp;

        $resp = $obj->doMiddleware($request, $reflection->getMethod($method));

        if ($resp !== null)
            return $resp;

        // 依赖注入
        App::getContainer()->injectProc($obj);

        // 若中间件未中断处理，则正常进入指定方法
        return $obj->$method();
    }
}