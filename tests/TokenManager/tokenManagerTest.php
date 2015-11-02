<?php

use TokenManager\TokenManager;

class tokenManagerTest extends PHPUnit_Framework_TestCase{
    private $tokenDirPath = './token/';
    
    public function __construct($name = null, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        mkdir($this->tokenDirPath);
    }
    
    public function __destruct() {
        $this->cleanTokenDir($this->tokenDirPath);
        rmdir($this->tokenDirPath);
    }
    
    /**
     * @covers TokenManager/TokenManager::__construct
     */
    public function testTokenManagerConstructor()
    {
        
        $options = array(
             'dir' => array(
                'true'=>  $this->tokenDirPath,
                'false'=>'./123456/'
            ),
            'prefix' => array(
                'true'=>'string',
                'false'=>'#string!'
            ),
            'salt' => array(
                'true'=>'string',
                'false'=>123456
            ),
            'hash' => array(
                'true'=>'md5',
                'false'=>'testhash'
            ),
            'minTimeout' => array(
                'true'=>1234,
                'false'=>'test'
            ),
            'maxTimeout' => array(
                'true'=>1234,
                'false'=>'test'
            )   
        );
        
        foreach ($options as $option => $value) {
            
            $rightTokenManager = new TokenManager(array($option=>$value['true']));
            $this->assertFalse($rightTokenManager->getLastError(),$rightTokenManager->getLastError());
            unset($rightTokenManager);
            
            
            $wrongTokenManager = new TokenManager(array($option=>$value['false']));
            $this->assertTrue(is_string($wrongTokenManager->getLastError()),$wrongTokenManager->getLastError());
            unset($wrongTokenManager);
        }
    }
    
    /**
     * @covers TokenManager/TokenManager::get
     * @covers TokenManager/TokenManager::create
     * @covers TokenManager/TokenManager::getExistingValid
     */
    public function testTokenManagerGet(){
        $options = array(
            'dir' => $this->tokenDirPath,
            'prefix' => '1234'
        );
        $this->cleanTokenDir($options['dir']);
        $tokenManager = new TokenManager($options);
        $token = $tokenManager->get();
        $this->assertTrue(is_string($token),$tokenManager->getLastError());
        $tokenFile = $options['dir'].$options['prefix'].$token;
        $this->assertTrue(file_exists($tokenFile));
        $token2 = $tokenManager->get();
        $this->assertEquals($token2, $token);
        $this->cleanTokenDir($options['dir']);
    }
    
    /**
     * @covers TokenManager/TokenManager::clean
     */
    public function testTokenManagerClean(){
        $options = array(
            'dir' =>  $this->tokenDirPath,
            'prefix' => '1234',
            'minTimeout'=>1,
            'maxTimeout'=>1
        );
        $this->cleanTokenDir($options['dir']);
        $tokenManager = new TokenManager($options);
        $token = $tokenManager->get();
        $tokenFile = $options['dir'].$options['prefix'].$token;
        sleep(2);
        $nb = $tokenManager->clean();
        $this->assertEquals(1, $nb);
        $this->assertFalse(file_exists($tokenFile));
    }
    
    private function cleanTokenDir($path){
        $files = glob($path.'*'); // get all file names
        foreach($files as $file){ // iterate files
          if(is_file($file))
            unlink($file); // delete file
        }
    }
}
