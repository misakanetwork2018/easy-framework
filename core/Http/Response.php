<?php

namespace EasyFrameworkCore\Http;

class Response
{
    public mixed $content;

    public int $code;

    public array $headers;

    public function __construct($content, $code = 200, $headers = [])
    {
        $this->content = $content;
        $this->code = $code;
        $this->headers = $headers;
    }

    public function header($key, $value): void
    {
        $this->headers[$key] = $value;
    }

    public static function make($content, $code = 200, $headers = []): Response
    {
        return new static($content, $code, $headers);
    }

    /**
     * 最后渲染
     */
    public function render(): void
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