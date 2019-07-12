<?php


namespace Porto\Core\Traits;


use Illuminate\Support\Str;

trait HasResourceKeyTrait
{
    public function getResourceKey() {
        if (isset($this->resourceKey)) {
            $resourceKey = $this->resourceKey;
        } else {
            $resourceKey = Str::snake(Str::pluralStudly(class_basename($this)));
        }
        return $resourceKey;
    }
}