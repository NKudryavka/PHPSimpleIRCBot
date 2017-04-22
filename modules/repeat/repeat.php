<?php

namespace Modules\Repeat;
use \Modules\Module;

class Repeat extends Module {
    public function onMessage($nick, $target, $message) {
        $this->sendNotice($target, $this->options['prefix'].'['.$nick.':'.$message.']');
    }
}