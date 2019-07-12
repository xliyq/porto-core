<?php


namespace Porto\Core\Exceptions;


use Porto\Core\Exceptions\Abstracts\CoreException;
use Symfony\Component\HttpFoundation\Response;

class UndefinedMethodException extends CoreException
{
    public $httpStatusCode = Response::HTTP_FORBIDDEN;

    public $message = 'Undefined HTTP Verb!';
}