<?php
/**
 * Created by PhpStorm.
 * User: liyq
 * Date: 2019/5/24
 * Time: 16:58
 */

namespace Porto\Core\Exceptions\Code;


use Illuminate\Support\Facades\File;
use Porto\Core\Support\Facades\Porto;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class ErrorCodeManager
{

    public static function getCode(array $error) {
        return self::getKeyFromArray($error, 'code', 0);
    }

    private static function getKeyFromArray(array $error, $key, $default) {
        return isset($error[$key]) ? $error[$key] : $default;
    }

    public static function getCodeTables() {
        $directory = app_path('Ship/Exceptions/Codes/');
        $codeTables = [];
        if (File::isDirectory($directory)) {
            $files = File::allFiles($directory);
            foreach ($files as $file) {
                if (File::isFile($files)) {
                    $codeTables[] = Porto::getClassFullNameFromFile($file->getPathname());
                }
            }
        }
//        $codeTables = [
//            ApplicationErrorCodesTable::class,
//            CustomErrorCodesTable::class
//        ];
        return $codeTables;
    }

    public static function getErrorsForCodeTable($codeTable) {
        try {
            $class = new $codeTable;
        } catch (\Exception $exception) {
            throw new InternalErrorException();
        }

        $reflectionClass = new \ReflectionClass($class);
        $constants = $reflectionClass->getConstants();
        return $constants;
    }

    public static function getErrorsForCodeTables() {
        $tables = self::getCodeTables();

        $result = [];

        foreach ($tables as $table) {
            $errors = self::getErrorsForCodeTable($table);
            $result = array_merge($result, $errors);
        }
        return $result;
    }
}