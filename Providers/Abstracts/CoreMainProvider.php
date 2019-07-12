<?php
/**
 * Created by PhpStorm.
 * User: liyq
 * Date: 2019/5/24
 * Time: 22:04
 */

namespace Porto\Core\Providers\Abstracts;

use Porto\Core\Loaders\AliasesLoaderTrait;
use Porto\Core\Loaders\ProvidersLoaderTrait;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

abstract class CoreMainProvider extends LaravelServiceProvider
{

    use ProvidersLoaderTrait;
    use AliasesLoaderTrait;

    protected $serviceProviders = [];

    public function boot() {
        $this->loadServiceProviders();
        $this->loadAliases();
    }

    public function register() {

    }
}