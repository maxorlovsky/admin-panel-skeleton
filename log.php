<?
if (!$_POST['data'] || !$_POST['id']) {
	exit();
}

$_cfg['host'] ='localhost';
$_cfg['port'] =3306;
$_cfg['user'] ='tmlogs';
$_cfg['pass'] ='troyTmLOGSss2@#11(*(#(2!!pishpish';
$_cfg['base'] ='themages_logs';
$dbcnx = mysql_connect($_cfg['host'].':'.$_cfg['port'],$_cfg['user'],$_cfg['pass']);
@mysql_select_db($_cfg['base'], $dbcnx);
mysql_query ("set names 'utf8'");

$data = unserialize(base64_decode($_POST['data']));
$id = base64_decode($_POST['id']);

mysql_query('INSERT INTO `logs`
SET `type` = "'.mysql_real_escape_string($data[0]).'",
	`date` = NOW(),
	`ip` = "'.mysql_real_escape_string($data[1]).'",
	`page` = "'.mysql_real_escape_string($data[2]).'",
	`old_value` = "'.mysql_real_escape_string($data[4]).'",
	`new_value` = "'.mysql_real_escape_string($data[5]).'",
	`user_id` = "'.mysql_real_escape_string($data[6]).'",
	`site` = "'.mysql_real_escape_string($id).'"
');
?>