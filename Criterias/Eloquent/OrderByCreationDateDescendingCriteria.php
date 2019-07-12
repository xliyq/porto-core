<?php


namespace Porto\Core\Criterias\Eloquent;


use Prettus\Repository\Contracts\RepositoryInterface;

class OrderByCreationDateDescendingCriteria extends Criteria
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
        return $model->orderBy('created_at', 'desc');
    }
}