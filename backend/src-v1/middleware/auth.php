<?php
$auth = function ($request, $response, $next) {
    $request = $request->withAttribute('isLogged', false);
    
    // Check if sessionToken header is in the call
    if ($request->hasHeader('sessionToken')) {
        // Header exist, check in Db
        $sessionToken = $request->getHeader('sessionToken')[0];

        $q = $this->db->prepare(
            'SELECT `a`.`id`, `a`.`login`, `a`.`email`, `a`.`level`, `a`.`custom_access` '.
            'FROM `mo_users_auth` AS `ua` '.
            'LEFT JOIN `mo_admins` AS `a` ON `ua`.`user_id` = `a`.`id` '.
            'WHERE `ua`.`token` = :sessionToken '.
            'LIMIT 1'
        );
        $q->bindParam(':sessionToken', $sessionToken, PDO::PARAM_STR);
        $q->execute();
        $user = $q->fetch(PDO::FETCH_OBJ);

        // Check if session token found in db
        if ($user) {
            $request = $request->withAttribute('isLogged', true);
            $request = $request->withAttribute('user', $user);

            $user->custom_access = json_decode($user->custom_access);

            $breakdown = explode('/', $request->getUri()->getPath());
            $path = $breakdown[1];
        
            // Fetching permissions
            $q = $this->db->query('SELECT `value` FROM `mo` WHERE `setting` = "menu"');
            $q->execute();
            $permissions = $q->fetch();
            $permissions = json_decode($permissions['value']);
            $key = array_search($path, array_column($permissions, 'key'));
            $level = $permissions[$key]->level;

            // Special cases, to fetch stats with custom access
            if ($user->level == 0) {
                $user->custom_access[] = 'multisite';
                $user->custom_access[] = 'menu';
                
                // Just for the sake of being secure, add dashboard so there wouldn't be unexpected errors
                $user->custom_access[] = 'dashboard';
            }

            // Check if user level is enough to use this API endpoint
            if ($user->level < $level && ($user->level == 0 && !in_array($path, $user->custom_access))) {
                $response = $response->withStatus(403);
                $data = array('message' => 'Access denied');
                
                return $response->withJson($data, null, JSON_NUMERIC_CHECK);
            }
        }
    }

    $response = $next($request, $response);
    
    return $response;
};