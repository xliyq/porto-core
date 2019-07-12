<?php


namespace Porto\Core\Loaders;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Porto\Core\Support\Facades\Porto;

/**
 * Trait SeederLoaderTrait
 *
 * @package Porto\Core\Loaders
 *
 * author liyq <2847895875@qq.com>
 */
trait SeederLoaderTrait
{

    /**
     * 加载数据填充
     */
    public function runLoadingSeeders() {
        $this->loadSeederFromContainers();
    }

    /**
     * 加载 Containers 中的数据填充
     */
    private function loadSeederFromContainers() {
        $seedersClasses = new Collection();
        $containersDirectories = [];

        foreach (Porto::getContainersNames() as $containersName) {
            $containersDirectories[] = base_path('app/Containers/' . $containersName
                . DIRECTORY_SEPARATOR . Config::get('porto.container.seeder'));
        }

        $seedersClasses = $this->findSeedersClasses($containersDirectories, $seedersClasses);
        $orderedSeederClasses = $this->sortSeeders($seedersClasses);

        $this->loadSeeders($orderedSeederClasses);
    }

    /**
     * 获取所有的seeder类
     *
     * @param array      $directories
     * @param Collection $seedersClasses
     *
     * @return Collection
     */
    private function findSeedersClasses(array $directories, Collection $seedersClasses) {
        foreach ($directories as $directory) {
            if (File::isDirectory($directory)) {
                $files = File::allFiles($directory);

                foreach ($files as $file) {
                    if (File::isFile($file)) {
                        $seedersClasses->push(
                            Porto::getClassFullNameFromFile(
                                $file->getPathname()
                            )
                        );
                    }
                }
            }
        }
        return $seedersClasses;
    }

    /**
     * 排序
     *
     * @param Collection $seedersClasses
     *
     * @return Collection
     */
    private function sortSeeders(Collection $seedersClasses) {
        $orderedSeederClasses = new Collection();
        if ($seedersClasses->isEmpty()) {
            return $orderedSeederClasses;
        }

        foreach ($seedersClasses as $key => $seederFullClassName) {
            // 如果namespace 包含"_"，需要按顺序排序
            if (preg_match('/_/', $seederFullClassName)) {
                // seeder 移动到需要排序的集合中
                $orderedSeederClasses->push($seederFullClassName);
                // 从seeder集合删除掉
                $seedersClasses->forget($key);
            }
        }

        // 对需要排序的seeder进行排序
        $orderedSeederClasses = $orderedSeederClasses->sortBy(function ($seederFullClassName) {
            $orderNumber = substr($seederFullClassName, strpos($seederFullClassName, '_') + 1);
            return $orderNumber;
        });

        // 将不需要排序的seeder添加到集合中
        foreach ($seedersClasses as $seederClass) {
            $orderedSeederClasses->push($seederClass);
        }

        return $orderedSeederClasses;
    }

    /**
     * 加载
     *
     * @param $seedersClasses
     */
    private function loadSeeders($seedersClasses) {
        foreach ($seedersClasses as $seeder) {
            $this->call($seeder);
        }
    }
}