<?php
namespace KR04\Exceptions;

use KR04\System\Colorize;

class InvalidClassException extends \Exception
{

    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString()
    {
        return Colorize::color('[bg-white][cyan]' . __CLASS__ . "\n"
            . '[bg-red][white][' . $this->code . ']: ' . $this->message . '[/]');
    }
}
