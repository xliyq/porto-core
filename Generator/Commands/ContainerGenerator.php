<?php


namespace Porto\Core\Generator\Commands;


use Porto\Core\Generator\GeneratorCommand;
use Porto\Core\Generator\Interfaces\ComponentsGenerator;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class ContainerGenerator extends GeneratorCommand implements ComponentsGenerator
{

    protected $name = 'generate:container';

    protected $description = '创建一个Container';

    protected $fileType = 'Container';

    protected $stubName = 'composer.stub';

    public function getUserInputs() {

        $model = $this->containerName;
        $models = Pluralizer::plural($model);

        //创建model 和 repository
        $this->printInfoMessage('生成Model 和 Repository');
        $this->call('generate:model', [
            '--container' => $this->containerName,
            '--file' => $model,
            '--repository' => true,
        ]);

        $this->printInfoMessage('生成 Migration');
        $this->call('generate:migration', [
            '--container' => $this->containerName,
            '--file' => 'create_' . Str::lower($this->containerName) . '_tables',
            '--tablename' => $models,
        ]);

        $this->printInfoMessage('生成 ServiceProvider');
        $this->call('generate:service-provider', [
            '--container' => $this->containerName,
            '--file' => 'MainServiceProvider',
            '--stub' => 'mainserviceprovider',
        ]);

        $this->printInfoMessage('生成 Config');
        $this->call('generate:config', [
            '--container' => $this->containerName
        ]);

        $this->printInfoMessage('生成 Action');
        $this->call('generate:action', [
            '--container' => $this->containerName,
            '--model' => $model
        ]);

        $this->printInfoMessage('生成 Task');
        $this->call('generate:task', [
            '--container' => $this->containerName,
            '--model' => $model
        ]);

        $this->printInfoMessage('生成 composer.json');
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
                'file-name' => $this->fileName
            ]
        ];
    }

    public function getDefaultFileName() {
        return 'composer';
    }

    public function getDefaultFileExtension() {
        return 'json';
    }
}