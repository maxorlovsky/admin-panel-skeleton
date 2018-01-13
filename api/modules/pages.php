<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/api/public/pages', function(Request $request, Response $response) {
    $attributes = array(
        'site_id'   => filter_var($request->getAttribute('siteId'), FILTER_SANITIZE_NUMBER_INT),
    );

    // Define controller, fill up main variables
    $pagesController = new PagesController($this->db);

    $pages = $pagesController->getPublicPages($attributes);

    $data = array(
        'pages' => $pages
    );

    return $response->withJson($data, null, JSON_NUMERIC_CHECK);
});

$app->get('/api/pages', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        $attributes = array(
            'site_id'   => filter_var($request->getAttribute('siteId'), FILTER_SANITIZE_NUMBER_INT),
        );

        // Define controller, fill up main variables
        $pagesController = new PagesController($this->db, $request->getAttribute('user'));

        $pages = $pagesController->getPages($attributes);

        $data = array(
            'pages' => $pages
        );
    }

    return $response->withJson($data, null, JSON_NUMERIC_CHECK);
})->add($auth);

$app->get('/api/pages/{id}', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        $attributes = array(
            'site_id'   => filter_var($request->getAttribute('siteId'), FILTER_SANITIZE_NUMBER_INT),
            'id'        => filter_var($request->getAttribute('id'), FILTER_SANITIZE_NUMBER_INT),
        );

        // Define controller, fill up main variables
        $pagesController = new PagesController($this->db, $request->getAttribute('user'));
        
        $data = array(
            'page' => $pagesController->getPage($attributes)
        );
    }

    return $response->withJson($data, null, JSON_NUMERIC_CHECK);
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
            'site_id'   => filter_var($body['site_id'], FILTER_SANITIZE_NUMBER_INT),
            'title'     => filter_var($body['meta_title'], FILTER_SANITIZE_STRING),
            'description'=> filter_var($body['meta_description'], FILTER_SANITIZE_STRING),
            'link'      => filter_var($body['link'], FILTER_SANITIZE_STRING),
            'logged_in' => filter_var($body['logged_in'], FILTER_SANITIZE_NUMBER_INT),
            'text'      => filter_var($body['text'], FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'enabled'   => filter_var($body['enabled'], FILTER_SANITIZE_NUMBER_INT),
        );
        
        // Define controller, fill up main variables
        $pagesController = new PagesController($this->db, $user);

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
                'info'      => 'Page added <b>' . $attributes['title'] . '</b>'
            ]);
        }
    }

    return $response->withJson($data, null, JSON_NUMERIC_CHECK);
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
            'title'     => filter_var($body['meta_title'], FILTER_SANITIZE_STRING),
            'description'=> filter_var($body['meta_description'], FILTER_SANITIZE_STRING),
            'link'      => filter_var($body['link'], FILTER_SANITIZE_STRING),
            'logged_in' => filter_var($body['logged_in'], FILTER_SANITIZE_NUMBER_INT),
            'text'      => filter_var($body['text'], FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'enabled'   => filter_var($body['enabled'], FILTER_SANITIZE_NUMBER_INT),
        );

        // Define controller, fill up main variables
        $pagesController = new PagesController($this->db, $user);

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
                'info'      => 'Page updated [<b>' . $attributes['id'] . '</b>]'
            ]);
        }
    }

    return $response->withJson($data, null, JSON_NUMERIC_CHECK);
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
        $pagesController = new PagesController($this->db, $user);

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

    return $response->withJson($data, null, JSON_NUMERIC_CHECK);
})->add($auth);

class PagesController
{
    private $db;
    private $user;
    public $fields;
    public $message;

    public function __construct($db, $user = null) {
        $this->db = $db;
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

    public function getPublicPages($attributes) {
        $q = $this->db->prepare(
            'SELECT `title`, `description`, `link`, `logged_in`, `text` '.
            'FROM `mo_pages` '.
            'WHERE `deleted` = 0 '.
            'AND `enabled` = 1 '.
            'AND `site_id` = :site_id '
        );

        $q->bindParam(':site_id', $attributes['site_id'], PDO::PARAM_INT);

        $q->execute();

        $pages = $q->fetchAll();

        foreach($pages as &$v) {
            $v['text'] = html_entity_decode($v['text'], ENT_QUOTES);
        }

        return $pages;
    }

    public function getPages($attributes) {
        $q = $this->db->prepare(
            'SELECT `id`, `title`, `link`, `logged_in`, `enabled` '.
            'FROM `mo_pages` '.
            'WHERE `deleted` = 0 '.
            'AND `site_id` = :site_id '
        );

        $q->bindParam(':site_id', $attributes['site_id'], PDO::PARAM_INT);

        $q->execute();

        return $q->fetchAll();
    }

    public function getPage($attributes) {
        $q = $this->db->prepare(
            'SELECT `id`, `title`, `description`, `link`, `logged_in`, `text`, `enabled` '.
            'FROM `mo_pages` '.
            'WHERE `id` = :id '.
            'AND `deleted` = 0 '.
            'AND `site_id` = :site_id '.
            'LIMIT 1'
        );

        $q->bindParam(':site_id', $attributes['site_id'], PDO::PARAM_INT);
        $q->bindParam(':id', $attributes['id'], PDO::PARAM_INT);

        $q->execute();

        $page = $q->fetch();

        $page['text'] = html_entity_decode($page['text'], ENT_QUOTES);
        
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
            '`site_id` = :site_id, '.
            '`title` = :title, '.
            '`description` = :description, '.
            '`link` = :link, '.
            '`logged_in` = :logged_in, '.
            '`text` = :text, '.
            '`enabled` = :enabled '
        );

        $logged_in = $attributes['logged_in'] ? true : false;
        $enabled = $attributes['enabled'] ? true : false;

        $q->bindParam(':site_id', $attributes['site_id'], PDO::PARAM_INT);
        $q->bindParam(':title', $attributes['title'], PDO::PARAM_STR);
        $q->bindParam(':description', $attributes['description'], PDO::PARAM_STR);
        $q->bindParam(':link', $attributes['link'], PDO::PARAM_INT);
        $q->bindParam(':logged_in', $logged_in, PDO::PARAM_BOOL);
        $q->bindParam(':text', $attributes['text'], PDO::PARAM_STR);
        $q->bindParam(':enabled', $enabled, PDO::PARAM_BOOL);
        
        try {
            $q->execute();
        } catch(Exception $e) {
            ddump($e->getMessage());
        }

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
            '`title` = :title, '.
            '`description` = :description, '.
            '`link` = :link, '.
            '`logged_in` = :logged_in, '.
            '`text` = :text, '.
            '`enabled` = :enabled '.
            'WHERE `id` = :id '
        );

        $logged_in = $attributes['logged_in'] ? true : false;
        $enabled = $attributes['enabled'] ? true : false;

        $q->bindParam(':title', $attributes['title'], PDO::PARAM_STR);
        $q->bindParam(':description', $attributes['description'], PDO::PARAM_STR);
        $q->bindParam(':link', $attributes['link'], PDO::PARAM_INT);
        $q->bindParam(':logged_in', $logged_in, PDO::PARAM_BOOL);
        $q->bindParam(':text', $attributes['text'], PDO::PARAM_STR);
        $q->bindParam(':enabled', $enabled, PDO::PARAM_BOOL);
        $q->bindParam(':id', $attributes['id'], PDO::PARAM_INT);

        $q->execute();

        return true;
    }

    private function checkForm($attributes, $type) {
        if (!$attributes['title']) {
            $this->message .= 'Meta title is empty<br />';
            $this->fields[] = 'title';
        } else if (strlen($attributes['title']) > 100) {
            $this->message .= 'Meta title is too long<br />';
            $this->fields[] = 'title';
        }

        if ($attributes['description'] && strlen($attributes['description']) > 160) {
            $this->message .= 'Meta description is too long<br />';
            $this->fields[] = 'description';
        }

        if (!$attributes['link']) {
            $this->message .= 'Link is empty<br />';
            $this->fields[] = 'link';
        } else if (strlen($attributes['link']) > 300) {
            $this->message .= 'Link is too long<br />';
            $this->fields[] = 'link';
        } else if (preg_match('/\s/', $attributes['link'])) {
            $this->message .= 'Link must not have spaces<br />';
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