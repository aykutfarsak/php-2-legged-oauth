<?php

use Symfony\Component\HttpFoundation\Request;

$before = function (Request $request) use ($app) {
    
    $all  = array_merge($request->query->all(), $request->request->all());
    $hash = isset($all['hash']) ? $all['hash'] : false;
    
    if ( !$hash ) {
        return $app->json(array('success' => false, 'error' => 'Missing argument: hash'), 500);
    }
    
    if ( !Hash::check($all, $hash) ) {
        return $app->json(array('success' => false, 'error' => 'Invalid hash'), 500);
    }
};

$app->post('/api/user', function () use ($app) {
    
    // ..
    
    $payload = array(
        'success'  => true,
        'user_id'  => 1
    );
    
    return $app->json($payload);
    
})->before($before);

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
    
})->before($before);