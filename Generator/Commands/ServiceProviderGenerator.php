<?php


namespace Porto\Core\Generator\Commands;


use Porto\Core\Generator\GeneratorCommand;
use Porto\Core\Generator\Interfaces\ComponentsGenerator;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ServiceProviderGenerator extends GeneratorCommand implements ComponentsGenerator
{

    protected $name = 'generate:service-provider';

    protected $description = '创建ServiceProvider';

    protected $fileType = 'provider';

    protected $stubName = 'providers/mainserviceprovider.stub';

    protected $inputs = [
        ['stub', null, InputOption::VALUE_OPTIONAL, 'The stub file to load for this generator']
    ];

    public function getUserInputs() {
        $stub = Str::lower($this->checkParameterOrChoice(
            'stub',
            '选择要加载的Stub',
            ['Generic', 'MainServiceProvider'],
            1
        ));

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
            ]
        ];
    }

    public function getDefaultFileName() {
        return 'MainServiceProvider';
    }
}