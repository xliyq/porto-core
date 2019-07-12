<?php


namespace Porto\Core\Providers\Abstracts;

use Porto\Core\Loaders\RoutesLoaderTrait;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as LaravelRouteServiceProvide;

abstract class CoreRoutesProvider extends LaravelRouteServiceProvide
{

    use RoutesLoaderTrait;

    protected $namespace;

    public function boot() {
        parent::boot();
    }

    public function map() {
        $this->runRoutesAutoLoader();
    }
}