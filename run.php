<?php
/* 
 * CMS The M.A.G.E.S. v4
 * https://www.themages.net
 * Credits: Maxtream
 * Github: https://github.com/Maxtream/themages-cms.git
 */

//This check is required if CMS will be injected in projects like Laravel.
//It run composer post-install script via cli interfect to cleanup project and if runs into this file, breaks.
//So we ignore complete initiation of CMS from cli interface. There are no intentions, at least for now to use it.
if (php_sapi_name() != 'cli') {

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

    // Adding site config
    // If config does not exist we run install.php
    // It won't create dynamic stuff like wordpress/phpmyadmin do, so it's safe to store this file
    if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/web') || !file_exists($_SERVER['DOCUMENT_ROOT'].'/config-tm.php')) {
        require_once $cfg['root'].'/install.php';
        exit();
    }
    else {
        require_once $_SERVER['DOCUMENT_ROOT'].'/config-tm.php';
    }

    //This file must run after user config is defined as we need to know full url to static files of CMS
    require_once dirname(__FILE__).'/cms/inc/config-post.php';

    //Adding not so huge functions, used by CMS, written by me for easier usage, half of them minght not be useful anymore, so probably need to clean up a bit.
    require_once $cfg['cmsinc'].'/functions.php';

    //If catching admin variable, running admin system
    if (isset($_GET['language']) && $_GET['language'] == $cfg['pathToDirectory']) {
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
    //If not admin and not ajax, do nothing and probably opening website
}