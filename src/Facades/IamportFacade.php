<?php
namespace Alliv\Iamport\Facades;

use Illuminate\Support\Facades\Facade;

class IamportFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'iamport';
    }
}
