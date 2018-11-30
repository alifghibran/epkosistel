<?php
	include("../inc/conn.php");
	include("../inc/functions.php");
	$localize = getLang("ajax");
	include("../inc/check_ajax.php");
	if(!isLoggedIn()){
		echo json_encode(array('code' => '403','error' => $localize['alert_not_loggedin']));
		exit();
	}
	$idpanitia = $_SESSION['os_id'];
	if($_SESSION['os_level'] == "3"){
		$idpanitia = $_SESSION['os_relate'];
	}
	$list = mysqli_query($connection,"SELECT * FROM tb_hakpilih WHERE id_panitia = $idpanitia ORDER BY tgl DESC");
	$list_num = mysqli_num_rows($list);
	if($list_num){
		$text = "";
		$init = 1;
		while($list_row = mysqli_fetch_array($list)){
			$nama = ucwords(getIdentity($connection,$list_row['no_induk'],"nama"));
			$waktunya = timeAgo($list_row['tgl']);
			$noinduk = $list_row['no_induk'];
			if(strlen($noinduk) > 10)
				$noinduk = "...".substr($noinduk,-10,10);
			$text .= "<tr class=\"pointer\" onclick=\"hakPilih('abort','$list_row[no_induk]')\">";
			$text .= "<td>$init</td>";
			$text .= "<td>$nama</td>";
			$text .= "<td>$noinduk</td>";
			$text .= "<td>$waktunya</td>";
			$text .= "</tr>";
			$init++;
		}
		$output = array(
			'code' => 'fill',
			'text' => $text,
			'voting' => getTotal($connection,"voting"),
			'suara' => getTotal($connection,"suara"),
			'date' => sprintf($func_lang['ago_long_at'], date($func_lang['time_format'],time()))
		);
	} else {
		$output = array(
			'code' => 'empty',
			'voting' => getTotal($connection,"voting"),
			'suara' => getTotal($connection,"suara"),
			'date' => sprintf($func_lang['ago_long_at'], date($func_lang['time_format'],time()))
		);
	}
	echo json_encode($output);
	mysqli_close($connection);
?>
