<?php
namespace Alliv\Iamport\Facades;

use Illuminate\Support\Facades\Facade;

class IamportFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'iamport';
    }
}
