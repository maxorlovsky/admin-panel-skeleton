<?php
//=====================================================
// Site config
//=====================================================
switch ( $cfg['env'] )
{
    case 'prod':
    case 'live':
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
        
        ini_set('display_errors', 0);
        
    break;
    case 'test':
    case 'beta':
    case 'staging':
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
        
        ini_set('display_errors', 1);
        ini_set('error_reporting', E_ALL);

    break;
    case 'dev':
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
        
        ini_set('display_errors', 1);
        ini_set('error_reporting', E_ALL);
        
    break;
}

$cfg['dir'] = $_SERVER['DOCUMENT_ROOT'];

$cfg['href'] = $cfg['site'].'/%lang%';

$cfg['inc'] = $cfg['dir'].'/inc';
$cfg['classes'] = $cfg['dir'].'/classes';
$cfg['template'] = $cfg['dir'].'/template';
$cfg['template'] = $cfg['dir'].'/template';
$cfg['static'] = $cfg['site'].'/web/static';
$cfg['img'] = $cfg['static'].'/images';
$cfg['uploads'] = $cfg['dir'].'/uploads';
$cfg['imgu'] = $cfg['site'].'/web/uploads';

$cfg['cronjob'] = 'askdjOLIKSJDoi2o12d09asLL';
$cfg['salt'] = 'eethaiASLDK21lae6AASDta9ChoDDCh';
$cfg['logs'] = 1;
$cfg['maxLevel'] = 4;
$cfg['allowedLanguages'] = array('en', 'ru');
$cfg['defaultLanguage'] = 'en';

$cfg['apiUrl'] = 'https://api.themages.net';
$cfg['apiUsername'] = '';
$cfg['apiPassword'] = '';

$cfg['recaptchaSiteKey'] = '';
$cfg['recaptchaSecretKey'] = '';

$cfg['ud_alter'] = array(
	array('tm_strings', ''),
	array('tm_pages', ''),
);

$cfg['demo'] = 0;