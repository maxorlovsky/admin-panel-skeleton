<?php
/* 
 * MOCMS v4
 * https://cms.maxorlovsky.com
 * Credits: Max Orlovsky
 * Github: https://github.com/maxorlovsky/cms
 */

// This check is required if CMS will be injected in projects like Laravel.
// It run composer post-install script via cli interfect to cleanup project and if runs into this file, breaks.
// So we ignore complete initiation of CMS from cli interface. There are no intentions, at least for now to use it.
if (php_sapi_name() != 'cli') {
    
    require_once 'vendor/autoload.php';

    // Adding general functions, used by CMS.
    require_once 'api/inc/functions.php';

    // Adding site config
    // If config does not exist we run install.php
    // It won't create dynamic stuff like wordpress/phpmyadmin do, so it's safe to store this file
    if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/mocms/config.php')) {
        require_once 'install/install.php';
        exit();
    }
    else {
        require_once $_SERVER['DOCUMENT_ROOT'].'/mocms/config.php';
    }

    // Initiate app
    $app = new \Slim\App([
        'settings' => $config,
        'debug' => true
    ]);

    $container = $app->getContainer();

    // Add monolog logger
    require_once 'api/inc/logger.php';
    // DB/PDO connection
    require_once 'api/inc/db.php';
    // Global params inside pages
    require_once 'api/inc/params.php';
    require_once 'api/inc/404.php';
    require_once 'api/inc/500.php';
    require_once 'api/inc/log.php';

    // SlimPHP specific middlewares
    require_once 'api/middleware/db-check.php';
    require_once 'api/middleware/auth.php';
    require_once 'api/middleware/cors.php';
    require_once 'api/middleware/multisite.php';

    // Routing / Modules
    require_once 'api/modules/login.php';
    require_once 'api/modules/logout.php';
    require_once 'api/modules/users.php';
    require_once 'api/modules/logs.php';
    require_once 'api/modules/user-data.php';
    require_once 'api/modules/permissions.php';
    require_once 'api/modules/pages.php';
    require_once 'api/modules/labels.php';

    // Adding site modules
    if (file_exists($_SERVER['DOCUMENT_ROOT'].'/mocms/modules.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'].'/mocms/modules.php';
    }

    // Routing, home page
    require 'api/modules/home.php';

    // Loading the whole system
    $app->run();
}