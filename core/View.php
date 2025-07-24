<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

namespace EasyFrameworkCore;

use EasyFrameworkCore\Http\Request;

/**
 * 视图姬
 * PHP原生，无需处理
 * 这玩意不是单例的，别把它怼进去了，直接用make方法快速生成
 */
class View
{
    /**
     * 视图文件
     */
    private string $view;

    /**
     * 布局文件
     */
    private string|null $layout = null;

    private Config $config;

    private array $data = [];

    private Request $request;

    /**
     * @throws \EasyFrameworkCore\Exception\ClassNotExistException
     */
    public function __construct($view)
    {
        $this->view = $view;

        $this->config = App::config();
    }

    /**
     * @param string $view
     * @return View
     */
    public function setView(string $view): View
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @param Request $request
     * @return View
     */
    public function setRequest(Request $request): View
    {
        $this->request = $request;

        return $this;
    }

    /**
     * 生成View对象
     *
     * @throws \EasyFrameworkCore\Exception\ClassNotExistException
     */
    public static function make($view, $data = []): View
    {
        return new static($view)->with($data)->setRequest(App::make(Request::class));
    }

    public function render(): void
    {
        if (empty($this->layout))
            new Render($this)();
        else
            new LayoutRender($this)();
    }

    public function isExist(): bool
    {
        return file_exists($this->getViewPath());
    }

    public function getViewPath(): string
    {
        if (empty($this->layout)) {
            $file_path = str_replace("." , DIRECTORY_SEPARATOR, $this->view);
        } else {
            $file_path = str_replace("." , DIRECTORY_SEPARATOR, $this->layout);
        }

        return APP_ROOT . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . $file_path . ".php";
    }

    public function getBodyViewPath(): string
    {
        $file_path = str_replace("." , DIRECTORY_SEPARATOR, $this->view);

        return APP_ROOT . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . $file_path . ".php";
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function __call($name, $arguments)
    {
        echo $this->data[$name] ?? null;
    }

    public function with($key, $value = null): View
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }
}