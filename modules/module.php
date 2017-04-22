<?php

namespace Modules;

abstract class Module {
    protected $options;
    protected $server;
    public final function __construct(\Server $server, $options) {
        $this->server = $server;
        $this->options = $options;
        $this->init();
    }

    protected function init() {}

    public function onPing(){}
    public function onMessage($nick, $target, $message) {}

    protected final function sendNotice($target, $message) {
        $this->server->sendNotice($target, $message);
    }
}