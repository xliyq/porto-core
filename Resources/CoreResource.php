<?php


namespace Porto\Core\Resources;

use Porto\Core\Traits\APIResourceTrait;
use Illuminate\Http\Resources\Json\Resource as LaravelJsonResource;
use Porto\Core\Support\Facades\Porto;

class CoreResource extends LaravelJsonResource
{
    use APIResourceTrait;

    public function __construct($resource, $includes = null) {
        parent::__construct($resource);
        if (!empty($includes) && is_array($includes)) {
            $this->setIncludes($includes);
        }
    }

    /**
     * 格式化数据
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request) {
        $collect = collect($this->resource);
        if (!in_array('*', $this->getDefaultFields())) {
            $collect = $collect->only($this->getDefaultFields());
        }
        return $collect->merge($this->getCustomData())->all();
    }

    public function resolve($request = null) {
        $resourceIdentifier = new ResourceInclude($this);
        return $resourceIdentifier->transform();
    }

    public static function collection($resource) {
        return tap(new ResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
//        return parent::collection($resource);
    }


    public function user() {
        return Porto::call('Authentication@GetAuthenticatedUserTask');
    }

    public function isAdmin($adminResponse, $clientResponse) {
        $user = $this->user();

        if (!is_null($user) && $user->hasAdminRole()) {
            return array_merge($clientResponse, $adminResponse);
        }

        return $clientResponse;
    }


}