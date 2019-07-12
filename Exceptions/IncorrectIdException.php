<?php


namespace Porto\Core\Exceptions;


use Porto\Core\Exceptions\Abstracts\CoreException;
use Symfony\Component\HttpFoundation\Response;

class IncorrectIdException extends CoreException
{
    public $httpStatusCode = Response::HTTP_BAD_REQUEST;

    public $message = 'ID input is incorrect';
}