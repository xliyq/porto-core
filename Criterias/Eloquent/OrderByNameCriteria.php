<?php


namespace Porto\Core\Criterias\Eloquent;


use Prettus\Repository\Contracts\RepositoryInterface;

class OrderByNameCriteria extends Criteria
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
        return $model->orderBy('name', 'asc');
    }
}