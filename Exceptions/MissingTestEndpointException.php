<?php


namespace Porto\Core\Exceptions;


use Porto\Core\Exceptions\Abstracts\CoreException;
use Symfony\Component\HttpFoundation\Response;

class MissingTestEndpointException extends CoreException
{
    public $httpStatusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

    public $message = 'Property ($this->endpoint) is missed in your test.';
}