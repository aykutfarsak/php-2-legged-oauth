<?php

require 'Hash.php';
require 'ApiRequest.php';

$api = new ApiRequest();
$api->setClientId('12345')
    ->setSecretKey('6429f859f57f376086a3c22eb20015ce')
    ->setEndPoint('http://localhost/2leggedoauth/api.php');

$response = $api->make('user/1');
// or
$response = $api->make('user', 'POST', array(
    'name'     => 'Aykut',
    'email'    => 'aykutfarsak@gmail.com',
    'password' => 's3cr3t'
));

echo '<pre>';
var_dump($response);