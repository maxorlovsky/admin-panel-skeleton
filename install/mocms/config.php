<?php
//=====================================================
// SlimPHP config
//=====================================================
$config = array(
    'displayErrorDetails' => true,
    'addContentLengthHeader' => false,
    'determineRouteBeforeAppMiddleware' => true,
);

// Dev/Test DB
$config['tdb'] = array(
    'host'  => '',
    'user'  => '',
    'pass'  => '',
    'dbname'=> ''
);

// Prod DB
$config['db'] = array(
    'host'  => '',
    'user'  => '',
    'pass'  => '',
    'dbname'=> ''
);

// Used for re-captcha secret
$config['captchaSecret'] = '';

// CORS allowance, add domain HOSTS
$config['cors_domains'] = ["*"];

// URL where panel is
$config['url_to_panel'] = '/';

// Available logins before promting recaptcha / IP block for 5 minutes
$config['availableLoginAttempts'] = 5;