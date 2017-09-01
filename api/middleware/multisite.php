<?php
$multisite = function ($request, $response, $next) {
    // Check if brand ID is specified
    if ($request->hasAttribute('multisite')) {
        $multisite = (int)$request->getAttribute('multisite');

        // Check what brand data we have in database
        $q = $this->db->prepare(
            'SELECT * '.
            'FROM `mo_multisite` '.
            'WHERE `id` = :multisiteId '.
            'LIMIT 1'
        );
        $q->bindParam(':multisiteId', $multisite, PDO::PARAM_INT);
        $q->execute();
        $multisite = $q->fetch();

        // Checking if multisite was fetched, add to request
        if ($multisite) {
            $request = $request->withAttribute('multisite', $multisite);
        }
    }

    $response = $next($request, $response);
    
    return $response;
};