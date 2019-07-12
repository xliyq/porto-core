<?php


namespace Porto\Core\Loaders;


use Porto\Core\Support\Facades\Porto;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait ConsoleLoaderTrait
{

    /**
     * 加载 Containers 中的 Commands
     *
     * @param $containerName
     */
    public function loadConsolesFromContainers($containerName) {
        $containerCommandDirectory = base_path('app/Containers/' . $containerName
            . DIRECTORY_SEPARATOR . config('porto.container.console'));

        $this->loadConsoles($containerCommandDirectory);
    }

    /**
     * 加载 Core 和 Ship 中的 Commands
     */
    public function loadConsolesFromCore() {
        $commandsFoldersPaths = [
            base_path('app/Ship/Commands'),
            __DIR__ . '/../Commands',
        ];
        foreach ($commandsFoldersPaths as $commandsDirectory) {
            $this->loadConsoles($commandsDirectory);
        }
    }

    private function loadConsoles($directory) {
        if (File::isDirectory($directory)) {
            $files = File::allFiles($directory);

            foreach ($files as $consoleFile) {

                //不能包含CoreCommands 抽象文件
                if (!($this->isRouteFile($consoleFile)
                    || Str::startsWith($consoleFile->getFilename(), 'Core'))
                ) {
                    $consoleClass = Porto::getClassFullNameFromFile($consoleFile->getPathname());

                    $this->commands([$consoleClass]);
                }
            }
        }
    }

    private function isRouteFile($consoleFile) {
        return $consoleFile->getFilename() === 'Routes,php';
    }

}