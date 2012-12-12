<?php

date_default_timezone_set('Europe/Istanbul');

$path = realpath(dirname(__FILE__)).'/';
require_once $path . 'vendor/autoload.php';
require_once 'lib/Hash.php';
require_once 'lib/ApiRequest.php';

// Silex
$app = new Silex\Application();
$app['debug'] = true;

// API Clients
Hash::setClients(array(
    'A_CLIENT' => 'CLIENT_SECRET_KEY',
));