<?php


namespace Porto\Core\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Porto\Core\Exceptions\CoreInternalErrorException;
use Porto\Core\Exceptions\UnsupportedResourceIncludeException;

class ResourceInclude
{
    /**
     * 当前标识符
     *
     * @var string
     */
    private $identifier;

    /**
     * 当前父级标识符
     * @var array|mixed
     */
    private $parentIdentifier = [];

    /**
     * 待处理资源
     *
     * @var CoreResource
     */
    private $resource;

    /**
     * 请求中的include集合
     * @var array|null
     */
    private static $requestIncludes = null;


    /**
     * 析构函数
     *
     * ResourceInclude constructor.
     *
     * @param CoreResource $resource
     * @param self         $resourceInclude
     */
    public function __construct(CoreResource $resource, ResourceInclude $resourceInclude = null) {
        //当前标识符
        if ($resource->resource) {
            $this->identifier = $resource->resource->getResourceKey();
        }

        //当前资源
        $this->resource = $resource;

        // 获取请求中的include
//        if (self::$requestIncludes == null) {
        self::$requestIncludes = $this->getRequestIncludes();
//        }
//        Log::debug('requestIncludes -->' . request()->fullUrl(), self::$requestIncludes);

        if ($resourceInclude != null) {
            $this->parentIdentifier = $resourceInclude->getParentIdentifier() + [$resourceInclude->getIdentifier()];
        }
    }

    /**
     * 转换数据
     *
     * @return array
     */
    public function transform() {
        // 获取当前资源的数据
//        $data = collect($this->resource->resource)->only($this->resource->getDefaultFields())->all();
        $data = $this->resource->toArray(request());
        if ($custom = $this->resource->getCustomData()) {
            $data = array_merge($custom, $data);
        }

        // 获取当前资源中可执行的include
        $includes = $this->findOutWhichIncludes();

        // 执行include
        foreach ($includes as $include) {
            $data[$include] = $this->callIncludeMethod($include);
        }
        return empty($data) ? null : $data;
    }

    /**
     * 构建一个子ResourceInclude
     *
     * @param $resource
     *
     * @return ResourceInclude
     */
    public function embedChildResource($resource): ResourceInclude {
        return new self($resource, $this);
    }

    /**
     * 获取当前资源的父级标识符
     *
     * @return array|mixed
     */
    public function getParentIdentifier() {
        return $this->parentIdentifier;
    }

    /**
     * 获取当前资源的标识符
     *
     * @return mixed
     */
    public function getIdentifier() {
        return $this->identifier;
    }


    /**
     * 在当前资源中执行指定的include
     *
     * @param $include
     *
     * @return array
     */
    public function callIncludeMethod($include) {
        $methodName = 'include' . Str::ucfirst($include);
        $includeData = [];
        try {
            //判断当前资源中是否存在相应的include 方法
            if (method_exists($this->resource, $methodName)) {

                //执行方法
                $resource = call_user_func([$this->resource, $methodName]);

                if ($resource instanceof ResourceCollection) {
                    //判断为列表数据
                    $resource->collection->map(function ($value) use ($include, &$includeData) {
                        $child = $this->embedChildResource($value);
                        $includeData[] = $child->transform();
                    });
                } elseif ($resource instanceof CoreResource) {
                    // 判断为对象数据
                    $child = $this->embedChildResource($resource);
                    $includeData = $child->transform();
                }
            }
        } catch (\ErrorException $exception) {
            if (config('porto.requests.force-valid-includes', true)) {
                throw new UnsupportedResourceIncludeException();
            }
        } catch (\Exception $exception) {
            throw new CoreInternalErrorException();
        }
        return $includeData;
    }

    /**
     * 获取request中的include
     *
     * @return array
     */
    private function getRequestIncludes() {
        $includes = request('include');
        $includes = explode(',', $includes);
        $includes = array_filter($includes);
        $parsed = [];
        //将roles.permissions 处理成[roles,roles.permissions]
        foreach ($includes as $include) {
            $nested = explode('.', $include);
            $part = array_shift($nested);
            $parsed[] = $part;

            while (count($nested) > 0) {
                $part .= '.' . array_shift($nested);
                $parsed[] = $part;
            }
            $requestIncludes[] = $include;
        }

        return array_values(array_unique($parsed));
    }


    /**
     * 判断指定的include是否在请求中
     *
     * @param string $include
     *
     * @return bool
     */
    private function isRequested(string $include): bool {
        if ($this->parentIdentifier) {
            $identifiers = array_slice($this->parentIdentifier, 1);
            array_push($identifiers, $this->identifier, $include);
        } else {
            $identifiers = [$include];
        }
        $str = implode('.', $identifiers);

        return in_array($str, self::$requestIncludes);
    }

    /**
     * 获取当前可执行的include
     * @return array
     */
    private function findOutWhichIncludes(): array {
        $includes = $this->resource->getDefaultIncludes();

        foreach ($this->resource->getAvailableIncludes() as $include) {
            //判断当前可用的include是否在请求中
            if ($this->isRequested($include)) {
                $includes[] = $include;
            }
        }
        return $includes;
    }

}