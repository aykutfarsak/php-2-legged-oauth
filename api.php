<?php

require 'Hash.php';

$inputData = $_REQUEST;
$payload   = array();

header('Content-Type: application/json');

try {
    
    if ( !isset($inputData['hash']) ) {
        throw new Exception('Missing argument: hash');
    }

    if ( !Hash::check($inputData, $inputData['hash']) ) {
        throw new Exception('Invalid hash');
    }
    
    // response body..
    
    $payload['success'] = true;

} catch (Exception $e) {
    $payload['success'] = false;
    $payload['error']   = $e->getMessage(); 
}

echo json_encode($payload);

