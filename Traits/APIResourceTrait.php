<?php


namespace Porto\Core\Traits;

/**
 * API资源
 *
 * Trait APIResourceTrait
 *
 * @package Porto\Core\Traits
 *
 * author liyq <2847895875@qq.com>
 */
trait APIResourceTrait
{
    /**
     * 默认返回的成员
     * @var array
     */
    protected $defaultFields = ['*'];

    /**
     * 自定义数据
     * @var array
     */
    protected $customData = [];

    /**
     * 默认调用的include
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * 可用的include
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * 获取当前可用的include
     *
     * @return array
     */
    public function getAvailableIncludes(): array {
        return $this->availableIncludes;
    }

    /**
     * 获取当前默认的include
     *
     * @return array
     */
    public function getDefaultIncludes(): array {
        return $this->defaultIncludes;
    }

    /**
     *  设置include
     *
     * @param array $includes
     *
     */
    public function setIncludes(array $includes): void {
        $this->defaultIncludes = array_merge($this->defaultIncludes, $includes);
    }

    /**
     * 获取默认的field
     *
     * @return array
     */
    public function getDefaultFields(): array {
        return $this->defaultFields;
    }


    /**
     * 获取自定义数据
     *
     * @return array
     */
    public function getCustomData(): array {
        return $this->customData;
    }
}