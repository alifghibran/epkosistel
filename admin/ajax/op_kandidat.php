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
	$idpanitia = $_SESSION['os_id'];
	$aksi = $_POST['aksi'];
	switch($aksi){
		case "fetch":
			$id = $_POST['id'];
			$fetch = mysqli_query($connection,"SELECT * FROM tb_kandidat WHERE id = $id");
			if(mysqli_num_rows($fetch) == 1){
				$fetch_row = mysqli_fetch_array($fetch);
				$output = array(
					'code' => "200",
					'nama' => $fetch_row['nama'],
					'kelas' => $fetch_row['kelas'],
					'fbid' => $fetch_row['fbid'],
					'bio' => $fetch_row['bio']
				);
			} else {
				$output = array(
					'code' => "404"
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
			$nama = ucwords(strtolower(netralize_nama($_POST['nama'])));
			$kelas = strtoupper(netralize_words($_POST['kelas']));
			$bio = netralize_article($_POST['bio']);
			$fbid = netralize_noinduk($_POST['fbid']);
			$code = "200";
			if(strlen($fbid) < 10) $code = "304";
			if(strlen($bio) < 10) $code = "303";
			if(strlen($kelas) < 1) $code = "302";
			if(strlen($nama) < 5) $code = "301";
			if($code == "200"){
				$tambah_kandidat = mysqli_query($connection, "INSERT INTO tb_kandidat (nama,kelas,bio,fbid) VALUES ('$nama','$kelas','$bio','$fbid')");
				if($tambah_kandidat)
					$code = "200"; else
					$code = "403";
					$err = mysqli_error($connection);
			}
			$output = array(
				'code' => $code,
				'error' => $err
			);
		break;
		case "edit":
			if(!requireLevel(1)){
				$output = array(
					'code' => '403',
					'error' => $localize['alert_administrator_previlege']
				);
				break;
			}
			$nama = ucwords(strtolower(netralize_nama($_POST['nama'])));
			$kelas = strtoupper(netralize_words($_POST['kelas']));
			$bio = netralize_article($_POST['bio']);
			$fbid = netralize_noinduk($_POST['fbid']);
			$id = netralize_noinduk($_POST['id']);
			$code = "200";
			if(strlen($fbid) < 10) $code = "304";
			if(strlen($bio) < 10) $code = "303";
			if(strlen($kelas) < 1) $code = "302";
			if(strlen($nama) < 5) $code = "301";
			$cek = mysqli_query($connection,"SELECT COUNT(*) FROM tb_kandidat WHERE id = $id");
			if(mysqli_fetch_array($cek)[0] != 1) $code = "404";
			if($code == "200"){
				$edit_kandidat = mysqli_query($connection, "UPDATE tb_kandidat SET nama='$nama', kelas='$kelas', fbid='$fbid', bio='$bio' WHERE id=$id");
				if($edit_kandidat)
					$code = "200"; else
					$code = "403";
					$err = mysqli_error($connection);
			}
			$pic = getFBPic($fbid);
			$nama = str_replace("\\","",$nama);
			$bio = nl2br($bio);
			$output = array(
				'code' => $code,
				'id' => $id,
				'nama' => $nama,
				'kelas' => $kelas,
				'bio' => $bio,
				'photo' => $pic,
				'error' => $err
			);
		break;
		case "hapus":
			if(!requireLevel(1)){
				$output = array(
					'code' => '403',
					'error' => $localize['alert_administrator_previlege']
				);
				break;
			}
			$id = netralize_noinduk($_POST['id']);
			$cek = mysqli_query($connection,"SELECT * FROM tb_kandidat WHERE id = $id");
			if(mysqli_num_rows($cek) == 1){
				$hapus = mysqli_query($connection,"DELETE FROM tb_kandidat WHERE id = $id");
				$code = "200";
			} else {
				$code = "404";
			}
			$output = array('code' => $code);
		break;
	}
	echo json_encode($output);
	mysqli_close($connection);
?>
