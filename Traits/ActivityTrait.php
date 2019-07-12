<?php


namespace Porto\Core\Traits;

use Spatie\Activitylog\Traits\LogsActivity;

trait ActivityTrait
{
    // 使用操作日志
    use LogsActivity;

    //自定义日志名称
    protected static $logName = 'system';

    //记录模型中在 $fillable 属性的更改
    protected static $logFillable = true;
    //记录模型中不在 $guarded 中属性的更改
    protected static $logUnguarded = true;

    //忽略属性的更改
    protected static $ignoreChangedAttributes = [];

    // 仅记录更改的数据
    protected static $logOnlyDirty = true;

    //防止保存没有更改属性的日志项
    protected static $submitEmptyLogs = false;
}