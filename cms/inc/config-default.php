<?php
//*===================================================*//
//*CMS TheMages Configuration file					  *//
//*===================================================*//

//=====================================================
// Making some defines for easyer coding (main)
$cfg['dir'] = $_SERVER['DOCUMENT_ROOT'].'/web';
$cfg['cmsdir'] = dirname(__DIR__);
date_default_timezone_set('UTC');
//=====================================================

//=====================================================
// Defining environment
$breakDown = explode('.', $_SERVER['HTTP_HOST']);
if ($breakDown[0] == 'dev') { //Development environment
    $cfg['env'] = 'dev';
}
else if ($breakDown[0] == 'test' || $breakDown[1] == 'test') { //Test environment
    $cfg['env'] = 'test';
}
else {
    //This is where CMS go live
    $cfg['env'] = 'prod';
}

// Defining main default variables
// DB config
$cfg['dbHost'] ='';
$cfg['dbBase'] ='';
$cfg['dbUser'] ='';
$cfg['dbPass'] ='';
$cfg['dbPort'] =3306;

//Admin email (in case of errors)
$cfg['adminEmail'] = '';
$cfg['site'] = '';

// SMTP config
$cfg['smtpMailName'] = '';
$cfg['smtpMailPort'] = '';
$cfg['smtpMailHost'] = '';
$cfg['smtpMailPass'] = '';
$cfg['smtpMailFrom'] = '';

//Additional variables
$cfg['maxLevel'] = 4;
$cfg['logs'] = 1;
$cfg['allowedLanguages'] = array('en', 'ru');
$cfg['defaultLanguage'] = 'en';
$cfg['availableLoginAttempts'] = 5;
$cfg['recaptchaSiteKey'] = '';
$cfg['recaptchaSecretKey'] = '';
$cfg['demo'] = 0;
$cfg['allowUpload'] = 1;

// Needed for Language functionality (to add/delete)
// Add new language table fields here
$cfg['ud_alter'] = array(
	array('tm_strings', ''),
	array('tm_pages', ''),
);