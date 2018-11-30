<?php
	include("./inc/conn.php");
	include("./inc/functions.php");
	$localize = getLang("admin");
	if(!isLoggedIn()){
		echo $localize['alert_not_logged_in'];
		exit();
	}
	if(!requireLevel(1)){
		echo $localize['alert_administrator_previlege'];
		exit();
	}
?>
<!doctype html>
<html lang="en">

<head>
	<title><?php echo $localize['title_pengaturan']; ?></title>
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
                        <a class="navbar-brand" href="#"><?php echo $localize['title_pengaturan']; ?> </a>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
										<div class="row">
											<div class="col-md-12">
													<div class="card card-nav-tabs">
															<div class="card-header" data-background-color="green">
																<div class="nav-tabs-navigation">
																	<div class="nav-tabs-wrapper">
																		<ul class="nav nav-tabs" data-tabs="tabs">
																			<li class="active">
																				<a href="#tab_umum" data-toggle="tab">
																					<i class="material-icons">settings</i>
																					<?php echo $localize['menu_general']; ?>
																				</a>
																			</li>
																			<li>
																				<a href="#tab_localization" data-toggle="tab">
																					<i class="material-icons">public</i>
																					<?php echo $localize['menu_regional']; ?>
																				</a>
																			</li>
																			<li>
																				<a href="#tab_suara" data-toggle="tab">
																					<i class="material-icons">record_voice_over</i>
																					<?php echo $localize['menu_votes']; ?>
																				</a>
																			</li>
																			<li>
																				<a href="#tab_grafik" data-toggle="tab">
																					<i class="material-icons">equalizer</i>
																					<?php echo $localize['menu_vote_count']; ?>
																				</a>
																			</li>
																		</ul>
																	</div>
																</div>
															</div>
															<div class="card-content">
																<div class="tab-content">
																	<div id="tab_umum" class="tab-pane fade in active">
																		<?php
																			$pengaturan = mysqli_query($connection,"SELECT * FROM tb_pengaturan WHERE id=1");
																			$pengaturan_row = mysqli_fetch_array($pengaturan);
																		?>
																		<div class="col-md-6">
																			<div class="form-group label-floating form-success">
																				<label class="control-label"><?php echo $localize['tbl_owner']; ?></label>
																				<input class="form-control" type="text" id="sett_judul" value="<?php echo $pengaturan_row['judul']; ?>">
																				<span class="material-input"></span>
																			</div>
																			<div class="form-group label-floating form-success">
																				<label class="control-label"><?php echo $localize['tbl_website_name']; ?></label>
																				<input class="form-control" type="text" id="sett_subjudul" value="<?php echo $pengaturan_row['subjudul']; ?>">
																				<span class="material-input"></span>
																			</div>
																			<div class="form-group label-floating form-success">
																				<label class="control-label"><?php echo $localize['tbl_instance']; ?></label>
																				<input class="form-control" type="text" id="sett_instansi" value="<?php echo $pengaturan_row['instansi']; ?>">
																				<span class="material-input"></span>
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																				<div class="togglebutton green">
																					<label>
																						<input type="checkbox" id="sett_enable" <?php if($pengaturan_row['enable_poll']) echo "checked=\"\""; ?>>
																						<?php echo $localize['text_allow_vote']; ?>
																					</label>
																				</div>
																			</div>
																			<div class="form-group label-floating form-success">
																				<label class="control-label"><?php echo $localize['text_if_vote_closed']; ?></label>
																				<textarea class="form-control" id="sett_message" rows="4"><?php echo $pengaturan_row['disabled_text']; ?></textarea>
																				<span class="material-input"></span>
																			</div>
																		</div>
																		<div class="right">
																			<button class="btn btn-success" id="btn_umum" onclick="pengaturan('umum')"><?php echo $localize['btn_save']; ?></button>
																		</div>
																	</div>
																	<div id="tab_localization" class="tab-pane fade">
																		<div class="col-md-6">
																			<style>
																			#zonawaktu-bg {
																				margin: 0 auto;
																				margin-top: 30px;
																				margin-bottom: 40px;
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
																			<div id="zonawaktu-bg">
																				<div id="zona-wib"></div>
																				<div id="zona-wita"></div>
																				<div id="zona-wit"></div>
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group label-floating form-success">
																				<label class="control-label" for="timezone"><?php echo $localize['tbl_server_timezone']; ?></label>
																				<select class="form-control" id="sett_timezone">
																					<?php echo getTimezoneList(); ?>
																				</select>
																			</div>
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
																				$("#sett_timezone option[value='<?php echo $pengaturan_row['timezone']; ?>']").attr("selected","selected").change();
																			</script>
																			<div class="form-group label-floating form-success">
																				<label class="control-label" for="timezone"><?php echo $localize['tbl_default_language']; ?></label>
																				<select class="form-control" id="sett_lang_default">
																					<?php echo getListLang("option", getWebProp($connection, "default_language")); ?>
																				</select>
																			</div>
																		</div>
																		<div class="right">
																			<button class="btn btn-success" id="btn_umum_2" onclick="pengaturan('umum')"><?php echo $localize['btn_save']; ?></button>
																		</div>
																	</div>
																	<div id="tab_suara" class="tab-pane fade">
																		<?php
																			$suara_jum = getTotal($connection,'suara');
																			$pemilih_jum = getTotal($connection,'siswa') + getTotal($connection,'guru');
																			if($suara_jum)
																				$text = sprintf($localize['text_vote_counts'], $suara_jum, $pemilih_jum); else
																				$text = $localize['text_vote_zero'];
																		?>
																		<div class="form-group">
																			<button class="btn btn-danger" id="btn_hapus_suara" onclick="pengaturan('hapus_poll')"><?php echo $localize['btn_empty_vote']; ?></button>
																			<label class="margin-left-10" id="label_suara"><?php echo $text; ?></label>
																		</div>
																	</div>
																	<div id="tab_grafik" class="tab-pane fade">
																		<div class="form-group">
																			<?php
																				if(is_publishedPoll()){
																					$pub_add = "";
																					$pub = $localize['btn_update'];
																				} else {
																					$pub_add = " style=\"display: none;\"";
																					$pub = $localize['btn_publish'];
																				}
																			?>
																			<button class="btn btn-danger" id="btn_hapus_hasil" onclick="pengaturan('hapus_hasil')"<?php echo $pub_add; ?>><?php echo $localize['btn_unpublish']; ?></button>
																			<button class="btn btn-success" id="btn_update_hasil" onclick="pengaturan('update_hasil')"><?php echo $pub; ?></button>
																			<label class="margin-left-10" id="label_hasil">
																				<?php
																					$result = getResultPoll();
																					if($result){
																						echo sprintf($localize['text_publication'], timeAgo($result[0]['date']));
																					} else {
																						echo $localize['text_no_publication'];
																					}
																				?>
																			</label>
																		</div>
																	</div>
																	<div class="clearfix"></div>
																</div>
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
<?php include("./components/bottom_script.php"); ?>
<script>

</script>
</html>
