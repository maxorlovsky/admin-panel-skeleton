<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/api/login', function (Request $request, Response $response) {
    if ($request->getAttribute('isLogged')) {
        $response = $response->withStatus(405);
        $data = array('message' => 'Already authorized');
    } else {
        // Fetching post parameters
        $body = $request->getParsedBody();

        $attributes = array(
            'login' => filter_var($body['login'], FILTER_SANITIZE_STRING),
            'password'  => filter_var($body['password'], FILTER_SANITIZE_STRING),
        );

        // Define controller, fill up main variables
        $loginController = new LoginController($this->params, $this->db);

        // Authenticating user and in case of success return token
        $checkUser = $loginController->login($attributes);

        if (!$checkUser) {
            $response = $response->withStatus(400);
            $data = array('message' => $loginController->getMessage());

            Log::save($this->db, [
                'module'=> 'login',
                'type'  => 'fail',
                'info'  => 'Error login as <b>' . $attributes['login'] . '</b> (' . $loginController->getMessage() . ')'
            ]);
        } else {
            // Passing session token to the user
            $data['sessionToken'] = $checkUser;
            Log::save($this->db, [
                'module'=> 'login',
                'type'  => 'success',
                'info'  => 'Success login as <b>'.$attributes['login'].'</b>'
            ]);
        }
    }

    return $response->withJson($data, null, JSON_NUMERIC_CHECK);
})->add($auth);

class LoginController
{
    private $db;
    private $params;
    private $message;

    public function __construct($params, $db) {
        $this->params = $params;
        $this->db = $db;
        $this->message = '';
    }

    public function getMessage() {
        return $this->message;
    }

    public function login($attributes) {
        if (!$attributes['login']) {
            $this->message = 'Login is empty';
            return false;
        } else if (!$attributes['password']) {
            $this->message = 'Password is empty';
            return false;
        }

        if (!$this->checkBruteForce()) {
            $this->message = 'Brute force detected, your IP is blocked for 5 minutes';
            return false;
        }

        $result = $this->authentication($attributes);

        if (!$result) {
            $this->message = 'Login or password is incorrect';
            return false;
        }

        $sessionToken = $this->createToken($result['id']);

        // Saving last login date/time
        $this->db->query(
            'UPDATE `mo_admins` SET `last_login` = NOW() '.
            'WHERE `id` = '.(int)$result['id'].' LIMIT 1'
        );

        // Clean up user attempts
        $q = $this->db->prepare('DELETE FROM `mo_users_auth_attempts` WHERE `ip` = :ip LIMIT 1');
        $q->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
        $q->execute();

        return $sessionToken;
    }

    private function checkBruteForce() {
        $q = $this->db->prepare(
            'DELETE FROM `mo_users_auth_attempts` WHERE '.
            '`ip` = :ip AND '.
            '`timestamp` < NOW() - INTERVAL 5 MINUTE '.
            'LIMIT 2'
        );
        $q->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
        $q->execute();

        // Check if there were more than 1 attempts to login from same IP address
        $q = $this->db->prepare(
            'SELECT `attempts` FROM `mo_users_auth_attempts` WHERE '.
            '`ip` = :ip '.
            'LIMIT 1'
        );
        $q->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
        $q->execute();
        $attempts = $q->fetch();

        if ($attempts) {
            // User doing more than one attempt
            $attemptsNumber = (int)$attempts['attempts'] + 1;
            $q = $this->db->prepare('UPDATE `mo_users_auth_attempts` SET '.
                '`attempts` = '.$attemptsNumber.' '.
                'WHERE `ip` = :ip'
            );
            $q->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
            $q->execute();

            if ($attemptsNumber > 5) {
                return false;
            }
        } else {
            // 1st attempt
            $q = $this->db->prepare('INSERT INTO `mo_users_auth_attempts` SET '.
                '`ip` = :ip, '.
                '`attempts` = 1'
            );
            $q->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
            $q->execute();
        }

        return true;
    }

    private function authentication($attributes) {
        $q = $this->db->prepare(
            'SELECT `id`, `password` FROM `mo_admins` WHERE `login` = :login LIMIT 1'
        );
        $q->bindParam(':login', $attributes['login'], PDO::PARAM_STR);
        $q->execute();
        $user = $q->fetch();

        if ($this->passwordVerify($attributes['password'], $user['password'])) {
            // register last login in database
            return $user;
        }

        return false;
    }

    private function passwordVerify($userSpecifiedPassword, $DbPassword) {
        if (password_verify($userSpecifiedPassword, $DbPassword)) {
            return true;
        }
        
        return false;
    }

    private function createToken($userId) {
        $token = sha1(rand(0,999999).time());

        $this->db->query('DELETE FROM `mo_users_auth` WHERE `user_id` = '.(int)$userId.' LIMIT 1');
        $this->db->query('INSERT INTO `mo_users_auth` SET `user_id` = '.(int)$userId.', `token` = "'.$token.'"');

        return $token;
    }
}