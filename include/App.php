<?php

use Container\Manager as ContainerMan;
use Exception\Handler;
use Exception\MiddlewareInterceptException;
use Helper\Str;
use Http\Request;
use Http\Response;

class App
{
    /**
     * @var ContainerMan
     */
    private static $container;

    /**
     * 实际上是容器管理器的一个alias
     *
     * @param $class
     * @param mixed ...$arguments
     * @return mixed
     * @throws \Exception\ClassNotExistException
     */
    public static function make($class, ...$arguments)
    {
        return self::$container->make($class, ...$arguments);
    }

    public function __construct()
    {
        self::$container = $container = new ContainerMan($this);

        $container->make(Handler::class)->register();

        // 初始化数据库组件
        $container->make(DB::class, self::config('db'));
    }

    public static function init()
    {
        session_start();

        require APP_ROOT . "/include/Helper/functions.php";

        spl_autoload_register([self::class, 'autoload']);

        date_default_timezone_set('Asia/Shanghai');

        return new static();
    }

    public static function autoload($class)
    {
        if (file_exists($file = APP_ROOT . "/include/" .
            str_replace("\\", DIRECTORY_SEPARATOR , $class) . ".php"))

        require_once $file;
    }

    public static function config($key = null, $default = null)
    {
        $config = self::make(Config::class);

        if (is_null($key))
            return $config;

        return $config->get($key, $default);
    }

    public function run()
    {
        if (self::config('maintain'))
            return false || print_r('维护中，请稍后再访问');

        $request = self::make(Request::class);

        $module = $request->get('m', 'index');
        $action = $request->get('a', 'index');

        $class = '\Module\\' . Str::toPascal($module);
        $method = Str::toCamel($action);

        if (!class_exists($class)) {
            http_response_code(404);
            View::make('404')->render();
            return false;
        }

        try {
            $obj = new $class();

            if (!method_exists($obj, $method)) {
                http_response_code(404);
                View::make('404')->render();
                return false;
            }

            $resp = $obj->$method();
        } catch (MiddlewareInterceptException $e) {
            $resp = $e->getContent();
        }

        if ($resp instanceof Response) {
            $resp->render();
        } elseif ($resp instanceof View) {
            $resp->render();
        } elseif (is_array($resp)) {
            Response::make($resp)->render();
        } else {
            echo $resp;
        }

        return true;
    }
}