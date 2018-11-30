<?php
	if(!file_exists("./inc/conn.php")){
		header("Location: ../install/?_rdr");
		exit();
	}
	include("./inc/conn.php");
	include("./inc/functions.php");
	$localize = getLang("admin");
	if(isLoggedIn()){
		echo $localize['logged_in'];
		echo "<script>top.location = './dashboard.php?_loggedin'</script>";
		exit();
	}
?>
<!doctype html>
<html lang="en">

<head>
	<title><?php echo $localize['login_title']; ?></title>
    <?php include("./components/header.php"); ?>
</head>

<body>
	<svg class="nice-background" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1440 810" preserveAspectRatio="xMinYMin slice" aria-hidden="true">
		<?php include("./components/nice-background.php"); ?>
	</svg>
    <div class="wrapper">
        <div class="main-panel width-full">
            <div class="content margin-top-20">
                <div class="container-fluid">
                    <div class="row">
											<div class="col-lg-4 col-centered">
													<?php
														if(file_exists("../install/")){
													?>
													<div class="alert alert-warning">
														<div class="container-fluid">
															<div class="alert-icon">
																<i class="material-icons">error_outline</i>
															</div>
															<?php echo $localize['install_dir_exist']; ?>
														</div>
													</div>
													<?php
														}
													?>
													<div class="card ovhidden">
														<div class="loader opacity-0"></div>
														<div id="login-card" class="card-content">
															<h2 class="web_title_1 text-success"><?php echo getWebProp($connection, "judul"); ?></h2>
															<div id="subtitle_1">
																<h4 class="web_title_2"><?php echo getWebProp($connection, "subjudul"); ?></h4>
																<h5 class="web_title_3"><?php echo getWebProp($connection, "instansi"); ?></h5>
															</div>
															<div id="subtitle_2">
																<div class="row">
																	<i class="material-icons text-success">account_circle</i>
																	<div class="pull-left">
																		<h4 class="web_title_2"></h4>
																		<h5 class="web_title_3"></h5>
																	</div>
																</div>
															</div>
															<div class="spacer"></div>
															<div id="form-induk">
																<div class="form-group label-floating form-success">
																	<label class="control-label"><?php echo $localize['id_type']; ?></label>
																	<input class="form-control" type="text" id="noinduk">
																	<span class="material-input"></span>
																</div>
																<div class="right">
																	<button class="btn btn-success" id="buttoner"><?php echo $localize['btn_next']; ?></button>
																</div>
															</div>
															<div id="form-pass" class="form">
																<div class="form-group label-floating form-success">
																	<label class="control-label"><?php echo $localize['txt_pass']; ?></label>
																	<input class="form-control" type="password" id="sandi">
																	<span class="material-input"></span>
																</div>
																<div class="right">
																	<a id="not-me" class="text-success pointer pull-left pull-fix"></a>
																	<button class="btn btn-success" id="buttoners"><?php echo $localize['btn_login']; ?></button>
																</div>
															</div>
														</div>
													</div>
													<footer class="footer login">
															<div class="pull-left">
																<nav class="pull-left">
						                        <ul>
																				<li class="dropdown">
						                                <a class="text-success pointer" data-toggle="dropdown">
																							<?php echo getCurrentLang(); ?> <b class="caret"></b>
						                                </a>
																						<ul class="dropdown-menu up">
																							<?php echo getListLang("menu", $_COOKIE['el_lang']); ?>
																						</ul>
						                            </li>
						                        </ul>
						                    </nav>
					                    </div>
															<p class="copyright pull-right">&copy; 2018 &nbsp;<strong>tripath</strong>projects </p>
													</footer>
											</div>
										</div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include("./components/bottom_script.php"); ?>
<script src="./js/index.js"></script>
</html>
<?php mysqli_close($connection); ?>
