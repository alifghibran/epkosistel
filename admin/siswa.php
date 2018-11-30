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
	<title><?php echo $localize['title_siswa']; ?></title>
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
                        <a class="navbar-brand" href="#"><?php echo $localize['title_siswa']; ?> </a>
                    </div>

                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card card-nav-tabs">
								<?php
									$limit = 8;
									$page = intval($_GET['hal']);
									if($page == 0) $page = 1;
									$offset = ($page - 1) * $limit;
									$search = netralize_words($_GET['kunci']);
									$search_n = $_GET['kunci'];
									if(strlen($search) > 0){
										$query = "WHERE no_induk = '$search' OR nama LIKE '%$search%' OR kelas = '$search'";
										$title = "$localize[text_search_for] ".htmlspecialchars($search_n);
									} else {
										$query = "";
										$title = $localize['text_show_all_student'];
									}
									$pencari = mysqli_query($connection,"SELECT COUNT(*) FROM tb_siswa $query");
									$siswa_num = mysqli_fetch_array($pencari)[0];
									$siswa_tot = getTotal($connection,"siswa");
									if($siswa_num < $siswa_tot)
										$cats = sprintf($localize['text_search_found_1'], $siswa_num, $siswa_tot); else
										$cats = sprintf($localize['text_search_found_2'], $siswa_num);
									$total_page = ceil($siswa_num / $limit);
									$siswa = mysqli_query($connection,"SELECT * FROM tb_siswa $query ORDER BY no_induk ASC LIMIT $limit OFFSET $offset");
								?>
                                <div class="card-header" data-background-color="red">
																	<?php if(requireLevel(1)){ ?>
																		<div class="dropdown float-right">
																			<button class="btn btn-danger btn-just-icon float-right" data-toggle="dropdown"><i class="material-icons">more_vert</i></button>
												        			<ul class="dropdown-menu">
																			  <li><a onclick="siswa('tambah')"><?php echo $localize['text_add_student']; ?></a></li>
																				<?php if(checkplugins("excel-importer")){
																					$addons = getplugindir("excel-importer")."/addons.php";
																					if(file_exists($addons)){
																						$submit_to = "siswa";
																						include($addons);
																					} else {
																						echo "<!-- Excel Importer plugin is broken! -->";
																					}
																				 } ?>
																			</ul>
												        		</div>
																	<?php } ?>
																		<h4 class="title"><?php echo $title; ?></h4>
									<p class="category"><?php echo $cats; ?></p>
                                </div>
                                <div class="card-content table-responsive">
                                    <table class="table table-hover">
                                        <thead class="text-danger">
											<th><?php echo $localize['tbl_num']; ?></th>
											<th><?php echo $localize['tbl_id']; ?></th>
                                            <th><?php echo $localize['tbl_name']; ?></th>
                                            <th><?php echo $localize['tbl_grade']; ?></th>
											<th><?php echo $localize['tbl_status']; ?></th>
                                        </thead>
                                        <tbody id="list_siswa">
											<?php if($siswa_num){
												$init = $offset + 1;
												while($siswa_row = mysqli_fetch_array($siswa)){
											?>
                                            <tr class="pointer" <?php if(requireLevel(1)){ ?> onclick="siswa('edit','<?php echo $siswa_row['no_induk'];?>')" <?php } ?>>
                                                <td><?php echo $init; ?></td>
												<td><?php echo $siswa_row['no_induk']; ?></td>
												<td><?php echo $siswa_row['nama']; ?></td>
												<td><?php echo $siswa_row['kelas']; ?></td>
												<td>
													<?php
														if(cekPilih($connection, $siswa_row['no_induk']))
															echo "<i class=\"material-icons cursor-help text-success\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".sprintf($localize['text_voted_at'], getPilihTime($connection,$siswa_row['no_induk']))."\">done</i>"; else
															echo "<i class=\"material-icons text-muted\" title=\"$localize[text_not_voted]\">panorama_fish_eye</i>";
													?>
												</td>
                                            </tr>
											<?php
													$init++;
												}
											} else {
												echo "<tr><td colspan=\"5\" align=\"center\">$localize[text_no_data]</td></tr>";
											} ?>
                                        </tbody>
                                    </table>
									<div class="center">
										<ul class="pagination pagination-danger">
											<?php
											if($siswa_num){
												$max = 5;
												if($page < ($max - 1))
													$startfrom = 1; else
													$startfrom = $page - 2;
												if($total_page <= $max)
													$endfrom = $total_page; else
													$endfrom = ($startfrom - 1) + $max;
												if($startfrom > ($total_page - $max)) $startfrom = $total_page - $max + 1;
												if($endfrom > $total_page) $endfrom = $total_page;
												if(strlen($search) > 0)
													$link = "./siswa.php?kunci=$search&"; else
													$link = "./siswa.php?";
												if($page > 1) echo "<li><a href=\"".$link."hal=".($page - 1)."\">&lt;</a></li>";
												if($startfrom > 1) echo "<li><a href=\"".$link."hal=1&_\">1</a></li>";
												if($startfrom > 2) echo "<li><a>...</a></li>";
												for($t = $startfrom;$t <= $endfrom;$t++){
													if($t <= 0) continue;
													$active = "";
													if($t == $page)
														$active = "class=\"active\"";
													echo "<li $active><a href=\"./".$link."hal=$t&_\">$t</a></li>";
												}
												if($startfrom < ($total_page - $max)) echo "<li><a>...</a></li>";
												if($startfrom < ($total_page - $max + 1)) echo "<li><a href=\"".$link."hal=$total_page&_\">$total_page</a></li>";
												if($page < $total_page) echo "<li><a href=\"".$link."hal=".($page + 1)."\">&gt;</a></li>";
											}
											?>
										</ul>
									</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-content">
																	<div id="card_cari">
                                    <div class="form-group label-floating">
										<label class="control-label"><?php echo $localize['text_search_student']; ?></label>
										<input class="form-control" id="siswa_cari" type="text" value="<?php echo $search_n; ?>">
										<span class="material-input"></span>
									</div>
									<div class="right">
										<button class="btn btn-primary" onclick="" id="btn_cari"><?php echo $localize['btn_search']; ?></button>
										<script>
											$('#btn_cari').click(function(){
												var key = $('#siswa_cari').val();
												if(key.length < 3){
													$('#siswa_cari').focus();
													return false;
												}
												top.location = './siswa.php?kunci='+key;
											})
											$('#siswa_cari').keypress(function(e){
												var key = e.which;
												if(key == 13){
													e.preventDefault();
													$('#btn_cari').click();
												}
											})
											<?php
												if((strlen($search_n) > 0) && $page == 1)
													echo "$('#siswa_cari').focus()";
											?>
										</script>
									</div>
								</div>

								<?php if(requireLevel(1)){ ?>
								<div id="card_forms" style="display: none;">
									<input type="hidden" id="siswa_id" value="">
									<div class="form-group label-floating">
										<label class="control-label"><?php echo $localize['tbl_id_student']; ?></label>
										<input class="form-control" id="siswa_noinduk" type="text" value="">
										<span class="material-input"></span>
									</div>
									<div class="form-group label-floating">
										<label class="control-label"><?php echo $localize['tbl_name']; ?></label>
										<input class="form-control" id="siswa_nama" type="text" value="">
										<span class="material-input"></span>
									</div>
									<div class="form-group label-floating">
										<label class="control-label"><?php echo $localize['tbl_grade']; ?></label>
										<input class="form-control" id="siswa_kelas" type="text" value="">
										<span class="material-input"></span>
									</div>
									<div class="right">
										<div class="float-left hidden" id="siswa_opt">
											<div class="dropdown">
												<button id="btn_option" class="btn btn-simple dropdown-toggle" data-toggle="dropdown">
														<?php echo $localize['btn_options']; ?>
														<b class="caret"></b>
												</button>
												<ul class="dropdown-menu">
													<li><a class="pointer" onclick="siswa('batal')"><?php echo $localize['btn_cancel']; ?></a></li>
													<li><a class="pointer" onclick="siswa('hapus')"><span class="text-danger"><?php echo $localize['btn_delete']; ?></span></a></li>
												</ul>
											</div>
										</div>
										<input type="hidden" id="method" value="">
										<button class="btn btn-default btn-simple" id="btn_batal" onclick="siswa('batal')"><?php echo $localize['btn_cancel']; ?></button>
										<button class="btn btn-primary" id="btn_post" onclick="siswa('post')"><?php echo $localize['btn_add']; ?></button>
									</div>
								</div>
							<?php } ?>
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
$('[data-toggle="tooltip"]').tooltip();
</script>
</html>
