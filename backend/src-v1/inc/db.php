<?php
$container['db'] = function ($container) {
    if ($container['params']['env'] === 'prod') {
        $db = $container['settings']['db'];
    } else {
        $db = $container['settings']['tdb'];
    }

    // Check db connection
    $pdo = null;

    try {
        $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
    } catch(PDOException $e) {
        // In case there is no database connection, return message
        return false;
    }

    $pdo->exec('SET NAMES "utf8"');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    return $pdo;
};