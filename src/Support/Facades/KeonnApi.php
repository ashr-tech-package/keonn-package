<?php

namespace Ashr\Keonn\Support\Facades;

use Ashr\Keonn\Services\KeonnApi as Service;
use Illuminate\Support\Facades\Facade;

class KeonnApi extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Service::class;
    }
}