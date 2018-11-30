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
	$id = netralize_noinduk($_POST['id']);
	$no_induk = netralize_noinduk($_POST['no_induk']);
	$nama = ucwords(strtolower(netralize_nama($_POST['nama'])));
	$jabatan = ucwords(strtolower(netralize_words($_POST['jabatan'])));
	$code = "200";
	if(strlen($no_induk) < 10){
		$code = "403";
		$output = array(
			'code' => '403',
			'error' => $localize['error_id_staff']
		);
	}
	if(strlen($nama) < 5){
		$code = "403";
		$output = array(
			'code' => '403',
			'error' => $localize['error_name']
		);
	}
	if(strlen($jabatan) < 1){
		$code = "403";
		$output = array(
			'code' => '403',
			'error' => $localize['error_position']
		);
	}

	switch($aksi){
		case "fetch":
			$fetch = mysqli_query($connection,"SELECT * FROM tb_guru WHERE no_induk = '$id'");
			if(mysqli_num_rows($fetch) == 1){
				$fetch_row = mysqli_fetch_array($fetch);
				$output = array(
					'code' => "200",
					'id' => $fetch_row['no_induk'],
					'no_induk' => $fetch_row['no_induk'],
					'nama' => $fetch_row['nama'],
					'jabatan' => $fetch_row['jabatan']
				);
			} else {
				$output = array(
					'code' => "404",
					'error' => $localize['error_staff_not_exist']
				);
			}
		break;
		case "add":
			$cek = mysqli_query($connection,"SELECT COUNT(*) FROM tb_guru WHERE no_induk = '$no_induk'");
			if(mysqli_fetch_array($cek)[0]){
				$code = "403";
				$txt = sprintf($localize['error_staff_404'], $no_induk);
				$output = array(
					'code' => '403',
					'error' => $txt
				);
			}
			if($code == "200"){
				$tambah_panitia = mysqli_query($connection, "INSERT INTO tb_guru (no_induk,nama,jabatan) VALUES ('$no_induk','$nama','$jabatan')");
				if($tambah_panitia)
					$code = "200"; else
					$code = "403";
				$err = mysqli_error($connection);
				if($code == "200"){
					$err = sprintf($localize['alert_staff_added'], $nama);
				}
				$output = array(
					'code' => $code,
					'no_induk' => $no_induk,
					'error' => $err
				);
			}
		break;
		case "update":
			if(strlen($id) < 10){
				$code = "403";
				$output = array(
					'code' => '403',
					'error' => $localize['error_id_invalid']
				);
			}
			$cek = mysqli_query($connection,"SELECT COUNT(*) FROM tb_guru WHERE no_induk = '$id'");
			if(mysqli_fetch_array($cek)[0] <> 1){
				$code = "403";
				$txt = $localize['error_staff_not_exist'];
				$output = array(
					'code' => '403',
					'error' => $txt
				);
			}
			if($code == "200"){
				$ubah_panitia = mysqli_query($connection, "UPDATE tb_guru SET nama='$nama', jabatan='$jabatan' WHERE no_induk='$id'");
				if(!$ubah_panitia){
					$code = "403";
					$err = mysqli_error($connection);
				} else {
					$err = $localize['alert_staff_updated'];
				}
				$output = array(
					'code' => $code,
					'no_induk' => $no_induk,
					'error' => $err
				);
			}
		break;
		case "delete":
			$code = "200";
			if($code == "200"){
				$hapus = mysqli_multi_query($connection,"DELETE FROM tb_polling WHERE no_induk = '$id'; DELETE FROM tb_hakpilih WHERE no_induk = '$id'; DELETE FROM tb_guru WHERE no_induk = '$id';");
				if($hapus)
					$code = "200"; else
					$code = "403";
				$err = mysqli_error($connection);
				if($code == "200"){
					$err = $localize['alert_staff_deleted'];
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
