<?php
class Dashboard
{
    public $system;

    function __construct($params = array()) {
        $this->system = $params['system'];

        $this->line = $this->fetchChangeLog();

        //Checking for version difference between current version and in changelog, if it is different, run SQL update
        $this->checkVersions();
        
        return $this;
    }
    
    protected function fetchChangeLog() {
    	$ccv = file('http://api.themages.net/changelog.txt');
        
        if ($ccv[0]) {
            $answer['version'] = trim($ccv[0]);
            
            $changeLog = '';
            $i = 3;
            foreach($ccv as $f) {
                $changeLog .= $f.'<br />';
                if (!trim($f)) {
                    --$i;
                }
                if ($i == 0) {
                    break(1);
                }
            }
            unset($ccv, $f);
            
            $answer['changeLog'] = $changeLog;
        }
        else {
            $answer['version'] = '<i>Not available</i>';
            $answer['changeLog'] = '<i>Change log not accessible at the moment</i><br />';
        }
    	
    	return $answer;
    }

    //This is only logical place for now to check versions and make SQL update. Usually it's something small
    protected function checkVersions() {
        //If version is different then trying to find higher version of SQL file
        if ($this->line['version'] != $this->system->data->cmsSettings['version']) {
            
            return true;
        }
        
        return false;
    }
}