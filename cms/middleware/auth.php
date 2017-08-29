<?php
$auth = function ($request, $response, $next) {
    $request = $request->withAttribute('isLogged', false);
    
    // Check if sessionToken header is in the call
    if ($request->hasHeader('sessionToken')) {
        // Header exist, check in Db
        $sessionToken = $request->getHeader('sessionToken')[0];

        $q = $this->db->prepare('SELECT `a`.`id`, `a`.`login`, `a`.`email`, `a`.`level`, `a`.`custom_access` '.
            'FROM `tm_users_auth` AS `ua` '.
            'LEFT JOIN `tm_admins` AS `a` ON `ua`.`user_id` = `a`.`id` '.
            'WHERE `ua`.`token` = :sessionToken '.
            'LIMIT 1'
        );
        $q->bindParam(':sessionToken', $sessionToken, PDO::PARAM_STR);
        $q->execute();
        $user = $q->fetch(PDO::FETCH_OBJ);

        // Session token not found in db, probably someone messing or it expired
        if ($user) {
            $request = $request->withAttribute('isLogged', true);
            $request = $request->withAttribute('user', $user);
        }
    }

    $response = $next($request, $response);
    
    return $response;
};