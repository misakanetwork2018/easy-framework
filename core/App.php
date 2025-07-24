<?php

namespace EasyFrameworkCore;

use EasyFrameworkCore\Container\Manager as ContainerMan;
use EasyFrameworkCore\Exception\ClassNotExistException;
use EasyFrameworkCore\Exception\Handler;
use EasyFrameworkCore\Exception\MiddlewareInterceptException;
use EasyFrameworkCore\Helper\Str;
use EasyFrameworkCore\Http\Request;
use EasyFrameworkCore\Http\Response;

class App
{
    private static ContainerMan $container;

    private static array $namespaces = ["EasyFrameworkCore" => "core"];

    private string $moduleNamespace = "App";

    /**
     * 实际上是容器管理器的一个alias
     *
     * @param $class
     * @param mixed ...$arguments
     * @return mixed
     * @throws ClassNotExistException
     */
    public static function make($class, ...$arguments): mixed
    {
        return self::$container->make($class, ...$arguments);
    }

    /**
     * @throws ClassNotExistException
     */
    public function __construct()
    {
        self::$container = $container = new ContainerMan($this);

        $container->make(Handler::class)->register();

        // 初始化数据库组件
        $container->make(DB::class, self::config('db'));
    }

    public static function init(): App
    {
        session_start();

        $helper_filename = APP_ROOT . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "functions.php";
        if (file_exists($helper_filename))
            require_once $helper_filename;

        spl_autoload_register([self::class, 'autoload']);

        date_default_timezone_set('Asia/Shanghai');

        return new static();
    }

    public static function autoload($class): void
    {
        $firstSlashIndex = strpos($class, "\\");
        $namespace = substr($class, 0, $firstSlashIndex);
        $subNamespace = substr($class, $firstSlashIndex + 1);

        if (isset(self::$namespaces[$namespace])) {
            $path = self::$namespaces[$namespace];
            if (!str_starts_with($path, DIRECTORY_SEPARATOR))
                $path = DIRECTORY_SEPARATOR . $path;
            if (!str_ends_with($path, DIRECTORY_SEPARATOR))
                $path = $path . DIRECTORY_SEPARATOR;
            if (file_exists($file = APP_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR .
                str_replace("\\", DIRECTORY_SEPARATOR , $subNamespace) . ".php"))
            require_once $file;
        }
    }

    /**
     * 获取配置
     * @throws ClassNotExistException
     */
    public static function config($key = null, $default = null)
    {
        $config = self::make(Config::class);

        if (is_null($key))
            return $config;

        return $config->get($key, $default);
    }

    /**
     * 绑定命名空间与目录，目录为相对路径，相对于APP_ROOT
     * @param $namespace
     * @param $path
     * @return bool
     */
    public static function bindVendorNamespace($namespace, $path): bool
    {
        if (isset(self::$namespaces[$namespace]))
            return false;

        self::$namespaces[$namespace] = $path;

        return true;
    }

    public function setModuleNamespace($appNamespace): void
    {
        $this->moduleNamespace = $appNamespace;
    }

    private static function show404Page(): void
    {
        http_response_code(404);
        try {
            View::make('404')->render();
        } catch (ClassNotExistException) {
            echo '404';
        }
    }

    public function run(): bool
    {
        try {
            if (self::config('maintain')) {
                print_r('维护中，请稍后再访问');
                return false;
            }

            $request = self::make(Request::class);
        } catch (ClassNotExistException) {
            return false;
        }

        $module = $request->get('m', 'index');
        $action = $request->get('a', 'index');

        $class = "\\" . $this->moduleNamespace . "\\" . Str::toPascal($module);
        $method = Str::toCamel($action);

        if (!class_exists($class)) {
            self::show404Page();
            return false;
        }

        try {
            $obj = new $class();

            if (!method_exists($obj, $method)) {
                self::show404Page();
                return false;
            }

            $resp = $obj->$method();
        } catch (MiddlewareInterceptException $e) {
            $resp = $e->content;
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