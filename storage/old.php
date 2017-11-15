<?php

// This file should be run from command line
// It is not included in run.php

class Cron
{
    private function atomicFileReplace( $file, $data )
    {
		$file_tmp = $file.'.tmp';
		
		return (file_put_contents($file_tmp, $data) !== FALSE) && rename($file_tmp, $file);
    }
    
    /* public function sqlCleanUp() {
        Db::multi_query('CALL cleanupCmsData()');
        
        do 
        {
            $r = Db::store_result();
        }
        while ( Db::more_results() && Db::next_result() );
        
        return true;
    } */
}

/* public static function multi_query($query) {
    if (self::$connection == NULL) {
        return false;
    }
    
    return self::$connection->multi_query($query);
}

public static function store_result() {
    return self::$connection->store_result();
}

public static function more_results() {
    return self::$connection->more_results();
}

public static function next_result() {
    return self::$connection->next_result();
} */

class User
{
    public static function login($data) {
        if (!isset($_SESSION['recaptcha_login'])) {
            $_SESSION['recaptcha_login'] = 0;
        }
        
        Db::query('DELETE FROM `tm_user_auth_attempts` WHERE `ip` = "'.Db::escape_tags($_SERVER['REMOTE_ADDR']).'" AND `timestamp` < NOW() - INTERVAL 5 MINUTE LIMIT 2');
        $row = Db::fetchRow('SELECT `attempts` FROM `tm_user_auth_attempts` WHERE `ip` = "'.Db::escape_tags($_SERVER['REMOTE_ADDR']).'"');
        
        if (isset($row->attempts) && $row->attempts >= 20) {
            return '0;Brute force detected, your IP is blocked for 5 minutes';
        }
        
        if ($row) {
            Db::query('UPDATE `tm_user_auth_attempts` SET `attempts` = `attempts` + 1, `timestamp` = NOW() WHERE `ip` = "'.Db::escape_tags($_SERVER['REMOTE_ADDR']).'"');
        }
        else {
            Db::query('INSERT INTO `tm_user_auth_attempts` SET `ip` = "'.Db::escape_tags($_SERVER['REMOTE_ADDR']).'", `attempts` = 1');
        }
        
        if (isset($_SESSION['recaptcha_login']) && $_SESSION['recaptcha_login'] >= _cfg('availableLoginAttempts')) {
            $_SESSION['recaptcha_login'] += 1;
            if (isset($data['g-recaptcha-response']) && $data['g-recaptcha-response']) {
                $queryUrl = 'https://www.google.com/recaptcha/api/siteverify';
                $queryUrl .= '?secret='._cfg('recaptchaSecretKey');
                $queryUrl .= '&response='.urlencode($data['g-recaptcha-response']);
                $queryUrl .= '&remoteip='.urlencode($_SERVER['REMOTE_ADDR']);
                $response = json_decode(file_get_contents($queryUrl));
                
                if ($response->success != 1) {
                    return '0;'.at('are_you_robot');
                }
            }
            else {
                return '0;'.at('prove_not_robot');
            }
        }
        
        if (!$data['login'] || !$data['password']) {
            $_SESSION['recaptcha_login'] += 1;
            unset($_SESSION['token']);
            return '0;'.at('login_pass_incorrect');
        }
        else {
            $row = Db::fetchRow(
                'SELECT `id`, `login`, `email`, `level`, `language`, `editRedirect` '.
                'FROM `tm_admins` '.
                'WHERE `login` = "'.Db::escape_tags($data['login']).'" AND `password` = "'.sha1($data['password']._cfg('salt')).'" '.
                'LIMIT 1'
            );
            
            if (!isset($row) || $row === false) {
                $_SESSION['recaptcha_login'] += 1;
                
                unset($_SESSION['token']);
                return '0;'.at('login_pass_incorrect');
            }
            else {
                $_SESSION['token'] = sha1(rand(0,9999).time());
                Db::query('DELETE FROM `tm_user_auth` WHERE `user_id` = "'.(int)$row->id.'" LIMIT 1');
                Db::query('INSERT IGNORE INTO `tm_user_auth` '.
                    'SET '.
                    '`user_id` = '.(int)$row->id.', '.
                    '`token` = "'.$_SESSION['token'].'", '.
                    '`timestamp` = '.time()
                );
                
                Db::query('UPDATE `tm_admins` '.
                    'SET '.
                    '`last_login` = NOW(), '.
                    '`login_count` = `login_count` + 1, '.
                    '`last_ip` = "'.$_SERVER['REMOTE_ADDR'].'" '.
                    'WHERE `id` = '.(int)$row->id
                );
                
                unset($_SESSION['recaptcha_login']);
                
                
                return $row;
            }
        }
        
        return false;
    }
}

class System
{
    public $data;
    public $user;
    public $logged_in;
    public $messages;
    public $page;
    public $language;
    public $defaultPage;
    public $subPageOpen;
    public $apcEnabled = false;
    
    public function __construct($status = 1) {
    	$this->loadClasses();
        
        //Run only once!
        if ($status != 1) {
            //Making a connection
            Db::connect();

            //Check if database is in place, if not we run installation
            $q = Db::query('SHOW TABLES LIKE "themagescms"');
            if ($q->num_rows === 0) {
                $this->sqlInstall();
            }
        }

        if (!$this->data) {
            $this->data = new stdClass();
        }
        $this->apcEnabled = extension_loaded('apc');
        $this->defaultPage = 'dashboard';
        $this->fetchParams($status);
    }
    
    public function run() {
        $this->checkGetData();
        $this->getStrings();
        
        $template = new Template();
        $template->parse();
    }
    
    public function fetchParams($status) {
        global $cfg;
        
        $this->data->cmsSettings = array();
        $this->data->settings = array();
        $this->data->pages = array();
        $this->data->subpages = array();
        
        $data = array_merge($_GET, $_POST, $_SESSION);
         
        if (!isset($data['val1'])) {
        	$data['val1'] = false;
        }
        if (!isset($data['token'])) {
        	$data['token'] = false;
        }

        //Checking if upload allowed, if yes checking if folder have permissions
        if (_cfg('allowUpload') == 1 && is_readable(_cfg('uploads')) && substr(sprintf('%o', fileperms(_cfg('uploads'))), -3) != '777') {
            $cfg['allowUpload'] = 0;
        }
        
        $rows = Db::fetchRows('SELECT * FROM `themagescms`');
        if ($rows) {
        	foreach($rows as $v) {
        		$this->data->cmsSettings[$v->setting] = $v->value;
        	}
        }
        
        $rows = Db::fetchRows('SELECT * FROM `tm_settings` '.
            'WHERE `type` = "level" OR `setting` LIKE "site_%" '.
        	'ORDER BY `setting` = "dashboard" DESC'
        );
        if ($rows) {
        	foreach($rows as $v) {
        		$this->data->settings[$v->setting] = $v->value;
        	}
        	 
        	foreach($this->data->settings as $k => $v) {
        		if (substr($k,0,4)!='site') {
        			$this->data->pages[] = array($k, at($k), $v);
                }
        	}
        }
        
        //If there is a token then probably user is already logged in
        if ($data['token'] && !isset($this->logged_in)) {
            $this->user = User::fetchUserByToken($data['token']);
            if ($this->user !== false) {
                $this->logged_in = 1;
            }
            else {
            	User::logout();
            }
        }
        else {
        	$this->logged_in = 0;
        }
        
        if (isset($this->logged_in) && $this->logged_in) {
        	$row = Db::fetchRows('SELECT * FROM `tm_modules`');
            $updatedModulesList = new stdClass();
            if ($row) {
                foreach($row as $k => $v) {
                    $v->displayName = preg_replace('/(?<!\ )[A-Z]/', ' $0', ucfirst($v->name));
                    $updatedModulesList->$k = $v;
                }
                $this->data->modules = $updatedModulesList;
            }
            else {
                $this->data->modules = array();
            }
        	
        	$this->language = $this->user->language;
        }
        else {
            $this->language = 'en';
        }
        
        //Loggin in admin
        if (isset($data['submit_login']) && $data['submit_login'] && !$data['token'] && $status == 1) {
            $this->logged_in = 0;
            $result = User::login($data);
            
            if (!is_object($result) && substr($result,0,1) == 0) {
                $this->data->login_error = 1;
                $breakDown = explode(';', $result);
                $this->messages['login_error'] = $breakDown[1];
                $this->log('Error login as <b>'.$data['login'].'</b>, ('.$this->messages['login_error'].')', array('module'=>'login', 'type'=>'fail'));
            }
            else if ($result !== false) {
                $this->logged_in = 1;
                $this->user = $result;
            	$this->log('Success login as <b>'.$data['login'].'</b>', array('module'=>'login', 'type'=>'success'));
                go(_cfg('cmssite').'/');
            }
        }
    }
    
    public function ajax($data) {
    	$this->checkGetData();
    	$this->getStrings();
    	
        $ajax = new Ajax();
        $ajax->ajaxRun($data);
    }
    
    public function cleanData() {
    	unset($_SESSION['token'], $_SESSION['recaptcha_login']);
    	$this->logged_in = 0;
    	$this->user = array();
    	go(_cfg('site').'/admin');
    }
    
    public static function errorMail($desc, $cls, $line, $fulldesc) {
        $timestamp = strftime('%Y-%m-%d %H:%M:%S %Z');
        
        system::sendMail(
            _cfg('adminEmail'),
            'Error '.$desc,
            "Summary: $desc\n" .
            "Time: $timestamp\n" . 
            "Source: $cls:$line\n" .
            'IP: '.$_SERVER['REMOTE_ADDR']."\n" .
            $fulldesc
        );
    }
    
    //@email - Send TO
    //@subject - Subject of email
    //@msg - Body of message (can be html)
    //@file - array, optional, attachment to email, required full link, data in array
    //@file['name'] - name of the file with extension
    //@file['content'] - plain text or plain html, it will be converted into attachment
    public static function sendMail($email, $subject, $msg) {
        if(!_cfg('smtpMailName') || !_cfg('smtpMailPass')) {
            return false;
        }
        
        // Connecting
        $transport = Swift_SmtpTransport::newInstance(_cfg('smtpMailHost'), _cfg('smtpMailPort'));
        $transport->setUsername(_cfg('smtpMailName'));
        $transport->setPassword(_cfg('smtpMailPass'));
        
        $message = Swift_Message::newInstance()
        // Give the message a subject
        ->setSubject($subject)
        // Set the From address with an associative array
        ->setFrom(array(_cfg('smtpMailName') => _cfg('smtpMailFrom')))
        // Set the To addresses with an associative array
        ->setTo(array($email))
        // Give it a body
        ->setBody($msg, 'text/html');
        // Optionally add any attachments
        //->attach(Swift_Attachment::fromPath('my-document.pdf'))
        
        //Sending message
        $mailer = Swift_Mailer::newInstance($transport);
        $mailer->send($message, $fails);
        
        if($fails) {
            $_SESSION['mailError'] = $fails;
            return false;
        }
        
        return true;
    }
    
    public function runAPI($apiAdditionalData) {
    	$startTime = microtime(true);
    	
    	$apiData = array(
    		'api_url' 		=> _cfg('apiUrl'),
    		'api_username'  => _cfg('apiUsername'),
    		'api_password'  => _cfg('apiPassword'),
    	);
    	
    	$apiArray = array_merge($apiData, $apiAdditionalData);
    	
    	Db::query('INSERT INTO `tm_api_request` SET '.
    		'`ip` = "'.Db::escape($_SERVER['REMOTE_ADDR']).'", '.
    		'`request_data` = "'.Db::escape( json_encode($apiArray) ).'"'
    	);
    	
    	$ch = curl_init();
    	$curlOptions = array (
    		CURLOPT_URL => _cfg('apiUrl'),
    		CURLOPT_FAILONERROR => 0,
    		CURLOPT_TIMEOUT => 3, //3s
    		CURLOPT_CONNECTTIMEOUT => 30,
    		CURLOPT_VERBOSE => 1,
    		CURLOPT_SSL_VERIFYPEER => 0,
    		CURLOPT_SSL_VERIFYHOST => FALSE,
    		CURLOPT_POST => 1,
    		CURLOPT_POSTFIELDS => $apiArray,
    		CURLOPT_RETURNTRANSFER => 1,
    	);
    	curl_setopt_array($ch, $curlOptions);
    	$response = curl_exec($ch); // run the whole process
    	
    	if ( $response ) {
    		$rspdata = &$response;
    	}
    	else {
    		$rspdata = curl_error($ch);
    	}
    	
    	curl_close($ch);
    	
    	$endTime = microtime(true);
    	$duration = $endTime - $startTime; //calculates total time taken
    	
    	Db::query('UPDATE `tm_api_request` SET '.
    		'`response_data` = "'.Db::escape( $rspdata ).'", '.
    		'`call_time` = '.(float)$duration.' '.
    		' WHERE `id` = '.Db::lastId()
    	);
    	
    	return $rspdata;
    }
    
    public function log($text, $type = array()) {
    	if (isset($this->user->id)) {
    		$userId = $this->user->id;
    	}
    	else {
    		$userId = 0;
    	}
    	
    	if (!isset($type['module'])) {
    		$type['module'] = '';
    	}
    	
    	if (!isset($type['type'])) {
    		$type['type'] = '';
    	}
    	
    	Db::query('INSERT INTO `tm_logs` '.
	    	'SET '.
    		'`module` = "'.Db::escape_tags(strtolower($type['module'])).'", '.
	    	'`type` = "'.Db::escape_tags($type['type']).'", '.
	    	'`user_id` = '.intval($userId).', '.
	    	'`date` = NOW(), '.
	    	'`ip` = "'.Db::escape_tags($_SERVER['REMOTE_ADDR']).'", '.
	    	'`info` = "'.Db::escape($text).'"'
    	);
    	
    	if (_cfg('logs') == 1) {
    		//If enabled, sending external logs
    		$array = array(
    			'control' => 'log',
    			'module' => Db::escape_tags(strtolower($type['module'])),
    			'type' => Db::escape_tags($type['type']),
    			'user_id' => intval($userId),
    			'ip' => Db::escape_tags($_SERVER['REMOTE_ADDR']),
    			'info' => Db::escape($text, '<b>'),
    		);
    		$this->runAPI($array);
    	}
    }
    
    /*Protected functions*/
    protected function loadClasses() {
        $directory = '../vendor/cms/classes';
    
        if (!file_exists($directory) && !is_dir($directory)) {
            exit('Directory does not exists');
        }

        $handler = opendir($directory);
        $ignoreFiles = array('system.php', '.svn');
        while($file = readdir($handler)) {
            //Checking if not hidden files
            if ($file != "." && $file != "..") {
                //Checking if file ignoring is required
                if(!in_array($file, $ignoreFiles)) {
                    require_once $directory.'/'.$file;
                }
            }
        }
        closedir($handler);
    }
    
    protected function serverParse($socket, $response, $line = __LINE__) {
        while (substr($server_response, 3, 1) != ' ') {
            if (!($server_response = fgets($socket, 256))) {
                echo 'Error: '.$server_response.', '. $line;
                return false;
            }
        }
        
        if (!(substr($server_response, 0, 3) == $response)) {
            echo 'Error: '.$server_response.', '. $line;
            return false;
        }
        
        return true;
    }

    public function getCache($key) {
        if ($this->apcEnabled === false) {
            return false;
        }
        
        $resouse = false;
        $data = apc_fetch($key, $resouse);
        return $resouse ? $data : null;
    }
    
    public function setCache($key, $data) {
        if ($this->apcEnabled === false) {
            return false;
        }
        
        return apc_store($key, $data, $this->cacheTtl);
    }

    public function deleteCache($key) {
        if ($this->apcEnabled === false) {
            return false;
        }
        
        return (apc_exists($key)) ? apc_delete($key) : true;
    }
    
    protected function getStrings() {
        global $astr;
        
        if ($this->language) {
        	require_once('../vendor/cms/locale/'.$this->language.'.php');
        }
        else {
        	require_once('../vendor/cms/locale/en.php');
        }
    }
    
    /*Private functions*/
    private function checkGetData() {
        global $cfg;
    
        if (isset($_GET['language']) && isset($_GET['val1']) && $_GET['val1'] == 'run') { //Special RUN command
            if ( isset( $_GET['val2'] ) ) {
                if ( $_GET['val2'] !== _cfg('cronjob') )
                {
                    die('Invalid secret');
                }
                
                set_time_limit(60);
    
                $cronClass = new Cron();
                //SQL involved functions
                $cronClass->sqlCleanUp();
                    
                //Others functions without SQL
                //$cronClass->cleanImagesTmp();
            }
            else {
                exit('Run command error');
            }
    
            exit();
        }
    
        //Setting - Languages
        if (!isset($_GET['language']) || !$_GET['language'] || !in_array($_GET['language'], _cfg('allowedLanguages'))) {
            $cfg['language'] = $cfg['defaultLanguage'];
            $cfg['href'] = $cfg['site'].'/'.$cfg['language'].'/';
        }
        else {
            $cfg['href'] .= $cfg['site'].'/'.$_GET['language'].'/';
            $cfg['language'] = $_GET['language'];
        }
    
        return true;
    }

    private function sqlInstall() {
        //Importing SQL file
        if (Db::multi_query(file_get_contents(_cfg('root').'/updates/install.sql'))) {
            do {
                if ($result = Db::store_result()) {
                    $result->free();
                }

                if (!Db::more_results()) {
                    break;
                }
            } while (Db::next_result());
        }

        echo 'System didn\'t found any databases for CMS so they were automatically created<br />';

        //Creating admin
        Db::query(
            'INSERT INTO `tm_admins` SET '.
            '`login` = "admin", '.
            '`password` = "'.sha1('admin'._cfg('salt')).'", '.
            '`level` = 4 '
        );
        echo 'New admin was create with nicknamen <b>admin</b>, password <b>admin</b>, please change it as soon as you log in. <br />';
        echo 'Please refresh the page';
        exit();
    }
}