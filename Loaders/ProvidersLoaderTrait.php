<?php

namespace Porto\Core\Loaders;

use Porto\Core\Support\Facades\Porto;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Trait ProvidersLoaderTrait
 *
 * @package Porto\Core\Loaders
 *
 * @author  liyq <2847895875@qq.com>
 */
trait ProvidersLoaderTrait
{

    /**
     *  仅从容器中加载MainProviders。
     *  所有Service Providers（在MainProviders内注册）将从MainProvider的父服务器上的`boot（）`函数加载
     *
     * @param string $containerName
     */
    public function loadOnlyMainProvidersFromContainers(string $containerName) {
        $containerProvidersDirectory = base_path("app/Containers/" . $containerName
            . DIRECTORY_SEPARATOR . config('porto.container.provider'));

        $this->loadProviders($containerProvidersDirectory);
    }

    /**
     * 加载指定目录中的Provider
     *
     * @param string $directory
     */
    public function loadProviders(string $directory) {
        $mainServiceProviderNameStartWith = 'Main';

        if (File::isDirectory($directory)) {
            $files = File::allFiles($directory);

            foreach ($files as $file) {

                if (File::isFile($file)) {

                    // 检查是否为MainProvider
                    if (Str::startsWith($file->getFilename(), $mainServiceProviderNameStartWith)) {
                        $serviceProviderClass = Porto::getClassFullNameFromFile($file->getPathname());
                        $this->loadProvider($serviceProviderClass);
                    }
                }
            }
        }
    }

    public function loadProvider(string $providerFullName) {
        App::register($providerFullName);
    }

    /**
     * 在MainProvider上加载所有serviceProviders。
     */
    public function loadServiceProviders() {
        foreach ($this->serviceProviders as $provider) {
            $this->loadProvider($provider);
        }
    }
}