<?php


namespace Porto\Core\Loaders;


use Faker\Generator;
use Illuminate\Database\Eloquent\Factory;

trait FactoriesLoaderTrait
{

    public function loadFactoriesFromContainers() {
        $newFactoriesPath = __DIR__ . '/FactoryMixer';

        $this->app->singleton(Factory::class, function ($app) use ($newFactoriesPath) {
            return Factory::construct($app->make(Generator::class), $newFactoriesPath);
        });


    }
}