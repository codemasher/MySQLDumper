<?php
if(!ob_start('ob_gzhandler')){
	ob_start();
}
include('./inc/functions.php');
$page = (isset($_GET['page'])) ? $_GET['page'] : 'main.php';
if(!file_exists(__DIR__.'/../work/config/mysqldumper.php')){
	header('location: install.php');
	ob_end_flush();
	die();
}
?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8"/>
		<meta name="robots" content="noindex,nofollow"/>
		<title>MySQLDumper</title>
	</head>

	<frameset border=0 cols="190,*">
		<frame name="MySQL_Dumper_menu" src="menu.php" scrolling="no" noresize
		       frameborder="0" marginwidth="0" marginheight="0">
		<frame name="MySQL_Dumper_content" src="<?php
		echo $page;
		?>"
		       scrolling="auto" frameborder="0" marginwidth="0" marginheight="0">
	</frameset>
	</html>
<?php
ob_end_flush();
