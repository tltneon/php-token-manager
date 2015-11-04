# php-token-manager [![Build Status](https://travis-ci.org/gbrousse/php-token-manager.svg?branch=master)](https://travis-ci.org/gbrousse/php-token-manager)  [![Coverage Status](https://coveralls.io/repos/gbrousse/php-token-manager/badge.svg?branch=master&service=github)](https://coveralls.io/github/gbrousse/php-token-manager?branch=master)

[![Total Downloads](https://img.shields.io/packagist/dt/gbrousse/php-token-manager.svg)](https://packagist.org/packages/gbrousse/php-token-manager)
[![Latest Stable Version](https://img.shields.io/packagist/v/gbrousse/php-token-manager.svg)](https://packagist.org/packages/gbrousse/php-token-manager)

Secure your web ressources (files, images, streams, ...) with simple token system.

## Installation

Install the latest version with

```bash
$ composer require gbrousse/php-token-manager
```

## Basic usage

### get a token 
```php
<?php

use TokenManager\TokenManager; 

// Setup
$options = array(
    'dir' => 'directory/where/stock/tokens',
    'prefix' => 'prefix_of_tokens_files',
    'salt' => 'salt',
    'hash' => 'md5', // hash use to generate token
    'maxTimeout' => 7200, //max lifetime for a token
    'maxTimeout' => 600, //min lifetime for a token
); 
$TokenMgr = new TokenManager($options);
     
// Get token
$token = $TokenMgr->get();

```
If you use a single configuration for the tokens, modify the attributes of the class instead of using options array.

### Verify a token
```php
<?php

use TokenManager\TokenManager; 


// Setup
$options = array(
    'dir' => 'directory/where/stock/tokens',
    'prefix' => 'prefix_of_tokens_files',
    'salt' => 'salt',
    'hash' => 'md5', // hash use to generate token
    'maxTimeout' => 7200, //max lifetime for a token
    'maxTimeout' => 600, //min lifetime for a token
); 
$TokenMgr = new TokenManager($options);
     
// Verify token validity
if($TokenMgr->isValid($token)){
    // action to do if token is OK
} 

```
If you use a single configuration for the tokens, modify the attributes of the class instead of using options array.


## Examples

- [Get a token](examples/usage-get.php)
- [Verify a token](examples/usage-isvalid.php)


## About

### Requirements

- php-token-manager works with PHP 5.3 or above.

### Submitting bugs and feature requests

Bugs and feature request are tracked on [GitHub](https://github.com/gbrousse/php-token-manager/issues)

### Author

Gregory Brousse - <pro@gregory-brousse.fr> - <http://gregory-brousse.fr>

### License

php-token-manager is licensed under the LGPL-3.0 License - see the `LICENSE` file for details
