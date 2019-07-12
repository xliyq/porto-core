<?php


namespace Porto\Core\Criterias\Eloquent;


use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class CountCriteria
 *
 * @package Porto\Core\Criterias\Eloquent
 *
 * author liyq <2847895875@qq.com>
 */
class CountCriteria extends Criteria
{

    /**
     * @var string
     */
    private $field;

    /**
     * CountCriteria constructor.
     *
     * @param $field
     */
    public function __construct($field) {
        $this->field = $field;
    }

    /**
     * Apply criteria in query repository
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository) {
        return DB::table($model->getMedel()->getTable())
            ->select($this->field, DB::raw("count({$this->field}) as total_count"))
            ->groupBy($this->field);
    }
}