<?php


namespace Porto\Core\Providers\Abstracts;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as LaravelServiceProvider;


abstract class CoreAuthProvider extends LaravelServiceProvider
{
    public function boot() {
        $this->registerPolicies();
    }
}