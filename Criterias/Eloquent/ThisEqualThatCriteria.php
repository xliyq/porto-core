<?php


namespace Porto\Core\Criterias\Eloquent;


use Prettus\Repository\Contracts\RepositoryInterface;

class ThisEqualThatCriteria extends Criteria
{

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $value;

    public function __construct($field, $value) {
        $this->field = $field;
        $this->value = $value;
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
        return $model->where($this->field, $this->value);
    }
}