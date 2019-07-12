<?php


namespace Porto\Core\Generator\Commands;


use Illuminate\Support\Str;
use Porto\Core\Generator\GeneratorCommand;
use Porto\Core\Generator\Interfaces\ComponentsGenerator;

class ConfigGenerator extends GeneratorCommand implements ComponentsGenerator
{

    protected $name = 'generate:config';

    protected $description = '在Container中创建config';

    protected $fileType = 'config';

    protected $stubName = 'config.stub';


    public function getUserInputs() {
        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
                'class-name' => $this->fileName,
            ],
            'file-parameters' => [
                'file-name' => $this->fileName,
            ],
        ];
    }

    public function getDefaultFileName() {
        return Str::lower($this->containerName);
    }
}