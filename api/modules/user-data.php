<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/api/menu', function (Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Define controller, fill up main variables
        $userDataController = new UserDataController($this->db, $request->getAttribute('user'));

        $data = $userDataController->fetchMenu();

        if (!$data) {
            $response = $response->withStatus(401);
            $data = array('message' => 'Authorization error');
        }
    }

    return $response->withJson($data);
})->add($auth);

$app->get('/api/multisite', function (Request $request, Response $response) {
    $data = [];

    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Define controller, fill up main variables
        $userDataController = new UserDataController($this->db, $request->getAttribute('user'));

        // There is no error checking required, empty array should be returned instead
        $data = $userDataController->fetchMultisites();
    }

    return $response->withJson($data);
})->add($auth);

$app->put('/api/user-data/change-password', function (Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Fetching post parameters
        $body = $request->getParsedBody();

        $attributes = array(
            'currentPass'   => filter_var($body['currentPass'], FILTER_SANITIZE_STRING),
            'newPass'       => filter_var($body['newPass'], FILTER_SANITIZE_STRING),
            'repeatPass'    => filter_var($body['repeatPass'], FILTER_SANITIZE_STRING),
        );

        // Define controller, fill up main variables
        $userDataController = new UserDataController($this->db, $request->getAttribute('user'));

        // Trying to register user
        $checkForm = $userDataController->updatePassword($attributes);

        if (!$checkForm) {
            $response = $response->withStatus(400);
            $data = array(
                'message' => $userDataController->getMessage(),
                'fields' => $userDataController->getFields(),
            );

            Log::save($this->db, [
                'module'    => 'user',
                'type'      => 'password-change',
                'user_id'   => $user->id,
                'info'      => 'Password change failed [<b>' . $userDataController->getMessage() . '</b>]'
            ]);
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Password updated',
            );

            Log::save($this->db, [
                'module'    => 'user',
                'type'      => 'password-change',
                'user_id'   => $user->id,
                'info'      => 'Password change success'
            ]);
        }
    }

    return $response->withJson($data);
})->add($auth);

class UserDataController
{
    private $db;
    private $user;
    public $fields;
    public $message;

    public function __construct($db, $user) {
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

    public function fetchMenu() {
        $q = $this->db->query('SELECT `value` FROM `mocms` WHERE `setting` = "menu"');
        $q->execute();
        $menu = $q->fetch();

        $menu = json_decode($menu['value']);
        
        $returnMenu = [];
        foreach($menu as $value) {
            // Check if access level allow user to see menu item
            if ($value->level <= $this->user->level || ($this->user->level == 0 && in_array($value->key, $this->user->custom_access))) {
                $returnMenu[$value->key] = [
                    'url'           => '/' . $value->key,
                    'title'         => $value->name,
                    'icon_classes'  => $value->icon_classes,
                ];

                if (isset($value->subCategories) && $value->subCategories) {
                    $returnMenu[$value->key]['sublinks'] = [];

                    foreach($value->subCategories as $subValue) {
                        if ($value->level <= $this->user->level || ($this->user->level == 0 && in_array($value->key, $this->user->custom_access))) {
                            $returnMenu[$value->key]['sublinks'][$subValue->key] = [
                                'url'           => '/' . $subValue->key,
                                'title'         => $subValue->name,
                                'icon_classes'  => $subValue->icon_classes,
                            ];
                        }
                    }
                }
            }
        }

        $returnMenu['logout'] = [
            'url'           => '/logout',
            'title'         => 'Logout',
            'icon_classes'  => 'fa fa-sign-out',
        ];

        return $returnMenu;
    }

    public function fetchMultisites() {
        $q = $this->db->query('SELECT `id`, `name` FROM `mo_multisite`');
        $q->execute();
        $multiSites = $q->fetchAll();

        // We don't send return here, as there might not be multiple websites instances
        return $multiSites;
    }

    public function updatePassword($attributes) {
        if (!$this->checkFormPassword($attributes)) {
            return false;
        }

        $convertedPassword = UsersController::passwordConvert($attributes['newPass']);
        $q = $this->db->prepare(
            'UPDATE `mo_admins` SET '.
            '`password` = :password '.
            'WHERE `id` = :id '.
            'LIMIT 1'
        );

        $q->bindParam(':password', $convertedPassword, PDO::PARAM_STR);
        $q->bindParam(':id', $this->user->id, PDO::PARAM_INT);

        $q->execute();

        return true; 
    }

    private function checkFormPassword($attributes) {
        if (!$attributes['currentPass']) {
            $this->message .= 'Current Password is empty<br />';
            $this->fields[] = 'currentPass';
        }

        $q = $this->db->prepare(
            'SELECT `password` FROM `mo_admins` '.
            'WHERE `id` = :id '.
            'LIMIT 1'
        );

        $q->bindParam(':id', $this->user->id, PDO::PARAM_INT);

        $q->execute();
        $checkPassword = $q->fetch();

        if (!UsersController::passwordVerify($attributes['currentPass'], $checkPassword['password'])) {
            $this->message .= 'Current password is incorrect<br />';
            $this->fields[] = 'currentPass';
        } 

        if (!$attributes['newPass']) {
            $this->message .= 'New Password is empty<br />';
            $this->fields[] = 'newPass';
        }

        if (strlen($attributes['newPass']) < 6) {
            $this->message .= 'New Password must be at least 6 characters long<br />';
            $this->fields[] = 'newPass';
        }

        if ($attributes['newPass'] !== $attributes['repeatPass']) {
            $this->message .= 'Passwords does not match<br />';
            $this->fields[] = 'repeatPass';
        }

        // In case if there are any messages, return false
        if ($this->message) {
            return false;
        }

        return true;
    }
}