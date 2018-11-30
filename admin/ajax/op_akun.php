<?php
	include("../inc/conn.php");
	include("../inc/functions.php");
	$localize = getLang("ajax");
	include("../inc/check_ajax.php");
	if(!isLoggedIn()){
		echo json_encode(array('code' => '403','error' => $localize['alert_not_loggedin']));
		exit();
	}
  $sandi_lama = $_POST['sandi_lama'];
  $sandi_baru = $_POST['sandi_baru'];
  if(strlen($sandi_lama) < 4){
    echo json_encode(array('code' => '403','error' => $localize['error_old_password']));
		exit();
  }
  if(strlen($sandi_baru) < 4){
    echo json_encode(array('code' => '403','error' => $localize['error_new_password']));
		exit();
  }
  $akun = mysqli_query($connection,"SELECT * FROM tb_panitia WHERE id=$_SESSION[os_id]");
  $row_akun = mysqli_fetch_array($akun);
  if(md5($sandi_lama) != $row_akun['password']){
    echo json_encode(array('code' => '403','error' => $localize['error_old_password_incorrect']));
		exit();
  }
  if(md5($sandi_baru) == $row_akun['password']){
    echo json_encode(array('code' => '403','error' => $localize['error_old_new_password_incorrect']));
		exit();
  }
  $akun = mysqli_query($connection,"UPDATE tb_panitia SET password=MD5('$sandi_baru') WHERE id=$_SESSION[os_id]");
  echo json_encode(array('code' => '200'));
  mysqli_close($connection);
?>
