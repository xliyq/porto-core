<?php


namespace Porto\Core\Exceptions\Abstracts;

use Exception;
use Illuminate\Http\JsonResponse;
use Optimus\Heimdal\Formatters\BaseFormatter as HeimdalBaseFormatter;

/**
 * Class ExceptionsFormatter
 *
 * @package Porto\Core\Abstracts\Exceptions
 *
 * author liyq <2847895875@qq.com>
 */
abstract class CoreExceptionsFormatter extends HeimdalBaseFormatter
{
    public function format(JsonResponse $response, Exception $exception, array $reporterResponses) {
        $response = $this->modifyResponse($exception, $response);

        $data = $response->getData(true);

        $data = array_merge($data, $this->responseData($exception, $response));

        $data = $this->appendCustomData($data, $exception);

        $data = $this->appendDebug($data, $exception);

        $data = $this->appendProfiler($data, $exception);

        $response->setStatusCode($this->statusCode());

        $response->setData($data);
    }

    private function appendCustomData($data, $exception) {
        if (method_exists($exception, 'getCustomData')) {
            $customData = $exception->getCustomData();

            if ($customData === null) {
                return $data;
            }

            if (!is_array($customData)) {
                $customData = ['customData' => $customData];
            }
            $data = array_merge($data, $customData);
        }
        return $data;
    }

    private function appendDebug($data, Exception $exception) {
        if (config('app.debug')) {
            $data = array_merge($data, [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);
        }
        return $data;
    }

    private function appendProfiler($data, $exception) {
        if (config('api.api.debug')) {
            $data = array_merge($data, [
                'trace' => (string)$exception
            ]);
        }
        return $data;
    }

    /**
     * @param CoreException $exception
     * @param JsonResponse  $response
     *
     * @return array
     */
    abstract function responseData(Exception $exception, JsonResponse $response): array;

    /**
     * @param CoreException $exception
     * @param JsonResponse  $response
     *
     * @return JsonResponse
     */
    abstract function modifyResponse(Exception $exception, JsonResponse $response): JsonResponse;

    /**
     * @return int
     */
    abstract function statusCode(): int;
}