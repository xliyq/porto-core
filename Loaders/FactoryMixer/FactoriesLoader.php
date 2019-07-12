<?php

use Porto\Core\Support\Facades\Porto;
use Illuminate\Support\Facades\File;

$containersFactoriesPath = config('porto.container.factory', 'Data/Factories');


foreach (Porto::getContainersNames() as $containersName) {
    $containersDirectory = base_path('app/Containers/' . $containersName
        . DIRECTORY_SEPARATOR . $containersFactoriesPath . DIRECTORY_SEPARATOR);

    if (File::isDirectory($containersDirectory)) {
        $files = File::allFiles($containersDirectory);

        foreach ($files as $factoryFile) {
            if (File::isFile($factoryFile)) {
                include($factoryFile);
            }
        }
    }

}