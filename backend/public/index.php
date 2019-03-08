<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Adding general functions
require_once __DIR__ . '/../src-v1/inc/functions.php';

// Adding config
require_once __DIR__ . '/../config.php';

// Initiate app
$app = new \Slim\App([
    'settings' => $config,
    'debug' => true
]);

$container = $app->getContainer();

// Add monolog logger
require_once __DIR__ . '/../src-v1/inc/logger.php';
// DB/PDO connection
require_once __DIR__ . '/../src-v1/inc/db.php';
// Global params inside pages
require_once __DIR__ . '/../src-v1/inc/params.php';
require_once __DIR__ . '/../src-v1/inc/404.php';
require_once __DIR__ . '/../src-v1/inc/500.php';
require_once __DIR__ . '/../src-v1/inc/log.php';

// SlimPHP specific middlewares
require_once __DIR__ . '/../src-v1/middleware/db-check.php';
require_once __DIR__ . '/../src-v1/middleware/auth.php';
require_once __DIR__ . '/../src-v1/middleware/cors.php';
require_once __DIR__ . '/../src-v1/middleware/multisite.php';

// Shareable stuff goes here
require_once __DIR__ . '/../src-v1/shareable-components.php';

// Routing / Modules
require_once __DIR__ . '/../src-v1/modules/labels.php';
require_once __DIR__ . '/../src-v1/modules/login.php';
require_once __DIR__ . '/../src-v1/modules/logout.php';
require_once __DIR__ . '/../src-v1/modules/logs.php';
require_once __DIR__ . '/../src-v1/modules/pages.php';
require_once __DIR__ . '/../src-v1/modules/permissions.php';
require_once __DIR__ . '/../src-v1/modules/user-data.php';
require_once __DIR__ . '/../src-v1/modules/users.php';

// Routing, home page
require __DIR__ . '/../src-v1/modules/home.php';

// Loading the whole system
$app->run();