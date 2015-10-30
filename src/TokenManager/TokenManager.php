<?php 
/*
 * This file is part of the TokenManager package.
 *
 * (c) Gregory Brousse <pro@gregory-brousse.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace TokenManager;

/**
 * TokenManager class
 *
 * manage file tokens.
 *
 * @author Gregory Brousse <pro@gregory-brousse.fr>
 */
class TokenManager{
    
    /**
     * Directory where the tokens are stored.
     * This directory must be innaccessible from outside (add an .htaccess)
     * @var string
     */
    private $dir = './'; 
    
    /**
     * Prefix for the filename.
     * !!! no specialchar except '-' or '_'
     * @var string
     */
    private $prefix = 'token_'; 
    
    /**
     * String used to salt the token
     * @var string
     */
    private $salt = '123456789';
    
    /**
     * Type of hash used for the token
     * @var string
     */
    private $hash = 'sha256';
    
    /**
     * Maximum time the token will be alive
     * @var integer
     */
    private $maxTimeout = 7200;
    
    /**
     * Minimum time the token will be alive
     * @var integer
     */
    private $minTimeout = 3600;
    
    /**
     * Last error
     * @var string
     */
    private $lastError = '';
    
    /**
     * Constructor of the class.
     * @param array $options
     */
    public function __construct($options){
        /* set attributs */
        foreach($options as $option => $value){
            switch ($option) {
                case 'dir':
                    /* test if the tokendir is valid */
                    if(!is_dir($value)) $this->lastError = 'Token directory is not a valid directory';
                    /* if dir not finish with a /, we add it */
                    $this->dir = $value.((substr($value, -1)=='/')?'':'/');
                    break;
                case 'prefix':
                    /* test if prefix contain special char */
                    if (preg_match('/[^a-zA-Z0-9\-_]+/', $value, $matches)) $this->lastError = 'Prefix must not contain special chars';
                    $this->prefix = $value;
                    break;
                case 'salt':
                    /* test if salt is a string */
                    if(!is_string($value)) $this->lastError = 'Salt must be a string';
                    $this->salt = $value;
                    break;
                case 'hash':
                    /* test if hash exists */
                    if(!in_array($value, hash_algos())) $this->lastError = 'Hash is not valid';
                    $this->hash = $value;
                    break;
                case 'maxTimeout':
                case 'minTimeout':
                    /* test if timeout is an integer */
                    if(!is_integer($value)) $this->lastError = 'Timeout must be an integer';
                    $this->$option = $value;
                    break;
                default:
                    break;
            }
        }   
    }
    
    /**
     * Create a token.
     * @return string the token created
     */
    private function create(){
        /* if no error before */
        if($this->lastError!='')return false;
		
        /* Generate the token */
        $token = hash($this->hash, $this->salt.time());
        
        /* Write the token */
        file_put_contents($this->dir.$this->prefix.$token,'');
        if(!file_exists($this->dir.$this->prefix.$token))return $this->altOnError('Token write error');
        
        /* return the created token */
        return $token;   
    }
    
    /**
     * Get an existing token.
     * @return string|boolean the found token or false
     */
    private function getExistingValid(){
        /* if no error before */
        if($this->lastError!='')return false;
        
        /* search for a token */
        $tokenDir = dir($this->dir);
        while(false !==($token = $tokenDir->read())){
            if($this->isTokenFile($token)){
                $tokenFile = $this->dir.$token;
                $tokenTime = time()-filemtime($tokenFile);
                /* if token is valid */
                if($tokenTime < ($this->maxTimeout - $this->minTimeout)){
                    return $this->getTokenFromFile($token);
                }
            }
        }
        return false;
    }
    
    /**
     * Get an existing token or create one. 
     * @return string|boolean the token or false
     */
    public function get(){
            /* look for an existing token */
            if($token = $this->getExistingValid()){
                    return $token;
            }
            /* if none is found, create one */
            return $this->create();
    }
    
    /**
     * Test if a token is valid 
     * @return boolean 
     */
    public function isValid($token){
        $tokenFile = $this->dir.$this->prefix.$token;
		
        /* test if token file exist */
        if(!file_exists($tokenFile))return $this->altOnError('Token not found');
        
        /* test if token is still valid */
        $tokenTime = time()-filemtime($tokenFile);
        if($tokenTime > $this->maxTimeout){
            /* the token is outdated, delete it */
            unlink($tokenFile);
            return $this->altOnError('Token timed out');
        }
        
        return true;
    }
    
    /**
     * Utility function used to clean the old tokens
     * @return integer number of deleted token 
     */
    public function clean(){
        $suppTokens = 0;
        $tokenDir = dir($this->dir);
        /* for each token found */
        while(false !==($token = $tokenDir->read())){
            if($this->isTokenFile($token)){
                $tokenFile = $this->dir.$token;
                $tokenTime = time()-filemtime($tokenFile);
                /* if token is timed out, delete it */
                if($tokenTime > $this->maxTimeout){
                    unlink($tokenFile);
                    $suppTokens++;
                }
            }
        }
		return $suppTokens;
    }
    
    /**
     * Utility function used to test if file is a token file
     * @return boolean
     */
    private function isTokenFile($fileName){
        if(strstr($fileName,$this->prefix))return true;
        return false;
    }
    
    /**
     * Utility function used to get token in a token file name
     * @return string
     */
    private function getTokenFromFile($filename){
        return str_replace($this->prefix, '', $filename);
    }
    
    /**
     * Utility function that set error and return false
     * @return boolean
     */
    private function altOnError($error){
            $this->lastError = $error;
            return false;
    }
    
    /**
     * Get the last error
     * @return boolean|string
     */
    public function getLastError(){
            return ($this->lastError == '')?false:$this->lastError;
    }
    
}

?>