<?php

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />

    <title>The M.A.G.E.S. CMS - Install</title>
        
    <style>
    	html, body {
    		margin: 0;
    		padding: 0;
    		width: 100%;
    		height: 100%;
    	}
    	body {
    		padding: 20px;
    		box-sizing: border-box;
    	}
    	textarea {
    		width: 1000px;
    		height: 500px;
    		background-color: #eee;
    	}
    </style>
</head>
<body>

<section id="main" class="wrapper">
	<div class="content">
		It seems that your website have The M.A.G.E.S. CMS but it wasn't installed or cleaned up properly.<br />
		Instalation process is more or less straight forward and not automated, which is better for developer, but not so friendly for plain user<br />
		<br />
		Step 1 (Linux/Apache):<br />
		<ul>
			<li>Run in terminal: sudo cp <?=$cfg['root']?>/.htaccess <?=$_SERVER['DOCUMENT_ROOT']?>/.htaccess</li>
			<li>Run in terminal: sudo cp <?=$cfg['cmsdir']?>/inc/config-for-install.php <?=$_SERVER['DOCUMENT_ROOT']?>/config-tm.php</li>
			<li>Run in terminal or open in editor: <?=$_SERVER['DOCUMENT_ROOT']?>/config-tm.php</li>
			<li>Go through it and configure as you see fit, there are comments for each variables, so additional guide/FAQ not required</li>
		</ul>
		<br />
		Step 1 (Windows):<br />
		<ul>
			<li>Copy file from <?=$cfg['root']?>.htaccess to <?=$_SERVER['DOCUMENT_ROOT']?>/.htaccess</li>
			<li>Create config-tm.php file in your root directory (<?=$_SERVER['DOCUMENT_ROOT']?>/config-tm.php)</li>
			<li>Copy paste content of text field below inside of the file</li>
			<li>Go through it and configure as you see fit, there are comments for each variables, so additional guide/FAQ not required</li>
		</ul>
		<br />
		<textarea><?=file_get_contents($cfg['cmsdir'].'/inc/config-for-install.php')?></textarea>
		<br />
		Step 2 - optional (Linux):<br />
		<ul>
			<li>You must give correct right to upload folder or you won't be able to upload files from CMS</li>
			<li>Run in terminal: sudo chmod -R 0755 <?=$_SERVER['DOCUMENT_ROOT']?>/web/uploads</li>
		</ul>
		<br /><br />
		After you done, you're ready to go. Refresh this page.<br />
	</div>
</section>

</body>
</html>