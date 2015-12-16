<?php
//*===================================================*//
//*CMS TheMages Configuration file					  *//
//*===================================================*//

//=====================================================
// Making some defines for easyer coding (main)
$cfg['dir'] = $_SERVER['DOCUMENT_ROOT'].'/web';
$cfg['cmsdir'] = dirname(__DIR__);
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
$cfg['logs'] = 0;
$cfg['allowedLanguages'] = array('en', 'ru');
$cfg['defaultLanguage'] = 'en';
$cfg['availableLoginAttempts'] = 5;
$cfg['recaptchaSiteKey'] = '';
$cfg['recaptchaSecretKey'] = '';
$cfg['demo'] = 0;
$cfg['allowUpload'] = 1;

//Url for TheMages API, required mostly to get changelog file inside of CMS
//It's also used for external logs, but I don't know if people need it and if this should be improved, for now leaving as I'm using it
//Clients usually did stupid stuff in the past and told me that they "didn't do it". For this case you usually needed external logs.
$cfg['apiUrl'] = 'https://api.themages.net';

// Needed for Language functionality (to add/delete)
// Add new language table fields here
$cfg['ud_alter'] = array(
	array('tm_strings', ''),
	array('tm_pages', ''),
);

//CMS cronjob safe value
//It's used for cleanup of SQL stuff, like logs and authorization hashes
$cfg['cmscronjob'] = 'g0394gj0394kg';

//Classes CMS folder
$cfg['cmsclasses'] = $cfg['cmsdir'].'/classes';
//Template CMS folder
$cfg['cmstemplate'] = $cfg['cmsdir'].'/template';
//Lib CMS folder
$cfg['cmslib'] = $cfg['cmsdir'].'/lib';
//Locale CMS folder
$cfg['cmslocale'] = $cfg['cmsdir'].'/locale';
//Modules CMS folder
$cfg['cmsmodules'] = $cfg['cmsdir'].'/modules';
//Inc CMS folder
$cfg['cmsinc'] = $cfg['cmsdir'].'/inc';

//Directory where CMS will be updated, if possible don't use /cms/ or /admin/
$cfg['pathToDirectory'] = 'admin';