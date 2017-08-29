<?php
/* 
 * TM CMS v4
 * https://cms.maxorlovsky.com
 * Credits: Max Orlovsky
 * Github: https://github.com/Maxtream/themages-cms.git
 */

// This check is required if CMS will be injected in projects like Laravel.
// It run composer post-install script via cli interfect to cleanup project and if runs into this file, breaks.
// So we ignore complete initiation of CMS from cli interface. There are no intentions, at least for now to use it.
if (php_sapi_name() != 'cli') {
    
    require_once 'vendor/autoload.php';

    // Session start if it wasn't still initiated
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Adding general functions, used by CMS.
    require_once 'cms/inc/functions.php';

    // Adding site config
    // If config does not exist we run install.php
    // It won't create dynamic stuff like wordpress/phpmyadmin do, so it's safe to store this file
    if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/tmcms/config.php')) {
        require_once 'install.php';
        exit();
    }
    else {
        require_once $_SERVER['DOCUMENT_ROOT'].'/tmcms/config.php';
    }

    // Initiate app
    $app = new \Slim\App([
        'settings' => $config,
        'debug' => true
    ]);

    $container = $app->getContainer();

    // Add monolog logger
    require 'cms/inc/logger.php';
    // DB/PDO connection
    require 'cms/inc/db.php';
    // Global params inside pages
    require 'cms/inc/params.php';
    require 'cms/inc/404.php';
    require 'cms/inc/500.php';
    require 'cms/inc/log.php';

    // SlimPHP specific middlewares
    require 'cms/middleware/auth.php';
    require 'cms/middleware/cors.php';

    // Routing / Modules
    require 'cms/modules/login.php';
    require 'cms/modules/logout.php';
    require 'cms/modules/users.php';
    require 'cms/modules/logs.php';
    require 'cms/modules/user-data.php';
    require 'cms/modules/permissions.php';

    // Adding site modules
    if (file_exists($_SERVER['DOCUMENT_ROOT'].'/tmcms/modules.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'].'/tmcms/modules.php';
    }

    // Routing, home page
    require 'cms/modules/home.php';

    // Loading the whole system
    $app->run();

    exit();
}