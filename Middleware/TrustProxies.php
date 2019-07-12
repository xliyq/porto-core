<?php
/**
 * Created by PhpStorm.
 * User: liyq
 * Date: 2019/5/24
 * Time: 17:18
 */

namespace Porto\Core\Middleware;

use Fideloper\Proxy\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * 授信的proxy
     *
     * @var array
     */
    protected $proxies;

    /**
     * 当前 proxy
     * @var array
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}