<?php

class Cron extends System {
    public function __construct() {
        parent::__construct();
    }
    
    public function cronExample() {
        $file = _cfg('uploads').'/cronExample';

        if (file_exists($file) && filemtime($file)+24*60*60 > time()) { //24h hours timer, no need to update the file
            return false;
        }
        else if (!file_exists($file)) {
            $fopen = fopen($file, 'w');
            fclose($fopen);
        }
        
        //do something
    }
}