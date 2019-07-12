<?php


namespace Porto\Core\Criterias\Eloquent;


use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\RepositoryInterface;

class ThisUserCriteria extends Criteria
{

    /**
     * @var int
     */
    private $userId;

    public function __construct($userId = null) {
        $this->userId = $userId;
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
        if (!$this->userId) {
            $this->userId = Auth::user()->id;
        }

        return $model->where('user_id', '=', $this->userId);
    }
}