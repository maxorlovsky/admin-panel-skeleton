<?php
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('mocms');
    $file_handler = new \Monolog\Handler\StreamHandler("../mo.log");
    $logger->pushHandler($file_handler);

    return $logger;
};