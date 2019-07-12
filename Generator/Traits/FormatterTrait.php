<?php


namespace Porto\Core\Generator\Traits;


trait FormatterTrait
{

    protected function trimString($string) {
        return trim($string);
    }

    public function capitalize($word) {
        return ucfirst($word);
    }

    public function prependOperationToName($operation, $class) {
        $className = ($operation == 'list') ? ngettext($class) : $class;
        return $operation . $this->capitalize($className);
    }
}