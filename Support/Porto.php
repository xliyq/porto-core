<?php


namespace Porto\Core\Support;


use Porto\Core\Exceptions\ClassDoesNotExistException;
use Porto\Core\Exceptions\MissingContainerException;
use Porto\Core\Traits\CallableTrait;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class Porto
{

    use CallableTrait;

    /**
     * 获取Container的Namespace
     *
     * @return string
     */
    public function getContainersNamespace() {
        return Config::get('porto.containers.namespace');
    }

    /**
     * 获取所有Container的名称
     *
     * @return array
     */
    public function getContainersNames() {
        $containersNames = [];

        foreach ($this->getContainersPaths() as $containersPath) {
            $containersNames[] = basename($containersPath);
        }
        return $containersNames;
    }

    /**
     * 获取Containers下的目录
     *
     * @return array
     */
    public function getContainersPaths() {
        return File::directories(app_path('Containers'));
    }

    /**
     * 根据文件获取类完整的名称（名称\命名空间）
     *
     * @param $filePathName
     *
     * @return string
     */
    public function getClassFullNameFromFile($filePathName) {
        return $this->getClassNamespaceFromFile($filePathName) . '\\' . $this->getClassNameFromFile($filePathName);
    }

    /**
     * 使用token从源代码中获取namespace
     *
     * @param $filePathName
     *
     * @return string|null
     */
    protected function getClassNamespaceFromFile($filePathName) {

        $src = file_get_contents($filePathName);
        // 解析php源码  https://www.php.net/manual/zh/function.token-get-all.php
        $tokens = token_get_all($src);
        $count = count($tokens);
        $i = 0;
        $namespace = '';
        $namespace_ok = false;
        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($tokens) && $token[0] === T_NAMESPACE) {
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespace_ok = true;
                        $namespace = trim($namespace);
                        break;
                    }
                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[1];
                }
                break;
            }
            $i++;
        }
        if (!$namespace_ok) {
            return null;
        } else {
            return $namespace;
        }

    }

    /**
     * 使用token从源代码中获取class name
     *
     * @param $filePathName
     *
     * @return mixed
     */
    protected function getClassNameFromFile($filePathName) {
        $source = file_get_contents($filePathName);

        $classes = array();
        // 解析php源码  https://www.php.net/manual/zh/function.token-get-all.php
        $tokens = token_get_all($source);
        $count = count($tokens);

        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] === T_CLASS
                && $tokens[$i - 1][0] === T_WHITESPACE
                && $tokens[$i][0] === T_STRING
            ) {
                $class_name = $tokens[$i][1];
                $classes[] = $class_name;
            }
        }

        return $classes[0];
    }

    public function buildClassFullName($containerName, $className) {
        return 'App\\Containers\\' . $containerName . '\\' . $this->getClassType($className) . 's\\' . $className;
    }

    public function getClassType($className) {
        $array = preg_split('/(?=[A-Z])/', $className);
        return end($array);
    }

    /**
     * 验证Container是否存在
     *
     * @param string $containerName
     */
    public function verifyContainerExist(string $containerName) {
        if (!is_dir(app_path('Containers/' . $containerName))) {
            throw  new MissingContainerException("Container ($containerName) is not exist");
        }
    }

    /**
     * 验证类是否存在
     *
     * @param string $className
     */
    public function verifyClassExist(string $className) {
        if (!class_exists($className)) {
            throw new ClassDoesNotExistException("Class ($className) is not exist");
        }
    }
}