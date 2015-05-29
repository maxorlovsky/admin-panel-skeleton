<?php
//REMOVE ON WEB, NEEDED ONLY FOR THEMAGES.NET
echo '<h1>CMS - TheMages</h1>';
echo '<p>Current version: 3.12</p>';
echo '<p>Credits:</p>';
echo '<p>(dev) Maxtream</p>';
echo '<p>(design): Maxtream, AnyaTheEagle</p>';
echo '<p><strong>Available for download on <a href="https://github.com/Maxtream/themages-cms">Github</a></strong></p>';
echo '<h2><a href="'._cfg('site').'/admin">DEMO</a></h2>';

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