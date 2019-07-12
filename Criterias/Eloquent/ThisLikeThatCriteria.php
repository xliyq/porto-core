<?php


namespace Porto\Core\Criterias\Eloquent;


use Prettus\Repository\Contracts\RepositoryInterface;

class ThisLikeThatCriteria extends Criteria
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string 包含$separator
     */
    private $valueSting;

    /**
     * @var string
     */
    private $separator;

    /**
     * @var string
     */
    private $wildcard;

    public function __construct($field, $valueSting, $separator = ',', $wildcard = '*') {
        $this->field = $field;
        $this->valueSting = $valueSting;
        $this->separator = $separator;
        $this->wildcard = $wildcard;
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
        return $model->where(function ($query) {
            $values = explode($this->separator, $this->valueSting);
            $query->where($this->field, 'LIKE', str_replace($this->wildcard, '%', array_shift($values)));
            foreach ($values as $value) {
                $query->orWhere($this->field, 'LIKE', str_replace($this->wildcard, '%', $value));
            }
        });
    }
}