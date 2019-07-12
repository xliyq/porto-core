<?php


namespace Porto\Core\Generator;


use Porto\Core\Generator\Interfaces\ComponentsGenerator;
use Porto\Core\Generator\Traits\FileSystemTrait;
use Porto\Core\Generator\Traits\FormatterTrait;
use Porto\Core\Generator\Traits\ParserTrait;
use Porto\Core\Generator\Traits\PrinterTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

abstract class GeneratorCommand extends Command
{
    use ParserTrait, FileSystemTrait, PrinterTrait, FormatterTrait;

    CONST ROOT = 'app';

    CONST STUB_PATH = 'Stubs/*';

    CONST CUSTOM_STUB_PATH = 'Generators/CustomStubs/*';

    CONST CONTAINER_DIRECTORY_NAME = 'Containers';

    protected $pathStructure = '{container-name}/';

    protected $nameStructure = '{file-name}';

    protected $fileType;

    protected $filePath;

    protected $containerName;

    protected $fileName;

    protected $userData;

    protected $parsedFileName;

    protected $stubName;

    protected $stubContent;

    protected $renderedStubContent;

    protected $inputs = [];

    /**
     * @var Filesystem
     */
    private $fileSystem;

    private $defaultInputs = [
        ['container', null, InputOption::VALUE_OPTIONAL, 'Container名称'],
        ['file', null, InputOption::VALUE_OPTIONAL, 'file名称'],
    ];

    public function __construct(Filesystem $fileSystem) {
        parent::__construct();
        $this->fileSystem = $fileSystem;
    }

    abstract public function getUserInputs();

    public function handle() {
        $this->validateGenerator($this);

        $this->containerName = ucfirst($this->checkParameterOrAsk('container', '请输入container name'));
        $this->fileName = $this->checkParameterOrAsk('file', '请输入' . $this->fileType . '文件名称', $this->getDefaultFileName());

        $this->containerName = $this->removeSpecialChars($this->containerName);
        $this->fileName = $this->removeSpecialChars($this->fileName);

        $this->printStartedMessage($this->containerName, $this->fileName);

        $this->userData = $this->getUserInputs();

        if ($this->userData === null) {
            return;
        }
        $this->userData = $this->sanitizeUserData($this->userData);

        $this->parsedFileName = $this->parseFileStructure($this->nameStructure, $this->userData['file-parameters']);
        $this->filePath = $this->getFilePath($this->parsePathStructure($this->getPathStructure(), $this->userData['path-parameters']));

        if (!$this->fileSystem->exists($this->filePath)) {
            $this->stubContent = $this->getStubContent();
            $this->renderedStubContent = $this->parseStubContent($this->stubContent, $this->userData['stub-parameters']);

            $this->generateFile($this->filePath, $this->renderedStubContent);
            $this->printFinishedMessage($this->fileType);
        }
        return 0;
    }

    private function validateGenerator($generator) {
        if (!$generator instanceof ComponentsGenerator) {
            throw new \GeneratorErrorException();
        }
    }

    protected function getPathStructure() {
        return $this->pathStructure . Config::get('porto.container.' . Str::lower($this->fileType)) . '/*';
    }

    protected function getFilePath($path) {
        $path = base_path() . '/'
            . str_replace('\\', '/', self::ROOT . '/' . self::CONTAINER_DIRECTORY_NAME . '/' . $path)
            . '.' . $this->getDefaultFileExtension();

        $this->createDirectory($path);

        return $path;
    }

    protected function getStubContent() {
        $path = __DIR__ . '/' . self::STUB_PATH;
        $file = str_replace('*', $this->stubName, $path);
        return $this->fileSystem->get($file);
    }

    protected function getOptions() {
        $arguments = array_merge($this->defaultInputs, $this->inputs);
        return $arguments;
    }

    protected function getInput($arg, $trim = true) {
        return $trim ? $this->trimString($this->argument($arg)) : $this->argument($arg);
    }

    protected function checkParameterOrAsk($param, $question, $default = null) {
        $value = $this->option($param);
        if ($value == null) {
            $value = $this->ask($question, $default);
        }

        return $value;
    }

    protected function checkParameterOrChoice($param, $question, $choices, $default = null) {
        $value = $this->option($param);
        if ($value == null) {
            $value = $this->choice($question, $choices, $default);
        }

        return $value;
    }

    protected function checkParameterOrConfirm($param, $question, $default = null) {
        $value = $this->option($param);
        if ($value == null) {
            $value = $this->confirm($question, $default);
        }

        return $value;
    }

    private function sanitizeUserData($data) {
        if (!array_key_exists('path-parameters', $data)) {
            $data['path-parameters'] = [];
        }

        if (!array_key_exists('stub-parameters', $data)) {
            $data['stub-parameters'] = [];
        }

        if (!array_key_exists('file-parameters', $data)) {
            $data['file-parameters'] = [];
        }

        return $data;
    }

    protected function getDefaultFileName() {
        return 'Default' . Str::ucfirst($this->fileType);
    }

    protected function getDefaultFileExtension() {
        return 'php';
    }


    protected function removeSpecialChars($str) {
        $str = preg_replace('/[^A-Za-z0-9]/', '', $str);
        return $str;
    }

}