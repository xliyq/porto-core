<?php


namespace Porto\Core\Exceptions;


use Porto\Core\Exceptions\Abstracts\CoreException;
use Symfony\Component\HttpFoundation\Response;

class CreateResourceFailedException extends CoreException
{
    public $httpStatusCode = Response::HTTP_EXPECTATION_FAILED;

    public $message = '创建资源失败';
}