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
	<title><?php echo $localize['title_kandidat']; ?></title>
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
                        <a class="navbar-brand" href="#"><?php echo $localize['title_kandidat']; ?> </a>
                    </div>

                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
										<div class="row">
											<div class="col-md-12">
													<div class="card card-nav-tabs">
															<div class="card-header" data-background-color="purple">
																<div class="nav-tabs-navigation">
																	<div class="nav-tabs-wrapper">
																		<ul class="nav nav-tabs" data-tabs="tabs">
																			<li class="active"><a href="#tab_cari" data-toggle="tab"><i class="material-icons">search</i></a></li>
																			<?php if(requireLevel(1)){ ?>
																				<li><a href="#tab_tambah" data-toggle="tab"><i class="material-icons">person_add</i></a></li>
																			<?php } ?>
																		</ul>
																	</div>
																</div>
															</div>
															<div class="card-content">
																<div class="tab-content">
																	<div id="tab_cari" class="tab-pane fade in active">
																		<div class="form-group label-floating">
																			<label class="control-label"><?php echo $localize['text_search_candidates']; ?></label>
																			<input class="form-control" id="kandidat_cari" type="text">
																			<span class="material-input"></span>
																		</div>
																		<div class="right">
																			<button class="btn btn-default" onclick="resetCari()" id="btn_reset"><?php echo $localize['btn_reset']; ?></button>
																			<script>
																				function resetCari(){
																					$('#kandidat_cari').val("").focus();
																					$('.col-md-4.margin-top-20').each(function(){
																						$(this).show('medium');
																					});
																				}
																				$('#kandidat_cari').keypress(function(e){
																					var keyCode = e.which;
																					if(keyCode == 13){
																						var keyword = $('#kandidat_cari').val().toLocaleLowerCase();
																						if(keyword.length == 0){
																							resetCari();
																							return false;
																						}
																						$('.col-md-4.margin-top-20').each(function(){
																							var nama = $(this).find('h3.card-title').text().toLocaleLowerCase();
																							var kelas = $(this).find('h5.card-class').text().toLocaleLowerCase();
																							var nama_cocok, kelas_cocok = true;
																							if(nama.indexOf(keyword) == -1) nama_cocok = false;
																							if(kelas != keyword) kelas_cocok = false;
																							if((nama_cocok == false) && (kelas_cocok == false)) $(this).hide('medium'); else $(this).show('medium');
																						});
																					}
																				}).keyup(function(e){
																					var keyCode = e.which;
																					if(keyCode == 27) resetCari();
																				});
																			</script>
																		</div>
																	</div>
																	<?php if(requireLevel(1)){ ?>
																		<div id="tab_tambah" class="tab-pane fade">
																			<div class="col-md-4">
																				<div class="form-group label-floating">
																					<label class="control-label"><?php echo $localize['tbl_name']; ?></label>
																					<input class="form-control" id="kand_nama" type="text">
																					<span class="material-input"></span>
																				</div>
																			</div>
																			<div class="col-md-4">
																				<div class="form-group label-floating">
																					<label class="control-label"><?php echo $localize['tbl_grade_position']; ?></label>
																					<input class="form-control" id="kand_kelas" type="text">
																					<span class="material-input"></span>
																				</div>
																			</div>
																			<div class="col-md-4">
																				<div class="form-group label-floating">
																					<label class="control-label"><?php echo $localize['tbl_fbid']; ?></label>
																					<input class="form-control" id="kand_fbid" type="text">
																					<span class="material-icons form-control-feedback">success</span>
																					<span class="material-input"></span>
																				</div>
																			</div>
																			<div class="col-md-12">
																				<div class="form-group label-floating">
																					<label class="control-label"><?php echo $localize['tbl_bio']; ?></label>
																					<textarea class="form-control" id="kand_bio" rows="4"></textarea>
																					<span class="material-input"></span>
																				</div>
																				<div class="right">
																					<button class="btn btn-primary" onclick="kandidat('tambah')" id="btn_tambah"><?php echo $localize['btn_add']; ?></button>
																				</div>
																			</div>
																		</div>
																	<?php } ?>
																	<div class="clearfix"></div>
																</div>
															</div>
													</div>
											</div>
										</div>
                    <div class="row" id="list_kandidat">
											<?php
											$kandidat = mysqli_query($connection,"SELECT * FROM tb_kandidat ORDER BY nama ASC");
											$kandidat_num = mysqli_num_rows($kandidat);
											if($kandidat_num){
												while($kandidat_row = mysqli_fetch_array($kandidat)){ ?>
											<div class="col-md-4 margin-top-20" id="card_<?php echo $kandidat_row['id']; ?>">
												<div class="card card-profile card-nav-tabs">
													<div class="card-avatar">
														<img class="img" id="inf_photo_<?php echo $kandidat_row['id']; ?>" src="<?php echo getFBPic($kandidat_row['fbid']); ?>">
													</div>
													<div class="card-content">
														<div class="tab-content">
															<div id="kd_info_<?php echo $kandidat_row['id']; ?>" class="tab-pane active">
																<h3 id="inf_nama_<?php echo $kandidat_row['id']; ?>" class="card-title"><?php echo $kandidat_row['nama']; ?></h3>
																<h5 id="inf_kelas_<?php echo $kandidat_row['id']; ?>" class="card-class"><?php echo $kandidat_row['kelas']; ?></h5>
																<p id="inf_bio_<?php echo $kandidat_row['id']; ?>"><?php echo nl2br($kandidat_row['bio']); ?></p>
																<?php if(requireLevel(1)){ ?>
																	<button class="btn btn-round btn-just-icon btn-info" onclick="kandidat('buka_edit','<?php echo $kandidat_row['id']; ?>')"><i class="material-icons">create</i></button>
																	<button class="btn btn-round btn-just-icon btn-danger" onclick="kandidat('hapus','<?php echo $kandidat_row['id']; ?>')"><i class="material-icons">delete_forever</i></button>
																<?php } ?>
															</div>
															<?php if(requireLevel(1)){ ?>
																<div id="kd_edit_<?php echo $kandidat_row['id']; ?>" class="tab-pane left">
																	<div class="form-group label-floating form-info">
																		<label class="control-label"><?php echo $localize['tbl_name']; ?></label>
																		<input class="form-control" id="ekand_nama_<?php echo $kandidat_row['id']; ?>" type="text">
																		<span class="material-input"></span>
																	</div>
																	<div class="form-group label-floating form-info">
																		<label class="control-label"><?php echo $localize['tbl_grade_position']; ?></label>
																		<input class="form-control" id="ekand_kelas_<?php echo $kandidat_row['id']; ?>" type="text">
																		<span class="material-input"></span>
																	</div>
																	<div class="form-group label-floating form-info">
																		<label class="control-label"><?php echo $localize['tbl_fbid']; ?></label>
																		<input class="form-control" id="ekand_fbid_<?php echo $kandidat_row['id']; ?>" type="text">
																		<span class="material-input"></span>
																	</div>
																	<div class="form-group label-floating form-info">
																		<label class="control-label"><?php echo $localize['tbl_bio']; ?></label>
																		<textarea class="form-control" id="ekand_bio_<?php echo $kandidat_row['id']; ?>" rows="4"></textarea>
																		<span class="material-input"></span>
																	</div>
																	<div class="right">
																		<button class="btn btn-danger btn-simple" onclick="kandidat('batal_edit','<?php echo $kandidat_row['id']; ?>')"><?php echo $localize['btn_cancel']; ?></button>
																		<button class="btn btn-info" onclick="kandidat('edit','<?php echo $kandidat_row['id']; ?>')"><?php echo $localize['btn_update']; ?></button>
																	</div>
																</div>
															<?php } ?>
														</div>
													</div>
												</div>
											</div>
										<?php }
									} else {
										echo "<div class=\"center\">$localize[text_no_candidate]</div>";
									}
										 ?>
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
<?php include("./components/bottom_script.php"); ?>
<script>
$("#kand_fbid").popover({
	placement: 'bottom',
	html: true,
	content: "<?php echo sprintf($localize['text_fbid_tooltip'], "10-15", "<a href='https://lookup-id.com/' target='_blank'>lookup-id.com</a>"); ?>"
}).focus(function(){
	$(this).popover('show');
}).blur(function(){
	$(this).popover('hide');
})
</script>
</html>
