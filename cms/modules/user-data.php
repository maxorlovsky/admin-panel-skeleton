<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/api/user-data', function (Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Define controller, fill up main variables
        $userDataController = new UserDataController($this->db, $request->getAttribute('user'));

        $data = $userDataController->fetchUserData();

        if (!$data) {
            $response = $response->withStatus(401);
            $data = array('message' => 'Authorization error');
        }
    }

    return $response->withJson($data);
})->add($auth);

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

$app->put('/user-data/profile', function (Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Fetching post parameters
        $body = $request->getParsedBody();

        $attributes = array(
            'name'      => filter_var($body['name'], FILTER_SANITIZE_STRING),
            'battletag' => filter_var($body['battletag'], FILTER_SANITIZE_STRING),
            'avatar'    => filter_var($body['avatar'], FILTER_SANITIZE_NUMBER_INT)
        );

        // Define controller, fill up main variables
        $userDataController = new UserDataController($this->db, $request->getAttribute('user'));

        // Trying to register user
        $checkForm = $userDataController->updatePublicProfile($attributes);

        if (!$checkForm) {
            $response = $response->withStatus(400);
            $data = array(
                'message' => $userDataController->getMessage(),
                'fields' => $userDataController->getFields(),
            );
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Data updated',
            );
        }
    }

    return $response->withJson($data);
})->add($auth);

$app->put('/user-data/change-password', function (Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Fetching post parameters
        $body = $request->getParsedBody();

        $attributes = array(
            'oldPass'   => filter_var($body['oldPass'], FILTER_SANITIZE_STRING),
            'pass'      => filter_var($body['pass'], FILTER_SANITIZE_STRING),
            'repeatPass'=> filter_var($body['repeatPass'], FILTER_SANITIZE_STRING)
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
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Password updated',
            );
        }
    }

    return $response->withJson($data);
})->add($auth);

$app->put('/user-data/settings', function (Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Fetching post parameters
        $body = $request->getParsedBody();

        $attributes = array(
            'timestyle'     => filter_var($body['timestyle'], FILTER_SANITIZE_NUMBER_INT),
            'subscription'  => filter_var($body['subscription'], FILTER_VALIDATE_BOOLEAN),
        );

        // Define controller, fill up main variables
        $userDataController = new UserDataController($this->db, $request->getAttribute('user'));

        // Trying to register user
        $checkForm = $userDataController->updateSettings($attributes);

        if (!$checkForm) {
            $response = $response->withStatus(400);
            $data = array(
                'message' => $userDataController->getMessage(),
                'fields' => $userDataController->getFields(),
            );
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Settings updated',
            );
        }
    }

    return $response->withJson($data);
})->add($auth);

$app->get('/user-data/{user-name}', function (Request $request, Response $response) {
    $attributes = array(
        'name'  => $request->getAttribute('user-name')
    );
    
    // Define controller, fill up main variables
    $userDataController = new UserDataController($this->db, new stdClass());

    // Trying to register user
    $checkUser = $userDataController->getPublicUserData($attributes['name']);

    if (!$checkUser) {
        $response = $response->withStatus(400);
        $data = array(
            'message' => $userDataController->getMessage()
        );
    } else {
        // Passing success message
        $data = array(
            'state' => 'success',
            'user' => $checkUser,
        );
    }

    return $response->withJson($data);
});

class UserDataController
{
    private $db;
    private $user;
    private $message;
    private $fields;

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

    public function fetchUserData() {
        // In case auth failed, but errored, we send complete failure
        if (!$this->user->id) {
            return false;
        }

        $q = $this->db->prepare('SELECT `u`.`id`, `u`.`email`, `u`.`login` '.
            'FROM `users` '.
            'WHERE `id` = :id '.
            'LIMIT 1'
        );
        $q->bindParam(':id', $this->user->id, PDO::PARAM_INT);
        $q->execute();

        $user = $q->fetch(PDO::FETCH_OBJ);
        
        // If SQL query failed, we send complete failure
        if (!$user) {
            return false;
        }
       
        return $user;
    }

    public function fetchMenu() {
        $q = $this->db->query('SELECT `value` FROM `themagescms` WHERE `setting` = "menu"');
        $q->execute();
        $menu = $q->fetch();

        $menu =  json_decode($menu['value']);
        
        $returnMenu = [];
        foreach($menu as $value) {
            // Check if access level allow user to see menu item
            if ($value->level <= $this->user->level) {
                $returnMenu[$value->key] = [
                    'url'           => '/' . $value->key,
                    'title'         => $value->name,
                    'icon_classes'  => $value->icon_classes,
                ];
            }
        }

        $returnMenu['logout'] = [
            'url'           => '/logout',
            'title'         => 'Logout',
            'icon_classes'  => 'fa fa-sign-out',
        ];

        return $returnMenu;
    }

    public function getPublicUserData($userName) {
        $q = $this->db->prepare(
            'SELECT `u`.`id`, `u`.`name`, `u`.`battletag`, `u`.`registration_date`, `u`.`avatar`, `u`.`experience` '.
            'FROM `users` AS `u` '.
            'WHERE `u`.`name` = :name '.
            'LIMIT 1'
        );
        $q->bindParam(':name', $userName, PDO::PARAM_STR);
        $q->execute();
        $user = $q->fetch();

        // Returning message in case if user not found
        if (!$user) {
            $this->message = 'User not found';
            return false;
        }

        $user['registration_date'] = date('d - M - Y', strtotime($user['registration_date']));

        // Getting summoners list
        /* $user['summoners'] = $this->db->fetch(
            'SELECT `region`, `summoner_id`, `name`, `league`, `division` FROM `summoners` '.
            'WHERE `user_id` = '.(int)$user->id.' AND '.
            '`approved` = 1 '
        ); */

        return $user;
    }

    public function updateSettings($attributes) {
        if (!$this->checkFormSettings($attributes)) {
            return false;
        }

        $q = $this->db->prepare(
            'UPDATE `users` SET '.
            '`timestyle` = :timestyle '.
            'WHERE `id` = :id '.
            'LIMIT 1'
        );
        $q->bindParam(':timestyle', $attributes['timestyle'], PDO::PARAM_INT);
        $q->bindParam(':id', $this->user->id, PDO::PARAM_INT);
        $q->execute();

        if ($attributes['subscription'] === true) {
            $subscriptionRemoved = 0;
        } else {
            $subscriptionRemoved = 1;
        }

        $q = $this->db->prepare(
            'UPDATE `subscribe` SET '.
            '`removed` = :subscription '.
            'WHERE `email` = :email '.
            'LIMIT 1'
        );
        $q->bindParam(':subscription', $subscriptionRemoved, PDO::PARAM_INT);
        $q->bindParam(':email', $this->user->email, PDO::PARAM_STR);
        $q->execute();

        return true; 
    }

    public function updatePassword($attributes) {
        if (!$this->checkFormPassword($attributes)) {
            return false;
        }

        $convertedPassword = UserDataController::passwordConvert($attributes['pass']);
        $q = $this->db->prepare(
            'UPDATE `users` SET '.
            '`password` = :password '.
            'WHERE `id` = :id '.
            'LIMIT 1'
        );
        $q->bindParam(':password', $convertedPassword, PDO::PARAM_STR);
        $q->bindParam(':id', $this->user->id, PDO::PARAM_INT);
        $q->execute();

        return true; 
    }

    public function updatePublicProfile($attributes) {
        if (!$this->checkFormPublic($attributes)) {
            return false;
        }

        $q = $this->db->prepare(
            'UPDATE `users` SET '.
            '`name` = :name, '.
            '`battletag` = :battletag, '.
            '`avatar` = :avatar '.
            'WHERE `id` = :id '.
            'LIMIT 1'
        );
        $q->bindParam(':name', $attributes['name'], PDO::PARAM_STR);
        $q->bindParam(':battletag', $attributes['battletag'], PDO::PARAM_STR);
        $q->bindParam(':avatar', $attributes['avatar'], PDO::PARAM_INT);
        $q->bindParam(':id', $this->user->id, PDO::PARAM_INT);
        $q->execute();

        return true; 
    }

    public static function passwordConvert($password) {
        $returnPassword = password_hash($password, PASSWORD_BCRYPT);
        
        return $returnPassword;
    }

    public static function passwordVerify($userSpecifiedPassword, $DbPassword) {
        if (password_verify($userSpecifiedPassword, $DbPassword)) {
            return true;
        }
        
        return false;
    }

    private function checkFormSettings($attributes) {
        if (!is_numeric($attributes['timestyle'])) {
            $this->message .= 'Time style is incorrect<br />';
            $this->fields[] = 'timestyle';
        }

        /* if ($attributes['subscription']) {
            $this->message .= 'Subscription error?<br />';
            $this->fields[] = 'subscription';
        } */

        // In case if there are any messages, return false
        if ($this->message) {
            return false;
        }

        return true;
    }

    private function checkFormPassword($attributes) {
        if (!$attributes['oldPass']) {
            $this->message .= 'Current Password is empty<br />';
            $this->fields[] = 'oldPass';
        }

        $q = $this->db->prepare(
            'SELECT `password` FROM `users` '.
            'WHERE `id` = :id '.
            'LIMIT 1'
        );
        $q->bindParam(':id', $this->user->id, PDO::PARAM_INT);
        $q->execute();
        $checkPassword = $q->fetch();

        if (!UserDataController::passwordVerify($attributes['oldPass'], $checkPassword['password'])) {
            $this->message .= 'Current password is incorrect<br />';
            $this->fields[] = 'oldPass';
        } 

        if (!$attributes['pass']) {
            $this->message .= 'Password is empty<br />';
            $this->fields[] = 'pass';
        }

        if (strlen($attributes['pass']) < 6) {
            $this->message .= 'Password must be at least 6 characters long<br />';
            $this->fields[] = 'pass';
        }

        if ($attributes['pass'] !== $attributes['repeatPass']) {
            $this->message .= 'Passwords does not match<br />';
            $this->fields[] = 'repeatPass';
        }

        // In case if there are any messages, return false
        if ($this->message) {
            return false;
        }

        return true;
    }

    private function checkFormPublic($attributes) {
        if (!$attributes['name']) {
            $this->message .= 'Name is empty<br />';
            $this->fields[] = 'name';
        }

        if (strlen($attributes['name']) > 32) {
            $this->message .= 'Name is too long<br />';
            $this->fields[] = 'name';
        }


        $q = $this->db->prepare(
            'SELECT `name` FROM `users` '.
            'WHERE `name` = :name '.
            'AND `id` != :id '.
            'LIMIT 1'
        );
        $q->bindParam(':name', $attributes['name'], PDO::PARAM_STR);
        $q->bindParam(':id', $this->user->id, PDO::PARAM_INT);
        $q->execute();
        $checkName = $q->fetch();

        if ($checkName) {
            $this->message .= 'Name is already taken<br />';
            $this->fields[] = 'name';
        } 

        // Only check battletag if something is actually is inside the field, this can be empty
        if ($attributes['battletag']) {
            $battleTagBreakdown = explode('#', $attributes['battletag']);
            if (!isset($battleTagBreakdown[0]) || !$battleTagBreakdown[0] || !isset($battleTagBreakdown[1]) || !is_numeric($battleTagBreakdown[1])) {
                $this->message .= 'Battletag is incorrect, must be in format [Name]#[Numbers]<br />';
                $this->fields[] = 'battletag';    
            }
            //Achievements::give(31);//The winter is comming.. I mean Blizzard!
        }

        // Should not happen, but still...
        if (!$attributes['avatar']) {
            $this->message .= 'Avatar is empty... how did you even do that?<br />';
            $this->fields[] = 'avatar';
        }

        // In case if there are any messages, return false
        if ($this->message) {
            return false;
        }

        return true;
    }
}