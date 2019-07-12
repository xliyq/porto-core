<?php


namespace Porto\Core\Http\Controllers;


use Porto\Core\Traits\ResponseTrait;

/**
 * Class ApiController
 *
 * @package Porto\Core\Http\Controllers
 *
 * author liyq <2847895875@qq.com>
 */
class ApiController extends Controller
{

    use ResponseTrait;

    /**
     * controller 的类型
     * @var string
     */
    public $ui = 'api';
}