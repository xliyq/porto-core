<?php


namespace Porto\Core\Exceptions;


use Porto\Core\Exceptions\Abstracts\CoreException;
use Symfony\Component\HttpFoundation\Response;

class WrongEndpointFormatException extends CoreException
{
    public $httpStatusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

    public $message = 'tests ($this->endpoint) property must be formatted as "verb@url".';
}