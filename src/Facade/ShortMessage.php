<?php

namespace Alish\ShortMessage\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static getDefaultDriver()
 * @method static driver(string $driver = null)
 */
class ShortMessage extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'short-message';
    }
}
