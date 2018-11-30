<?php
 error_reporting(0);
	session_start();
	$db_host = "localhost";
	$db_user = "root";
	$db_pass = "";
	$db_name = "kepo";
	$connection = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
	if(!$connection){
		echo "FATAL ERROR!";
		exit();
	}
?>
