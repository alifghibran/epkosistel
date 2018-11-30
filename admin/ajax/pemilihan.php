<?php
	include("../inc/conn.php");
	include("../inc/functions.php");
	$localize = getLang("ajax");
	include("../inc/check_ajax.php");

	$step = $_POST['step'];
	switch($step){
		case "start":
			$kode_akses = md5(strtoupper($_POST['kode_akses']));
			$noinduk = getKodeAkses($connection,$kode_akses);
			$code = "200";
			if(strlen($_POST['kode_akses']) <> 5){
				$code = "403";
				$txt = $localize['error_access_code'];
			}
			if($code = "200"){
				$isEnabled = mysqli_query($connection,"SELECT * FROM tb_pengaturan WHERE id = 1");
				$row_isEnabled = mysqli_fetch_array($isEnabled);
				if($row_isEnabled['enable_poll'] == 0){
					$code = "403";
					$txt = $row_isEnabled['disabled_text'];
				}
			}
			if($code == "200"){
				if($noinduk == false){
					$code = "403";
					$txt = $localize['error_access_code_incorrect'];
				}
			}
			if($code == "200"){
				if(cekPilih($connection,$noinduk)){
					$code = "403";
					$txt = sprintf($localize['error_voted'], getIdentity($connection,$noinduk,"nama"), getPilihTime($connection,$noinduk));
				}
			}
			if($code == "200"){
				if(cekHakPilih($connection,$noinduk) == false){
					$code = "403";
					$txt = $localize['error_not_registered'];
				} else {
					if($code == "200"){
						$newtimestamp = strtotime(currentTimestamp().' + 1 minute');
						$_SESSION['os_pemilih'] = $noinduk;
						$_SESSION['os_pemilih_panitia'] = hakPilihRegistrator($connection,$noinduk);
						$_SESSION['os_pemilih_kadaluarsa'] = date('Y-m-d H:i:s', $newtimestamp);
						$delete_sess = mysqli_query($connection,"DELETE FROM tb_hakpilih WHERE no_induk = '$noinduk'");
					}
				}
			}
			$output = array('code' => $code, 'error' => $txt);
		break;
		case "end":
			$noinduk = $_SESSION['os_pemilih'];
			$id_kandidat = intval($_POST['id_kandidat']);
			$code = "200";
			if(strlen($id_kandidat) <= 0){
				$code = "403";
				$txt = $localize['error_voting_no_candidate'];
			}
			if(isRegistered() == false){
				$code = "403";
				$txt = $localize['error_not_registered'];
			}
			if($code == 200){
				$cek_kdt = mysqli_query($connection,"SELECT * FROM tb_kandidat WHERE id = $id_kandidat");
				if(mysqli_num_rows($cek_kdt) <> 1){
					$code = "403";
					$txt = $localize['error_no_candidate'];
				}
			}
			if($code == "200"){
				if(cekPilih($connection,$_SESSION['os_pemilih'])){
					$code = "403";
					$txt = sprintf($localize['error_voted'], getIdentity($connection,$noinduk,"nama"), getPilihTime($connection,$noinduk));
				}
			}
			if($code == "200"){
				$insert = mysqli_query($connection, "INSERT INTO tb_polling (no_induk, id_panitia, id_kandidat) VALUES ('$_SESSION[os_pemilih]',$_SESSION[os_pemilih_panitia],$id_kandidat)");
				unset($_SESSION['os_pemilih'],$_SESSION['os_pemilih_kadaluarsa'],$_SESSION['os_pemilih_panitia']);
			}
			$output = array('code' => $code, 'error' => $txt);
		break;
	}
	echo json_encode($output);
	mysqli_close($connection);
?>
