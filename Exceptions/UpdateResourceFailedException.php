<?php


namespace Porto\Core\Exceptions;


use Porto\Core\Exceptions\Abstracts\CoreException;
use Symfony\Component\HttpFoundation\Response;

class UpdateResourceFailedException extends CoreException
{
    public $httpStatusCode = Response::HTTP_EXPECTATION_FAILED;

    public $message = '更新资源失败';
}