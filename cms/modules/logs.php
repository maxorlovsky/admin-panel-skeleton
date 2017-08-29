<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/api/logs', function(Request $request, Response $response) {
    if (!$request->getAttribute('isLogged')) {
        $response = $response->withStatus(401);
        $data = array('message' => 'Authorization required');
    } else {
        // Fetching post parameters
        $body = $request->getParsedBody();

        $attributes = array(
            'module'=> filter_var($body['module'], FILTER_SANITIZE_STRING),
            'type'  => filter_var($body['type'], FILTER_SANITIZE_STRING),
            'offset'=> filter_var($body['offset'], FILTER_SANITIZE_NUMBER_INT),
            'page'  => filter_var($body['page'], FILTER_SANITIZE_NUMBER_INT),
        );

        // Define controller, fill up main variables
        $logsController = new LogsController($this->db, $request->getAttribute('user'));

        $data = array(
            'logs'      => $logsController->getLogs($attributes),
            'maxAmount' => $logsController->getMaxAmount()
        );
    }

    return $response->withJson($data);
})->add($auth);

class LogsController
{
    private $db;
    private $user;
    private $wherePrepare;
    private $offset;
    private $limit;

    public function __construct($db, $user) {
        $this->db = $db;
        $this->user = $user;
        $this->wherePrepare = '';
        $this->offset = 0;
        $this->limit = 20;
    }

    public function getLogs($attributes) {
        $this->applyParameters($attributes);

        $this->offset = $this->limit * ($attributes['page'] - 1);

        $q = $this->db->prepare(
            'SELECT `l`.*, `a`.`login` '.
			'FROM `tm_logs` AS `l` '.
			'LEFT JOIN `tm_admins` AS `a` ON `l`.`user_id` = `a`.`id` '.
            'WHERE 1=1 '.
            $this->wherePrepare .
            'ORDER BY `l`.`id` DESC '.
            'LIMIT ' . $this->offset . ', ' . $this->limit
        );

        if (isset($attributes['module']) && $attributes['module']) {
            $q->bindValue(':module', $attributes['module'], PDO::PARAM_STR);
        }

        if (isset($attributes['type']) && $attributes['type']) {
            $q->bindValue(':type', $attributes['type'], PDO::PARAM_STR);
        }
        
        $q->execute();

        $logs = $q->fetchAll();

        if (!$logs) {
            return false;
        }
        
        return $logs;
    }

    public function getMaxAmount() {
        $q = $this->db->query('SELECT COUNT(`id`) AS `amount` FROM `tm_logs`');
        $row = $q->fetch();
        
        return (int)$row['amount'];
    }

    private function applyParameters($attributes) {
        // Game filter
        if (isset($attributes['module']) && $attributes['module']) {
            $this->wherePrepare .= 'AND `l`.`module` = :module ';
        }

        if (isset($attributes['type']) && $attributes['type']) {
            $this->wherePrepare .= 'AND `l`.`type` = :type ';
        }

        // Offset parameter
        if (isset($attributes['offset']) && $attributes['offset']) {
            $this->offset = (int)$attributes['offset'];
        }

        return true;
    }
}