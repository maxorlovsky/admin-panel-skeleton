<?php
$app->add(function ($request, $response, $next) {
    // If no connection, send proper message as auth checked used everywhere
    if (!$this->db) {
        $response = $response->withStatus(503);
        $data = array('message' => 'No database connection denied');

        return $response->withJson($data);
    }

    $response = $next($request, $response);
    
    return $response;
});