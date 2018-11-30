<?php
  error_reporting(0);
  $part = $_GET['part'];
  if($part != "installation"){
    if(file_exists("../admin/inc/conn.php")) include("../admin/inc/conn.php");
  }
  include("../admin/inc/functions.php");
  if($part == "main")
    $def_lang = getWebProp($connection, "default_language"); else
    $def_lang = $_COOKIE['el_lang'];
  echo json_encode(getLang($part));
  mysqli_close($connection);
?>
