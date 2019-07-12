<?php


namespace Porto\Core\Traits;


use Illuminate\Http\JsonResponse;
use ReflectionClass;
use Illuminate\Support\Arr;

trait ResponseTrait
{
    protected $metaData = [];

    public function transform() {
    }

    public function withMeta($data) {
    }

    public function json($message, $status = 200, array $headers = [], $options = 0) {
        return new JsonResponse($message, $status, $headers, $options);
    }

    public function created($message = null, $status = 201, array $headers = [], $options = 0) {
        return $this->json($message, $status, $headers, $options);
    }

    public function accepted($message = null, $status = 202, array $headers = [], $options = 0) {
        return $this->json($message, $status, $headers, $options);
    }

    /**
     * @param null $responseArray
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function deleted($responseArray = null) {
        if ($responseArray) {
            return $this->accepted();
        }
        $id = $responseArray->getHashedKey();
        $className = (new ReflectionClass($responseArray))->getShortName();
        return $this->accepted(['message' => "$className ($id) Deleted Successfully."]);
    }

    public function noContent($status = 204) {
        return new JsonResponse(null, $status);
    }

//    private function filterResponse(array $responseArray, array $filters) {
//        $filteredData = null;
//        $responseArrayWihoitMeta = Arr::except($responseArray, ['meta']);
//        if ($this->array_is_associative($responseArrayWihoitMeta)) {
//            $filteredData = $this->filterObjectKeys($responseArrayWihoitMeta, $filters);
//        } else {
//            foreach ($responseArrayWihoitMeta as $key => $value) {
//                Arr::set($filteredData, $key, $this->filterResponse($value, $filters));
//            }
//        }
//        $filteredData['meta'] = Arr::get($responseArray, 'meta', []);
//        return $filteredData;
//    }

    public function array_is_associative($array) {
        $compareArray = array_pad([], count($array), 0);
        return (count(array_diff_key($array, $compareArray))) ? true : false;
    }

    public function filterObjectKeys($obj, $filters) {
        $filteredData = [];
        foreach (Arr::dot($obj) as $key => $value) {
            foreach ($filters as $filter) {
                $keyWithWildcard = preg_replace("/\.(\d+)+\./", '.*.', $key);
                if ($keyWithWildcard === $filter || preg_match("/^{$filters}\./", $keyWithWildcard)) {
                    Arr::set($filteredData, $key, $value);
                }
            }
        }
        return $filteredData;
    }
}