<?php


namespace Porto\Core\Providers;


use Barryvdh\Cors\ServiceProvider as CorsServiceProvider;
use Barryvdh\Debugbar\Facade;
use Barryvdh\Debugbar\ServiceProvider;
use Hashids\Hashids;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Laravel\Tinker\TinkerServiceProvider;
use Optimus\Heimdal\Provider\LaravelServiceProvider as HeimdalServiceProvider;
use Porto\Core\Generator\GeneratorServiceProvider;
use Porto\Core\Loaders\AutoLoaderTrait;
use Porto\Core\Loaders\FactoriesLoaderTrait;
use Porto\Core\Providers\Abstracts\CoreMainProvider;
use Porto\Core\Support\Porto;
use Porto\Core\Traits\ValidationTrait;
use Prettus\Repository\Providers\RepositoryServiceProvider;
use Spatie\Activitylog\ActivitylogServiceProvider;

/**
 * Class CoreProvider
 *
 * @package Porto\Core\Providers
 *
 * author liyq <2847895875@qq.com>
 */
class CoreProvider extends CoreMainProvider
{

    use FactoriesLoaderTrait;
    use AutoLoaderTrait;
    use ValidationTrait;

    public $serviceProviders = [
        // 第三方包服务
        RepositoryServiceProvider::class,
        CorsServiceProvider::class,
        ServiceProvider::class,
        HeimdalServiceProvider::class,
        ActivitylogServiceProvider::class,

        // laravel Tinker
        TinkerServiceProvider::class,

        //Api Provider
        RoutesProvider::class,
        GeneratorServiceProvider::class,
    ];

    protected $aliases = [
        'Hashids'  => Hashids::class,
        'Debugbar' => Facade::class,
    ];

    /**
     * 启动
     * 等所有的服务注册完后执行
     */
    public function boot() {


        $this->mergeConfigs();

        // 加载containers
        $this->runLoaderBoot();

        parent::boot();

        Schema::defaultStringLength(191);

        // 注册自定义验证规则
        $this->extendValidationRules();
    }

    /**
     * 注册服务
     */
    public function register() {

        parent::register();

        $this->overrideLaravelBaseProviders();

        $this->app->alias(Porto::class, 'Porto');

    }

    private function overrideLaravelBaseProviders() {
        //
    }

    private function mergeConfigs() {
        $files = File::allFiles(__DIR__ . '/../Configs');
        $publish = [];
        foreach ($files as $file) {
            if (File::isFile($file)) {
                $publish[$file->getPathname()] = app_path("Ship/Configs/" . $file->getFilename());
                $this->mergeConfigFrom($file->getPathname(), $file->getFilenameWithoutExtension());
            }
        }
        $this->publishes($publish, 'porto-core-config');
    }
}