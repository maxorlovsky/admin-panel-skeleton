<?php
/* 
 * CMS TheMages v3.15
 * http://www.themages.net
 * Credits (dev): Maxtream
 * Credits (design): Maxtream, AnyaTheEagle
 * Github: https://github.com/Maxtream/themages-cms.git
 */

// Maintenance check
// While moving to composer this won't work, need to implement different approach, probably through SQL
// Also while moving to composer, this "exit" doesn't make any sence as it was used for SVN, while doing the update
if (file_exists('maint_mode')) {
	die('This site is on maintenance');
}

//Session start if it wasn't still initiated, PHP >= 5.4
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//Much needed variable to hold all configuration through all functions/classes, it must be updated through the process so constants are not a good choise here
global $cfg;

//Breaking all friendly GET parameters into variables
if (isset($_GET['params']) && $_GET['params']) {
    $breakdown = explode('/', $_GET['params']);
    if ($breakdown) {
        $i = 0;
        foreach($breakdown as $f) {
            $_GET[($i==0?'language':'val'.$i)] = $f;
            ++$i;
        }
    }
}

//Define where are directory lies to make path absolete
$cfg['root'] = str_replace('\\', '/', __DIR__);

date_default_timezone_set('UTC');

//Running initial config that will create needed variables for CMS
//This file must not be touched as it is not a real config
require_once dirname(__FILE__).'/cms/inc/config-default.php';
print_r($cfg);

//Adding not so huge functions, used by CMS, written by me for easier usage, half of them minght not be useful anymore, so probably need to clean up a bit.
//require_once $cfg['cmsinc'].'/functions.php';
echo $cfg['root'].'/config.php';
// Adding site config
if (!file_exists($cfg['root'].'/config.php')) {
    exit('Config file not exist, please create '.$cfg['dir'].'/inc/config.php file from '.$cfg['dir'].'/inc/config.sample.php');
}
else {
    require_once $cfg['dir'].'/inc/config.php';
}


//=====================================================
// Making some defines for easyer coding (directories)
$cfg['cmssite'] = $cfg['site'].'/admin';
$cfg['cmsinc'] = $cfg['cmsdir'].'/inc';
$cfg['cmsclasses'] = $cfg['cmsdir'].'/classes';
$cfg['cmstemplate'] = $cfg['cmsdir'].'/template';
$cfg['cmslib'] = $cfg['cmsdir'].'/lib';
$cfg['cmsstatic'] = $cfg['site'].'/cms/static';
$cfg['cmsimg'] = $cfg['site'].'/cms/static/images';
$cfg['cmslocale'] = $cfg['cmsdir'].'/locale';
$cfg['cmsmodules'] = $cfg['cmsdir'].'/modules';
$cfg['cmslib'] = $cfg['cmsdir'].'/lib';
$cfg['uploads'] = $cfg['dir'].'/uploads';
$cfg['imgu'] = $cfg['site'].'/web/uploads';
$cfg['pages'] = $cfg['dir'].'/pages';
//=====================================================

//If catching admin variable, running admin system
if (isset($_GET['language']) && $_GET['language'] == 'admin') {
    //Global variable with translation for CMS
    global $astr;

    //Loading main class
    require_once _cfg('cmsclasses').'/system.php';
    
    //Loading whole system
    $system = new System(0);
    $system->run();
}
//If catching AJAX request, sending to ajax directly
else if(isset($_POST['control']) && $_POST['control']) {
    //Loading main class
    require_once _cfg('cmsclasses').'/system.php';
    
    //Loading whole system
    $system = new System(0);
    $system->ajax($_POST);
}
//If not admin and not ajax, opening just website
else {
    require_once dirname(__FILE__).'/web/index.php';
}