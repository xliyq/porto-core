<?php


namespace Porto\Core\Exceptions\Handlers;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as LaravelExceptionHandler;
use Optimus\Heimdal\ExceptionHandler as HeimdalExceptionHandler;
use Porto\Core\Exceptions\MissingJSONHeaderException;

class CoreExceptionHandler extends HeimdalExceptionHandler
{
    public function render($request, Exception $exception) {

        if ($request->expectsJson()
            || config('porto.requests.force-accept-header') && $exception instanceof MissingJSONHeaderException) {
            return parent::render($request, $exception);
        }

        return LaravelExceptionHandler::render($request, $exception);
    }
}