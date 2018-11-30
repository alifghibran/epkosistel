<?php
	include("../inc/conn.php");
	include("../inc/functions.php");
	$localize = getLang("ajax");
	include("../inc/check_ajax.php");
	if(!isLoggedIn()){
		echo json_encode(array('code' => '403','error' => $localize['alert_not_loggedin']));
		exit();
	}
	if(!requireLevel(1)){
		echo json_encode(array('code' => '403','error' => $localize['alert_administrator_previlege']));
		mysqli_close($connection);
		exit();
	}
	$aksi = $_POST['aksi'];
	$id = intval($_POST['id']);
	$no_induk = netralize_noinduk($_POST['no_induk']);
	$nama = ucwords(netralize_nama($_POST['nama']));
	$level = intval($_POST['level']);
	$chpass = $_POST['chpass'];
	$sandi = $_POST['sandi'];
	$code = "200";
	if(strlen($no_induk) < 4){
		$code = "403";
		$output = array(
			'code' => '403',
			'error' => $localize['error_id']
		);
	}
	if(strlen($nama) < 5){
		$code = "403";
		$output = array(
			'code' => '403',
			'error' => $localize['error_name']
		);
	}
	if(($level > 3) || ($level < 1)){
		$code = "403";
		$output = array(
			'code' => '403',
			'error' => $localize['error_previlege_select']
		);
	}

	switch($aksi){
		case "fetch":
			$id = $_POST['id'];
			$fetch = mysqli_query($connection,"SELECT * FROM tb_panitia WHERE id = $id");
			if(mysqli_num_rows($fetch) == 1){
				$fetch_row = mysqli_fetch_array($fetch);
				$output = array(
					'code' => "200",
					'id' => $fetch_row['id'],
					'no_induk' => $fetch_row['no_induk'],
					'nama' => $fetch_row['nama'],
					'level' => $fetch_row['level']
				);
			} else {
				$output = array(
					'code' => "403",
					'error' => localize['error_admin_not_exist']
				);
			}
		break;
		case "tambah":
			if(!requireLevel(1)){
				$output = array(
					'code' => '403',
					'error' => $localize['alert_administrator_previlege']
				);
				break;
			}
			if(strlen($sandi) < 4){
				$code = "403";
				$output = array(
					'code' => '403',
					'error' => $localize['error_password']
				);
			}
			$sandi = md5($sandi);
			$cek = mysqli_query($connection,"SELECT COUNT(*) FROM tb_panitia WHERE no_induk = '$no_induk'");
			if(mysqli_fetch_array($cek)[0]){
				$code = "403";
				$txt = sprintf($localize['error_admin_exist'], $no_induk);
				$output = array(
					'code' => '403',
					'error' => $txt
				);
			}
			if($code == "200"){
				$tambah_panitia = mysqli_query($connection, "INSERT INTO tb_panitia (no_induk,nama,level,password) VALUES ('$no_induk','$nama',$level,'$sandi')");
				if($tambah_panitia)
					$code = "200"; else
					$code = "403";
				$err = mysqli_error($connection);
				if($code == "200"){
					$err = sprintf($localize['alert_admin_added'], $nama);
				}
				$output = array(
					'code' => $code,
					'error' => $err
				);
			}
		break;
		case "ubah":
			if(!requireLevel(1)){
				$output = array(
					'code' => '403',
					'error' => $localize['alert_administrator_previlege']
				);
				break;
			}
			if($id == 0){
				$code = "403";
				$output = array(
					'code' => '403',
					'error' => $localize['error_id_invalid']
				);
			}
			if($chpass == "true"){
				if(strlen($sandi) < 4){
					$code = "403";
					$output = array(
						'code' => '403',
						'error' => $localize['error_password']
					);
				}
				$sandi = md5($sandi);
				$add_query = "password='$sandi',";
			} else {
				$add_query = "";
			}
			if(getPanitia($connection,$id,"no_induk") == false){
				$code = "403";
				$txt = $localize['error_admin_not_exist'];
				$output = array(
					'code' => '403',
					'error' => $txt
				);
			}
			if($code == "200"){
				$ubah_panitia = mysqli_query($connection, "UPDATE tb_panitia SET $add_query no_induk='$no_induk', nama='$nama', level=$level WHERE id=$id");
				if($ubah_panitia){
					if($id == $_SESSION['os_id']){
						$_SESSION['os_relate'] = $id;
						$_SESSION['os_noinduk'] = $no_induk;
						$_SESSION['os_nama'] = $nama;
						$_SESSION['os_level'] = $level;
					}
				} else
					$code = "403";
				$err = mysqli_error($connection);
				if($code == "200"){
					$err = $localize['alert_admin_updated'];
				}
				$output = array(
					'code' => $code,
					'error' => $err
				);
			}
		break;
		case "hapus":
			if(!requireLevel(1)){
				$output = array(
					'code' => '403',
					'error' => $localize['alert_administrator_previlege']
				);
				break;
			}
			$code = "200";
			$theId = getPanitia($connection,$id,"id");
			if($theId == false){
				$code = "403";
				$txt = $localize['error_admin_not_exist'];
				$output = array(
					'code' => '403',
					'error' => $txt
				);
			}
			if($theId == $_SESSION['os_id']){
				$code = "403";
				$txt = $localize['error_admin_deleteself'];
				$output = array(
					'code' => '403',
					'error' => $txt
				);
			}
			if($code == "200"){
				$hapus = mysqli_query($connection,"DELETE FROM tb_panitia WHERE id = $id");
				if($hapus)
					$code = "200"; else
					$code = "403";
				$err = mysqli_error($connection);
				if($code == "200"){
					$err = $localize['alert_admin_deleted'];
				}
				$output = array(
					'code' => $code,
					'error' => $err
				);
			}
		break;
	}
	echo json_encode($output);
	mysqli_close($connection);
?>
