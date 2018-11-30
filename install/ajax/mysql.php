<?php
  error_reporting(0);
  include("../../admin/inc/functions.php");
  $localize = getLang("installation", "default", "../../languages");
  include("../../admin/inc/check_ajax.php");
  $host = $_POST['host'];
  $user = $_POST['user'];
  $pass = $_POST['pass'];
  $db = netralize_db($_POST['db']);
  switch($_POST['act']){
    case "cek":
      $con = mysqli_connect($host,$user,$pass);
      if($con){
        $code = "200";
      } else {
        $code = "404";
        $err = mysqli_connect_error();
      }
      mysqli_close($con);
    break;
    case "db_cek":
      $con = mysqli_connect($host,$user,$pass,$db);
      if($con){
        $code = "exist";
      } else {
        $code = "ok";
      }
      mysqli_close($con);
    break;
    case "db_create":
      $con = mysqli_connect($host,$user,$pass);
      if($con){
        $query = mysqli_query($con, "CREATE DATABASE `$db` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci");
        if($query){
          $code = "200";
        } else {
          $code = "404";
          $err = mysqli_error($con);
        }
      } else {
        $code = "404";
        $err = mysqli_error($con);
      }
      mysqli_close($con);
    break;
    case "conf_create":
      $content = "<?php\n";
      $content .= " error_reporting(0);\n";
      $content .= "	session_start();\n";
      $content .= "	\$db_host = \"$host\";\n";
      $content .= "	\$db_user = \"$user\";\n";
      $content .= "	\$db_pass = \"$pass\";\n";
      $content .= "	\$db_name = \"$db\";\n";
      $content .= "	\$connection = mysqli_connect(\$db_host,\$db_user,\$db_pass,\$db_name);\n";
      $content .= "	if(!\$connection){\n";
      $content .= "		echo \"FATAL ERROR!\";\n";
      $content .= "		exit();\n";
      $content .= "	}\n";
      $content .= "?>\n";
      $write = file_put_contents("../../admin/inc/conn.php", $content);
      if($write === false){
        $code = "404";
        $err = $localize['alert_error_save_config_mysql'];
      } else {
        $code = "200";
      }
    break;
    case "tb_create":
      include("../../admin/inc/conn.php");
      $query = file_get_contents("../database/table.sql");
      $query = str_replace("\n","",$query);
      $query = mysqli_multi_query($connection, $query);
      if($query){
        $code = "200";
      } else {
        $code = "404";
        $err = mysqli_error($connection);
      }
      mysqli_close($connection);
    break;
    case "tb_dump":
      include("../../admin/inc/conn.php");
      $query = file_get_contents("../database/alter.sql");
      $query = str_replace("\n","",$query);
      $query = mysqli_multi_query($connection, $query);
      if($query){
        $code = "200";
      } else {
        $code = "404";
        $err = mysqli_error($connection);
      }
      mysqli_close($connection);
    break;
    case "tb_alter":
      include("../../admin/inc/conn.php");
      $query = file_get_contents("../database/dump.sql");
      $query = str_replace("\n","",$query);
      $query = mysqli_multi_query($connection, $query);
      if($query){
        $code = "200";
      } else {
        $code = "404";
        $err = mysqli_error($connection);
      }
      mysqli_close($connection);
    break;
    case "save_webinfo":
      $code = "200";
      $instansi = netralize_words($_POST['instansi']);
      if(strlen($instansi) < 5){
        $code = "403";
        $err = $localize['alert_error_instance'];
      }
      $subjudul = netralize_judul($_POST['subjudul']);
      if(strlen($subjudul) < 5){
        $code = "403";
        $err = $localize['alert_error_web_name'];
      }
      $judul = netralize_words($_POST['judul']);
      if(strlen($judul) < 5){
        $code = "403";
        $err = $localize['alert_error_owner'];
      }
      $timezone = netralize_timezone($_POST['timezone']);
      if($code == "200"){
        include("../../admin/inc/conn.php");
        $ubah = mysqli_query($connection,"UPDATE tb_pengaturan SET timezone='$timezone', judul='$judul', subjudul='$subjudul', instansi='$instansi', default_language='$_COOKIE[el_lang]' WHERE id=1");
        if(!$ubah){
          $code = "403";
          $err = mysqli_error($connection);
        }
        mysqli_close($connection);
      }
    break;
  }
  $output = array(
    'code' => $code,
    'error' => $err
  );
  echo json_encode($output);
?>
