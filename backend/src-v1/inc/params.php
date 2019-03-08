<?php
$container['params'] = function($container) {
    $availableParams = array(
        'maxLevel'      => $container['settings']['maxLevel'],
    );

    // Defining environment
    $breakDown = explode('.', $_SERVER['HTTP_HOST']);
    // Check for local env as well
    if ($breakDown[0] == 'localhost:9000') {
        // Development environment
        $availableParams['env'] = 'dev';
        ini_set('display_errors', 1);
    } else if ($breakDown[1] == 'test') {
        // Test environment
        $availableParams['env'] = 'test';
        ini_set('display_errors', 1);
    } else {
        // Prod environment
        $availableParams['env'] = 'prod';
    }

    return $availableParams;
};