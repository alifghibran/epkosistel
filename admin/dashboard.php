<?php
	include("./inc/conn.php");
	include("./inc/functions.php");
	$localize = getLang("admin");
	if(!isLoggedIn()){
		echo $localize['alert_not_logged_in'];
		exit();
	}
?>
<!doctype html>
<html lang="en">

<head>
	<title><?php echo $localize['title_dashboard']; ?></title>
    <?php include("./components/header.php"); ?>
</head>

<body>
    <div class="wrapper">
        <?php include("./components/nav.php"); ?>
        <div class="main-panel">
            <nav class="navbar navbar-transparent navbar-absolute">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <?php include("./components/navbar_header.php"); ?>
                        <a class="navbar-brand" href="#"><?php echo $localize['title_dashboard']; ?> </a>
                    </div>

                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="orange">
                                    <i class="material-icons">record_voice_over</i>
                                </div>
                                <div class="card-content">
                                    <p class="category"><?php echo $localize['text_invote']; ?></p>
                                    <h3 class="title" id="num_voting"></h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">schedule</i> <span id="label_voting"><?php echo $localize['text_loading']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="green">
                                    <i class="material-icons">move_to_inbox</i>
                                </div>
                                <div class="card-content">
                                    <p class="category"><?php echo $localize['text_vote_count']; ?></p>
                                    <h3 class="title" id="num_vote"></h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">schedule</i> <span id="label_vote"><?php echo $localize['text_loading']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="red">
                                    <i class="material-icons">wc</i>
                                </div>
                                <div class="card-content">
                                    <p class="category"><?php echo $localize['title_siswa']; ?></p>
                                    <h3 class="title"><?php echo getTotal($connection,"siswa"); ?></h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> <span class="momentjs" data-tgl="<?php echo currentTimestamp(); ?>"><?php echo $localize['text_loading']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="blue">
                                    <i class="material-icons">business_center</i>
                                </div>
                                <div class="card-content">
                                    <p class="category"><?php echo $localize['title_guru']; ?></p>
                                    <h3 class="title"><?php echo getTotal($connection,"guru"); ?></h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> <span class="momentjs" data-tgl="<?php echo currentTimestamp(); ?>"><?php echo $localize['text_loading']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
											<?php if(requireLevel(2)){
															$class = "col-lg-6";
											 ?>
                        <div class="col-lg-6 col-md-12">
                            <div class="card card-nav-tabs">
                                <div class="card-header" data-background-color="purple">
                                    <h4 class="title"><?php echo $localize['text_registration']; ?></h4>
                                    <p class="category"><?php echo $localize['text_registration_sub']; ?></p>
                                </div>
                                <div class="card-content">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="inputnis">
																					<div class="form-group label-floating is-empty">
																						<label class="control-label"><?php echo $localize['label_register_id']; ?></label>
																						<input class="form-control" id="ni_pemilih" type="text">
																						<script>
																							$('#ni_pemilih').keypress(function(e){
																								var key = e.which;
																								if(key == 13){
																									e.preventDefault();
																									hakPilih(1);
																								}
																							})
																						</script>
																						<span class="material-input"></span>
																					</div>
																					<div class="right">
																						<?php
																							if(file_exists("./plugins/receipt/"))
																								include("./plugins/receipt/include.php");
																						?>
																						<button class="btn btn-primary" onclick="hakPilih(1)" id="btn_cari"><?php echo $localize['btn_validate']; ?></button>
																					</div>
                                        </div>
                                        <div class="tab-pane" id="datapemilih">
                                            <div class="center">
																							<h3 id="pilih_nama"></h3>
																							<h5 id="pilih_noinduk"></h5>
																							<div>
																								<button class="btn btn-default" onclick="hakPilih(0)"><?php echo $localize['btn_cancel']; ?></button>
																								<button class="btn btn-primary" onclick="hakPilih(2)" id="btn_daftar"><?php echo $localize['btn_register']; ?></button>
																								<script>
																									$('#btn_daftar').keyup(function(e){
																										var key = e.which;
																										if(key == 27){
																											e.preventDefault();
																											hakPilih(0);
																										}
																									})
																								</script>
																							</div>
																						</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
											<?php } else $class = "col-lg-12"; ?>
                        <div class="<?php echo $class; ?> col-md-12">
                            <div class="card">
                                <div class="card-header" data-background-color="orange">
																		<?php if($_SESSION['os_level'] == "3"){ ?><button class="btn btn-warning btn-just-icon float-right" onclick="hakPilih('relate')"><i class="material-icons">person</i></button><?php } ?>
                                    <h4 class="title"><?php echo $localize['text_registered_voters']; ?></h4>
                                    <p class="category" id="update_hakpilih"><?php echo $localize['text_fetching']; ?></p>
                                </div>
                                <div class="card-content table-responsive">
                                    <table class="table table-hover">
                                        <thead class="text-warning">
                                            <th><?php echo $localize['tbl_num']; ?></th>
                                            <th><?php echo $localize['tbl_name']; ?></th>
                                            <th><?php echo $localize['tbl_id']; ?></th>
                                            <th><?php echo $localize['tbl_register']; ?></th>
                                        </thead>
                                        <tbody id="list_hakpilih">
                                            <tr>
                                                <td colspan="4"><?php echo $localize['text_loading']; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer">
                <?php include("./components/footer.php"); ?>
            </footer>
        </div>
    </div>
		<?php include("./components/modals.php"); ?>
</body>
<script src="./js/jquery.print.js"></script>
<?php include("./components/bottom_script.php"); ?>
<script src="../js/moment.min.js"></script>
<script>
	moment.locale("<?php echo getCurrentLocale(); ?>");
	function getUpdateTime(){
		var tgt = $('.momentjs');
		tgt.each(function(){
			var tgl = $(this).attr('data-tgl');
			$(this).text(moment(tgl,"YYYY-MM-DD HH:mm:ss").fromNow());
		});
	}
	setInterval(getUpdateTime, 3000);
	setInterval(fetchHakPilih, 30000);
	fetchHakPilih();
</script>
</html>
