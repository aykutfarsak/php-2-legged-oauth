<?php

use Symfony\Component\HttpFoundation\Request;

// before middleware
$hashValidation = function (Request $request) use ($app) {
    
    $allInputs = array_merge($request->query->all(), $request->request->all());
    $hash      = $request->get('hash');
    
    try {
    
        if ( !$hash ) {
            throw new Exception('Missing argument: hash');
        }
        
        if ( !Hash::check($allInputs, $hash) ) {
            throw new Exception('Invalid hash');
        }
    
    } catch (Exception $e) {
        return $app->json(array('success' => false, 'error' => $e->getMessage()), 500);
    }
};

// save a user
$app->post('/api/user', function () use ($app) {
    
    // ..
    
    $payload = array(
        'success'  => true,
        'user_id'  => 1
    );
    
    return $app->json($payload);
    
})->before($hashValidation);

// get a user info
$app->get('/api/user/{id}', function ($id) use ($app) {
    
    // ..
    
    $user = array(
        'success' => true,
        'user'    => array(
            'id'      => $id,
            'name'    => 'Aykut Farsak',
            'email'   => 'aykutfarsak@gmail.com'
        )
    );
    
    return $app->json($user);
    
})->before($hashValidation);