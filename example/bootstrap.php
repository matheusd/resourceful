<?php

// use Composer autoloader
if (!@include(__DIR__.'/../vendor/autoload.php')) { 
    die('Could not find Composer autoloader');
}

// manually add Resourceful's autoloader (if using resourceful from composer,
// this would be ignored)
if (!@include(__DIR__.'/../src/Resourceful/autoload.php')) { 
    die('Could not find Resourceful autoloader');
}

//load the project's autoloader
if (!@include(__DIR__.'/modules/autoload.php')) { 
    die('Could not find the project autoloader');
}

//FIXME: change to autoload
require("config/Di.php");
//require("Hello.php");
//require("Templater.php");
//require("orm/Product.php");

$container = new Pimple\Container();

$provider = new WebAppDIProvider();
$provider->register($container);
