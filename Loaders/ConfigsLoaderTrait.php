<?php


namespace Porto\Core\Loaders;


use Illuminate\Support\Facades\File;

/**
 * Trait ConfigsLoaderTrait
 *
 * @package Porto\Core\Loaders
 *
 * author liyq <2847895875@qq.com>
 */
trait ConfigsLoaderTrait
{
    /**
     * 加载Containers中的配置文件
     *
     * @param $containerName
     */
    public function loadConfigFromContainers($containerName) {
        $containerConfigsDirectory = base_path('app/Containers/' . $containerName . DIRECTORY_SEPARATOR . config('porto.container.config'));
        $this->loadConfigs($containerConfigsDirectory);
    }

    /**
     * 加载 Core 中的配置，将配置文件发布到config
     */
    public function loadConfigsFormCore() {
        // 注意顺序，前面的配置覆盖后面的
        $configsDirectory = [
            app_path('Ship/Configs'),
            __DIR__ . '/../Configs',
        ];

        foreach ($configsDirectory as $directory) {
            $this->loadConfigs($directory);
        }

    }

    /**
     * 加载配置文件进行合并
     *
     * @param $directory
     */
    private function loadConfigs($directory) {
        if (File::isDirectory($directory)) {
            $files = File::allFiles($directory);

            foreach ($files as $file) {
                if (File::isFile($file)) {
                    $fileNameOnly = str_replace('.php', '', $file->getFilename());

                    $this->mergeConfigFrom($file->getPathname(), $fileNameOnly);
                }
            }
        }
    }


}