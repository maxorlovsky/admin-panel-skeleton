<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/api/logout', function (Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        $user = $request->getAttribute('user'); 

        // Define controller, fill up main variables
        $logoutController = new LogoutController($this->params, $this->db, $user);

        // Authenticating user and in case of success return token
        $logoutController->logout();

        $data = array('state' => 'success');

        Log::save($this->db, [
            'module'=> 'logout',
            'type'  => 'success',
            'info'  => 'Success logout as <b>' . $user->login . '</b>'
        ]);
    }

    return $response->withJson($data);
})->add($auth);

class LogoutController
{
    private $db;
    private $params;
    private $user;

    public function __construct($params, $db, $user) {
        $this->params = $params;
        $this->db = $db;
        $this->user = $user;
    }

    public function logout() {
        $q = $this->db->prepare('DELETE FROM `mo_users_auth` WHERE `user_id` = :userId LIMIT 1');
        $q->bindParam(':userId', $this->user->id, PDO::PARAM_INT);
        $q->execute();

        return true;
    }
}