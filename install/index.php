<?php
	error_reporting(0);
	include("../admin/inc/functions.php");
	$localize = getLang("installation", "default", "../languages");
?>
<!doctype html>
<html lang="en">

<head>
	<title><?php echo $localize['title_install']; ?></title>
	<meta charset="utf-8" />
	<link rel="shortcut icon" href="../favicon.png">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width" />
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />

	<!-- Bootstrap core CSS     -->
	<link href="../admin/css/bootstrap.min.css" rel="stylesheet" />
	<!--  Material Dashboard CSS    -->
	<link href="../admin/css/material-dashboard.css?v=1.2.0" rel="stylesheet" />
	<link href="../admin/css/material-tripath.css?v=1.0" rel="stylesheet" />
	<!--     Fonts and icons     -->
	<link href="../css/font-awesome.min.css" rel="stylesheet">
	<link href="../css/material-icons/material-icons.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
<script src="../admin/js/jquery-3.2.1.min.js" type="text/javascript"></script>
</head>

<body style="overflow-x: hidden">
	<svg class="nice-background" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1440 810" preserveAspectRatio="xMinYMin slice" aria-hidden="true">
		<?php include("../admin/components/nice-background.php"); ?>
	</svg>
    <div class="wrapper">
        <div class="main-panel width-full">
            <div class="content">
                <div class="container-fluid">
                    <div id="welcome" class="row instalasi">
											<div class="col-lg-6 col-centered">
												<div class="card card-nav-tabs">
													<div class="card-content">
														<div class="center">
															<h1 class="text-faded" id="welcome-text"><?php echo $localize['welcomes'][0]; ?></h1>
															<div class="form-group label-floating form-success">
																<label class="control-label" for="timezone"><?php echo $localize['label_installation_language']; ?></label>
																<select class="form-control" id="sett_lang">
																	<?php echo getListLang("option", $_COOKIE['el_lang']); ?>
																</select>
															</div>
															<button class="btn btn-success" onclick="instalasi('lisensi')"><?php echo $localize['btn_next']; ?></button>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div id="license" class="row instalasi">
											<div class="col-lg-6 col-centered">
												<div class="card card-nav-tabs">
													<div class="card-header" data-background-color="green">
														<h4 class="title"><?php echo $localize['title_licensing']; ?></h4>
													</div>
													<div class="card-content">
														<div class="lisensi">
															<?php echo str_replace("\n","<br>",file_get_contents("../license.tp")); ?>
														</div>
														<div class="right">
															<button class="btn btn-danger btn-simple" onclick="instalasi('back_welcome')"><?php echo $localize['btn_dont_agree']; ?></button> <button onclick="instalasi('mysql')" class="btn btn-success" id="lic_agree" disabled="disabled"><?php echo $localize['btn_agree']; ?></button>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div id="mysql" class="row instalasi">
											<div class="col-lg-6 col-centered">
												<div class="card card-nav-tabs">
													<div class="card-header" data-background-color="green">
														<h4 class="title"><?php echo $localize['title_mysql']; ?></h4>
													</div>
													<div class="card-content">
														<div id="mysql_1">
															<div class="form-group label-floating form-success">
																<label class="control-label"><?php echo $localize['label_host']; ?></label>
																<input class="form-control" type="text" id="con_host" value="localhost">
																<span class="material-input"></span>
															</div>
															<div class="form-group label-floating form-success">
																<label class="control-label"><?php echo $localize['label_username']; ?></label>
																<input class="form-control" type="text" id="con_user" value="root">
																<span class="material-input"></span>
															</div>
															<div class="form-group label-floating form-success">
																<label class="control-label"><?php echo $localize['label_password']; ?></label>
																<input class="form-control" type="password" id="con_pass" value="">
																<span class="material-input"></span>
															</div>
															<div class="right">
																<button id="mysql_btn_cek" class="btn btn-success btn-simple" onclick="instalasi('mysql_cek')"><?php echo $localize['btn_connect']; ?></button>
															</div>
														</div>
														<div id="mysql_2" class="instalasi">
															<div class="form-group label-floating form-success">
																<label class="control-label"><?php echo $localize['label_database']; ?></label>
																<input class="form-control" type="text" id="con_db" value="db_election">
																<span class="material-input"></span>
															</div>
															<div class="right">
																<button class="btn btn-default btn-simple" onclick="instalasi('mysql_back')"><?php echo $localize['btn_back']; ?></button>
																<button id="mysql_btn_db" class="btn btn-success" onclick="instalasi('mysql_db_cek')"><?php echo $localize['btn_begin_install']; ?></button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div id="process" class="row instalasi">
											<div class="col-lg-6 col-centered">
												<div class="card card-nav-tabs">
													<div class="card-content">
														<h3 id="process_title"><?php echo $localize['title_installing']; ?></h3>
														<div id="process_loader" class="loader mb-10"></div>
														<ul class="nav flex-column install-status">
															<li id="prog_db" class="nav-item"><i class="material-icons text-success">done</i> <?php echo $localize['text_creating_database']; ?></li>
															<li id="prog_conf" class="nav-item"><i class="material-icons text-success">done</i> <?php echo $localize['text_config_save']; ?></li>
															<li id="prog_tbl" class="nav-item"><i class="material-icons text-success">done</i> <?php echo $localize['text_creating_table']; ?></li>
															<li id="prog_dump" class="nav-item"><i class="material-icons text-success">done</i> <?php echo $localize['text_modify_structure']; ?></li>
															<li id="prog_alter" class="nav-item"><i class="material-icons text-success">done</i> <?php echo $localize['text_insert_default_values']; ?></li>
														</ul>
														<small id="prog_text"><?php echo $localize['text_installing_warning']; ?></small>
													</div>
												</div>
											</div>
										</div>
										<div id="zonawaktu" class="row instalasi">
											<div class="col-lg-6 col-centered">
												<div class="card card-nav-tabs">
													<div class="card-content">
														<h3><?php echo $localize['title_timezone']; ?></h3>
														<div id="zonawaktu-bg">
															<div id="zona-wib"></div>
															<div id="zona-wita"></div>
															<div id="zona-wit"></div>
														</div>
														<div class="col-lg-6 col-centered">
															<div class="form-group label-floating form-success">
																<select class="form-control" id="sett_timezone">
																	<?php echo getTimezoneList(); ?>
																</select>
																<script>
																	$("#zonawaktu-bg div").on("click", function(){
																		switch($(this).attr("id")){
																			case "zona-wib":
																				$("#sett_timezone").val("Asia/Jakarta").change();
																			break;
																			case "zona-wita":
																				$("#sett_timezone").val("Asia/Makassar").change();
																			break;
																			case "zona-wit":
																				$("#sett_timezone").val("Asia/Jayapura").change();
																			break;
																		}
																	});
																	$("#sett_timezone").on("change", function(){
																		$("#zonawaktu-bg div").each(function(){
																			$(this).removeClass("active");
																		});
																		switch($(this).val()){
																			case "Asia/Jakarta":
																				$("#zona-wib").addClass("active");
																			break;
																			case "Asia/Makassar":
																				$("#zona-wita").addClass("active");
																			break;
																			case "Asia/Jayapura":
																				$("#zona-wit").addClass("active");
																			break;
																		}
																	});
																</script>
															</div>
														</div>
														<div class="center">
															<button class="btn btn-success" onclick="instalasi('webinfo')"><?php echo $localize['btn_next']; ?></button>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div id="information" class="row instalasi">
											<div class="col-lg-6 col-centered">
												<div class="card card-nav-tabs">
													<div class="card-content">
														<h3><button id="info_back" class="btn btn-default btn-fab btn-simple" onclick="instalasi('zonawaktu')"><i class="material-icons">chevron_left</i></button> <?php echo $localize['title_web_info']; ?></h3>
														<div class="form-group label-floating form-success">
															<label class="control-label"><?php echo $localize['label_owner']; ?></label>
															<input class="form-control" type="text" id="sett_judul" value="eLection">
															<span class="material-input"></span>
														</div>
														<div class="form-group label-floating form-success">
															<label class="control-label"><?php echo $localize['label_website_name']; ?></label>
															<input class="form-control" type="text" id="sett_subjudul" value="Web Based Election System">
															<span class="material-input"></span>
														</div>
														<div class="form-group label-floating form-success">
															<label class="control-label"><?php echo $localize['label_instance']; ?></label>
															<input class="form-control" type="text" id="sett_instansi" value="Tripath Projects">
															<span class="material-input"></span>
														</div>
														<div class="right">
															<button id="info_skip" class="btn btn-success btn-simple" onclick="instalasi('skip_webinfo')"><?php echo $localize['btn_skip']; ?></button>
															<button id="info_save" class="btn btn-success" onclick="instalasi('save_webinfo')"><?php echo $localize['btn_save']; ?></button>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div id="success" class="row instalasi">
											<div class="col-lg-6 col-centered">
												<div class="card card-nav-tabs">
													<div class="card-content">
														<h3><?php echo $localize['text_done']; ?></h3>
														<p><?php echo $localize['text_done_1']; ?></p>
														<p><?php echo $localize['text_done_id']; ?> <strong>1234</strong><br><?php echo $localize['text_done_pass']; ?> <strong>1234</strong></p>
														<small class="text-danger"><?php echo $localize['text_done_nb']; ?></small>
														<div class="right">
															<button class="btn btn-success" onclick="top.location = '../admin/?_installed'"><?php echo $localize['btn_login_now']; ?></button>
														</div>
													</div>
												</div>
											</div>
										</div>
                </div>
            </div>
        </div>
    </div>
</body>
<!--   Core JS Files   -->
<script src="../admin/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../admin/js/material.min.js" type="text/javascript"></script>
<!--  Dynamic Elements plugin -->
<script src="../admin/js/arrive.min.js"></script>
<!--  PerfectScrollbar Library -->
<script src="../admin/js/perfect-scrollbar.jquery.min.js"></script>
<!--  Notifications Plugin    -->
<script src="../admin/js/bootstrap-notify.js"></script>
<!-- Material Dashboard javascript methods -->
<script src="../admin/js/material-dashboard.js?v=1.2.0"></script>
<link href="../admin/css/animate.css" rel="stylesheet" />
<style>
#zonawaktu-bg {
	margin: 0 auto;
	margin-top: 30px;
	width: 400px;
	height: 180px;
	position: relative;
	border: 1px solid #ddd;
	box-shadow: 0 0 5px #eee;
	background: url(./img/map-indo.jpg) left top no-repeat;
}
	#zonawaktu-bg div {
		height: 180px;
		position: absolute;
		opacity: 0;
		cursor: pointer;
		transition: all .3s ease-in-out;
	}
	#zonawaktu-bg div.active {
		opacity: 1;
	}
	#zona-wib {
		width: 192px;
		background: url(./img/map-indo-wib.png) left top no-repeat;
	}
	#zona-wita {
		width: 104px;
		left: 167px;
		background: url(./img/map-indo-wita.png) left top no-repeat;
	}
	#zona-wit {
		width: 154px;
		right: -1px;
		background: url(./img/map-indo-wit.png) left top no-repeat;
	}
</style>
<script src="../admin/js/jquery.animatecss.min.js"></script>
<script src="../js/jquery.cookie.js"></script>
<script src="./js/tripath.localization.js"></script>
<script src="./js/install.js"></script>
<script>
instalasi("welcome");

$(".lisensi").scroll(function() {
    if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
        $("#lic_agree").prop('disabled', false);
    } else {
        $("#lic_agree").prop('disabled', true);
    }
});
</script>
</html>
