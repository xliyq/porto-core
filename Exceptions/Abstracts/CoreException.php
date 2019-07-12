<?php
/**
 * Created by PhpStorm.
 * User: liyq
 * Date: 2019/5/24
 * Time: 16:36
 */

namespace Porto\Core\Exceptions\Abstracts;

use Porto\Core\Exceptions\Code\ErrorCodeManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class AbstractException
 *
 * @package Porto\Core\Exceptions
 *
 * author liyq <2847895875@qq.com>
 */
abstract class CoreException extends HttpException
{
    /**
     * MessageBag errors
     *
     * @var MessageBag
     */
    protected $errors;

    /**
     * 默认错误码
     *
     * @var int
     */
    CONST DEFAULT_STATUS_CODE = Response::HTTP_INTERNAL_SERVER_ERROR;

    /**
     * 环境
     *
     * @var string
     */
    protected $environment;

    /**
     * 自定义数据
     *
     * @var mixed
     */
    protected $customData = null;

    /**
     * AbstractException constructor.
     *
     * @param null               $message
     * @param null               $errors
     * @param null               $statusCode
     * @param int                $code
     * @param CoreException|null $previous
     * @param array              $headers
     */
    public function __construct(
        $message = null,
        $errors = null,
        $statusCode = null,
        int $code = 0,
        CoreException $previous = null,
        $headers = []) {
        $this->environment = Config::get('app.env');

        $message = $this->prepareMessage($message);
        $this->errors = $this->prepareError($errors);
        $statusCode = $this->prepareStatusCode($statusCode);

        $this->logTheError($statusCode, $message, $code);

        parent::__construct($statusCode, $message, $previous, $headers, $code);

        $this->customData = $this->getCustomData();

        $this->code = $this->useErrorCode();
    }

    /**
     * 帮助开发人员调试错误
     * 用法：`throw (new MyCustomException())->debug($e)`.
     *
     * @param      $error
     * @param bool $force
     *
     * @return $this
     */
    public function debug($error, $force = false) {
        if ($error instanceof CoreException) {
            $error = $error->getMessage();
        }
        if ($this->environment != 'testing' || $force === true) {
            Log::error('[DEBUG]' . $error);
        }
        return $this;
    }

    /**
     * 获取错误包
     *
     * @return MessageBag
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * 是否存在错误包
     *
     * @return bool
     */
    public function hasErrors() {
        return !$this->errors->isEmpty();
    }

    /**
     * 记录错误信息
     *
     * @param $statusCode
     * @param $message
     * @param $code
     */
    private function logTheError($statusCode, $message, $code): void {
        if ($this->environment != 'testing') {
            Log::error('[ERROR]' .
                "Status Code: {$statusCode} | Message: {$message} | Errors: {$this->errors} | Code: {$code}");
        }
    }

    /**
     * @param null $errors
     *
     * @return MessageBag|null
     */
    private function prepareError($errors = null) {
        return is_null($errors) ? new MessageBag() : $this->prepareArrayError($errors);

    }

    /**
     * @param array $errors
     *
     * @return array|MessageBag
     */
    private function prepareArrayError(array $errors = []) {
        return is_array($errors) ? new MessageBag($errors) : $errors;
    }

    /**
     * @param null $message
     *
     * @return null
     */
    private function prepareMessage($message = null) {
        return is_null($message) && property_exists($this, 'message') ? $this->message : $message;
    }

    /**
     * @param null $statusCode
     *
     * @return int
     */
    private function prepareStatusCode($statusCode = null): int {
        return is_null($statusCode) ? $this->findStatusCode() : $statusCode;
    }

    /**
     * @return int
     */
    private function findStatusCode(): int {
        return property_exists($this, 'httpStatusCode') ? $this->httpStatusCode : Self::DEFAULT_STATUS_CODE;
    }

    /**
     * @return mixed
     */
    public function getCustomData() {
        return $this->customData;
    }

    /**
     * @return void
     */
    protected function addCustomData() {
        $this->customData = null;
    }

    /**
     * 将CustomData附加到异常并返回异常！
     *
     * @param $customData
     *
     * @return $this
     */
    public function overrideCustomData($customData) {
        $this->customData = $customData;
        return $this;
    }

    /**
     * @return int
     */
    public function useErrorCode() {
        return $this->code;
    }

//    /**
//     * 计算错误码
//     *
//     * @return int
//     */
//    private function evaluateErrorCode() {
//        $code = $this->useErrorCode();
//        if (is_array($code)) {
//            $code = ErrorCodeManager::getCode($code);
//        }
//        return $code;
//    }


}