<?php


namespace Porto\Core\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Api
 *
 * @package Porto\Core\Support\Facades
 *
 * @method static string getClassFullNameFromFile($filePathName)
 * @method static array getContainersNames()
 * @method static array getContainersPaths()
 * @method static string getContainersNamespace()
 * @method static void verifyContainerExist($containerName)
 * @method static void verifyClassExist($className)
 * @method static mixed call($class, $runMethodArguments = [], $extraMethodsToCall = [])
 * @method static string buildClassFullName($containerName, $className)
 *
 * author liyq <2847895875@qq.com>
 */
class Porto extends Facade
{

    protected static function getFacadeAccessor() {

        return 'Porto';
    }
}