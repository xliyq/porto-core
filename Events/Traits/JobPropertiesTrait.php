<?php


namespace Porto\Core\Events\Traits;


trait JobPropertiesTrait
{

    /**
     * 如果实现了shouldHandle 接口
     * 任务被处理之前的延迟时间（秒）
     *
     *
     * @var  int|null|\DateInterval|\DateTimeInterface $jobDelay
     */
    public $delay;


    /**
     * 如果实现了shouldHandle 接口
     * 任务将被推送到的连接名称.
     *
     * @var string $jobQueue
     */
    public $queue;
}