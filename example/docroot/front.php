<?php

include_once(__DIR__."/../bootstrap.php");

if (preg_match('|^/assets/(.+)/(.+)$|', $_SERVER['REQUEST_URI'], $matches)) {
    return false;
}

if ($container['config/devVersion']) {
    error_log('');
    error_log('vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv');
    error_log('');
}



$response = $container['response'];
$emitter = $container['responseEmitter'];
$emitter->emit($response);
