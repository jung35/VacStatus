<?php

namespace Steam\Facades;

use Illuminate\Support\Facades\Facade;

class Steam extends Facade {

    protected static function getFacadeAccessor() { return 'steam'; }

}
