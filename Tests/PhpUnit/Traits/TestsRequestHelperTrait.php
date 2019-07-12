<?php


namespace Porto\Core\Tests\PhpUnit\Traits;


use Porto\Core\Exceptions\MissingTestEndpointException;
use Porto\Core\Exceptions\UndefinedMethodException;
use Porto\Core\Exceptions\WrongEndpointFormatException;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Trait TestsRequestHelperTrait
 *
 * @package Porto\Core\Tests\PhpUnit\Traits
 *
 * author liyq <2847895875@qq.com>
 */
trait TestsRequestHelperTrait
{

    /**
     * @var string
     */
    protected $endpoint = '';

    /**
     * @var bool
     */
    protected $auth = true;

    /**
     * @var TestResponse
     */
    protected $response;

    /**
     * @var string
     */
    protected $responseContent;

    /**
     * @var array
     */
    protected $responseContentArray;

    /**
     * @var \stdClass
     */
    protected $responseContentObject;

    /**
     * @var string
     */
    protected $overrideEndpoint;

    /**
     * @var string
     */
    protected $overrideAuth;

    /**
     * @param array $data
     * @param array $headers
     *
     * @return TestResponse
     * @throws UndefinedMethodException
     */
    public function makeCall(array $data = [], array $headers = []) {
        $this->getTestingUser();

        $endpoint = $this->parseEndpoint();
        $verb = $endpoint['verb'];
        $url = $endpoint['url'];
        $headers = $this->injectAccessToken($headers);

        switch ($verb) {
            case 'get':
                $url = $this->dataArrayToQueryParam($data, $url);
                break;
            case 'post':
            case 'put':
            case 'patch':
            case 'delete':
                break;
            default:
                throw new UndefinedMethodException('不支持HTTP请求方式 (' . $verb . ')');
        }

        $httpResponse = $this->json($verb, $url, $data, $headers);
//        echo "| {$verb} | {$url} | " . json_encode($data) . " | " . json_encode($headers) . json_encode($httpResponse->getContent()) . "\n";
        return $this->setResponseObjectAndContent($httpResponse);
    }

    /**
     * @param $httpResponse
     *
     * @return TestResponse
     */
    public function setResponseObjectAndContent($httpResponse) {
        $this->setResponseContent($httpResponse);

        return $this->response = $httpResponse;
    }

    public function setResponseContent(TestResponse $httpResponse) {
        return $this->responseContent = $httpResponse->getContent();
    }

    public function getResponseContent() {
        return $this->responseContent;
    }

    public function getResponseContentArray() {
        return $this->responseContentArray ?: $this->responseContentArray = json_decode($this->getResponseContent(), true);
    }

    public function getResponseContentObject() {
        return $this->responseContentArray ?: $this->responseContentArray = json_decode($this->getResponseContent(), false);
    }

    /**
     * 注入请求地址中的id
     *
     * @param        $id
     * @param bool   $skipEncoding
     * @param string $replace
     *
     * @return $this
     */
    public function injectId($id, $skipEncoding = false, $replace = '{id}') {
        $id = $this->hashEndpointId($id, $skipEncoding);
        $this->endpoint = str_replace($replace, $id, $this->endpoint);

        return $this;
    }

    public function endpoint($endpoint) {
        $this->overrideEndpoint = $endpoint;
        return $this;
    }

    public function getEndpoint() {
        return !is_null($this->overrideEndpoint) ? $this->overrideEndpoint : $this->endpoint;
    }

    public function auth(bool $auth) {
        $this->overrideAuth = $auth;
        return $this;
    }

    public function getAuth() {
        return !is_null($this->overrideAuth) ? $this->overrideAuth : $this->auth;
    }

    private function buildUrlForUri($uri) {
        if (!Str::startsWith($uri, '/')) {
            $uri = '/' . $uri;
        }
        return config('porto.api.url') . $uri;
    }

    private function injectAccessToken(array $headers = []) {
        if ($this->getAuth() && !$this->headersContainAuthorization($headers)) {
            $headers['Authorization'] = 'Bearer ' . $this->getTestingUser()->accessToken;
        }
        return $headers;
    }

    private function headersContainAuthorization($headers) {
        return Arr::has($headers, 'Authorization');
    }

    private function dataArrayToQueryParam($data, $url) {
        // 判断url中是否已经包含?
        $delimiter = Str::contains($url, '?') ? '&' : '?';

        return $data ? $url . $delimiter . http_build_query($data) : $url;
    }

    private function getJsonVerb($text) {
        return Str::replaceFirst('json:', '', $text);
    }

    private function hashEndpointId($id, $skipEncoding = false) {
        return config('porto.hash-id') && !$skipEncoding ? Hashids::encode($id) : $id;
    }

    private function parseEndpoint() {
        $this->validateEndpointExist();

        $separator = '@';

        $this->validateEndpointFormat($separator);

        $asArray = explode($separator, $this->getEndpoint(), 2);

        extract(array_combine(['verb', 'uri'], $asArray));
        /** @var TYPE_NAME $verb */
        /** @var TYPE_NAME $uri */
        return [
            'verb' => $verb,
            'uri'  => $uri,
            'url'  => $this->buildUrlForUri($uri)
        ];
    }

    private function validateEndpointExist() {
        if (!$this->getEndpoint()) {
            throw new MissingTestEndpointException();
        }
    }

    private function validateEndpointFormat($separator) {
        if (!strpos($this->getEndpoint(), $separator)) {
            throw new WrongEndpointFormatException();
        }
    }

    protected function transformHeadersToServerVars(array $headers) {
        return collect($headers)->mapWithKeys(function ($value, $name) {
            $name = strtr(strtoupper($name), '-', '_');
            return [$this->formatServerHeaderKey($name) => $value];
        })->all();
    }
}