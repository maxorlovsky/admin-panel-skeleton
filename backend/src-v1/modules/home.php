<?php
// Home page
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// In case some random path is triggered, display nothing
$app->any('/[{path:.*}]', function (Request $request, Response $response) {
    $response = $response->withStatus(403);
    
    $data = array('message' => '*Beep-Boop* nothing to see here');
    
    return $response->withJson($data, null, JSON_NUMERIC_CHECK);
});