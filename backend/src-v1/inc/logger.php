<?php
$container['logger'] = function($container) {
    $logger = new \Monolog\Logger('mo-cms');
    $file_handler = new \Monolog\Handler\StreamHandler("../mo-cms.log");
    $logger->pushHandler($file_handler);

    return $logger;
};