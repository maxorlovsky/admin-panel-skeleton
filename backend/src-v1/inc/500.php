<?php
$container['errorHandler'] = function($container) {
    if ($container['params']['env'] !== 'dev') {
        return function ($request, $response) use ($container) {
            $newResponse = $container['response']->withStatus(500);

            $data = array('message' => '*Beep-Boop* something went wrong :(');

            return $newResponse->withJson($data);
        };
    }
};