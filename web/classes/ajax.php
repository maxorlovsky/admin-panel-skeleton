<?php
class Ajax extends System
{
    public function __construct() {
        parent::__construct();
    }
    
    private $allowed_ajax_methods = array(
        'example',
	);
	
    public function ajaxRun($data) {
    	$controller = $data['ajax'];
        
        if ( in_array( $controller, $this->allowed_ajax_methods ) ) {
            echo $this->$controller($data);
            return true;
        }
        else {
            echo '0;'.t('controller_not_exist');
            return false;
        }
    }
    
    protected function example() {
        
    }
}
