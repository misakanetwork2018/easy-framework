<?php

use Http\Request;

/**
 * 视图姬
 * PHP原生，无需处理
 * 这玩意不是单例的，别把它怼进去了，直接用make方法快速生成
 */
class View
{
    /**
     * 视图文件
     *
     * @var string
     */
    private $view;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var Request
     */
    private $request;

    public function __construct($view)
    {
        $this->view = $view;

        $this->config = App::config();
    }

    /**
     * @param string $view
     * @return View
     */
    public function setView(string $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @param Request $request
     * @return View
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    public static function make($view, $data = [])
    {
        return (new static($view))->with($data)->setRequest(App::make(Request::class));
    }

    public function render()
    {
        include APP_ROOT . "/views/" . $this->getViewPath() . ".php";
    }

    public function isExist()
    {
        return file_exists(APP_ROOT . "/views/" . $this->getViewPath() . ".php");
    }

    private function getViewPath()
    {
        return str_replace("." , DIRECTORY_SEPARATOR, $this->view);
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function __call($name, $arguments)
    {
        echo $this->data[$name] ?? null;
    }

    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }
}