<?php
//Dumping array data, it is much more cooler then print_r
function ddump($data, $font = 10) {
	echo '<div style="text-align: left; padding-left: '.$font.'px;"><pre>';
	print_r($data);
	echo '</pre></div>';
	exit();
}