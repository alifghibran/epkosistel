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
	$kelas = strtoupper(netralize_words($_POST['kelas']));
	$code = "200";
	if(strlen($no_induk) < 4){
		$code = "403";
		$output = array(
			'code' => '403',
			'error' => $localize['error_id_student']
		);
	}
	if(strlen($nama) < 5){
		$code = "403";
		$output = array(
			'code' => '403',
			'error' => $localize['error_name']
		);
	}
	if(strlen($kelas) < 1){
		$code = "403";
		$output = array(
			'code' => '403',
			'error' => $localize['error_grade']
		);
	}

	switch($aksi){
		case "fetch":
			$fetch = mysqli_query($connection,"SELECT * FROM tb_siswa WHERE no_induk = '$id'");
			if(mysqli_num_rows($fetch) == 1){
				$fetch_row = mysqli_fetch_array($fetch);
				$output = array(
					'code' => "200",
					'id' => $fetch_row['no_induk'],
					'no_induk' => $fetch_row['no_induk'],
					'nama' => $fetch_row['nama'],
					'kelas' => $fetch_row['kelas']
				);
			} else {
				$output = array(
					'code' => "403",
					'error' => $localize['error_student_not_exist']
				);
			}
		break;
		case "add":
			$cek = mysqli_query($connection,"SELECT COUNT(*) FROM tb_siswa WHERE no_induk = '$no_induk'");
			if(mysqli_fetch_array($cek)[0]){
				$code = "403";
				$txt = sprintf($localize['error_student_404'], $no_induk);
				$output = array(
					'code' => '403',
					'error' => $txt
				);
			}
			if($code == "200"){
				$tambah_panitia = mysqli_query($connection, "INSERT INTO tb_siswa (no_induk,nama,kelas) VALUES ('$no_induk','$nama','$kelas')");
				if($tambah_panitia)
					$code = "200"; else
					$code = "403";
				$err = mysqli_error($connection);
				if($code == "200"){
					$err = sprintf($localize['alert_student_added'], $nama);
				}
				$output = array(
					'code' => $code,
					'no_induk' => $no_induk,
					'error' => $err
				);
			}
		break;
		case "update":
			if(strlen($id) <= 3){
				$code = "403";
				$output = array(
					'code' => '403',
					'error' => $localize['alert_system_error']
				);
			}
			$cek = mysqli_query($connection,"SELECT COUNT(*) FROM tb_siswa WHERE no_induk = '$id'");
			if(mysqli_fetch_array($cek)[0] <> 1){
				$code = "403";
				$txt = $localize['error_student_not_exist'];
				$output = array(
					'code' => '403',
					'error' => $txt
				);
			}
			if($code == "200"){
				$ubah_panitia = mysqli_query($connection, "UPDATE tb_siswa SET nama='$nama', kelas='$kelas' WHERE no_induk='$id'");
				if(!$ubah_panitia){
					$code = "403";
					$err = mysqli_error($connection);
				} else {
					$err = $localize['alert_student_updated'];
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
				$hapus = mysqli_multi_query($connection,"DELETE FROM tb_polling WHERE no_induk = '$id'; DELETE FROM tb_hakpilih WHERE no_induk = '$id'; DELETE FROM tb_siswa WHERE no_induk = '$id';");
				if($hapus)
					$code = "200"; else
					$code = "403";
				$err = mysqli_error($connection);
				if($code == "200"){
					$err = $localize['alert_student_deleted'];
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
