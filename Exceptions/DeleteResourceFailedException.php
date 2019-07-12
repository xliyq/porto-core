<?php


namespace Porto\Core\Exceptions;


use Porto\Core\Exceptions\Abstracts\CoreException;
use Symfony\Component\HttpFoundation\Response;

class DeleteResourceFailedException extends CoreException
{
    public $httpStatusCode = Response::HTTP_EXPECTATION_FAILED;

    public $message = '删除资源失败';
}