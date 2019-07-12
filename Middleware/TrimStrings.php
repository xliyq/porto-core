<?php
/**
 * Created by PhpStorm.
 * User: liyq
 * Date: 2019/5/24
 * Time: 17:14
 */

namespace Porto\Core\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * 不进行去空处理的参数
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation'
    ];
}