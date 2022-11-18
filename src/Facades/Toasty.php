<?php

namespace WithCandour\StatamicToasty\Facades;

use Illuminate\Support\Facades\Facade;


/**
 * @method static \WithCandour\StatamicToasty\Contracts\Toasty listen(string $event)
 *
 * @see \WithCandour\Contracts\StatamicToasty\Toasty
 */
class Toasty extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \WithCandour\StatamicToasty\Contracts\Toasty::class;
    }
}
