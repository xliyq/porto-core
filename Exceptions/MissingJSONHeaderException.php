<?php
/**
 * Created by PhpStorm.
 * User: liyq
 * Date: 2019/5/24
 * Time: 17:35
 */

namespace Porto\Core\Exceptions;


use Porto\Core\Exceptions\Abstracts\CoreException;
use Symfony\Component\HttpFoundation\Response;

class MissingJSONHeaderException extends CoreException
{
    public $httpStatusCode = Response::HTTP_BAD_REQUEST;

    public $message = '请求头中必须包含 [Accept = application/json].';
}