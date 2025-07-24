<?php

namespace EasyFrameworkCore\Exception;

use Throwable;
use EasyFrameworkCore\View;
use EasyFrameworkCore\App;

class Handler
{
    private string $view = 'error';

    public int $memoryReserveSize = 262144;//备用内存大小

    private mixed $_memoryReserve;//备用内存

    public function register(): void
    {
        ini_set('display_errors', 0);
        set_exception_handler(array($this, 'handleException'));//截获未捕获的异常
        set_error_handler(array($this, 'handleError'));//截获各种错误 此处切不可掉换位置
        //留下备用内存 供后面拦截致命错误使用
        $this->memoryReserveSize > 0 && $this->_memoryReserve = str_repeat('x', $this->memoryReserveSize);
        register_shutdown_function(array($this, 'handleFatalError'));//截获致命性错误
    }

    public function unregister(): void
    {
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * @noreturn
     * @noinspection PhpNoReturnAttributeCanBeAddedInspection
     */
    public function handleException($exception): void
    {
        $this->unregister();

        $this->render($exception);

        $this->report($exception);

        exit(1);
    }

    public function handleFatalError(): void
    {
        unset($this->_memoryReserve);//释放内存供下面处理程序使用
        $error = error_get_last();//最后一条错误信息
        if (ErrorHandlerException::isFatalError($error)) {//如果是致命错误进行处理
            $exception = new ErrorHandlerException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
            $this->report($exception);
            exit(1);
        }
    }

    /**
     * @throws ErrorHandlerException
     */
    public function handleError($code, $message, $file, $line): bool
    {
        //该处思想是将错误变成异常抛出 统一交给异常处理函数进行处理
        if ((error_reporting() & $code) && !in_array($code,
                [E_NOTICE, E_WARNING, E_USER_NOTICE, E_USER_WARNING, E_DEPRECATED])) {
            //此处只记录严重的错误 对于各种WARNING NOTICE不作处理
            $exception = new ErrorHandlerException($message, $code, $code, $file, $line);
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_shift($trace);//trace的第一个元素为当前对象 移除
            foreach ($trace as $frame) {
                if ($frame['function'] == '__toString') {//如果错误出现在 __toString 方法中 不抛出任何异常
                    $this->handleException($exception);
                    exit(1);
                }
            }
            throw $exception;
        }
        return false;
    }

    /**
     * 渲染错误页面
     *
     * @param $exception
     */
    public function render($exception): void
    {
        try {
            if (defined('CLI_MODE') && constant('CLI_MODE')) {
                echo "***System error***\n";
                echo $exception->getMessage() . "\n\n";
                debug_print_backtrace();
            } else {
                ob_end_clean();
                http_response_code(500);
                $view = View::make($this->view, ['e' => $exception, 'show' => App::config('debug')]);
                if ($view->isExist())
                    $view->render();
                else
                    echo "Error, and no default error page.";
            }
        } catch (ClassNotExistException) {
            // 万一连这个都坏了，为了避免死循环，将会直接输出以下文本
            echo "App broken, you should check the completeness of the system.";
        }
    }

    /**
     * 报告错误
     *
     * @param Throwable $exception
     */
    public function report(Throwable $exception): void
    {
        $log_dir = APP_ROOT . '/runtime/logs';
        if (!is_dir($log_dir))
            mkdir($log_dir, 0755, true);

        $filename = $log_dir . '/' . date('Ymd') . '.log';
        $handle = fopen($filename, "a+");
        fwrite($handle, date('Y-m-d H:i:s') . " system error: {$exception->getMessage()} at ".
            "{$exception->getFile()}:{$exception->getLine()}\n");
        fwrite($handle, "{$exception->getTraceAsString()}\n");
        fclose($handle);
    }
}