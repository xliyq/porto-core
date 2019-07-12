<?php


namespace Porto\Core\Abstracts\Repositories;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Prettus\Repository\Eloquent\BaseRepository as PrettusRepository;
use Prettus\Repository\Criteria\RequestCriteria as PrettusRequestCriteria;
use Prettus\Repository\Contracts\CacheableInterface as PrettusCacheable;
use Prettus\Repository\Traits\CacheableRepository as PrettusCacheableRepository;

/**
 * Class CoreRepository
 *
 * @package Porto\Core\Abstracts\Repositories
 *
 * author liyq <2847895875@qq.com>
 */
abstract class CoreRepository extends PrettusRepository implements PrettusCacheable
{
    use PrettusCacheableRepository {
        paginate as public paginateExtend;
    }

    protected $container;

    /**
     * 定义每页条数
     *
     * 设置0表示禁用
     *
     * @var int
     */
    protected $maxPaginationLimit = 0;


    public function model() {
        // 获取当前类完整的命名空间
        $fullName = get_called_class();
        //获取类名
        $className = substr($fullName, strrpos($fullName, '\\') + 1);
        //获取去除Repository后的名称
        $classOnly = str_replace('Repository', '', $className);
        //拼接model命名空间
        $modelNamespace = 'App\Containers\\' . $this->getCurrentContainer() . '\\Models\\' . $classOnly;
        return $modelNamespace;
    }

    public function boot() {
        if (Config::get('porto.requests.automatically-apply-request-criteria', true)) {
            $this->pushCriteria(app(PrettusRequestCriteria::class));
        }
    }

    /**
     * 分页
     *
     * Apply pagination to the response. Use ?limit= to specify the amount of entities in the response.
     * The client can request all data (skipping pagination) by applying ?limit=0 to the request, if
     * PAGINATION_SKIP is set to true.
     *
     * @param null   $limit
     * @param array  $columns
     * @param string $method
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate") {
        $limit = $limit ?: Request::get('limit');

        // 检查是否跳过分页处理
        if (Config::get('repository.pagination.skip') && $limit == '0') {
            return $this->all($columns);
        }

        if (is_int($this->maxPaginationLimit)
            && $this->maxPaginationLimit > 0
            && $limit > $this->maxPaginationLimit) {
            $limit = $this->maxPaginationLimit;
        }

        return $this->paginateExtend($limit, $columns, $method);
    }

    /**
     * 获取当前容器名称
     *
     * @return string
     */
    private function getCurrentContainer(): string {
        return substr(
            str_replace("App\\Containers\\", "", get_called_class()),
            0,
            strpos(str_replace("App\\Containers\\", "", get_called_class()), '\\')
        );
    }
}