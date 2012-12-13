<?php

class HashAndApiRequestTestCase extends PHPUnit_Framework_TestCase {
    
    protected $clientId;
    protected $secret;
    protected $inputs;

    public function setUp() {
        
        $clients = array(
            'test_client' => 'test_s3cr3t'
        );
        
        Hash::setClients($clients);
        
        $this->clientId = 'test_client';
        $this->secret   = 'test_s3cr3t';
        
        // inputs init
        $this->inputs = array(
            'client_id' => $this->clientId,
            'time'      => (int) strtotime(gmdate('d.m.Y H:i:s'))
        );
    }
    
    public function tearDown() {
        unset($this->inputs);
    }
    
    /**
     * @expectedException HashException
     */
    public function testHashCheckThrowsExceptionWithoutTime() {
        
        $this->inputs['uri'] = 'user';
        unset($this->inputs['time']);
        $hash = Hash::make($this->inputs, $this->secret);
        
        Hash::check($this->inputs, $hash);
    }
    
    /**
     * @expectedException HashException
     */
    public function testHashCheckThrowsExceptionWithoutClient_id() {
        
        $this->inputs['uri'] = 'user';
        unset($this->inputs['client_id']);
        $hash = Hash::make($this->inputs, $this->secret);
        
        Hash::check($this->inputs, $hash);
    }
    
    /**
     * @expectedException HashException
     */
    public function testHashCheckThrowsExceptionWithoutUri() {
        
        $this->inputs['uri'] = 'user';
        unset($this->inputs['uri']);
        $hash = Hash::make($this->inputs, $this->secret);
        
        Hash::check($this->inputs, $hash);
    }
    
    /**
     * @expectedException HashException
     */
    public function testHashCheckThrowsExceptionWithOldTime() {
        
        $this->inputs['uri']  = 'user';
        $this->inputs['time'] = strtotime(gmdate('d.m.Y H:i:s')) - (60 * 30);
        $hash = Hash::make($this->inputs, $this->secret);
        
        Hash::check($this->inputs, $hash);
    }
    
    /**
     * @expectedException HashException
     */
    public function testHashCheckThrowsExceptionWithNonExistClient_id() {
        
        $this->inputs['uri']       = 'user';
        $this->inputs['client_id'] = 'non_exist';
        $hash = Hash::make($this->inputs, $this->secret);
        
        Hash::check($this->inputs, $hash);
    }
    
    public function testHashCheckWithWrongSecret() {
        
        $this->inputs['uri'] = 'user';
        $this->secret        = 'wrong_secret';
        $hash = Hash::make($this->inputs, $this->secret);
        
        $response = Hash::check($this->inputs, $hash);
        $this->assertFalse($response);
    }
    
    
    public function testHashCheckWithValidData() {
        
        $inputs = array_merge($this->inputs, array(
            'uri'       => 'user',
            'name'      => 'Foo',
            'surname'   => 'Bar',
            'email'     => 'api@sp.com',
            'password'  => '12345'
        ));
        
        $hash = Hash::make($inputs, $this->secret);
        $response = Hash::check($inputs, $hash);
        $this->assertTrue($response);
    }
    
    public function testHashCheckWithHash() {
        
        $inputs = array_merge($this->inputs, array(
            'uri'       => 'user',
            'name'      => 'Foo',
            'surname'   => 'Bar',
            'email'     => 'api@sp.com',
            'password'  => '12345'
        ));
        
        $hash = Hash::make($inputs, $this->secret);
        $inputs['hash'] = $hash;
        $response = Hash::check($inputs, $hash);
        $this->assertTrue($response);
    }
    
    /**
     * @expectedException ApiRequestException
     */
    public function testApiRequestGetParamsThrowsExceptionWithReservedParameter() {
        
        $api = new ApiRequest();
        $api->setClientId($this->clientId)->setSecretKey($this->secret);
        
        $data = array(
            'name'      => 'Foo',
            'surname'   => 'Bar',
            'email'     => 'api@sp.com',
            'password'  => '12345',
            'time'      => time() // reserved!
        );
        
        $api->getParams('user', $data);
    }
    
    public function testApiRequestGetParams() {
        
        $api = new ApiRequest();
        $api->setClientId($this->clientId)->setSecretKey($this->secret);
        
        $now  = strtotime(gmdate('d.m.Y H:i:s'));
        $data = array(
            'name'      => 'Foo',
            'surname'   => 'Bar',
            'email'     => 'api@sp.com',
            'password'  => '12345'
        );
        
        $params = $api->getParams('user', $data);
        
        $this->assertEquals("user", $params['uri']);
        $this->assertEquals("Foo", $params['name']);
        $this->assertEquals("api@sp.com", $params['email']);
        $this->assertSame("12345", $params['password']);
        $this->assertEquals($api->getClientId(), $params["client_id"]);
        $this->assertTrue( $params['time'] >= $now );
        $this->assertTrue( isset($params['hash']) );
    }
    
}