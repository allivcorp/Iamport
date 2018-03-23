<?php
namespace Alliv\Iamport\Exceptions;

class RequestException extends \Exception
{
    protected $response;

    public function __construct($response)
    {
        parent::__construct($response->message, $response->code);
    }
}
