<?php


namespace Porto\Core\Providers\Abstracts;


use Porto\Core\Loaders\MiddlewaresLoaderTrait;

abstract class CoreMiddlewareProvider extends CoreMainProvider
{
    use MiddlewaresLoaderTrait;

    protected $middlewares = [];

    protected $middlewareGroups = [];

    protected $routeMiddleware = [];

    public function boot() {
        $this->loadMiddlewares();
    }

    public function register() {

    }
}