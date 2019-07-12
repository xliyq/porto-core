<?php


namespace Porto\Core\Generator\Commands;


use Porto\Core\Generator\GeneratorCommand;
use Porto\Core\Generator\Interfaces\ComponentsGenerator;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class TaskGenerator extends GeneratorCommand implements ComponentsGenerator
{

    protected $name = 'generate:task';

    protected $description = '创建一个Task';

    protected $fileType = 'Task';

    protected $stubName = 'tasks/generic.stub';

    public $inputs = [
        ['model', null, InputOption::VALUE_OPTIONAL, 'The model this task is for.'],
        ['stub', null, InputOption::VALUE_OPTIONAL, 'The stub file to load for this generator.'],
    ];


    public function getUserInputs() {
        $model = $this->checkParameterOrAsk('model', '输入Task的model名称。', $this->containerName);
        $stub = $this->checkParameterOrChoice(
            'stub',
            '选择要加载的stub',
            ['Generic', 'GetAll', 'Find', 'Create', 'Update', 'Delete'],
            0
        );

        $this->stubName = 'tasks/' . $stub . '.stub';

        $models = Pluralizer::plural($model);

        return [
            'path-parameters' => [
                'container-name' => $this->containerName
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
                'class-name' => $this->fileName,
                'model' => $model,
                'models' => $models,
            ],
            'file-parameters' => [
                'file-name' => $this->fileName
            ]
        ];
    }

    public function getDefaultFileName() {
        return 'DefaultTask';
    }
}