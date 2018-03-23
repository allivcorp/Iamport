<?php
namespace Alliv\Iamport;

use Throwable;

class Result
{
    public $success;
    public $data;
    public $error;

    public function __construct($success = false, $data = null, Throwable $e = null)
    {
        $this->success = $success;
        $this->data = $data;
        $this->error = $e === null ? null : [
            'code' => $e->getCode(),
            'message' => $e->getMessage()
        ];
    }
}
