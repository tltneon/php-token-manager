<?php
require '../vendor/autoload.php';

use TokenManager\TokenManager; 

if(isset($_GET['token'])){
    // Setup
    $options = array(
        'dir' => './tokens/',
        'prefix' => 'token',
        'salt' => 'salt',
        'hash' => 'sha256', // hash use to generate token
        'maxTimeout' => '120', //max lifetime for a token
        'maxTimeout' => '30', //min lifetime for a token
    ); 
    $TokenMgr = new TokenManager($options);

    // Verify token
    if($TokenMgr->isValid($_GET['token'])){
        header('Content-type: text/txt');
        header('Content-Disposition: attachment; filename="test.txt"');
        echo file_get_contents('./test.txt');
    }else{
        echo 'Error token not valid';
    }
}else{
    echo 'Error token missing';
}
?>
