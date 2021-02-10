<?php

namespace Http;

class Response
{
    /**
     * @var mixed
     */
    private $content;

    /**
     * @var int
     */
    private $code;

    /**
     * @var array
     */
    private $headers;

    public function __construct($content, $code = 200, $headers = [])
    {
        $this->content = $content;
        $this->code = $code;
        $this->headers = $headers;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code)
    {
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function header($key, $value)
    {
        $this->headers[$key] = $value;
    }

    public static function make($content, $code = 200, $headers = [])
    {
        return new static($content, $code, $headers);
    }

    /**
     * 最后渲染
     */
    public function render()
    {
        foreach ($this->headers as $key => $val) {
            header("$key: $val");
        }

        http_response_code($this->code);

        if (is_array($this->content)) { // 自动处理数组
            header('content-type:application/json;charset=utf8');
            echo json_encode($this->content);
        } else { // 其他情况下依靠__toString
            echo $this->content;
        }
    }
}