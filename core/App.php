<?php

namespace EasyFrameworkCore;

use EasyFrameworkCore\Container\Manager as ContainerMan;
use EasyFrameworkCore\Exception\ClassNotExistException;
use EasyFrameworkCore\Exception\Handler;
use EasyFrameworkCore\Exception\MiddlewareInterceptException;
use EasyFrameworkCore\Exception\MissingModuleNamespaceConfigException;
use EasyFrameworkCore\Helper\Str;
use EasyFrameworkCore\Http\Request;
use EasyFrameworkCore\Http\Response;

class App
{
    private static ContainerMan $container;

    private static array $namespaces = ["EasyFrameworkCore" => "core"];

    private(set) string $module_namespace;

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
    public function __construct($moduleNamespace)
    {
        self::$container = $container = new ContainerMan($this);
        $this->module_namespace = $moduleNamespace;

        $container->make(Handler::class)->register();

        // 初始化数据库组件
        $container->make(DB::class, self::config('db'));
    }

    /**
     * @throws MissingModuleNamespaceConfigException
     * @throws ClassNotExistException
     */
    public static function init($moduleNamespace = "App"): App
    {
        ob_start();
        session_start();

        if (isset(self::$namespaces[$moduleNamespace])) {
            $modules_path = self::$namespaces[$moduleNamespace];
            if (!str_starts_with($modules_path, DIRECTORY_SEPARATOR))
                $modules_path = DIRECTORY_SEPARATOR . $modules_path;
            if (!str_ends_with($modules_path, DIRECTORY_SEPARATOR))
                $modules_path = $modules_path . DIRECTORY_SEPARATOR;
            $helper_filename = APP_ROOT . $modules_path . "functions.php";
            if (file_exists($helper_filename))
                require_once $helper_filename;
        } else {
            throw new MissingModuleNamespaceConfigException();
        }

        spl_autoload_register([self::class, 'autoload']);

        date_default_timezone_set('Asia/Shanghai');

        return new static($moduleNamespace);
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

    /**
     * 优先走Error->show404
     *
     * @return void
     */
    private function show404Page(): void
    {
        http_response_code(404);
        try {
            $class = "\\" . $this->module_namespace . "\\Error";
            $method = Str::toCamel('show404');

            if (method_exists($class, $method)) {
                $obj = new $class();
                $resp = $obj->$method();

                self::render($resp);
            } else {
                $view = View::make('404');
                if ($view->isExist())
                    $view->render();
                else
                    echo '404';
            }
        } catch (ClassNotExistException) {
            echo '404';
        }
    }

    private static function render($resp): void
    {
        if ($resp instanceof Response) {
            $resp->render();
        } elseif ($resp instanceof View) {
            $resp->render();
        } elseif (is_array($resp)) {
            Response::make($resp)->render();
        } else {
            echo $resp;
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

        $class = "\\" . $this->module_namespace . "\\" . Str::toPascal($module);
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

        self::render($resp);

        return true;
    }
}