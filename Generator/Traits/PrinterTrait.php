<?php


namespace Porto\Core\Generator\Traits;


trait PrinterTrait
{
    public function printStartedMessage($containerName, $fileName) {
        $this->printInfoMessage("> Generating ( $fileName ) in ( $containerName ) Container");
    }

    public function printFinishedMessage($type) {
        $this->printInfoMessage($type . '生成成功');
    }

    public function printErrorMessage($message) {
        $this->error($message);
    }

    public function printInfoMessage($message) {
        $this->info($message);
    }
}
