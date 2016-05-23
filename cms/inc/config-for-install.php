<?php
//=====================================================
// Site config
//=====================================================

//Default timezone, self explanatory, this is the default timezone for website
//I think it's logical to set it to UTC and then conver however you need
date_default_timezone_set('UTC');

//Checking if cloudflare used with https or if https is available
if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])){
    $cfg['protocol'] = $_SERVER['HTTP_X_FORWARDED_PROTO'];
}
else{
    $cfg['protocol'] = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
}

//Getting on what environment we on
//Normaly while developing you have 3-4 environments
//dev - for local stuff
//staging - for alpha/inner test
//beta - for client/external test
//live - for all usage, you can completely cleanup this config and leave only 1 or 2 environment.
switch ( $cfg['env'] )
{
    case 'prod':
    case 'live':
    	//Database configuration
        $cfg['dbHost'] =''; //Host
        $cfg['dbBase'] =''; //Database name
        $cfg['dbUser'] =''; //Database user
        $cfg['dbPass'] =''; //Database password
        $cfg['dbPort'] =3306; //Database port
        
        //Admin email (in case of errors and can be used for more)
        $cfg['adminEmail'] = '';

        //Website URL, sometimes required to not get into link problems
        $cfg['site'] = '';
        
        // SMTP config
        $cfg['smtpMailName'] = ''; //Nickname for SMTP authentication, it depends on SMTP provider what login you have, it's either email or just a nickname
        $cfg['smtpMailPort'] = ''; //Port for SMTP port (465/587/25)
        $cfg['smtpMailHost'] = ''; //SMTP hostname, link to their system
        $cfg['smtpMailPass'] = ''; //Password for SMTP authentication
        $cfg['smtpMailFrom'] = ''; //Additional field for email headers, you might want to name it info@your.site, but real email who send's it will be info-was-take@your.site
        
        //It's usually a good idea to disable display of errors on live website and send emails in case of emergency
        ini_set('display_errors', 0);
    break;
    case 'test':
    case 'beta':
    case 'staging':
        $cfg['dbHost'] =''; //Host
        $cfg['dbBase'] =''; //Database name
        $cfg['dbUser'] =''; //Database user
        $cfg['dbPass'] =''; //Database password
        $cfg['dbPort'] =3306; //Database port

        //Admin email (in case of errors and can be used for more)
        $cfg['adminEmail'] = '';

        //Website URL, sometimes required to not get into link problems
        $cfg['site'] = '';
        
        // SMTP config
        $cfg['smtpMailName'] = ''; //Nickname for SMTP authentication, it depends on SMTP provider what login you have, it's either email or just a nickname
        $cfg['smtpMailPort'] = ''; //Port for SMTP port (465/587/25)
        $cfg['smtpMailHost'] = ''; //SMTP hostname, link to their system
        $cfg['smtpMailPass'] = ''; //Password for SMTP authentication
        $cfg['smtpMailFrom'] = ''; //Additional field for email headers, you might want to name it info@your.site, but real email who send's it will be info-was-take@your.site
        
        //Last line of (defense) testing, should display everything to not hit live
        ini_set('display_errors', 1);
        ini_set('error_reporting', E_ALL);
    break;
    case 'dev':
        $cfg['dbHost'] =''; //Host
        $cfg['dbBase'] =''; //Database name
        $cfg['dbUser'] =''; //Database user
        $cfg['dbPass'] =''; //Database password
        $cfg['dbPort'] =3306; //Database port
        
        //Admin email (in case of errors and can be used for more)
        $cfg['adminEmail'] = '';

        //Website URL, sometimes required to not get into link problems
        $cfg['site'] = '';
        
        // SMTP config
        $cfg['smtpMailName'] = ''; //Nickname for SMTP authentication, it depends on SMTP provider what login you have, it's either email or just a nickname
        $cfg['smtpMailPort'] = ''; //Port for SMTP port (465/587/25)
        $cfg['smtpMailHost'] = ''; //SMTP hostname, link to their system
        $cfg['smtpMailPass'] = ''; //Password for SMTP authentication
        $cfg['smtpMailFrom'] = ''; //Additional field for email headers, you might want to name it info@your.site, but real email who send's it will be info-was-take@your.site
        
        //Developer should be able to see what wen't wrong
        ini_set('display_errors', 1);
        ini_set('error_reporting', E_ALL);
    break;
}

//This variables required for The M.A.G.E.S. API
//It's used for external logs, but I don't know if people need it and if this should be improved, for now leaving as I'm using it
//Clients usually did stupid stuff in the past and told me that they "didn't do it". For this case you usually needed external logs.
$cfg['apiUsername'] = '';
$cfg['apiPassword'] = '';

//Google ReCaptcha parameters, required for CMS in terms someone trying to brute-force login, usually bot's stupid and can't go through it
$cfg['recaptchaSiteKey'] = '';
$cfg['recaptchaSecretKey'] = '';

//Variable required for SQL tables when you need different languages in the module.
//For example strings, content highly dependable on the language
//So if you need blog/news module that will be able to support different language, add name of SQL tables to variable below
//Variable 1: Name of SQL table
//Variable 2: Prefix for field name that will be altered inside of the table, without it, it's just a language name
//Example: array('news', 'prefix'), when you add/delete language create in table news new field "prefix{language}"
$cfg['ud_alter'] = array(
	array('tm_strings', ''),
	array('tm_pages', ''),
);

//href is a variable mainly used in templates, to not write languages separately all the time, this %lang% placeholder will be changed further down the road
$cfg['href'] = $cfg['site'].'/%lang%';

//What languages are supported for website and CMS
//Must be set as ISO-2 code
$cfg['allowedLanguages'] = array('en', 'ru');

//Default language for website and CMS (default: en)
//Must be set as ISO-2 code
$cfg['defaultLanguage'] = 'en';

//Salt used for admins in CMS, must be a long hash of total randomness
//If you change it after you created the admins all passwords will stop working of course, so change it once, make it huge.
//Or if you completely compromized, don't forget to update the passwords after changing the salt
$cfg['salt'] = '';

//Variable that can be used to define folders
//$cfg['dir'] - directory of the website (/var/www/project-name)

//Variable to send external logs, if you don't have apiUsername and apiPassword, don't set it to one (default: 0)
//$cfg['logs'] = 0;

//Maximum level of admin in CMS (default: 4)
//$cfg['maxLevel'] = 4;

//Attempts that user have to login to CMS, after that he will be kicked for 5 minutes (default: 5)
//Safe feature versus bruteforce
//$cfg['availableLoginAttempts'] = 5;

///web/include directory
$cfg['inc'] = $cfg['dir'].'/inc';
//web/classes directory
$cfg['classes'] = $cfg['dir'].'/classes';
//web/template directory
$cfg['template'] = $cfg['dir'].'/template';
//web/static files directory
$cfg['static'] = $cfg['site'].'/web/static';
//web/images directory
$cfg['img'] = $cfg['static'].'/images';
//web/uploads directory
$cfg['uploads'] = $cfg['dir'].'/uploads';
//web/uploads link for website
$cfg['imgu'] = $cfg['site'].'/web/uploads';
//web/pages directory
$cfg['pages'] = $cfg['dir'].'/pages';

//Directory where CMS will be updated, if possible don't use /cms/ or /admin/
//$cfg['pathToDirectory'] = 'admin';