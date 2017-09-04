<?php
$container['db'] = function ($c) {
    if ($c['params']['env'] === 'prod') {
        $db = $c['settings']['db'];
    } else {
        $db = $c['settings']['tdb'];
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