<?php


namespace Porto\Core\Middleware;


use Illuminate\Http\Request;
use Porto\Core\Middleware\AbstractMiddleware;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

/**
 * Class ProcessETagHeadersMiddleware
 *
 * @package Porto\Core\Middleware
 *
 * author liyq <2847895875@qq.com>
 */
class ProcessETagHeadersMiddleware extends AbstractMiddleware
{

    public function handle(Request $request, \Closure $next) {

        //判断是否开启etag，默认关闭
        if (config('porto.requests.use-tag', false) === false) {
            return $next($request);
        }

        // 检查 请求类型是否支持if-none-match
        if ($request->hasHeader('if-none-match')) {
            if (!in_array(strtoupper($request->method()), ['GET', 'HEAD'])) {
                throw new PreconditionFailedHttpException('HTTP Header IF-NoneMatch is only allowed fo GET and HEAD requests');
            }
        }

        $response = $next($request);

        // 根据response 内容生成etag
        $content = $response->getContent();
        $etag = md5($content);
        $response->headers->set('Etag', $etag);

        // 比对request 中etag值，一致则返回304，表示数据没有更新
        if ($request->hasHeader('if-none-match')) {
            if ($request->header('if-none-match') == $etag) {
                $response->setStatusCode(304);
            }
        }
        return $response;
    }
}