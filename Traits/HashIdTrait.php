<?php


namespace Porto\Core\Traits;


use Porto\Core\Exceptions\IncorrectIdException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Vinkla\Hashids\Facades\Hashids;

trait HashIdTrait
{

    private $skippedEndpoints = [];

    public function getHashedKey($field = null) {
        if ($field === null) {
            $field = $this->getKeyName();
        }
        $value = $this->getAttribute($field);

        if (Config::get('porto.hash-id')) {
            return $this->encoder($value);
        }

        return $value;
    }

    protected function decodeHashedIdsBeforeValidation(array $requestData) {
        if (Config::get('porto.hash-id') && isset($this->decode) && !empty($this->decode)) {
            foreach ($this->decode as $key) {
                $requestData = $this->locateAndDecodeIds($requestData, $key);
            }
        }
        return $requestData;
    }


    private function locateAndDecodeIds($requestData, $key) {
        $fields = explode('.', $key);
        $transformedData = $this->processField($requestData, $fields);
        return $transformedData;
    }

    private function processField($data, $keysTodo) {
        if (empty($keysTodo)) {
            $decodedId = $this->decode($data);
            return $decodedId;
        }

        $field = array_shift($keysTodo);

        if ($field == '*') {
            $data = is_array($data) ? $data : [$data];

            $fields = $data;

            foreach ($fields as $key => $value) {
                $data[$key] = $this->processField($value, $keysTodo);
            }
            return $data;
        } else {
            if (!Arr::exists($field, $data)) {
                return $data;
            }

            $value = $data[$field];
            $data[$field] = $this->processField($value, $keysTodo);
            return $data;
        }
    }

    public function findKeyAndReturnValue(&$subject, $findKey, $callback) {
        if (!is_array($subject)) {
            return $subject;
        }

        foreach ($subject as $key => $item) {
            if ($key == $findKey && isset($subject[$findKey])) {
                $subject[$key] = $callback($subject[$findKey]);
                break;
            }
            $this->findKeyAndReturnValue($item, $findKey, $callback);
        }

    }

    public function decodeArray(array $ids) {
        $result = [];
        foreach ($ids as $id) {
            $result[] = $this->decode($id);
        }
        return $result;
    }

    public function decode($id, $parameter = null) {
        if (is_null($id) || strtolower($id) == 'null') {
            return $id;
        }

        if (is_numeric($id)) {
            throw  new IncorrectIdException('Only Hashed ID\'s allowed' . (!is_null($parameter) ? " ($parameter)." : '.'));
        }
        $decode = $this->decoder($id);

        return empty($decode) ? [] : $decode[0];
    }

    public function encode($id) {
        return $this->encoder($id);
    }

    private function decoder($id) {
        return Hashids::decode($id);
    }

    public function encoder($id) {
        return Hashids::encode($id);
    }

    public function runHashedIdsDecoder() {
        if (Config::get('porto.hash-id')) {
            Route::bind('id', function ($id, $route) {
                if (!in_array($route->uri(), $this->skippedEndpoints)) {
                    $decoded = $this->decoder($id);
                    if (empty($decoded)) {
                        throw new IncorrectIdException('ID (' . $id . ') is incorrect, consider using the hashed ID
                        instead of the numeric ID.');
                    }
                    return $decoded[0];
                }
            });
        }
    }

}