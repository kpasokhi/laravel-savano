<?php

namespace kpasokhi\savano;

use Illuminate\Support\Facades\Facade;

class SavanoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Savano';
    }
}