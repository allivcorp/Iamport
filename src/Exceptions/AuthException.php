<?php
namespace Alliv\Iamport\Exceptions;

use Throwable;

class AuthException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
