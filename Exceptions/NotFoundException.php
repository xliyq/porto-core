<?php


namespace Porto\Core\Exceptions;


use Porto\Core\Exceptions\Abstracts\CoreException;
use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends CoreException
{
    public $httpStatusCode = Response::HTTP_NOT_FOUND;

    public $message = '未找到请求的资源';
}