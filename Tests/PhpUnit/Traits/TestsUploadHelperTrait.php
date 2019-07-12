<?php


namespace Porto\Core\Tests\PhpUnit\Traits;


use Illuminate\Http\UploadedFile;

trait TestsUploadHelperTrait
{

    public function getTestingFile($fileName, $stubDirPath, $mimeType = 'text/plain', $size = null) {
        $file = $stubDirPath . $fileName;
        return new UploadedFile($file, $fileName, $mimeType, $size, null, true);
    }

    public function getTestingImage($imageName, $stubDirPath, $mimeType = 'image/jpeg', $size = null) {
        return $this->getTestingFile($imageName, $stubDirPath, $mimeType, $size);
    }
}