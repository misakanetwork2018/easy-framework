<?php

namespace Exception;

use ErrorException;
use Exception;

class ErrorHandlerException extends ErrorException
{
    const LOCAL_CODE = [
        E_COMPILE_ERROR => 4001,
        E_COMPILE_WARNING => 4002,
        E_CORE_ERROR => 4003,
        E_CORE_WARNING => 4004,
        E_DEPRECATED => 4005,
        E_ERROR => 4006,
        E_NOTICE => 4007,
        E_PARSE => 4008,
        E_RECOVERABLE_ERROR => 4009,
        E_STRICT => 4010,
        E_USER_DEPRECATED => 4011,
        E_USER_ERROR => 4012,
        E_USER_NOTICE => 4013,
        E_USER_WARNING => 4014,
        E_WARNING => 4015,
        4016 => 4016,
    ];

    const LOCAL_NAME = [
        E_COMPILE_ERROR => 'PHP Compile Error',
        E_COMPILE_WARNING => 'PHP Compile Warning',
        E_CORE_ERROR => 'PHP Core Error',
        E_CORE_WARNING => 'PHP Core Warning',
        E_DEPRECATED => 'PHP Deprecated Warning',
        E_ERROR => 'PHP Fatal Error',
        E_NOTICE => 'PHP Notice',
        E_PARSE => 'PHP Parse Error',
        E_RECOVERABLE_ERROR => 'PHP Recoverable Error',
        E_STRICT => 'PHP Strict Warning',
        E_USER_DEPRECATED => 'PHP User Deprecated Warning',
        E_USER_ERROR => 'PHP User Error',
        E_USER_NOTICE => 'PHP User Notice',
        E_USER_WARNING => 'PHP User Warning',
        E_WARNING => 'PHP Warning',
        4016 => 'Customer`s Error',
    ];

    public function __construct($message = '', $code = 0, $severity = 1, $filename = __FILE__, $line = __LINE__,
                                Exception $previous = null)
    {
        parent::__construct($message, $code, $severity, $filename, $line, $previous);
    }

    /**
     * 是否致命性错误
     *
     * @param $error
     * @return bool
     */
    public static function isFatalError($error)
    {
        $fatalErrors = array(
            E_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_CORE_WARNING,
            E_COMPILE_ERROR,
            E_COMPILE_WARNING
        );
        return isset($error['type']) && in_array($error['type'], $fatalErrors);
    }

    /**
     * 自定义代码
     *
     * @param $code
     * @return string
     */
    public static function getLocalCode($code)
    {
        return self::LOCAL_CODE[$code] ?? self::LOCAL_CODE[4016];
    }

    /**
     * 自定义错误名称
     *
     * @param $code
     * @return mixed
     */
    public static function getName($code)
    {
        return self::LOCAL_NAME[$code] ?? self::LOCAL_NAME[4016];
    }
}