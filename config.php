<?php

date_default_timezone_set('Europe/Istanbul');

$path = realpath(dirname(__FILE__)).'/';
require_once $path . 'vendor/autoload.php';
require_once 'lib/Hash.php';
require_once 'lib/ApiRequest.php';

// silex
$app = new Silex\Application();
$app['debug'] = true;