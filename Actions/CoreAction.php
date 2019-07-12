<?php


namespace Porto\Core\Actions;


use Porto\Core\Traits\CallableTrait;
use Porto\Core\Traits\HasRequestCriteriaTrait;

abstract class CoreAction
{
    use CallableTrait;
    use HasRequestCriteriaTrait;

    protected $ui;

    public function setUI($interface) {
        $this->ui = $interface;
        return $this;
    }

    public function getUI() {
        return $this->ui;
    }
}