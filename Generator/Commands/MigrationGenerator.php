<?php


namespace Porto\Core\Generator\Commands;


use Porto\Core\Generator\GeneratorCommand;
use Porto\Core\Generator\Interfaces\ComponentsGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MigrationGenerator extends GeneratorCommand implements ComponentsGenerator
{

    protected $name = 'generate:migration';

    protected $description = '创建在Container中一个空的migration 文件';

    protected $fileType = 'Migration';

    protected $nameStructure = '{date}_{file-name}';

    protected $stubName = 'migration.stub';

    protected $inputs = [
        ['tablename', null, InputOption::VALUE_OPTIONAL, 'The name for the database table']
    ];


    public function getUserInputs() {
        $tableName = Str::lower($this->checkParameterOrAsk('tablename', '请输入数据表名'));

        $exists = false;

        $folder = $this->parsePathStructure($this->pathStructure, ['container-name' => $this->containerName]);
        $folder = $this->getFilePath($folder);
        $folder = rtrim($folder, $this->parsedFileName . '.' . $this->getDefaultFileExtension());

        $migrationName = $this->fileName . '.' . $this->getDefaultFileExtension();

        $files = File::allFiles($folder);
        foreach ($files as $file) {
            if (Str::endsWith($file->getFilename(), $migrationName)) {
                $exists = true;
            }
        }

        if ($exists) {
            // 已经创建过了
            return null;
        }

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
                'class-name' => Str::studly($this->fileName),
                'table-name' => $tableName
            ],
            'file-parameters' => [
                'date' => Carbon::now()->format('Y_m_d_His'),
                'file-name' => $this->fileName
            ]
        ];
    }

    public function getDefaultFileName() {
        return 'create_' . Str::lower($this->containerName) . '_tables';
    }

    public function removeSpecialChars($str) {
        return $str;
    }
}