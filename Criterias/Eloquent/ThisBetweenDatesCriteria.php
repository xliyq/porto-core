<?php


namespace Porto\Core\Criterias\Eloquent;


use Carbon\Carbon;
use Prettus\Repository\Contracts\RepositoryInterface;

class ThisBetweenDatesCriteria extends Criteria
{

    /**
     * @var Carbon
     */
    private $start;

    /**
     * @var Carbon
     */
    private $end;

    /**
     * @var string
     */
    private $field;

    public function __construct($field, Carbon $start, Carbon $end) {
        $this->field = $field;
        $this->start = $start;
        $this->end = $end;
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
        return $model->whereBetween($this->field, [$this->start->toDateString(), $this->end->toDateString()]);
    }
}