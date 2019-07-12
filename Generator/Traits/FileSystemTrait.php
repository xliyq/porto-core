<?php


namespace Porto\Core\Generator\Traits;


trait FileSystemTrait
{
    protected function alreadyExists($path) {
        return $this->fileSystem->exists($path);
    }

    public function generateFile($filePath, $stubContent) {
        return $this->fileSystem->put($filePath, $stubContent);
    }

    public function createDirectory($path) {
        if ($this->alreadyExists($path)) {
            $this->printErrorMessage($this->fileType . 'already exists');
            return;
        }
        try {
            if (!$this->fileSystem->isDirectory(dirname($path))) {
                $this->fileSystem->makeDirectory(dirname($path), 0777, true, true);
            }
        } catch (\Exception $e) {
            $this->printErrorMessage('Could not create ' . $path);
        }
    }
}