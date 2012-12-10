<?php

class Hash {
    
    public static $clients   = array(
        '12345' => '6429f859f57f376086a3c22eb20015ce',
    );
    public static $algorithm = 'sha256';
    public static $timeout   = 120; // second
    
    public static function make(array $data, $secret) {
        $json = json_encode($data);
        return hash_hmac(self::$algorithm, $json, $secret);
    }
    
    public static function check(array $data, $hash) {
        
        // all required argument have to be in input data
        $requiredArguments = array('client_id', 'uri', 'time');
        foreach ($requiredArguments as $value) {
            if ( !isset($data[$value]) ) {
                throw new Exception("Missing argument: $value");
            }
        }
        
        // remove hash from input data
        if ( isset($data['hash']) ) {
            unset($data['hash']);
        }
        
        // check timeout
        $data['time'] = (int) $data['time'];
        $currentTime  = strtotime(gmdate('d.m.Y H:i:s'));
        
        if ( $currentTime > ($data['time'] + self::$timeout) ) {
            throw new Exception("Request timeout (current timestamp is $currentTime, request time is ".$data['time']);
        }
        
        // get secret from client_id
        $id = $data['client_id'];
        
        if ( !array_key_exists($id, self::$clients) ) {
            throw new Exception("Invalid client_id");
        }
        
        return self::make($data, self::$clients[$id]) === $hash;
    }
    
}