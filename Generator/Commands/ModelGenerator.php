<?php


namespace Porto\Core\Generator\Commands;


use Porto\Core\Generator\GeneratorCommand;
use Porto\Core\Generator\Interfaces\ComponentsGenerator;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ModelGenerator extends GeneratorCommand implements ComponentsGenerator
{

    protected $name = 'generate:model';

    protected $description = '创建一个Model类文件';

    protected $fileType = 'Model';

    protected $stubName = 'model.stub';

    protected $inputs = [
        ['repository', null, InputOption::VALUE_OPTIONAL, '是否为此model生成相应的Repository?'],
    ];

    public function getUserInputs() {
        $repository = $this->checkParameterOrConfirm('repository', '是否为此model生成相应的Repository?', true);
        if ($repository) {
            $status = $this->call('generate:repository', [
                '--container' => $this->containerName,
                '--file' => $this->fileName . 'Repository'
            ]);
            if ($status == 0) {
                $this->printInfoMessage('repository 生成成功');
            } else {
                $this->printErrorMessage('repository 生成失败');
            }
        }

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
                'class-name' => $this->fileName,
                'resource-key' => strtolower(Pluralizer::plural($this->fileName)),
            ],
            'file-parameters' => [
                'file-name' => $this->fileName,
            ]
        ];
    }
}