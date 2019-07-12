<?php
/**
 * Created by PhpStorm.
 * User: liyq
 * Date: 2019/5/24
 * Time: 16:31
 */

namespace Porto\Core\Middleware;

use App\Containers\Authentication\Exceptions\AuthenticationException;
use Exception;
use Illuminate\Auth\Middleware\Authenticate as LaravelAuthenticate;

class Authenticate extends LaravelAuthenticate
{
    protected function authenticate($request, array $guards) {
        try {
            parent::authenticate($request, $guards);
            return;
        } catch (Exception $exception) {
            throw new AuthenticationException();
        }
    }
}