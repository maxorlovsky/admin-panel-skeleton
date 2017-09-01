<?php
//=====================================================
// SlimPHP config
//=====================================================
$config = array(
    'displayErrorDetails' => false,
    'addContentLengthHeader' => false,
    'determineRouteBeforeAppMiddleware' => true,
);

// Dev/Test DB - pentaclick_dev
$config['tdb'] = array(
    'host'  => '',
    'user'  => '',
    'pass'  => '',
    'dbname'=> ''
);

// Prod DB - petnaclick_prod
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
$config['url_to_panel'] = '/admin';

// Available logins before promting recaptcha / IP block for 5 minutes
$config['availableLoginAttempts'] = 5;

// Needed for Language functionality (to add/delete)
// Add new language table fields here
$cfg['ud_alter'] = array(
	array('tm_strings', ''),
	array('tm_pages', ''),
);