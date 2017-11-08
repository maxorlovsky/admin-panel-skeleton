<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/api/users', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Define controller, fill up main variables
        $usersController = new UsersController($this->params, $this->db, $request->getAttribute('user'));
        
        $data = array(
            'admins' => $usersController->getAdmins()
        );
    }

    return $response->withJson($data);
})->add($auth);

$app->get('/api/users/{id}', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        $attributes = array(
            'id'    => $request->getAttribute('id'),
        );

        // Define controller, fill up main variables
        $usersController = new UsersController($this->params, $this->db, $request->getAttribute('user'));
        
        $data = array(
            'admin' => $usersController->getAdmin($attributes['id'])
        );
    }

    return $response->withJson($data);
})->add($auth);

$app->delete('/api/users/delete/{id}', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        $user = $request->getAttribute('user');

        $attributes = array(
            'id'    => $request->getAttribute('id'),
        );

        // Define controller, fill up main variables
        $usersController = new UsersController($this->params, $this->db, $request->getAttribute('user'));

        $checkUser = $usersController->deleteAdmin($attributes['id']);
        
        if (!$checkUser) {
            $response = $response->withStatus(400);
            $data = array(
                'message' => $usersController->getMessage(),
            );

            Log::save($this->db, [
                'module'    => 'users',
                'type'      => 'delete',
                'user_id'   => $user->id,
                'info'      => 'Admin deletion failed [<b>' . $attributes['id'] . '</b>] <b>' . $usersController->getMessage() . '</b>'
            ]);
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Admin removed',
            );

            Log::save($this->db, [
                'module'    => 'users',
                'type'      => 'delete',
                'user_id'   => $user->id,
                'info'      => 'Admin removed [<b>' . $attributes['id'] . '</b>]'
            ]);
        }
    }

    return $response->withJson($data);
})->add($auth);

$app->post('/api/users/add', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Fetching post parameters
        $body = $request->getParsedBody();

        $user = $request->getAttribute('user');
        
        $attributes = array(
            'login'     => filter_var($body['login'], FILTER_SANITIZE_STRING),
            'password'  => filter_var($body['password'], FILTER_SANITIZE_STRING),
            'email'     => filter_var($body['email'], FILTER_SANITIZE_STRING),
            'level'     => filter_var($body['level'], FILTER_SANITIZE_NUMBER_INT),
            'permissions'=> $body['permissions'],
        );
        
        // Define controller, fill up main variables
        $usersController = new UsersController($this->params, $this->db, $user);

        // Trying to register user
        $checkUser = $usersController->register($attributes);

        if (!$checkUser) {
            $response = $response->withStatus(400);
            $data = array(
                'message' => $usersController->getMessage(),
                'fields' => $usersController->getFields(),
            );

            Log::save($this->db, [
                'module'    => 'users',
                'type'      => 'add',
                'user_id'   => $user->id,
                'info'      => 'Admin creation failed <b>' . $usersController->getMessage() . '</b>'
            ]);
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Success! New admin is created.',
            );

            Log::save($this->db, [
                'module'    => 'users',
                'type'      => 'add',
                'user_id'   => $user->id,
                'info'      => 'Admin is created <b>' . $attributes['login'] . '</b>'
            ]);
        }
    }

    return $response->withJson($data);
})->add($auth);

$app->post('/api/users/edit', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Fetching post parameters
        $body = $request->getParsedBody();

        $user = $request->getAttribute('user');
        
        $attributes = array(
            'id'        => filter_var($body['id'], FILTER_SANITIZE_NUMBER_INT),
            'password'  => filter_var($body['password'], FILTER_SANITIZE_STRING),
            'email'     => filter_var($body['email'], FILTER_SANITIZE_STRING),
            'level'     => filter_var($body['level'], FILTER_SANITIZE_NUMBER_INT),
            'permissions'=> $body['permissions'],
        );
        
        // Define controller, fill up main variables
        $usersController = new UsersController($this->params, $this->db, $request->getAttribute('user'));

        // Trying to register user
        $checkUser = $usersController->editAdmin($attributes);

        if (!$checkUser) {
            $response = $response->withStatus(400);
            $data = array(
                'message' => $usersController->getMessage(),
                'fields' => $usersController->getFields(),
            );

            Log::save($this->db, [
                'module'    => 'users',
                'type'      => 'edit',
                'user_id'   => $user->id,
                'info'      => 'Admin update failed [<b>' . $attributes['id'] . '</b>] <b>' . $usersController->getMessage() . '</b>'
            ]);
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Success! Admin updated.',
            );

            Log::save($this->db, [
                'module'    => 'users',
                'type'      => 'edit',
                'user_id'   => $user->id,
                'info'      => 'Admin updated [<b>' . $attributes['id'] . '</b>]'
            ]);
        }
    }

    return $response->withJson($data);
})->add($auth);

class UsersController
{
    private $db;
    private $params;
    private $message;
    private $fields;
    private $user;

    public function __construct($params, $db, $user) {
        $this->params = $params;
        $this->db = $db;
        $this->user = $user;
        $this->message = '';
        $this->fields = array();
    }

    public function getMessage() {
        // Return all messages and strip <br /> at the end of the line
        return rtrim($this->message, '<br />');
    }

    public function getFields() {
        // Return unique fields
        return array_unique($this->fields);
    }

    public function deleteAdmin($id) {
        $admin = $this->getAdmin($id);

        if ($this->user->id === $admin['id']) {
            $this->message = 'You can not delete yourself';
            return false;
        } else if ($this->user->level <= $admin['level']) {
            $this->message = 'Your access levels is the same or lower then admin you are trying to delete';
            return false;
        }

        $q = $this->db->prepare('UPDATE `mo_admins` SET `deleted` = 1 WHERE `id` = :id LIMIT 1');
        $q->bindParam(':id', $id, PDO::PARAM_INT);
        $q->execute();

        return true;
    }

    public function getAdmin($id) {
        $q = $this->db->prepare(
            'SELECT `id`, `login`, `email`, `level`, `custom_access` '.
            'FROM `mo_admins` '.
            'WHERE `id` = :id '.
            'AND `deleted` = 0 '.
            'LIMIT 1'
        );
        $q->bindParam(':id', $id, PDO::PARAM_INT);
        $q->execute();

        $user = $q->fetch();

        $user['custom_access'] = json_decode($user['custom_access']);
        
        return $user;
    }

    public function getAdmins() {
        $q = $this->db->prepare(
            'SELECT `id`, `login`, `email`, `level`, `last_login` FROM `mo_admins` '.
            'WHERE `deleted` = 0'
        );
        $q->execute();
        
        return $q->fetchAll();
    }

    public function register($attributes) {
        $formData = $this->checkForm($attributes, 'add');

        // In case check failed, $message should have the error
        if (!$formData) {
            return false;
        }
        
        $q = $this->db->prepare(
            'INSERT INTO `mo_admins` SET '.
            '`login` = :login, '.
            '`password` = :password, '.
            '`email` = :email, '.
            '`level` = :level, '.
            '`custom_access` = :permissions'
        );

        // Converting password from plain text to proper encrypted text
        $convertedPassword = UsersController::passwordConvert($attributes['password']);
        $permissions = json_encode($attributes['permissions']);
        $level = (int)$attributes['level'];

        $q->bindParam(':login', $attributes['login'], PDO::PARAM_STR);
        $q->bindParam(':password', $convertedPassword, PDO::PARAM_STR);
        $q->bindParam(':email', $attributes['email'], PDO::PARAM_STR);
        $q->bindParam(':level', $level, PDO::PARAM_INT);
        $q->bindParam(':permissions', $permissions, PDO::PARAM_STR);
        $q->execute();
        
        return true;
    }

    public function editAdmin($attributes) {
        $formData = $this->checkForm($attributes, 'edit');
        
        // In case check failed, $message should have the error
        if (!$formData) {
            return false;
        }

        // Update in case if password is set
        if ($attributes['password']) {
            $q = $this->db->prepare(
                'UPDATE `mo_admins` SET '.
                '`password` = :password, '.
                '`email` = :email, '.
                '`level` = :level, '.
                '`custom_access` = :permissions '.
                'WHERE `id` = :id'
            );
        } else {
            $q = $this->db->prepare(
                'UPDATE `mo_admins` SET '.
                '`email` = :email, '.
                '`level` = :level, '.
                '`custom_access` = :permissions '.
                'WHERE `id` = :id'
            );
        }
        
        $level = (int)$attributes['level'];
        $permissions = json_encode($attributes['permissions']);

        $q->bindParam(':email', $attributes['email'], PDO::PARAM_STR);
        $q->bindParam(':level', $level, PDO::PARAM_INT);
        $q->bindParam(':permissions', $permissions, PDO::PARAM_STR);
        $q->bindParam(':id', $attributes['id'], PDO::PARAM_INT);

        // Update in case if password is set
        if ($attributes['password']) {
            // Converting password from plain text to proper encrypted text
            $convertedPassword = UsersController::passwordConvert($attributes['password']);
            $q->bindParam(':password', $convertedPassword, PDO::PARAM_STR);
        }

        try {
            $q->execute();
        } catch(Exception $e) {
            ddump($e->getMessage());
        }
        
        return true;
    }

    private function checkForm($attributes, $type) {
        if ($type === 'add') {
            if (!$attributes['login']) {
                $this->message .= 'Login is empty<br />';
                $this->fields[] = 'login';
            } else if (strlen($attributes['login']) > 32) {
                $this->message .= 'Login is too long<br />';
                $this->fields[] = 'login';
            } else if (strpos($attributes['login'], ' ')) {
                $this->message .= 'Logins are not allowed to have spaces<br />';
                $this->fields[] = 'login';
            }
        }

        if ($attributes['email'] && !filter_var($attributes['email'], FILTER_VALIDATE_EMAIL)) {
            $this->message .= 'Email is incorrect, it should match the example *example@provider.com*<br />';
            $this->fields[] = 'email';
        }
        
        if ($type === 'add' || ($type === 'edit' && $attributes['password'])) {
            if (!$attributes['password']) {
                $this->message .= 'Password is empty<br />';
                $this->fields[] = 'password';
            }

            if (strlen($attributes['password']) < 6) {
                $this->message .= 'Password must be at least 6 characters long<br />';
                $this->fields[] = 'password';
            }
        }

        if ($attributes['level'] > $this->user->level) {
            $this->message .= 'Access level is too high, you can not manipulate admin of higher level then yourself';
            $this->fields[] = 'level';
        } else if ($type === 'edit') {
            $admin = $this->getAdmin($attributes['id']);

            if ($admin['level'] > $this->user->level) {
                $this->message .= 'Access level of this admin is too high, you can not edit it';
                $this->fields[] = 'level';
            }
        }

        if ($type === 'add') {
            $q = $this->db->prepare(
                'SELECT `login` FROM `mo_admins` '.
                'WHERE `login` = :login '.
                'AND `deleted` = 0 '.
                'LIMIT 1'
            );
            $q->bindParam(':login', $attributes['login'], PDO::PARAM_STR);
            $q->execute();
            $user = $q->fetch();

            if ($user) {
                $this->message .= 'Sorry, login is already taken<br />';
                $this->fields[] = 'login';
            }
        }

        if ($this->message) {
            return false;
        }

        return true;
    }

    public static function passwordConvert($password) {
        $returnPassword = password_hash($password, PASSWORD_BCRYPT);
        
        return $returnPassword;
    }
}