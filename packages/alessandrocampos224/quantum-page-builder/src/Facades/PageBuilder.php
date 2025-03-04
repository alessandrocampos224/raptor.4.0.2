<?php

namespace Quantum\Page\Builder\PageBuilder\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Quantum\Page\Builder\PageBuilder\PageBuilder
 */
class PageBuilder extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Quantum\Page\Builder\PageBuilder\PageBuilder::class;
    }
}
