<?php


namespace Porto\Core\Loaders;


use Porto\Core\Support\Facades\Porto;

trait AutoLoaderTrait
{
    use ConfigsLoaderTrait;
    use MigrationsLoaderTrait;
    use AliasesLoaderTrait;
    use ConsoleLoaderTrait;

    public function runLoaderBoot() {
        $this->loadConfigsFormCore();
        $this->loadConsolesFromCore();
        $this->loadMigrationsFromShip();

        foreach (Porto::getContainersNames() as $containerName) {
            $this->loadConfigFromContainers($containerName);
            $this->loadOnlyMainProvidersFromContainers($containerName);
            $this->loadMigrationsFromContainers($containerName);
            $this->loadConsolesFromContainers($containerName);
        }

        $this->loadFactoriesFromContainers();
    }

}