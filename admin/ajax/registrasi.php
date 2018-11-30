<?php
	include("../inc/conn.php");
	include("../inc/functions.php");
	$localize = getLang("ajax");
	include("../inc/check_ajax.php");
	if(!isLoggedIn()){
		echo json_encode(array('code' => '403','error' => $localize['alert_not_loggedin']));
		exit();
	}
	if(!requireLevel(2)){
		echo json_encode(array('code' => '403','error' => $localize['alert_registrator_previlege']));
		mysqli_close($connection);
		exit();
	}
	$nomor = netralize_noinduk($_POST['noinduk']);
	if(strlen($nomor) <= 5) $table = "tb_siswa"; else $table = "tb_guru";
	switch($_POST['step']){
		case "1":
			$noinduk = mysqli_query($connection,"SELECT * FROM $table WHERE no_induk = '$nomor'");
			$noinduk_num = mysqli_num_rows($noinduk);
			$noinduk_row = mysqli_fetch_array($noinduk);
			if($noinduk_num == 1){
				$sudah = mysqli_query($connection,"SELECT * FROM tb_polling WHERE no_induk = '$nomor'");
				$sudah_num = mysqli_num_rows($sudah);
				$sudah_row = mysqli_fetch_array($sudah);
				if($sudah_num){
					$panitia = mysqli_query($connection,"SELECT * FROM tb_panitia WHERE id = $sudah_row[id_panitia]");
					$panitia_row = mysqli_fetch_array($panitia);
					$waktunya = timeAgo($sudah_row['tgl']);
					$output = array(
						'code' => '403',
						'no_induk' => $noinduk_row['no_induk'],
						'error' => sprintf($localize['error_register_voted'], $noinduk_row['nama'], $waktunya, $panitia_row['nama'])
					);
				} else {
					$sedang = mysqli_query($connection,"SELECT * FROM tb_hakpilih WHERE no_induk = '$nomor'");
					$sedang_num = mysqli_num_rows($sedang);
					$sedang_row = mysqli_fetch_array($sedang);
					$waktunya = timeAgo($sedang_row['tgl']);
					$panitia = getPanitia($connection,$sedang_row['id_panitia'],"nama");
					if($sedang_num){
						$output = array(
							'code' => '403',
							'no_induk' => $noinduk_row['no_induk'],
							'error' => sprintf($localize['error_register_registered'], $noinduk_row['nama'], $panitia, $waktunya)
						);
					} else {
						$kode_akses = generateAccessCode();
						$output = array(
							'code' => '200',
							'no_induk' => $noinduk_row['no_induk'],
							'nama' => $noinduk_row['nama'],
							'kode_akses' => $kode_akses
						);
					}
				}
			} else {
				$output = array(
					'code' => '403',
					'error' => $localize['error_register_404']
				);
			}
		break;
		case "2":
			$kode_akses = md5(strtoupper($_POST['kode_akses']));
			if(cekKodeAkses($connection,$kode_akses) == false){
				$input = mysqli_query($connection,"INSERT INTO tb_hakpilih (no_induk,id_panitia,kode_akses) VALUES ($nomor,$_SESSION[os_id],'$kode_akses')");
				if($input){
					$output = array(
						'code' => '200'
					);
				} else {
					$desc = mysqli_error($connection);
					$output = array(
						'code' => '403',
						'desc' => $desc
					);
				}
			} else {
				$output = array(
					'code' => '403',
					'desc' => $localize['error_access_code_exist']
				);
			}
		break;
		case "batal":
			$cek = mysqli_query($connection,"SELECT * FROM tb_hakpilih WHERE no_induk = $nomor");
			$cek_num = mysqli_num_rows($cek);
			$cek_row = mysqli_fetch_array($cek);
			if($cek_num == 1){
				if($cek_row['id_panitia'] == $_SESSION['os_id']){
					$batal = mysqli_query($connection,"DELETE FROM tb_hakpilih WHERE no_induk = $nomor");
					if($batal){
						$output = array(
							'code' => '200',
							'no_induk' => $nomor
						);
					} else {
						$output = array(
							'code' => '500',
							'desc' => mysqli_error($connection)
						);
					}

				} else {
					$output = array(
						'code' => '403',
						'no_induk' => $nomor
					);
				}
			} else {
				$output = array(
					'code' => '404',
					'no_induk' => $nomor
				);
			}
		break;
	}
	echo json_encode($output);
	mysqli_close($connection);
?>
