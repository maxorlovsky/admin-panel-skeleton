<?php
$app->add(new \Tuupola\Middleware\CorsMiddleware([
    "origin" => $config['cors_domains'],
    "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
    "headers.allow" => ['sessionToken', 'siteId'],
    "headers.expose" => [],
    "credentials" => true,
    "cache" => 0,
]));