<?php
	error_reporting(0);
	include("./inc/functions.php");
	$localize = getLang("admin");
	unset($_SESSION['os_id'],$_SESSION['os_noinduk'],$_SESSION['os_nama'],$_SESSION['os_level'],$_SESSION['os_related']);
	echo $localize['logged_in'];
	echo "<script>top.location = './index.php?_loggedout'</script>";
?>
