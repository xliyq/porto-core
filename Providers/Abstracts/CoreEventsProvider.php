<?php


namespace Porto\Core\Providers\Abstracts;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as LaravelEventServiceProvider;

abstract class CoreEventsProvider extends LaravelEventServiceProvider
{
    public function boot() {
        parent::boot();
    }
}