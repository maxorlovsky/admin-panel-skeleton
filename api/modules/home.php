<?php
// Home page
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/[{path:.*}]', function (Request $request, Response $response) {
    $file = file_get_contents(__DIR__ . '/../../dist/index.html');

    return $file;
});