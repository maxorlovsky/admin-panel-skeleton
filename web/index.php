<?php
//REMOVE ON WEB, NEEDED ONLY FOR THEMAGES.NET
echo '<h1>CMS - TheMages</h1>
<h2><a href="'._cfg('site').'/admin">DEMO</a></h2>
<p>Current version: <b>3.12</b></p>
<p>Credits:</p>
<p>(dev) Maxtream</p>
<p>(design): Maxtream, AnyaTheEagle</p>
<p><strong>Available for download on <a href="https://github.com/Maxtream/themages-cms">Github</a></strong></p>';
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