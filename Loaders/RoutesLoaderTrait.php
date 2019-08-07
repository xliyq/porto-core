<?php


namespace Porto\Core\Loaders;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Porto\Core\Support\Facades\Porto;
use Symfony\Component\Finder\SplFileInfo;

trait RoutesLoaderTrait
{
    /**
     * 加载所有container的路由
     */
    public function runRoutesAutoLoader() {
        $containersPaths = Porto::getContainersPaths();
        $containersNamespace = Porto::getContainersNamespace();

        foreach ($containersPaths as $containerPath) {
            $this->loadContainerRoutes($containerPath, $containersNamespace, "API");
//            $this->loadContainerRoutes($containerPath, $containersNamespace, "WEB");
        }
    }

    /**
     * 加载指定UI的路由
     *
     * @param $path
     * @param $namespace
     * @param $ui
     */
    public function loadContainerRoutes($path, $namespace, $ui) {
        //路由目录
        $apiRoutesPath = $path . DIRECTORY_SEPARATOR . str_replace('{ui}', $ui, config('porto.container.route'));
        //控制器命名空间
        $controllerNamespace = $namespace . '\\Containers\\' . basename($path) . "\\UI\\{$ui}\\Controllers";

        //遍历路由目录
        if (File::isDirectory($apiRoutesPath)) {
            $files = File::allFiles($apiRoutesPath);
            $files = Arr::sort($files, function ($file) {
                return $file->getFilename();
            });
            foreach ($files as $file) {
                if ($ui == 'API') {
                    $this->loadApiRoute($file, $controllerNamespace);
                } else if ($ui == 'WEB') {
                }
            }
        }
    }

    /**
     * 加载Api路由定义文件
     * app/Containers/ContainerName/UI/API/Routes
     *
     * @param $file
     * @param $controllerNamespace
     */
    private function loadApiRoute($file, $controllerNamespace) {

//        $prefix = is_string($file) ? $file : $this->getApiVersionPrefix($file);
        $group = $this->getRouteGroup($file, $controllerNamespace);
        Route::prefix($group['prefix'])
            ->middleware($group['middleware'])
            ->namespace($group['namespace'])
            ->group($file);
    }

    /**
     *
     * @param      $endpointFileOrPrefixString
     * @param null $controllerNamespace
     *
     * @return array
     */
    public function getRouteGroup($endpointFileOrPrefixString, $controllerNamespace = null) {
        return [
            'namespace'  => $controllerNamespace,
            'middleware' => $this->getMiddlewares(),
            'prefix'     => $this->getApiVersionPrefix($endpointFileOrPrefixString),
        ];
    }

//    /**
//     * @deprecated
//     * @return mixed
//     */
//    private function getApiUrl() {
//        return Config::get('porto.api.url');
//    }

    /**
     * 获取接口前缀
     *
     * @param string|File $file
     *
     * @return string
     */
    private function getApiVersionPrefix($file) {
        $versionPrefix = '';
        // 检查是否开启版本前缀
        if (Config::get('porto.api.enable_version_prefix')) {
            $versionPrefix = is_file($file) ? $this->getRouteFileVersionFromFileName($file) : $file;
        }
        return rtrim(Config::get('porto.api.prefix'), '/') . '/' . $versionPrefix;
    }

    /**
     * 获取中间件
     *
     * @return array
     */
    private function getMiddlewares() {
        return array_filter([
            'api',
            $this->getRateLimitMiddleware(),
        ]);
    }

    /**
     * 获取频率中间件
     *
     * @return string|null
     */
    private function getRateLimitMiddleware() {
        $rateLimitMiddleware = null;
        if (config('porto.api.throttle.enabled')) {
            $throttle = config('porto.api.throttle');
            $rateLimitMiddleware = 'throttle:' . $throttle['attempts'] . ',' . $throttle['expires'];
        }
        return $rateLimitMiddleware;
    }

    /**
     * 根据路由文件获取版本 eg:route.v1.php
     *
     * @param $file
     *
     * @return mixed
     */
    private function getRouteFileVersionFromFileName($file) {
        // 获取不含后缀的文件名
        $fileNameWithoutExtension = $this->getRouteFileNameWithoutExtension($file);
        // 以.分割成数组
        $fileNameWithoutExtensionExploded = explode('.', $fileNameWithoutExtension);

        // 返回文件中的版本号
        $apiVersion = end($fileNameWithoutExtensionExploded);

        return $apiVersion;
    }

    /**
     * 获取文件名，不包含后缀
     *
     * @param SplFileInfo $file
     *
     * @return mixed
     */
    private function getRouteFileNameWithoutExtension(SplFileInfo $file) {
        $fileInfo = pathinfo($file->getFilename());
        return $fileInfo['filename'];
    }

}