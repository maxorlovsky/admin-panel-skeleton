<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/api/labels/public/{site_id}', function(Request $request, Response $response) {
    $attributes = array(
        'site_id'   => filter_var($request->getAttribute('site_id'), FILTER_SANITIZE_NUMBER_INT),
    );

    // Define controller, fill up main variables
    $labelsController = new LabelsController($this->db, $this->params);

    $labels = $labelsController->getPublicLabels($attributes);

    $data = array(
        'labels' => $labels
    );

    return $response->withJson($data);
});

$app->get('/api/labels/{site_id}', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        $attributes = array(
            'site_id'   => filter_var($request->getAttribute('site_id'), FILTER_SANITIZE_NUMBER_INT),
        );

        // Define controller, fill up main variables
        $labelsController = new LabelsController($this->db, $this->params, $request->getAttribute('user'));

        $labels = $labelsController->getLabels($attributes);

        $data = array(
            'labels' => $labels
        );
    }

    return $response->withJson($data);
})->add($auth);

$app->get('/api/labels/{site_id}/{id}', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        $attributes = array(
            'site_id'   => filter_var($request->getAttribute('site_id'), FILTER_SANITIZE_NUMBER_INT),
            'id'        => filter_var($request->getAttribute('id'), FILTER_SANITIZE_NUMBER_INT),
        );

        // Define controller, fill up main variables
        $labelsController = new LabelsController($this->db, $this->params, $request->getAttribute('user'));
        
        $data = array(
            'label' => $labelsController->getLabel($attributes)
        );
    }

    return $response->withJson($data);
})->add($auth);

$app->post('/api/labels/add', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Fetching post parameters
        $body = $request->getParsedBody();

        $user = $request->getAttribute('user');

        $attributes = array(
            'name'      => filter_var($body['name'], FILTER_SANITIZE_STRING),
            'output'    => filter_var($body['output'], FILTER_SANITIZE_STRING),
            'site_id'   => filter_var($body['site_id'], FILTER_SANITIZE_NUMBER_INT),
        );
        
        // Define controller, fill up main variables
        $labelsController = new LabelsController($this->db, $this->params, $user);

        // Trying to register user
        $checkLabel = $labelsController->addLabel($attributes);

        if (!$checkLabel) {
            $response = $response->withStatus(400);
            $data = array(
                'message' => $labelsController->getMessage(),
                'fields' => $labelsController->getFields(),
            );

            Log::save($this->db, [
                'module'    => 'labels',
                'type'      => 'add',
                'user_id'   => $user->id,
                'info'      => 'Label creation failed <b>' . $labelsController->getMessage() . '</b>'
            ]);
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Success! New label added.',
            );

            Log::save($this->db, [
                'module'    => 'labels',
                'type'      => 'add',
                'user_id'   => $user->id,
                'info'      => 'Label added <b>' . $attributes['name'] . '</b>'
            ]);
        }
    }

    return $response->withJson($data);
})->add($auth);

$app->post('/api/labels/edit', function(Request $request, Response $response) {
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
            'output'    => filter_var($body['output'], FILTER_SANITIZE_STRING),
        );

        // Define controller, fill up main variables
        $labelsController = new LabelsController($this->db, $this->params, $user);

        // Trying to register user
        $checkLabel = $labelsController->editLabel($attributes);

        if (!$checkLabel) {
            $response = $response->withStatus(400);
            $data = array(
                'message' => $labelsController->getMessage(),
                'fields' => $labelsController->getFields(),
            );

            Log::save($this->db, [
                'module'    => 'labels',
                'type'      => 'edit',
                'user_id'   => $user->id,
                'info'      => 'Label update failed <b>' . $labelsController->getMessage() . '</b>'
            ]);
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Label updated.',
            );

            Log::save($this->db, [
                'module'    => 'labels',
                'type'      => 'edit',
                'user_id'   => $user->id,
                'info'      => 'Label updated <b>' . $attributes['name'] . '</b>'
            ]);
        }
    }

    return $response->withJson($data);
})->add($auth);

$app->delete('/api/labels/delete/{id}', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        $user = $request->getAttribute('user');

        $attributes = array(
            'id' => $request->getAttribute('id'),
        );

        // Define controller, fill up main variables
        $labelsController = new LabelsController($this->db, $this->params, $request->getAttribute('user'));

        $checkLabel = $labelsController->deleteLabel($attributes['id']);
        
        if (!$checkLabel) {
            $response = $response->withStatus(400);
            $data = array(
                'message' => $labelsController->getMessage(),
            );

            Log::save($this->db, [
                'module'    => 'labels',
                'type'      => 'delete',
                'user_id'   => $user->id,
                'info'      => 'Label deletion failed [<b>' . $attributes['id'] . '</b>] <b>' . $labelsController->getMessage() . '</b>'
            ]);
        } else {
            // Passing success message
            $data = array(
                'state' => 'success',
                'message' => 'Label removed',
            );

            Log::save($this->db, [
                'module'    => 'labels',
                'type'      => 'delete',
                'user_id'   => $user->id,
                'info'      => 'Label removed [<b>' . $attributes['id'] . '</b>]'
            ]);
        }
    }

    return $response->withJson($data);
})->add($auth);

class LabelsController
{
    private $db;
    private $params;
    private $user;
    public $fields;
    public $message;

    public function __construct($db, $params, $user = null) {
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

    public function getPublicLabels($attributes) {
        $q = $this->db->prepare(
            'SELECT `name`, `output` '.
            'FROM `mo_labels` '.
            'WHERE `deleted` = 0 '.
            'AND `site_id` = :site_id '.
            'ORDER BY `name` ASC '
        );
        
        $q->bindParam(':site_id', $attributes['site_id'], PDO::PARAM_INT);

        $q->execute();

        return $q->fetchAll();
    }

    public function getLabels($attributes) {
        $q = $this->db->prepare(
            'SELECT `id`, `name`, `output` '.
            'FROM `mo_labels` '.
            'WHERE `deleted` = 0 '.
            'AND `site_id` = :site_id '.
            'ORDER BY `name` ASC '
        );
        
        $q->bindParam(':site_id', $attributes['site_id'], PDO::PARAM_INT);

        $q->execute();

        return $q->fetchAll();
    }

    public function getLabel($attributes) {
        $q = $this->db->prepare(
            'SELECT `id`, `name`, `output` '.
            'FROM `mo_labels` '.
            'WHERE `id` = :id '.
            'AND `site_id` = :site_id '.
            'AND `deleted` = 0 '.
            'LIMIT 1'
        );

        $q->bindParam(':id', $attributes['id'], PDO::PARAM_INT);
        $q->bindParam(':site_id', $attributes['site_id'], PDO::PARAM_INT);

        $q->execute();

        $label = $q->fetch();

        $label['output'] = html_entity_decode($label['output'], ENT_QUOTES);
        
        return $label;
    }

    public function checkIfLabelExist($name, $id = 0) {
        $q = $this->db->prepare(
            'SELECT `id` '.
            'FROM `mo_labels` '.
            'WHERE `name` = :name '.
            'AND `deleted` = 0 '.
            'AND `id` != :id '.
            'LIMIT 1'
        );
        $q->bindParam(':name', $name, PDO::PARAM_STR);
        $q->bindParam(':id', $id, PDO::PARAM_INT);
        $q->execute();

        if ($q->fetch()) {
            return true;
        }

        return false;
    }

    public function addLabel($attributes) {
        $formData = $this->checkForm($attributes, 'add');

        // In case check failed, $message should have the error
        if (!$formData) {
            return false;
        }

        $q = $this->db->prepare(
            'INSERT INTO `mo_labels` SET '.
            '`site_id` = :site_id, '.
            '`name` = :name, '.
            '`output` = :output '
        );

        $q->bindParam(':site_id', $attributes['site_id'], PDO::PARAM_INT);
        $q->bindParam(':name', $attributes['name'], PDO::PARAM_STR);
        $q->bindParam(':output', $attributes['output'], PDO::PARAM_STR);
        
        $q->execute();

        return true;
    }

    public function editLabel($attributes) {
        $formData = $this->checkForm($attributes, 'edit');

        // In case check failed, $message should have the error
        if (!$formData) {
            return false;
        }

        $q = $this->db->prepare(
            'UPDATE `mo_labels` SET '.
            '`name` = :name, '.
            '`output` = :output '.
            'WHERE `id` = :id '
        );

        $q->bindParam(':name', $attributes['name'], PDO::PARAM_STR);
        $q->bindParam(':output', $attributes['output'], PDO::PARAM_STR);
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
        } else if (preg_match('/\s/',$attributes['name'])) {
            $this->message .= 'Name must not have spaces<br />';
            $this->fields[] = 'name';
        } else if ($type === 'add' && $this->checkIfLabelExist($attributes['name'])) {
            $this->message .= 'Label with this key name is already in use<br />';
            $this->fields[] = 'name';
        } else if ($type === 'edit' && $this->checkIfLabelExist($attributes['name'], $attributes['id'])) {
            $this->message .= 'Label with this key name is already in use<br />';
            $this->fields[] = 'name';
        }

        if ($this->message) {
            return false;
        }

        return true;
    }

    public function deleteLabel($id) {
        $this->db->query('UPDATE `mo_labels` SET `deleted` = 1 WHERE `id` = ' . (int)$id . ' LIMIT 1');
        
        return true;
    }
}