<?php


namespace Porto\Core\Dto;


use Dto\Dto as BaseDto;
use Dto\RegulatorInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Porto\Core\Requests\Request;
use Porto\Core\Traits\SanitizerTrait;

abstract class Dto extends BaseDto
{
    use SanitizerTrait;

    private $instances = [];

    public function __construct($input = null, $schema = null, RegulatorInterface $regulator = null) {

        if ($input instanceof Request) {
            $content = $input->toArray();
            $headers = ['_headers' => $input->headers->all()];

            $input = array_merge($headers, $content);
        }


        parent::__construct($input, $schema, $regulator);
    }

    public function getInputByKey($key = null, $default = null) {
        return Arr::get($this->toArray(), $key, $default);
    }

    public function setInstance($key, $value) {
        $this->instances[$key] = $value;
    }

    public function only($keys) {
        return collect($this->toArray())->only($keys)->toArray();
    }

    public function __get($name) {

        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if (!$this->exists($name)) {
            return null;
        }

        $field = parent::__get($name);

        $type = $field->getStorageType();
        $value = call_user_func([$field, 'to' . Str::ucfirst($type)]);
        return $value;
    }
}