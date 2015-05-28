<?php
//REMOVE ON WEB, NEEDED ONLY FOR CMS
echo 'Direct access registered.<br />';
echo '<a href="'._cfg('site').'/admin">Please move to "CMS" directory</a>';
exit();

require_once _cfg('classes').'/system.php';

if(isset($_POST['ajax']) && $_POST['ajax']) {
	$system = new System(0);
	$system->ajax($_POST);
}
else {
	//Loading whole system
	$system = new System(0);
	$system->run();
}