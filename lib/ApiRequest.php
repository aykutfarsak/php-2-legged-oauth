<?php

class ApiRequestException extends Exception {}

class ApiRequest {

    /**
     * API client id
     * 
     * @var mixed 
     */
    protected $clientId;
    
    /**
     * API secret key
     * 
     * @var string 
     */
    protected $secretKey;
    
    /**
     * API endpoint URL
     * 
     * @var type 
     */
    protected $endPoint;
    
    /**
     * Set client id
     * 
     * @param mixed $id
     * @return ApiRequest
     */
    public function setClientId($id) {
        $this->clientId = $id;
        return $this;
    }
    
    /**
     * Set secret key
     * 
     * @param string $key
     * @return ApiRequest
     */
    public function setSecretKey($key) {
        $this->secretKey = $key;
        return $this;
    }
    
    /**
     * Set API endpoint URL
     * 
     * @param string $url
     * @return ApiRequest
     */
    public function setEndpointUrl($url) {
        $this->endPoint = $url;
        return $this;
    }

    /**
     * Make API call with all input data
     * 
     * @param string $uri
     * @param string $method
     * @param array $data
     * @return string
     */
    public function make($uri, $method = 'GET', $data = array()) {

        $url    = $this->endPoint . $uri;
        $params = $this->getParams($uri, $data);

        return json_decode($this->call($url, $params, strtoupper($method)), true);
    }

    /**
     * Get prepared inputs with hash
     * 
     * @param type $uri
     * @param type $data
     * @return array
     * @throws ApiRequestException
     */
    protected function getParams($uri, $data = array()) {

        // check for reserved keys
        if (!empty($data)) {
            foreach (array('uri', 'client_id', 'time', 'hash') as $arg) {
                if (array_key_exists($arg, $data)) {
                    throw new ApiRequestException("You can't use '$arg' parameter, it is reserved");
                }
            }
        }

        // merge input data and generated parameters
        $data = array_merge($data, array(
            'uri'       => $uri,
            'client_id' => $this->clientId,
            'time'      => strtotime(gmdate('d.m.Y H:i:s')),
        ));

        $hash = Hash::make($data, $this->secretKey);

        // return merged parameters with hash
        return array_merge($data, array('hash' => $hash));
    }

    /**
     * Make cURL call
     * 
     * @param string $url
     * @param array $params
     * @param string $method
     * @return string
     * @throws ApiRequestException
     */
    protected function call($url, array $params, $method = 'POST') {

        $ch = curl_init();
        
        if ($method == 'GET') {
            $url .= '?' . http_build_query($params);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        $output = curl_exec($ch);

        if ($output === false || curl_errno($ch)) {
            throw new ApiRequestException('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        return $output;
    }

}