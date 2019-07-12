<?php


namespace Porto\Core\Generator;


use Porto\Core\Generator\Commands\ConfigGenerator;
use Porto\Core\Generator\Commands\ActionGenerator;
use Porto\Core\Generator\Commands\ContainerGenerator;
use Porto\Core\Generator\Commands\MigrationGenerator;
use Porto\Core\Generator\Commands\ModelGenerator;
use Porto\Core\Generator\Commands\RepositoryGenerator;
use Porto\Core\Generator\Commands\ServiceProviderGenerator;
use Porto\Core\Generator\Commands\TaskGenerator;
use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    public function boot() {
    }

    public function register() {
        $this->registerGenerators([
            ActionGenerator::class,
            ConfigGenerator::class,
            ContainerGenerator::class,
            MigrationGenerator::class,
            ModelGenerator::class,
            RepositoryGenerator::class,
            ServiceProviderGenerator::class,
            TaskGenerator::class,
        ]);
    }

    public function registerGenerators(array $classes) {
        foreach ($classes as $class) {
            $lowerClass = strtolower($class);

            $this->app->singleton("command.porto.$lowerClass", function ($app) use ($class) {
                return $app[$class];
            });

            $this->commands("command.porto.$lowerClass");
        }
    }
}