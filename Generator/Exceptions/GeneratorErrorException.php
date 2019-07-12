<?php


use Porto\Core\Exceptions\Abstracts\CoreException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Class GeneratorErrorException
 *
 * @author  Mahmoud Zalt  <mahmoud@zalt.me>
 */
class GeneratorErrorException extends CoreException
{

    public $httpStatusCode = SymfonyResponse::HTTP_BAD_REQUEST;

    public $message = 'Generator Error.';

}
