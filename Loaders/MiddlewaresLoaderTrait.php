<?php


namespace Porto\Core\Loaders;

use Illuminate\Contracts\Http\Kernel;

/**
 * Class MiddlewaresLoaderTrait
 *
 * @package Porto\Core\Loaders
 *
 * author liyq <2847895875@qq.com>
 */
trait MiddlewaresLoaderTrait
{
    public function loadMiddlewares() {
        $this->registerMiddleware($this->middlewares);
        $this->registerMiddlewareGroups($this->middlewareGroups);
        $this->registerRouteMiddleware($this->routeMiddleware);
    }

    /**
     * 注册全局中间件
     *
     * @param array $middlewares
     */
    private function registerMiddleware(array $middlewares = []) {
        $httpKernel = $this->app->make(Kernel::class);
        foreach ($middlewares as $middleware) {
            $httpKernel->prependMiddleware($middleware);
        }

    }

    /**
     * 注册中间件组
     *
     * @param array $middlewareGroups
     */
    private function registerMiddlewareGroups(array $middlewareGroups = []) {
        foreach ($middlewareGroups as $key => $group) {
            if (!is_array($group)) {
                $this->app['router']->pushMiddlewareToGroup($key, $group);
            } else {
                foreach ($group as $item) {
                    $this->app['router']->pushMiddlewareToGroup($key, $item);
                }
            }
        }
    }

    /**
     * 注册路由中间件
     *
     * @param array $routeMiddleware
     */
    private function registerRouteMiddleware(array $routeMiddleware = []) {
        foreach ($routeMiddleware as $key => $middleware) {
            $this->app['router']->aliasMiddleware($key, $middleware);
        }
    }


}