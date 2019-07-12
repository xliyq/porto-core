<?php


namespace Porto\Core\Loaders;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

trait MigrationsLoaderTrait
{

    /**
     * 加载 Containers 中的迁移文件
     *
     * @param $containerName
     */
    public function loadMigrationsFromContainers($containerName) {
        $containerMigrationDirectory = base_path('app/Containers/' . $containerName
            . DIRECTORY_SEPARATOR . Config::get('porto.container.migration'));

        $this->loadMigrations($containerMigrationDirectory);
    }

    public function loadMigrationsFromShip() {
        $migrationDirectory = base_path('app/Ship/Migrations');
        $this->loadMigrations($migrationDirectory);
    }

    /**
     * 加载指定目录的迁移文件
     *
     * @param $directory
     */
    public function loadMigrations($directory) {
        if (File::isDirectory($directory)) {
            $this->loadMigrationsFrom($directory);
        }
    }

}