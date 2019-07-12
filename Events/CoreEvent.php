<?php


namespace Porto\Core\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class CoreEvent
{


    use Dispatchable;

    // 广播
    use InteractsWithSockets;
    // 序列化
    use SerializesModels;

}