<?php
function _cfg($key = '') {
    global $cfg;
    
    if (!$key) {
        return $cfg;
    }
    else if (!isset($cfg[$key])) {
    	return 'Config not found';
    }
    
    return $cfg[$key];
}

// Writting strings
function t($key) {
	global $str;
	
	$key = strtolower($key);
    
	if (isset($str[$key])) {
        return $str[$key];
    }
	
    return $key;
}

// Writing admin strings
function at($key = '') {
	global $astr;
    
    if (isset($astr[$key]) && $astr[$key]) {
	   return $astr[$key];
	}
    else {
        return $key;
    }
    
    return $key;
}

//Dumping array data, it is much more cooler then print_r
function dump($data, $font = 10) {
	echo '<div style="text-align: left; padding-left: '.$font.'px;"><pre>';
	print_r($data);
	echo '</pre></div>';
}

//Dumping array data, it is much more cooler then print_r
function ddump($data, $font = 10) {
	echo '<div style="text-align: left; padding-left: '.$font.'px;"><pre>';
	print_r($data);
	echo '</pre></div>';
	exit();
}

//Month value to show month as 01,02,11 and not like 1,2,11
function m_value($p) {
	if ($p > 9) {
		return $p;
	}
	else {
		return '0'.$p;
	}
}