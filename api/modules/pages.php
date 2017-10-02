<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/api/pages', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Define controller, fill up main variables
        $pagesController = new PagesController($this->db, $this->params, $request->getAttribute('user'));

        $pages = $pagesController->getPages();

        $data = array(
            'pages' => $pages
        );
    }

    return $response->withJson($data);
})->add($auth);

$app->get('/api/pages/{id}', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        $attributes = array(
            'id' => $request->getAttribute('id'),
        );

        // Define controller, fill up main variables
        $pagesController = new PagesController($this->db, $this->params, $request->getAttribute('user'));
        
        $data = array(
            'page' => $pagesController->getPage($attributes['id'])
        );
    }

    return $response->withJson($data);
})->add($auth);

$app->post('/api/pages/add', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Fetching post parameters
        $body = $request->getParsedBody();

        $user = $request->getAttribute('user');

        $attributes = array(
            'name'      => filter_var($body['name'], FILTER_SANITIZE_STRING),
            'link'      => filter_var($body['link'], FILTER_SANITIZE_STRING),
            'logged_in' => filter_var($body['logged_in'], FILTER_SANITIZE_NUMBER_INT),
            'text'      => filter_var($body['text'], FILTER_SANITIZE_STRING),
            'enabled'   => filter_var($body['enabled'], FILTER_SANITIZE_NUMBER_INT),
        );
        
        // Define controller, fill up main variables
        $pagesController = new PagesController($this->db, $this->params, $user);

        // Trying to register user
        $checkPage = $pagesController->addPage($attributes);

        if (!$checkPage) {
            $response = $response->withStatus(400);
            $data = array(
                'message' => $pagesController->getMessage(),
                'fields' => $pagesController->getFields(),
            );

            Log::save($this->db, [
                'module'    => 'pages',
                'type'      => 'add',
                'user_id'   => $user->id,
                'info'      => 'Page creation failed <b>' . $pagesController->getMessage() . '</b>'
            ]);
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Success! New page added.',
            );

            Log::save($this->db, [
                'module'    => 'pages',
                'type'      => 'add',
                'user_id'   => $user->id,
                'info'      => 'Page added <b>' . $attributes['name'] . '</b>'
            ]);
        }
    }

    return $response->withJson($data);
})->add($auth);

$app->post('/api/pages/edit', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Fetching post parameters
        $body = $request->getParsedBody();

        $user = $request->getAttribute('user');

        $attributes = array(
            'id'        => filter_var($body['id'], FILTER_SANITIZE_NUMBER_INT),
            'name'      => filter_var($body['name'], FILTER_SANITIZE_STRING),
            'link'      => filter_var($body['link'], FILTER_SANITIZE_STRING),
            'logged_in' => filter_var($body['logged_in'], FILTER_SANITIZE_NUMBER_INT),
            'text'      => filter_var($body['text'], FILTER_SANITIZE_STRING),
            'enabled'   => filter_var($body['enabled'], FILTER_SANITIZE_NUMBER_INT),
        );

        // Define controller, fill up main variables
        $pagesController = new PagesController($this->db, $this->params, $user);

        // Trying to register user
        $checkPage = $pagesController->editPage($attributes);

        if (!$checkPage) {
            $response = $response->withStatus(400);
            $data = array(
                'message' => $pagesController->getMessage(),
                'fields' => $pagesController->getFields(),
            );

            Log::save($this->db, [
                'module'    => 'pages',
                'type'      => 'edit',
                'user_id'   => $user->id,
                'info'      => 'Page update failed <b>' . $pagesController->getMessage() . '</b>'
            ]);
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Page updated.',
            );

            Log::save($this->db, [
                'module'    => 'pages',
                'type'      => 'edit',
                'user_id'   => $user->id,
                'info'      => 'Page updated <b>' . $attributes['name'] . '</b>'
            ]);
        }
    }

    return $response->withJson($data);
})->add($auth);

$app->delete('/api/pages/delete/{id}', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        $user = $request->getAttribute('user');

        $attributes = array(
            'id' => $request->getAttribute('id'),
        );

        // Define controller, fill up main variables
        $pagesController = new PagesController($this->db, $this->params, $request->getAttribute('user'));

        $checkPage = $pagesController->deletePage($attributes['id']);
        
        if (!$checkPage) {
            $response = $response->withStatus(400);
            $data = array(
                'message' => $pagesController->getMessage(),
            );

            Log::save($this->db, [
                'module'    => 'pages',
                'type'      => 'delete',
                'user_id'   => $user->id,
                'info'      => 'Page deletion failed [<b>' . $attributes['id'] . '</b>] <b>' . $pagesController->getMessage() . '</b>'
            ]);
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Page removed',
            );

            Log::save($this->db, [
                'module'    => 'pages',
                'type'      => 'delete',
                'user_id'   => $user->id,
                'info'      => 'Page removed [<b>' . $attributes['id'] . '</b>]'
            ]);
        }
    }

    return $response->withJson($data);
})->add($auth);

class PagesController
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

    public function getPages() {
        $q = $this->db->query(
            'SELECT `id`, `name`, `link`, `logged_in`, `enabled` '.
            'FROM `mo_pages` AS '.
            'WHERE `deleted` = 0 '
        );
        $q->execute();

        $pages = $q->fetchAll();

        return $pages;
    }

    public function getPage($id) {
        $q = $this->db->prepare(
            'SELECT `id`, `name`, `link`, `logged_in`, `text`, `enabled` '.
            'FROM `mo_pages` '.
            'WHERE `id` = :id AND `deleted` = 0 '.
            'LIMIT 1'
        );
        $q->bindParam(':id', $id, PDO::PARAM_INT);
        $q->execute();

        $page = $q->fetch();
        
        return $page;
    }

    public function addPage($attributes) {
        $formData = $this->checkForm($attributes, 'add');

        // In case check failed, $message should have the error
        if (!$formData) {
            return false;
        }

        $q = $this->db->prepare(
            'INSERT INTO `mo_pages` SET '.
            '`name` = :name, '.
            '`link` = :link, '.
            '`logged_in` = :logged_in, '.
            '`text` = :text, '.
            '`enabled` = :enabled '
        );

        $logged_in = $attributes['logged_in'] ? true : false;
        $enabled = $attributes['enabled'] ? true : false;

        $q->bindParam(':name', $attributes['name'], PDO::PARAM_STR);
        $q->bindParam(':link', $attributes['link'], PDO::PARAM_INT);
        $q->bindParam(':logged_in', $logged_in, PDO::PARAM_BOOL);
        $q->bindParam(':text', $attributes['text'], PDO::PARAM_STR);
        $q->bindParam(':enabled', $enabled, PDO::PARAM_BOOL);
        
        $q->execute();

        return true;
    }

    public function editPage($attributes) {
        $formData = $this->checkForm($attributes, 'edit');

        // In case check failed, $message should have the error
        if (!$formData) {
            return false;
        }

        $q = $this->db->prepare(
            'UPDATE `mo_pages` SET '.
            '`name` = :name, '.
            '`link` = :link, '.
            '`logged_in` = :logged_in, '.
            '`text` = :text, '.
            '`enabled` = :enabled '.
            'WHERE `id` = :id '
        );

        $enabled = $attributes['enabled'] ? true : false;
        $dates = json_encode($attributes['dates']);

        $q->bindParam(':name', $attributes['name'], PDO::PARAM_STR);
        $q->bindParam(':link', $attributes['link'], PDO::PARAM_INT);
        $q->bindParam(':logged_in', $logged_in, PDO::PARAM_BOOL);
        $q->bindParam(':text', $attributes['text'], PDO::PARAM_STR);
        $q->bindParam(':enabled', $enabled, PDO::PARAM_BOOL);
        $q->bindParam(':id', $attributes['id'], PDO::PARAM_INT);

        $q->execute();

        return true;
    }

    private function checkForm($attributes, $type) {
        if (!$attributes['name']) {
            $this->message .= 'Name is empty<br />';
            $this->fields[] = 'name';
        } else if (strlen($attributes['name']) > 100) {
            $this->message .= 'Name is too long<br />';
            $this->fields[] = 'name';
        }

        if (!$attributes['link']) {
            $this->message .= 'Link is empty<br />';
            $this->fields[] = 'link';
        } else if (strlen($attributes['link']) > 300) {
            $this->message .= 'Link is too long<br />';
            $this->fields[] = 'link';
        }

        if ($this->message) {
            return false;
        }

        return true;
    }

    public function deletePage($id) {
        $this->db->query('UPDATE `mo_pages` SET `deleted` = 1 WHERE `id` = ' . (int)$id . ' LIMIT 1');
        
        return true;
    }
}