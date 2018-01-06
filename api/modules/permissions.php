<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/api/permissions', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Define controller, fill up main variables
        $permissionsController = new PermissionsController($this->db, $this->params, $request->getAttribute('user'));

        $permissions = $permissionsController->getPermissions();

        $data = array(
            'permissions'   => $permissions
        );
    }

    return $response->withJson($data, null, JSON_NUMERIC_CHECK);
})->add($auth);

$app->put('/api/permissions', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Fetching post parameters
        $body = $request->getParsedBody();

        $user = $request->getAttribute('user');

        // Define controller, fill up main variables
        $permissionsController = new PermissionsController($this->db, $this->params, $request->getAttribute('user'));

        // Trying to register user
        $checkUser = $permissionsController->updatePermissions($body);

        if (!$checkUser) {
            $response = $response->withStatus(400);
            $data = array(
                'message' => $permissionsController->getMessage(),
                'fields' => $permissionsController->getFields(),
            );

            Log::save($this->db, [
                'module'    => 'permissions',
                'type'      => 'edit',
                'user_id'   => $user->id,
                'info'      => 'Permissions update failed <b>' . $permissionsController->getMessage() . '</b>'
            ]);
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Permissions updated.',
            );

            Log::save($this->db, [
                'module'    => 'permissions',
                'type'      => 'edit',
                'user_id'   => $user->id,
                'info'      => 'Permissions updated'
            ]);
        }
    }

    return $response->withJson($data, null, JSON_NUMERIC_CHECK);
})->add($auth);

class PermissionsController
{
    private $db;
    private $params;
    private $user;

    public function __construct($db, $params, $user) {
        $this->db = $db;
        $this->params = $params;
        $this->user = $user;
    }

    public function getMessage() {
        // Return all messages and strip <br /> at the end of the line
        return rtrim($this->message, '<br />');
    }

    public function getFields() {
        // Return unique fields
        return array_unique($this->fields);
    }

    public function getPermissions() {
        $q = $this->db->query('SELECT `value` FROM `mocms` WHERE `setting` = "menu" LIMIT 1');
        $q->execute();
        $row = $q->fetch();

        return json_decode($row['value']);
    }

    public function updatePermissions($attributes) {
        $checkForm = $this->checkUpdateForm($attributes);

        if (!$checkForm) {
            return false;
        }

        $value = json_encode($attributes);
        $q = $this->db->prepare('UPDATE `mocms` SET `value` = :value WHERE `setting` = "menu"');
        $q->bindParam(':value', $value, PDO::PARAM_STR);
        $q->execute();

        return true;
    }

    private function checkUpdateForm($attributes) {
        foreach($attributes as $v) {
            if (!$v['name']) {
                $this->message .= ucfirst($v['key']) . ' name is empty<br />';
                $this->fields[] = $v['key'];
            }

            if (!$v['level']) {
                $this->message .= ucfirst($v['key']) . ' level is empty<br />';
                $this->fields[] = $v['key'];
            }
        }

        if ($this->message) {
            return false;
        }

        return true;
    }
}