<?php


namespace Porto\Core\Traits;


use Porto\Core\Dto\Dto;
use Porto\Core\Exceptions\CoreInternalErrorException;
use Porto\Core\Requests\Request;
use Illuminate\Support\Arr;

trait SanitizerTrait
{

    /**
     * @param array $fields
     *
     * @return array
     * @throws \Dto\Exceptions\InvalidDataTypeException
     */
    public function sanitizeInput(array $fields) {
        $data = $this->getData();

        $search = [];

        foreach ($fields as $field) {
            Arr::set($search, $field, true);
        }
        $data = $this->recursiveArrayIntersectKey($data, $search);
        return $data;
    }

    /**
     * @return array
     * @throws \Dto\Exceptions\InvalidDataTypeException
     */
    private function getData() {
        if($this instanceof Dto){
            $data = $this->toArray();
        }elseif ($this instanceof Request) {
            $data = $this->all();
        } else {
            throw new CoreInternalErrorException('Unsupported class type for sanitization.');
        }
        return $data;
    }

    private function recursiveArrayIntersectKey(array $a, array $b) {
        $a = array_intersect_key($a, $b);
        foreach ($a as $key => &$value) {
            if (is_array($value) && is_array($b[$key])) {
                $value = $this->recursiveArrayIntersectKey($value, $b[$key]);
            }
        }
        return $a;
    }
}