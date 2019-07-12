<?php


namespace Porto\Core\Generator\Traits;


trait ParserTrait
{

    public function parsePathStructure($path, $data) {
        $path = str_replace(
            array_map(array($this, 'maskPathVariables'), array_keys($data)),
            array_values($data),
            $path);
        $path = str_replace('*', $this->parsedFileName, $path);
        return $path;
    }

    public function parseFileStructure($fileName, $data) {
        $fileName = str_replace(array_map([$this, 'maskFileVariables'], array_keys($data)), array_values($data), $fileName);
        return $fileName;
    }

    public function parseStubContent($stub, $data) {
        $stub = str_replace(array_map([$this, 'maskStubVariables'], array_keys($data)), array_values($data), $stub);
        return $stub;
    }

    private function maskPathVariables($key) {
        return '{' . $key . '}';
    }

    private function maskFileVariables($key) {
        return '{' . $key . '}';
    }

    private function maskStubVariables($key) {
        return '{{' . $key . '}}';
    }
}