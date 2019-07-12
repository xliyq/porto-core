<?php


namespace Porto\Core\Tests\PhpUnit\Traits;


trait TestsResponseHelperTrait
{

    /**
     * 判断Response中是否包含指定的key
     *
     * @param array|string $keys
     */
    public function assertResponseContainKeys($keys) {
        if (!is_array($keys)) {
            $keys = (array)$keys;
        }

        $arrayResponse = $this->removeDataKeyFromResponse($this->getResponseContentArray());

        foreach ($keys as $key) {
            $this->assertTrue(array_key_exists($key, $arrayResponse));
        }
    }

    /**
     * 判断数组是否包含子数组
     *
     * @param array $subArr
     * @param array $array
     */
    public function assertArrayContain(array $subArr, array $array) {
        foreach ($subArr as $value) {
            $this->assertTrue(in_array($value, $array));
        }
    }


    public function assertValidationErrorContain(array $messages) {
        $responseContent = $this->getResponseContentObject();
        foreach ($messages as $key => $value) {
            $this->assertEquals($responseContent->errors->{$key}[0], $value);
        }
    }

    private function removeDataKeyFromResponse(array $responseContent) {
        if (array_key_exists('data', $responseContent)) {
            //如果分页
            if (array_key_exists('links', $responseContent)) {
                return $responseContent['data'][0];
            }
            return $responseContent['data'];
        }
        return $responseContent;
    }
}