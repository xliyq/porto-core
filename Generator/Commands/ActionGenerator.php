<?php


namespace Porto\Core\Generator\Commands;


use Porto\Core\Generator\GeneratorCommand;
use Porto\Core\Generator\Interfaces\ComponentsGenerator;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ActionGenerator extends GeneratorCommand implements ComponentsGenerator
{

    protected $name = 'generate:action';

    protected $description = '在container中创建一个Action文件';

    protected $fileType = 'Action';

    protected $stubName = 'actions/generic.stub';

    protected $inputs = [
        ['model', null, InputOption::VALUE_OPTIONAL, 'The model this action is for'],
        ['stub', null, InputOption::VALUE_OPTIONAL, 'The stub file  to load for this generator.']
    ];


    public function getUserInputs() {
        $model = $this->checkParameterOrAsk('model', '请输入Action中需要操作的model', $this->containerName);
        $stub = Str::lower($this->checkParameterOrChoice(
            'stub',
            '请选择想要加载的Stub',
            ['Generic', 'GetAll', 'Find', 'Create', 'Update', 'Delete'],
            0
        ));

        $this->stubName = 'actions/' . $stub . '.stub';
        $models = Pluralizer::plural($model);

        $entity = Str::lower($model);
        $entities = Pluralizer::plural($entity);

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
                'class-name' => $this->fileName,
                'model' => $model,
                'models' => $models,
                'entity' => $entity,
                'entities' => $entities,
            ],
            'file-parameters' => [
                'file-name' => $this->fileName,
            ]
        ];
    }

    protected function getDefaultFileName() {
        return 'DefaultAction';
    }
}