<?php
$container['errorHandler'] = function($c) {
    if ($c['params']['env'] !== 'dev') {
        return function ($request, $response) use ($c) {
            $newResponse = $c['response']->withStatus(500);

            $data = array('message' => '*Beep-Boop* something went wrong :(');

            return $newResponse->withJson($data);
        };
    }
};