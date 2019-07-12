<?php
/**
 * Created by PhpStorm.
 * User: liyq
 * Date: 2019/5/24
 * Time: 17:13
 */

namespace Porto\Core\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as CookieEncryptMiddleware;

/**
 * Class EncryptCookies
 *
 * @package Porto\Core\Middlewares
 *
 * author liyq <2847895875@qq.com>
 */
class EncryptCookies extends CookieEncryptMiddleware
{

    protected $except = [];
}