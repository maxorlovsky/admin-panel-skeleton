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
    	
        if ($this->system->getCache('changelog')) {
            $ccv = $this->system->getCache('changelog');
        }
        else {
            $ch = curl_init();
            $curlOptions = array (
                CURLOPT_URL => 'http://api.themages.net/changelog.txt',
                CURLOPT_FAILONERROR => 0,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_VERBOSE => 1,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_POST => 0,
                CURLOPT_RETURNTRANSFER => 1,
            );
            curl_setopt_array($ch, $curlOptions);
            $ccv = curl_exec($ch); // run the whole process

            //Saving to cache
            $this->system->setCache('changelog', $ccv);
        }

        $ccv = explode("\n", $ccv);
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
            $this->updateSQL();
            return true;
        }
        
        return false;
    }

    //Run .sql file from /updates/ folder
    protected function updateSQL() {
        $breakdown = explode('.', $this->system->data->cmsSettings['version']);
        $majorVersion['cms'] = (int)$breakdown[0];
        $minorVersion['cms'] = (int)$breakdown[1];

        $breakdown = explode('.', $this->line['version']);
        $majorVersion['available'] = (int)$breakdown[0];
        $minorVersion['available'] = (int)$breakdown[1];

        //Checking if there is a major change
        if ($majorVersion['cms'] < $majorVersion['available']) {
            //For now not touching it, as there was no major releases since moving to this feature
        }

        //Comparing minor versions
        if ($minorVersion['cms'] < $minorVersion['available']) {
            //Checking how big is the difference, to loop over update .sql files
            $versionsDifferention = $minorVersion['available'] - $minorVersion['cms'];

            //Starting from next version
            $startLoop = $minorVersion['cms'] + 1;

            //Looping
            for($i = $startLoop; $i <= $minorVersion['available']; ++$i) {
                //Searching if SQL file of this version exists, if not, nothing to update (or forgot to upload in repo)
                if (file_exists(_cfg('root').'/updates/'.$majorVersion['cms'].'.'.$i.'.sql')) {
                    //Executing SQL file silently
                    if (Db::multi_query(file_get_contents(_cfg('root').'/updates/'.$majorVersion['cms'].'.'.$i.'.sql'))) {
                        do {
                            if ($result = Db::store_result()) {
                                $result->free();
                            }

                            if (!Db::more_results()) {
                                break;
                            }
                        } while (Db::next_result());
                    }
                }
            }
        }

        return false;
    }
}