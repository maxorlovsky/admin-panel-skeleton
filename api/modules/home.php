<?php
// Home page
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->any('/[{path:.*}]', function (Request $request, Response $response) {
    $response = $response->withStatus(403);
    
    if ($request->getMethod() === 'GET') {
        $file = file_get_contents(__DIR__ . '/../../dist/index.html');

        return $file;
    } else {
        $data = array('message' => '*Beep-Boop* nothing to see here');
        
        return $response->withJson($data);
    }
});