<?php
require '../src/TokenManager/TokenManager.php';

use TokenManager\TokenManager; 

// Setup
$options = array(
    'dir' => './tokens/',
    'prefix' => 'token',
    'salt' => 'salt',
    'hash' => 'sha256', // hash use to generate token
    'maxTimeout' => 120, //max lifetime for a token
    'maxTimeout' => 30, //min lifetime for a token
); 
$TokenMgr = new TokenManager($options);
     
// Get token
$token = $TokenMgr->get();
?>

<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>php-token-manager : get</title>
  <meta name="description" content="php-token-manager : get">
  <meta name="author" content="Gregory Brousse">
</head>

<body>
    <a href="./usage-verify.php?token=<?php echo $token ?>">Test with right token</a><br/>
    <a href="./usage-verify.php?token=123456">Test with wrong token</a><br/>
    <a href="./usage-verify.php">Test with no token</a><br/>
    <br/>
    Last error : <?php echo $TokenMgr->getLastError(); ?> 
    
</body>
</html>
