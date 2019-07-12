<?php


namespace Porto\Core\Criterias\Eloquent;


use Carbon\Carbon;
use Prettus\Repository\Contracts\RepositoryInterface;

class CreatedTodayCriteria extends Criteria
{

    /**
     * Apply criteria in query repository
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository) {
        return $model->where('created_at', '>=', Carbon::today()->toDateString());
    }
}