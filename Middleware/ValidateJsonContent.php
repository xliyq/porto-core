<?php
/**
 * Created by PhpStorm.
 * User: liyq
 * Date: 2019/5/24
 * Time: 17:27
 */

namespace Porto\Core\Middleware;


use Porto\Core\Exceptions\MissingJSONHeaderException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ValidateJsonContent extends AbstractMiddleware
{

    public function handle(Request $request, \Closure $next) {
        $acceptHeader = $request->header('accept');
        $contentType = 'application/json';

        // 检查请求头中是否设置accept 值为application/json
        if (strpos($acceptHeader, $contentType) === false) {
            // 是否开启强制请求头的accept
            if (Config::get('porto.request.force-accept-header')) {
                throw new MissingJSONHeaderException();
            }
        }

        $response = $next($request);

        $response->headers->set('Content-Type', $contentType);

        if (strpos($acceptHeader, $contentType) === false) {
            $warnCode = '199'; // https://www.iana.org/assignments/http-warn-codes/http-warn-codes.xhtml
            $warnMessage = 'Missing request header [ accept = ' . $contentType . ' ] when calling a JSON API.';
            $response->headers->set('Warning', $warnCode . ' ' . $warnMessage);
        }

        return $response;
    }
}