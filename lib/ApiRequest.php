<?php

class ApiRequest {

    protected $clientId;
    protected $secretKey;
    protected $endPoint;
    
    public function setClientId($id) {
        $this->clientId = $id;
        return $this;
    }
    
    public function setSecretKey($key) {
        $this->secretKey = $key;
        return $this;
    }
    
    public function setEndPoint($url) {
        $this->endPoint = $url;
        return $this;
    }

    public function make($uri, $method = 'GET', $data = array()) {

        $url    = $this->endPoint . $uri;
        $params = $this->getParams($uri, $data);

        return json_decode($this->call($url, $params, strtoupper($method)), true);
    }

    protected function getParams($uri, $data = array()) {

        if (!empty($data)) {
            foreach (array('uri', 'client_id', 'time', 'hash') as $arg) {
                if (array_key_exists($arg, $data)) {
                    throw new Exception("You can't use '$arg' parameter, it is reserved");
                }
            }
        }

        $data = array_merge($data, array(
            'uri'       => $uri,
            'client_id' => $this->clientId,
            'time'      => strtotime(gmdate('d.m.Y H:i:s')),
        ));

        $hash = Hash::make($data, $this->secretKey);

        return array_merge($data, array('hash' => $hash));
    }

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

        if ($output === false) {
            throw new Exception(curl_errno($ch));
        }

        curl_close($ch);

        return $output;
    }

}