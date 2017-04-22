<?php

error_reporting(E_ALL);

require 'config.php';

if(empty($argv[1]) or empty($config['servers'][$argv[1]])) {
    echo "Invalid server config name\r\n";
    exit(1);
}

spl_autoload_register(function($name) {
    require str_replace('\\', '/', strtolower($name)).'.php';
});

date_default_timezone_set($config['timezone']);

require 'classes/log.php';
if(@$config['debug']) Log::setDebug();

$config = $config['servers'][$argv[1]];
require 'classes/server.php';
require 'modules/module.php';

$server = Server::forge($argv[1], $config);
while(true) {
    $server->connect();
    sleep(5);
}