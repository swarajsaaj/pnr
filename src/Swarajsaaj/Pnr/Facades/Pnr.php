<?php namespace Swarajsaaj\Pnr\Facades;

use Illuminate\Support\Facades\Facade;

class Pnr extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'pnr'; }

}