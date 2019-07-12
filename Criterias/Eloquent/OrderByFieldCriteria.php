<?php


namespace Porto\Core\Criterias\Eloquent;


use Illuminate\Support\Str;
use Prettus\Repository\Contracts\RepositoryInterface;

class OrderByFieldCriteria extends Criteria
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $sortOrder;

    /**
     * OrderByFieldCriteria constructor.
     *
     * @param $field
     * @param $sortOrder
     */
    public function __construct($field, $sortOrder) {
        $this->field = $field;
        $sortOrder = Str::lower($sortOrder);
        $availableDirections = [
            'asc',
            'desc'
        ];
        if (!array_search($sortOrder, $availableDirections)) {
            $sortOrder = 'asc';
        }
        $this->sortOrder = $sortOrder;
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
        return $model->orderBy($this->field, $this->sortOrder);
    }
}