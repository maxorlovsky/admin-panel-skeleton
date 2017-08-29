<?php
$container['notFoundHandler'] = function($c) {
    return function ($request, $response) use ($c) {
        $newResponse = $c['response']->withStatus(404);

        $data = array('message' => 'Page not found');
        
        return $newResponse->withJson($data);
    };
};
