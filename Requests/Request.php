<?php


namespace Porto\Core\Requests;

use App\Containers\Authentication\Tasks\GetAuthenticatedUserTask;
use App\Containers\User\Models\User;
use Illuminate\Support\Facades\App;
use Porto\Core\Traits\SanitizerTrait;
use Porto\Core\Dto\DataDto;
use Porto\Core\Dto\Dto;
use Illuminate\Support\Facades\Config;
use Porto\Core\Traits\HashIdTrait;
use Illuminate\Foundation\Http\FormRequest as LaravelRequest;
use Illuminate\Support\Arr;

class Request extends LaravelRequest
{
    use HashIdTrait;
    use SanitizerTrait;

    protected $dto = DataDto::class;

    protected $access = [];

    protected $urlParameters = [];

    public function authorize() {
        return $this->check(['hasAccess']);
    }

    /**
     * @param null $keys
     *
     * @return array
     */
    public function all($keys = null) {
        $requestData = parent::all($keys);
        $requestData = $this->mergeUrlParametersWithRequestData($requestData);
        $requestData = $this->decodeHashedIdsBeforeValidation($requestData);
        return $requestData;
    }


    /**
     * 检查用户是否有操作权限
     *
     * @param User|null $user
     *
     * @return bool
     */
    public function hasAccess(User $user = null) {
        $user = $user ?: $this->user();
        if ($user) {
            $autoAccessRoles = Config::get('porto.requests.allow-roles-to-access-all-routes');
            if (!empty($autoAccessRoles)) {
                $hasAutoAccessByRole = $user->hasAnyRole($autoAccessRoles);
                if ($hasAutoAccessByRole) {
                    return true;
                }
            }
        }

        $hasAccess = array_merge(
            $this->hasAnyPermissionAccess($user),
            $this->hasAnyRoleAccess($user)
        );
        return empty($hasAccess) ? true : in_array(true, $hasAccess);
    }


    public function mapInput(array $fields) {
        $data = $this->all();
        foreach ($fields as $oldKey => $newKey) {
            if (!Arr::has($data, $oldKey)) {
                continue;
            }
            //用新的key替换旧的key
            Arr::set($data, $newKey, Arr::get($data, $oldKey));
            Arr::forget($data, $oldKey);
        }
        $this->replace($data);
    }

    private function mergeUrlParametersWithRequestData(array $requestData) {
        if (isset($this->urlParameters) && !empty($this->urlParameters)) {
            foreach ($this->urlParameters as $parameter) {
                $requestData[$parameter] = $this->route($parameter);
            }
        }
        return $requestData;
    }

    /**
     * 检查权限
     *
     * @param array $functions
     *
     * @return bool
     */
    protected function check(array $functions) {
        // |间隔使用或运算
        $orIndicator = '|';
        $returns = [];

        foreach ($functions as $function) {
            // 判断是否存在或运算
            if (!strpos($function, $orIndicator)) {
                $returns[] = $this->{$function}();
            } else {
                $orReturn = false;

                // 或运算结果中有一个为true
                foreach (explode($orIndicator, $function) as $orFunction) {
                    $orReturn = $this->{$orFunction}();
                    //true 跳出循环
                    if ($orReturn) {
                        break;
                    }
                }

                $returns[] = $orReturn;
            }
        }

        return in_array(false, $returns) ? false : true;
    }

    /**
     * 判断用户是否有访问权限
     *
     * @param $user
     *
     * @return array
     */
    private function hasAnyPermissionAccess($user) {
        if (!array_key_exists('permissions', $this->access) || !$this->access['permissions']) {
            return [];
        }
        $permissions = is_array($this->access['permissions']) ? $this->access['permissions']
            : explode('|', $this->access['permissions']);
        $hasAccess = array_map(function ($permission) use ($user) {
            return $user->hasPermissionTo($permission);
        }, $permissions);

        return $hasAccess;
    }

    /**
     * 判断用户角色是否有访问权限
     *
     * @param $user
     *
     * @return array
     */
    private function hasAnyRoleAccess($user) {
        if (!array_key_exists('roles', $this->access) || !$this->access['roles']) {
            return [];
        }

        $roles = is_array($this->access['roles']) ? $this->access['roles']
            : explode('|', $this->access['roles']);

        $hasAccess = array_map(function ($role) use ($user) {
            return $user->hasRole($role);
        }, $roles);

        return $hasAccess;
    }

    /**
     * 获取解码后的数据
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function getInputByKey($key, $default = null) {
        return data_get($this->all(), $key, $default);
    }

    public function isOwner() {
        return app(GetAuthenticatedUserTask::class)->run()->id == $this->id;
    }

    /**
     * 用于单元测试使用
     *
     * @param array     $parameters
     * @param User|null $user
     * @param array     $cookies
     * @param array     $files
     * @param array     $server
     *
     * @return LaravelRequest
     */
    public static function injectData($parameters = [], User $user = null, $cookies = [], $files = [], $server = []) {
        if ($user) {
            $app = App::getInstance();
            $app['auth']->guard($driver = "api")->setUser($user);
            $app['auth']->shouldUse($driver);
        }
        $request = parent::create('/', 'GET', $parameters, $cookies, $files, $server);

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $request;
    }


    public function getDto() {
        if ($this->dto == null) {

        }
        return $this->dto;
    }

    public function toDto() {
        $dtoClass = $this->getDto();

        /** @var Dto $transporter */
        $dto = new $dtoClass;
        $dto->setInstance('request', $this);

        return $dto;
    }

}