<?php

namespace Dearpos\Customer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dearpos\Customer\Customer
 */
class Customer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Dearpos\Customer\Customer::class;
    }
}
