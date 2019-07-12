<?php


namespace Porto\Core\Exceptions;


use Porto\Core\Exceptions\Abstracts\CoreException;
use Symfony\Component\HttpFoundation\Response;

class UnsupportedResourceIncludeException extends CoreException
{
    public $httpStatusCode = Response::HTTP_BAD_REQUEST;

    public $message = 'request中的include 参数验证失败';
}