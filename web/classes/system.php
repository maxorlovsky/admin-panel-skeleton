<?php

class System
{
    public $data;
    public $page;
    public $user;
    public $logged_in;
    public $links;
    protected $userClass;
    
    public function __construct() {
    	if (!$this->data) {
    		$this->data = new stdClass();
    	}
    	
    	$this->loadClasses();

        //Making a connection
        Db::connect();
        
        $this->fetchParams();
    }
    
    public function run() {
        $this->checkGetData();
        $this->getStrings();
        
        $template = new Template();
        $template->parse();
    }
    
    public function fetchParams() {
        global $cfg;
        
        $this->data->settings = array();
        $this->data->links = new stdClass();
        
        $data = array_merge($_GET, $_POST, $_SESSION);
         
        if (!isset($data['val1'])) {
        	$data['val1'] = false;
        }
        if (!isset($data['token'])) {
        	$data['token'] = false;
        }
        
        $rows = Db::fetchRows('SELECT * FROM `tm_settings`');
        if ($rows) {
        	foreach($rows as $v) {
        		$this->data->settings[$v->setting] = $v->value;
        	}
        }
        
        $rows = Db::fetchRows('SELECT * FROM `tm_links` '.
            'WHERE `able` = 1 '.
            'ORDER BY `position` '
        );
        
        if ($rows) {
        	$this->data->links = $rows;
        }
        
        if (!isset($this->data->langugePicker)) {
            $this->data->langugePicker = array();
        }
        
        if (!$this->data->langugePicker && _cfg('language') != 'Config not found') {
            $languageRows = Db::fetchRows('SELECT `title`, `flag` FROM `tm_languages`');
            foreach($languageRows as $v) {
                if ($v->flag != _cfg('language')) {
                    $this->data->langugePicker[] = $v;
                }
                else {
                    $this->data->langugePicker['picked'] = $v;
                }
            }
        }
        
      	if ($data['val1']) {
        	$this->page = $data['val1'];
        }
        else {
        	$this->page = 'home';
        }
        
        $this->logged_in = 0;
    }
    
    public function ajax($data) {
    	$this->checkGetData();
    	$this->getStrings();
    	
        $ajax = new Ajax();
        $ajax->ajaxRun($data);
    }
    
    public function cleanData() {
    	unset($_SESSION['token']);
    	$this->logged_in = 0;
    	$this->user = array();
    	go(_cfg('site'));
    }
    
    //@email - Send TO
    //@subject - Subject of email
    //@msg - Body of message (can be html)
    //@file - array, optional, attachment to email, required full link, data in array
    //@file['name'] - name of the file with extension
    //@file['content'] - plain text or plain html, it will be converted into attachment
    public function sendMail($email, $subject, $msg, $files = array()) {
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
        
        /*if ($files) {
            foreach($files as $k => $v) {
                if ($v['content']) {
                    $mailData .= 'Content-Type: application/octet-stream; name='.$v['name'].''."\r\n";
                    $mailData .= 'Content-Transfer-Encoding: base64 '."\r\n";
                    $mailData .= 'Content-Disposition: attachment; filename="'.$v['name'].'" '."\r\n";
                    $mailData .= "\r\n".base64_encode($v['content'])."\r\n\r\n";
                    
                    $mailData .= '--'.$mime_boundary."\r\n";
                }
            }
        }*/
        
        if($fails) {
            $_SESSION['mailError'] = $fails;
            return false;
        }
        
        return true;
    }
    
    /*Protected functions*/
    protected function loadClasses() {
    	require_once _cfg('cmsclasses').'/db.php';
    	require_once _cfg('classes').'/ajax.php';
        require_once _cfg('classes').'/cron.php';
    	require_once _cfg('classes').'/template.php';
    }
    
    protected function serverParse($socket, $response, $line = __LINE__) {
    	$server_response = '';
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
    
    protected function getStrings() {
        global $str;
        
        $rows = Db::fetchRows('SELECT `key`, `'._cfg('fullLanguage').'` AS `value` FROM `tm_strings`');
        if ($rows) {
        	foreach($rows as $v) {
        		$str[$v->key] = $v->value;
        	}
        }
        
        return true;
    }
    
    /*Private functions*/
    private function checkGetData() {
        global $cfg;
    
        if (isset($_GET['language']) && $_GET['language'] == 'run') { //Special RUN command
            if (isset($_GET['val1'])) {
                if ($_GET['val1'] === _cfg('cronjob')) {
                    set_time_limit(300);
                    $cronClass = new Cron();
                    $cronClass->cronExample();
                }
                else {
                    exit('Run command error');
                }
            }
            
            exit();
        }
    
        $availableLanguages = array();
        $fetchingFullLanguage = array();
        $languageRows = Db::fetchRows('SELECT `title`, `flag` FROM `tm_languages`');
        foreach($languageRows as $v) {
        	$availableLanguages[] = $v->flag;
            $fetchingFullLanguage[$v->flag] = $v->title;
        }
        
        //Setting - Languages
        if (isset($_GET['language']) && $_GET['language'] && in_array($_GET['language'], $availableLanguages)) {
            $cfg['language'] = $_GET['language'];
            setcookie('language', _cfg('language'), time()+7776000, '/', 'site.com');
        }
        else if (isset($_COOKIE['language']) && $_COOKIE['language'] && in_array($_COOKIE['language'], $availableLanguages)) {
            $cfg['language'] = $_COOKIE['language'];
        }
        else {
        	$cfg['language'] = 'en';
        }
        
        $cfg['fullLanguage'] = $fetchingFullLanguage[$cfg['language']];
        
        $cfg['href'] = str_replace('%lang%', $cfg['language'], $cfg['href']);
        //$cfg['somesite'] = $cfg['href'].'/somesite';

        return true;
    }
}