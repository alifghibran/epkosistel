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
	//Pemilihan belum dibuka atau sudah berakhir.
	$aksi = $_POST['aksi'];
	switch($aksi){
		case "umum":
			$status = "200";
			$enabled = $_POST['enabled'];
			$disabled_text = str_replace("'","\\'",$_POST['disabled_text']);
			$instansi = netralize_words($_POST['instansi']);
			if(strlen($instansi) < 5){
				$status = "403";
				$teks = $localize['error_instance'];
			}
			$subjudul = netralize_judul($_POST['subjudul']);
			if(strlen($subjudul) < 5){
				$status = "403";
				$teks = $localize['error_website_name'];
			}
			$judul = netralize_words($_POST['judul']);
			if(strlen($judul) < 5){
				$status = "403";
				$teks = $localize['error_owner'];
			}
			$timezone = netralize_timezone($_POST['timezone']);
			$language = $_POST['default_lang'];
			if($status == "200"){
				$ubah = mysqli_query($connection,"UPDATE tb_pengaturan SET enable_poll=$enabled, timezone='$timezone', default_language='$language', disabled_text='$disabled_text', judul='$judul', subjudul='$subjudul', instansi='$instansi' WHERE id=1");
			}
			$output = array(
				'code' => $status,
				'error' => $teks
			);
		break;
		case "hapus_poll":
			$hapus = mysqli_query($connection,"TRUNCATE TABLE tb_polling");
			$output = array('code' => '200');
		break;
		case "hapus_hasil":
			$hasil = getPollDir();
			if(file_exists($hasil)) unlink($hasil);
			$output = array('code' => '200');
		break;
		case "update_hasil":
			$file = getPollDir();
			$tgl = currentTimestamp();
			$kandidat = mysqli_query($connection,"SELECT * FROM tb_kandidat ORDER BY nama ASC");
			$num_kandidat = mysqli_num_rows($kandidat);
			if($num_kandidat == 0){
				$output = array('code' => '200');
				break;
			}
			$hasil = array();
			$hasil_s['date'] = $tgl;
			$hasil_s['total_suara'] = intval(getTotal($connection,"suara"));
			$hasil_s['total_pemilih'] = getTotal($connection,"siswa") + getTotal($connection,"guru");
			$hasil_s['total_kandidat'] = $num_kandidat;
			$init = 1;
			while($row_kandidat = mysqli_fetch_array($kandidat)){
				$count = mysqli_query($connection,"SELECT COUNT(*) FROM tb_polling WHERE id_kandidat=$row_kandidat[id]");
				$hasil_s["kandidat"]["kandidat_$init"]['id'] = $row_kandidat['id'];
				$hasil_s["kandidat"]["kandidat_$init"]['nama'] = $row_kandidat['nama'];
				$hasil_s["kandidat"]["kandidat_$init"]['kelas'] = $row_kandidat['kelas'];
				$hasil_s["kandidat"]["kandidat_$init"]['fbid'] = $row_kandidat['fbid'];
				$hasil_s["kandidat"]["kandidat_$init"]['bio'] = $row_kandidat['bio'];
				$hasil_s["kandidat"]["kandidat_$init"]['suara'] = intval(mysqli_fetch_array($count)[0]);
				$init++;
			}
			array_push($hasil,$hasil_s);
			file_put_contents($file, json_encode($hasil, JSON_PRETTY_PRINT));
			$output = array('code' => '200');
		break;
	}
	echo json_encode($output);
	mysqli_close($connection);
?>
