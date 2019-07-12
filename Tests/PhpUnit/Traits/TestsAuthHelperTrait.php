<?php


namespace Porto\Core\Tests\PhpUnit\Traits;


use App\Containers\User\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Trait TestsAuthHelperTrait
 *
 * @package Porto\Core\Tests\PhpUnit\Traits
 *
 * author liyq <2847895875@qq.com>
 */
trait TestsAuthHelperTrait
{
    /**
     * 登录的用户对象
     * @var
     */
    protected $testingUser;

    /**
     * 要添加到用户上的角色和权限
     * @var array
     */
    protected $access = [
        'permissions' => '',
        'roles'       => ''
    ];

    /**
     * 尝试获取最后登录的用户，如果没有则创建新的用户
     *
     * @param null $userDetails 要附加到用户对象上的内容，如果提供，将始终创建新用户，不再获取最后登录的用户
     * @param null $access 用户的访问权限
     *
     * @return User|null
     */
    public function getTestingUser($userDetails = null, $access = null) {
        return is_null($userDetails) ? $this->findOrCreateTestingUser($userDetails, $access)
            : $this->createTestingUser($userDetails, $access);
    }

    /**
     * 与getTestingUser 功能相同，但始终重写访问权限
     *
     * @param null $userDetails
     *
     * @return User|TestsAuthHelperTrait|null
     */
    public function getTestingUserWithoutAccess($userDetails = null) {
        return $this->getTestingUser($userDetails, $this->getNullAccess());
    }

    /**
     * 查找或创建用户
     *
     * @param $userDetails
     * @param $access
     *
     * @return User|TestsAuthHelperTrait|null
     */
    public function findOrCreateTestingUser($userDetails, $access) {
        return $this->testingUser ?: $this->createTestingUser($userDetails, $access);
    }

    /**
     * 创建用户
     *
     * @param null $userDetails
     * @param null $access
     *
     * @return User|TestsAuthHelperTrait|null
     */
    private function createTestingUser($userDetails = null, $access = null) {
        if (is_array($userDetails)) {
            $defaults = [];
            $userDetails = array_merge($defaults, $userDetails);
        }

        // 创建用户
        $user = $this->factoryCreateUser($userDetails);

        // 给用户授权角色和权限
        $user = $this->setupTestingUserAccess($user, $access);
        $user->accessToken = $user->createToken('')->accessToken;

        //验证用户身份
        $this->actingAs($user, 'api');

        return $this->testingUser = $user;
    }


    /**
     * @param null $userDetails
     *
     * @return User
     */
    private function factoryCreateUser($userDetails = null) {
        return factory(User::class)->create($this->prepareUserDetails($userDetails));
    }

    private function prepareUserDetails($userDetails = null) {
        $defaultUserDetails = [
            'name'     => $this->faker->name,
            'email'    => $this->faker->email,
            'phone'    => $this->faker->phoneNumber,
            'password' => 'testing-password',
        ];

        return $this->prepareUserPassword($userDetails ?: $defaultUserDetails);
    }

    private function prepareUserPassword($userDetails) {
        $password = isset($userDetails['password']) ? $userDetails['password'] : $this->faker->password;
        $userDetails['password'] = Hash::make($password);

        return $userDetails;
    }

    private function getAccess() {
        return isset($this->access) ? $this->access : null;
    }

    private function setupTestingUserAccess($user, $access = null) {
        $access = $access ?: $this->getAccess();

        $user = $this->setupTestingUserPermissions($user, $access);
        $user = $this->setupTestingUserRoles($user, $access);

        return $user;
    }

    private function setupTestingUserRoles(User $user, $access) {
        if (isset($access['roles']) && !empty($access['roles'])) {
            if (!$user->hasRole($access['roles'])) {
                $user->assignRole($access['roles']);
                $user = $user->fresh();
            }
        }
        return $user;
    }

    private function setupTestingUserPermissions(User $user, $access) {
        if (isset($access['permissions']) && !empty($access['permissions'])) {
            $user->givePermissionTo($access['permissions']);
            $user = $user->fresh();
        }
        return $user;
    }

    private function getNullAccess() {
        return [
            'permissions' => null,
            'roles'       => null
        ];
    }
}