<?php
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('themages');
    $file_handler = new \Monolog\Handler\StreamHandler("../tm.log");
    $logger->pushHandler($file_handler);

    return $logger;
};