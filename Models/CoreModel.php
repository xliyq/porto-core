<?php


namespace Porto\Core\Models;

use Porto\Core\Traits\ActivityTrait;
use Porto\Core\Traits\HasResourceKeyTrait;
use Illuminate\Database\Eloquent\Model as LaravelEloquentModel;
use Porto\Core\Traits\HashIdTrait;


abstract class CoreModel extends LaravelEloquentModel
{
    use ActivityTrait;
    use HashIdTrait;
    use HasResourceKeyTrait;

}