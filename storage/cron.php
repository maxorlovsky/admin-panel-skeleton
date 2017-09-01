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