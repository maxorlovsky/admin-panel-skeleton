<?php
$container['params'] = function($c) {
    $availableParams = array(
        'captchaSecret' => $c['settings']['captchaSecret'],
        'maxLevel'      => $c['settings']['maxLevel'],
    );

    // Defining environment
    $breakDown = explode('.', $_SERVER['HTTP_HOST']);
    if ($breakDown[0] == 'dev' || strpos($_SERVER['HTTP_HOST'], 'localhost') >= 0) {
        // Development environment
        $availableParams['env'] = 'dev';
    } else if ($breakDown[0] == 'test') {
        // Test environment
        $availableParams['env'] = 'test';
    } else {
        // Prod environment
        $availableParams['env'] = 'prod';
    }

    return $availableParams;
};