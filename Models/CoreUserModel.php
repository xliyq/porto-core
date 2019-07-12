<?php


namespace Porto\Core\Models;

use Porto\Core\Traits\ActivityTrait;
use Porto\Core\Traits\HasResourceKeyTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as LaravelAuthenticatableUser;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Porto\Core\Traits\HashIdTrait;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class CoreUserModel
 *
 * @mixin \Eloquent
 *
 * @package Porto\Core\Models
 *
 * author liyq <2847895875@qq.com>
 */
abstract class CoreUserModel extends LaravelAuthenticatableUser
{
    use Notifiable;
    use SoftDeletes;
    use HashIdTrait;
    use HasRoles;
    use HasApiTokens;
    use ActivityTrait;
    use HasResourceKeyTrait;

    public function findForPassport($identifier) {
        $allowedLoginAttributes = config('authentication.login.allowed_login_attributes', ['phone' => []]);
        $fields = array_keys($allowedLoginAttributes);

        $builder = $this;

        foreach ($fields as $field) {
            $builder = $builder->orWhere($field, $identifier);
        }
        $builder = $builder->first();
        return $builder;
    }
}