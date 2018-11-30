<?php
session_start();
function getLang($part=false, $setlang="default", $langdir="default"){
	if($part === false) return false;
	if(!isset($_COOKIE['el_lang'])) {
    setcookie('el_lang', 'en-us', 0, "/");
    $_COOKIE['el_lang'] = 'en-us';
	}
	$curlang = $_COOKIE['el_lang'];
	if($setlang != "default"){
		$curlang = $setlang;
	}
	if($langdir == "default")
		$filelang = __DIR__."/../../languages/$curlang/loc_$part.tp"; else
		$filelang = "$langdir/$curlang/loc_$part.tp";
	if(!file_exists($filelang)) return true;
	$output = file_get_contents($filelang);
	return json_decode($output, true);
}
$func_lang = getLang("functions");
function getWebProp($con,$name){
	$prop = mysqli_query($con,"SELECT * FROM tb_pengaturan WHERE id = 1");
	return mysqli_fetch_array($prop)[$name];
}
function getCurrentLang(){
	$curlang = $_COOKIE['el_lang'];
	return getLangProp($curlang, "lang_short");
}
function getCurrentLocale(){
	$curlang = $_COOKIE['el_lang'];
	return getLangProp($curlang, "locale");
}
function getLangProp($lang_id="en-us", $prop="id"){
	$filelang = __DIR__."/../../languages/$lang_id/info.tp";
	if(!file_exists($filelang)) return false;
	$output = file_get_contents($filelang);
	$output = json_decode($output, true);
	return $output[$prop];
}
function getListLang($type="menu", $selected="en-us"){
	$dirlang = __DIR__."/../../languages/";
	$langlist = array_diff(scandir($dirlang), array('..', '.'));
	$output = "";
	switch($type){
		case "menu":
			foreach ($langlist as $ls) {
				if(!is_dir($dirlang.$ls)) continue;
				$langname = getLangProp($ls, "lang_short");
				if($selected == $ls)
					$langname = "<strong>$langname</strong>";
				$output .= "<li class=\"display-block\"><a onclick=\"changeLanguage('$ls')\">$langname</a></li>\n";
			}
		break;
		case "menu_home":
			foreach ($langlist as $ls) {
				if(!is_dir($dirlang.$ls)) continue;
				$langname = getLangProp($ls, "lang_short");
				if($selected == $ls)
					$langname = "<strong>$langname</strong>";
				$output .= "<a class=\"dropdown-item\" href=\"javascript:changeLanguage('$ls')\">$langname</a>\n";
			}
		break;
		case "option":
			foreach ($langlist as $ls) {
				if(!is_dir($dirlang.$ls)) continue;
				$langname = getLangProp($ls, "lang");
				if($selected == $ls)
					$bb = "selected=\"selected\""; else
					$bb = "";
				$output .= "<option value=\"$ls\" $bb>$langname</option>\n";
			}
		break;
	}
	return $output;
}

function isLoggedIn(){
	if(isset($_SESSION['os_noinduk'],$_SESSION['os_nama'],$_SESSION['os_level'],$_SESSION['os_id'])) return true; else return false;
}
function isRegistered(){
	if(isset($_SESSION['os_pemilih'],$_SESSION['os_pemilih_kadaluarsa'],$_SESSION['os_pemilih_panitia'])) return true; else return false;
}
function getName(){
	return ucwords($_SESSION['os_nama']);
}
function getLevel(){
	switch($_SESSION['os_level']){
		case "1":
			return "Administrator";
		break;
		case "2":
			return "Registrator";
		break;
		case "3":
			return "Inspector";
		break;
	}
}
function getLevelFrom($data,$cat){
	if($cat == "id"){
		switch($data){
			case "1":
				return "Administrator";
			break;
			case "2":
				return "Registrator";
			break;
			case "3":
				return "Inspector";
			break;
		}
	} else if($cat == "label") {
		$data = strtolower($data);
		switch($data){
			case "administrator":
				return "1";
			break;
			case "registrator":
				return "2";
			break;
			case "inspector":
				return "3";
			break;
		}
	}
	return false;
}
function randomizeColor($type=""){
	$color = array('blue','purple','green','orange','red');
	if($type == "model")
		$color = array('info','primary','success','warning','danger');
	return $color[rand(0,4)];
}
function getIdentity($con,$induk,$col){
	$nomor = $induk;
	if(strlen($nomor) <= 5) $table = "tb_siswa"; else $table = "tb_guru";
	$noinduk = mysqli_query($con,"SELECT * FROM $table WHERE no_induk = '$nomor'");
	$noinduk_row = mysqli_fetch_array($noinduk);
	if(mysqli_num_rows($noinduk) == 1)
		return $noinduk_row[$col]; else
		return false;
}
function getPanitia($con,$id,$col){
	$noinduk = mysqli_query($con,"SELECT * FROM tb_panitia WHERE id = '$id'");
	$noinduk_row = mysqli_fetch_array($noinduk);
	if(mysqli_num_rows($noinduk) == 1)
		return $noinduk_row[$col]; else
		return false;
}
function getTotal($con,$type){
	$tabel = false;
	switch($type){
		case "siswa":
			$tabel = "tb_siswa";
		break;
		case "guru":
			$tabel = "tb_guru";
		break;
		case "voting":
			$tabel = "tb_hakpilih";
		break;
		case "suara":
			$tabel = "tb_polling";
		break;
		case "panitia":
			$tabel = "tb_panitia";
		break;
	}
	if($tabel === false) return 0;
	$jum = mysqli_query($con,"SELECT COUNT(*) FROM $tabel");
	return mysqli_fetch_array($jum)[0];
}
function netralize_noinduk($data){
	return preg_replace("/[^0-9]/", "", $data);
}
function netralize_nama($data){
	$data = preg_replace("/[^A-Za-z.,\-'\s]/", "", $data);
	$data = trim($data);
	return str_replace("'","\'",$data);
}
function netralize_words($data){
	$data = preg_replace("/[^0-9A-Za-z\s]/", "", $data);
	$data = trim($data);
	return $data;
}
function netralize_article($data){
	$data = preg_replace("/[^0-9A-Za-z.,:!\s\n\r]/", "", $data);
	$data = trim($data);
	return $data;
}
function netralize_db($data){
	$data = preg_replace("/[^0-9A-Za-z._\s]/", "", $data);
	$data = trim($data);
	return $data;
}
function netralize_timezone($data){
	return preg_replace("/[^A-Za-z\/]/", "", $data);
}
function netralize_judul($data){
	$data = preg_replace("/[^0-9A-Za-z.&\-'\s]/", "", $data);
	$data = trim($data);
	return str_replace("'","\'",$data);
}
function getDateTime($timestamp){
	global $func_lang;
	$datetime = strtotime($timestamp);
	$theHari = $func_lang['days'];
	$theBulan = $func_lang['months'];
	$harinya = $theHari[date("w", $datetime)];
	$lebihseminggu = $harinya.", ".date("j", $datetime)." ".$theBulan[date("n", $datetime)-1]." ".date("Y", $datetime);
	return $lebihseminggu." $func_lang[time_on] ".date($func_lang['time_format'], $datetime);
}
function getDateTimeSecond($timestamp){
	global $func_lang;
	$datetime = strtotime($timestamp);
	$theHari = $func_lang['days'];
	$theBulan = $func_lang['months'];
	$harinya = $theHari[date("w", $datetime)];
	$lebihseminggu = $harinya.", ".date("j", $datetime)." ".$theBulan[date("n", $datetime)-1]." ".date("Y", $datetime);
	return $lebihseminggu." ".date($func_lang['time_format_sec'], $datetime);
}
function timeAgo($timestamp){
		global $func_lang;
    $datetime1=new DateTime("now");
    $datetime2=date_create($timestamp);
    $diff=date_diff($datetime1, $datetime2);
		$theHari = $func_lang['days'];
		$theBulan = $func_lang['months'];
		$harinya = $theHari[date("w",strtotime($timestamp))];
		$lebihseminggu = $harinya.", ".date("j",strtotime($timestamp))." ".$theBulan[date("n",strtotime($timestamp))-1]." ".date("Y",strtotime($timestamp));
    if($diff->d > 1){
     return sprintf($func_lang['ago_long_at'], $lebihseminggu." ".date($func_lang['time_format'],strtotime($timestamp)));
    }
		if($diff->d == 1){
     return sprintf($func_lang['ago_yesterday'], date($func_lang['time_format'],strtotime($timestamp)));
    }
		else if($diff->h == 1){
     return $func_lang['ago_hour'];
    }
    else if($diff->h > 0){
     return $diff->h ." ".$func_lang['ago_hours'];
    }
		else if($diff->i == 1){
     return $func_lang['ago_minute'];
    }
    else if($diff->i > 0){
     return $diff->i ." ".$func_lang['ago_minutes'];
    }
    else if($diff->s > 10){
     return $diff->s ." ".$func_lang['ago_seconds'];
    }
		else if($diff->s >= 0){
     return $func_lang['ago_just_now'];
    }
}
function cekPilih($con,$noinduk){
	$voted = mysqli_query($con,"SELECT * FROM tb_polling WHERE no_induk = '$noinduk'");
	if(mysqli_num_rows($voted))
		return true; else
		return false;
}
function cekHakPilih($con,$noinduk){
	$voted = mysqli_query($con,"SELECT * FROM tb_hakpilih WHERE no_induk = '$noinduk'");
	if(mysqli_num_rows($voted))
		return true; else
		return false;
}
function hakPilihRegistrator($con,$noinduk){
	$voted = mysqli_query($con,"SELECT * FROM tb_hakpilih WHERE no_induk = '$noinduk'");
	if(mysqli_num_rows($voted)==1)
		return mysqli_fetch_array($voted)['id_panitia']; else
		return false;
}
function getPilihTime($con,$noinduk){
	$voted = mysqli_query($con,"SELECT * FROM tb_polling WHERE no_induk = '$noinduk'");
	if(mysqli_num_rows($voted) == 1)
		return timeAgo(mysqli_fetch_array($voted)['tgl']); else
		return false;
}

//Set Default Timezone
$tz = getWebProp($connection, "timezone");
if(strtolower($tz) != "default"){
	date_default_timezone_set($tz);
}

function getFBPic($fbid){
	$fbid = netralize_noinduk($fbid);
	if(strlen($fbid) >= 10)
		return "https://graph.facebook.com/$fbid/picture?type=large"; else
		return "";
}
function requireLevel($idlevel){
	if($_SESSION['os_level'] <= $idlevel)
		return true; else
		return false;
}
function getResultPoll(){
	$file = getPollDir();
	if(file_exists($file)){
		$output = json_decode(file_get_contents($file),true);
		return $output;
	} else {
		return false;
	}
}
function getPollDir(){
	return __DIR__."/../../data/hasil.json";
}
function is_publishedPoll(){
	return file_exists(getPollDir());
}
function currentTimestamp(){
	return date("Y-m-d H:i:s",time());
}
function generateAccessCode($length = 5) {
    $characters = '0123456789ABCDEFGHIJKLMNoPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function cekKodeAkses($con,$kode_akses){
	$kode = mysqli_query($con,"SELECT * FROM tb_hakpilih WHERE kode_akses = '$kode_akses'");
	if(mysqli_num_rows($kode)){
		return true;
	} else return false;
}
function getKodeAkses($con,$kode_akses){
	$kode = mysqli_query($con,"SELECT * FROM tb_hakpilih WHERE kode_akses = '$kode_akses'");
	if(mysqli_num_rows($kode) == 1){
		return mysqli_fetch_array($kode)['no_induk'];
	} else return false;
}
function checkplugins($name=""){
	if($name == "") return false;
	$dir = __DIR__."/../plugins/";
	if(file_exists($dir.$name)) return true; else return false;
}
function getplugindir($name=""){
	if($name == "") return false;
	return __DIR__."/../plugins/$name";
}
function getVersion($con,$type="string"){
	$qry = mysqli_query($con,"SELECT * FROM tb_pengaturan WHERE id = 1");
	$data = mysqli_fetch_array($qry);
	if($type == "formatted")
		return array("major" => $data['v_major'], "minor" => $data['v_minor']); else
		return "v".$data['v_major'].".".$data['v_minor'];
}

function timezone_list() {
    static $timezones = null;

    if ($timezones === null) {
        $timezones = [];
        $offsets = [];
        $now = new DateTime('now', new DateTimeZone('UTC'));

        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $now->setTimezone(new DateTimeZone($timezone));
            $offsets[] = $offset = $now->getOffset();
            $timezones[$timezone] = '(' . format_GMT_offset($offset) . ') ' . format_timezone_name($timezone);
        }

        array_multisort($offsets, $timezones);
    }

    return $timezones;
}

function format_GMT_offset($offset) {
    $hours = intval($offset / 3600);
    $minutes = abs(intval($offset % 3600 / 60));
    return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
}

function format_timezone_name($name) {
    $name = str_replace('/', ', ', $name);
    $name = str_replace('_', ' ', $name);
    $name = str_replace('St ', 'St. ', $name);
    return $name;
}
function getTimezoneList(){
	$timezones = timezone_list();
	$output = "<option value=\"default\">System Default</option>\n";
	foreach($timezones as $val => $label){
		$output .= "<option value=\"$val\">$label</option>\n";
	}
	return $output;
}
?>
