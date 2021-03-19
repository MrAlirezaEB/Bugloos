<?php

namespace MrAlirezaEb\BugloosTest\Facades;

use Illuminate\Support\Facades\Facade;

class BLTable extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bltable';
    }
}