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
        require_once 'storage/install.php';
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
    require 'api/inc/logger.php';
    // DB/PDO connection
    require 'api/inc/db.php';
    // Global params inside pages
    require 'api/inc/params.php';
    require 'api/inc/404.php';
    require 'api/inc/500.php';
    require 'api/inc/log.php';

    // SlimPHP specific middlewares
    require 'api/middleware/auth.php';
    require 'api/middleware/cors.php';
    require 'api/middleware/multisite.php';

    // Routing / Modules
    require 'api/modules/login.php';
    require 'api/modules/logout.php';
    require 'api/modules/users.php';
    require 'api/modules/logs.php';
    require 'api/modules/user-data.php';
    require 'api/modules/permissions.php';

    // Adding site modules
    if (file_exists($_SERVER['DOCUMENT_ROOT'].'/mocms/modules.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'].'/mocms/modules.php';
    }

    // Routing, home page
    require 'api/modules/home.php';

    // Loading the whole system
    $app->run();

    exit();
}