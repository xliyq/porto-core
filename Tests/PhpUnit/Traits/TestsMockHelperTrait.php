<?php


namespace Porto\Core\Tests\PhpUnit\Traits;


use Illuminate\Support\Facades\App;

trait TestsMockHelperTrait
{
    public function mock($class, ?\Closure $mock = null) {
        !$mock && $mock = \Mockery::mock($class);
        App::instance($class, $mock);

        return $mock;
    }
}