<?php


namespace Porto\Core\Http\Controllers;

use Porto\Core\Traits\HashIdTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelController;

class Controller extends LaravelController
{
    use  AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use HashIdTrait;

}