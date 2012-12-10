<?php

require 'Hash.php';
require 'ApiRequest.php';

$api = new ApiRequest();
$api->setClientId('YOUR_CLIENT_ID')
    ->setSecretKey('YOUR_SECRET_KEY')
    ->setEndPoint('YOUR_API_ENDPOINT');

$response = $api->make('user/1');
// or
$response = $api->make('user', 'POST', array(
    'name'     => 'Aykut',
    'email'    => 'aykutfarsak@gmail.com',
    'password' => 's3cr3t'
));

echo '<pre>';
var_dump($response);