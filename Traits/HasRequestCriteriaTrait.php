<?php


namespace Porto\Core\Traits;

use Porto\Core\Abstracts\Repositories\CoreRepository;
use Porto\Core\Exceptions\CoreInternalErrorException;
use Prettus\Repository\Criteria\RequestCriteria;


trait HasRequestCriteriaTrait
{
    /**
     * 添加请求中的查询条件
     *
     * @param null $repository
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function addRequestCriteria($repository = null) {
        $validatedRepository = $this->validateRepository($repository);
        $validatedRepository->pushCriteria(app(RequestCriteria::class));
    }

    public function removeRequestCriteria($repository = null) {
        $validatedRepository = $this->validateRepository($repository);
        $validatedRepository->popCriteria(RequestCriteria::class);
    }

    /**
     * @param $repository
     *
     * @return CoreRepository;
     * @throws CoreInternalErrorException
     */
    private function validateRepository($repository) {
        $validateRepository = $repository;
        if (null === $repository) {
            //检查是否定义repository
            if (!isset($this->repository)) {
                throw new CoreInternalErrorException('repository 不是protected 或者 public 无法访问');
            }
            $validateRepository = $this->repository;
        }

        // 检查是否为空
        if (null === $validateRepository) {
            throw new CoreInternalErrorException('No protected or public accessible repository available');
        }

        // 检查是否为CoreRepository实体
        if (!($validateRepository instanceof CoreRepository)) {
            throw new CoreInternalErrorException();
        }


        return $validateRepository;
    }
}