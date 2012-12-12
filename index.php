<?php

require_once 'config.php';
require_once 'api.php';

$app->get('/', function () {

    $api = new ApiRequest();
    $api->setClientId('A_CLIENT')
        ->setSecretKey('CLIENT_SECRET_KEY')
        ->setEndpointUrl('http://localhost/2leggedoauth/api/');

    $response1 = $api->make('user/1');
    
    echo '<pre>';
    var_dump($response1);
    echo '</pre>';
    
    // or
    $response2 = $api->make('user', 'POST', array(
        'name'     => 'Aykut',
        'email'    => 'aykutfarsak@gmail.com',
        'password' => 's3cr3t'
    ));

    echo '<pre>';
    var_dump($response2);
    echo '</pre>';
    
    return '';
});

$app->run();