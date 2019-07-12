<?php


namespace Porto\Core\Generator\Commands;


use Porto\Core\Generator\GeneratorCommand;
use Porto\Core\Generator\Interfaces\ComponentsGenerator;
use Illuminate\Support\Str;

class RepositoryGenerator extends GeneratorCommand implements ComponentsGenerator
{

    protected $name = 'generate:repository';

    protected $description = '创建一个Repository';

    protected $fileType = 'Repository';

    protected $stubName = 'repository.stub';

    protected $input = [];

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
            ]
        ];
    }
}