<?php
$app->add(function ($request, $response, $next) {
    // Check if siteId header is present
    if ($request->hasHeader('siteId')) {
        // Header exist, check in Db
        $siteId = $request->getHeader('siteId')[0];

        $q = $this->db->prepare(
            'SELECT * FROM `mo_multisite` '.
            'WHERE `id` = :id '.
            'LIMIT 1'
        );
        $q->bindParam(':id', $siteId, PDO::PARAM_INT);
        $q->execute();

        $multiSite = $q->fetch();

        // Session token not found in db, probably someone messing or it expired
        if (!$multiSite) {
            $request = $request->withAttribute('siteId', 0);
        } else {
            $request = $request->withAttribute('siteId', $multiSite['id']);
        }
    }
    
    $response = $next($request, $response);

    return $response;
});