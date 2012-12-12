<?php

class HashException extends Exception {}

class Hash {
    
    /**
     * All permitted clients with secret key
     * 
     * @var array
     */
    public static $clients = array();
    
    /**
     * Hash algorithm
     * 
     * @var string
     */
    public static $algorithm = 'sha256';
    
    /**
     * Timeout value (second) to check if request is timeout or not
     * It is necessary for repeat attacks.
     * 
     * @var int
     */
    public static $timeout = 120;
    
    /**
     * Set the permitted clients.
     * 
     * Array keys represent client id and
     * values represent secret key.
     * 
     * @param array $clients
     */
    public static function setClients(array $clients) {
        self::$clients = $clients;
    }
    
    /**
     * Make a hash with all input data with your secret key
     * 
     * @param array $inputData
     * @param string $secret
     * @return string
     */
    public static function make(array $inputData, $secret) {
        $json = json_encode($inputData);
        return hash_hmac(self::$algorithm, $json, $secret);
    }
    
    /**
     * Check hash if it is valid or not
     * 
     * @param array $inputData
     * @param string $hash
     * @return boolean
     * @throws HashException
     */
    public static function check(array $inputData, $hash) {
        
        // all required argument have to be in input data
        $requiredArguments = array('client_id', 'uri', 'time');
        foreach ($requiredArguments as $value) {
            if ( !isset($inputData[$value]) ) {
                throw new HashException("Missing argument: $value");
            }
        }
        
        // remove hash key
        if ( isset($inputData['hash']) ) {
            unset($inputData['hash']);
        }
        
        // check request time and server time for timeout
        $inputData['time'] = (int) $inputData['time'];
        $currentTime       = strtotime(gmdate('d.m.Y H:i:s'));
        
        if ( $currentTime > ($inputData['time'] + self::$timeout) ) {
            throw new HashException("Request timeout (current timestamp is $currentTime, request time is ".$inputData['time']);
        }
        
        // get secret from client_id
        $clientId = $inputData['client_id'];
        
        if ( !array_key_exists($clientId, self::$clients) ) {
            throw new HashException("Invalid client_id");
        }
        
        // compare our hash and request hash
        return self::make($inputData, self::$clients[$clientId]) === $hash;
    }
    
}