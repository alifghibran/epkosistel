<?php
	include("../inc/conn.php");
	include("../inc/functions.php");
	$localize = getLang("ajax");
	include("../inc/check_ajax.php");
	if(isset($_COOKIE['blocked'])) {
		echo "blocked"; exit();
	}
	$noinduk = preg_replace("/[^0-9]/", "", $_POST['noinduk']);
	switch($_POST['step']){
		case "1":
			$noinduk = mysqli_query($connection,"SELECT * FROM tb_panitia WHERE no_induk = '$noinduk'");
			$noinduk_num = mysqli_num_rows($noinduk);
			if($noinduk_num == 1){
				setcookie("blocked_num","",time()-3600,"/");
				$noinduk_row = mysqli_fetch_array($noinduk);
				$result = array(
					'nama' => $noinduk_row['nama'],
					'role' => getLevelFrom($noinduk_row['level'], "id"),
					'not_me' => sprintf($localize['text_not_me'], $noinduk_row['nama'])
				);
				echo json_encode($result);
				mysqli_close($connection);
				exit();
				//echo "<span>".$noinduk_row['nama']."</span>".$noinduk_row['no_induk']."@eLection"; exit();
			} else {
				$try = 0;
				if(isset($_COOKIE['blocked_num'])){
					$try = intval($_COOKIE['blocked_num'])+1;
				}
				if(intval($_COOKIE['blocked_num']) > 3){
					setcookie("blocked","blocked",time()+3600,"/");
					setcookie("blocked_num","",time()-3600,"/");
					echo "blocked";
					mysqli_close($connection); exit();
				} else setcookie("blocked_num","$try",time()+3600,"/");
				echo "false";
				mysqli_close($connection); exit();
			}
		break;
		case "2":
			$sandi = md5($_POST['sandi']);
			$login = mysqli_query($connection,"SELECT * FROM tb_panitia WHERE no_induk = '$noinduk' AND password = '$sandi'");
			$login_num = mysqli_num_rows($login);
			if($login_num == 1){
				session_start();
				setcookie("blocked_num","",time()-3600,"/");
				$login_row = mysqli_fetch_array($login);
				$_SESSION['os_id'] = $login_row['id'];
				$_SESSION['os_relate'] = $login_row['id'];
				$_SESSION['os_noinduk'] = $login_row['no_induk'];
				$_SESSION['os_nama'] = $login_row['nama'];
				$_SESSION['os_level'] = $login_row['level'];
				echo "success";
			} else {
				$try = 0;
				if(isset($_COOKIE['blocked_num'])){
					$try = intval($_COOKIE['blocked_num'])+1;
				}
				if(intval($_COOKIE['blocked_num']) > 4){
					setcookie("blocked","blocked",time()+3600,"/");
					setcookie("blocked_num","",time()-3600,"/");
					echo "blocked";
					mysqli_close($connection); exit();
				} else setcookie("blocked_num","$try",time()+3600,"/");
				echo "false";
				mysqli_close($connection); exit();
			}
		break;
	}
?>
