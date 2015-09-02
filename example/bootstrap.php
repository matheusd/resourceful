<?php

// use Composer autoloader
if (!@include(__DIR__.'/../vendor/autoload.php')) { 
    die('Could not find Composer autoloader');
}


//load the project's autoloader
if (!@include(__DIR__.'/modules/autoload.php')) { 
    die('Could not find the project autoloader');
}

require("config/Di.php");

$container = new Pimple\Container();

$provider = new WebAppDIProvider();
$provider->register($container);
