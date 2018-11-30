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
	<title><?php echo $localize['title_panitia']; ?></title>
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
                        <a class="navbar-brand" href="#"><?php echo $localize['title_panitia']; ?> </a>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card card-nav-tabs">
															<?php
																$limit = 5;
																$page = intval($_GET['hal']);
																if($page == 0) $page = 1;
																$offset = ($page - 1) * $limit;
																$search = netralize_words($_GET['kunci']);
																$search_n = $_GET['kunci'];
																$level = getLevelFrom($search,"label");
																if($level)
																	$query_level = "OR level = '".$level."' "; else
																	$query_level = "";
																if(strlen($search) > 0){
																	$query = "WHERE no_induk = '$search' OR nama LIKE '%$search%' $query_level";
																	$title = "$localize[text_search_for] ".htmlspecialchars($search_n);
																} else {
																	$query = "";
																	$title = $localize['text_show_all_admin'];
																}
																$siswa = mysqli_query($connection,"SELECT COUNT(*) FROM tb_panitia $query");
																$siswa_num = mysqli_fetch_array($siswa)[0];
																$siswa_tot = getTotal($connection,"panitia");
																if($siswa_num < $siswa_tot)
																$cats = sprintf($localize['text_search_found_1'], $siswa_num, $siswa_tot); else
																$cats = sprintf($localize['text_search_found_2'], $siswa_num);
																$total_page = ceil($siswa_num / $limit);
																$siswa = mysqli_query($connection,"SELECT * FROM tb_panitia $query ORDER BY level ASC, nama ASC LIMIT $limit OFFSET $offset");
															?>
                                <div class="card-header" data-background-color="orange">
																		<button class="btn btn-warning btn-just-icon float-right" id="btn_add" onclick="panitia('tambah')"><i class="material-icons">add</i></button>
                                    <h4 class="title"><?php echo $title; ?></h4>
																		<p class="category"><?php echo $cats; ?></p>
                                </div>
                                <div class="card-content table-responsive">
                                    <table class="table table-hover">
                                        <thead class="text-warning">
																					<th><?php echo $localize['tbl_num']; ?></th>
																					<th><?php echo $localize['tbl_id']; ?></th>
																					<th><?php echo $localize['tbl_name']; ?></th>
																					<th><?php echo $localize['tbl_previlege']; ?></th>
                                        </thead>
                                        <tbody id="list_guru">
																					<?php if($siswa_num){
																						$init = $offset + 1;
																						while($siswa_row = mysqli_fetch_array($siswa)){
																							if($siswa_row['id'] == $_SESSION['os_id'])
																								$add_class = "text-success"; else
																								$add_class = "";
																					?>
                                            <tr class="pointer <?php echo $add_class; ?>" onclick="panitia('edit','<?php echo $siswa_row['id']; ?>')">
                                                <td><?php echo $init; ?></td>
																								<td><?php echo $siswa_row['no_induk']; ?></td>
																								<td><?php echo $siswa_row['nama']; ?></td>
																								<td><?php echo getLevelFrom($siswa_row['level'],"id"); ?></td>
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
																			<ul class="pagination pagination-warning">
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
																						$link = "./panitia.php?kunci=$search&"; else
																						$link = "./panitia.php?";
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
																	<div id="pnt_cari">
																		<div class="form-group label-floating form-success">
																			<label class="control-label"><?php echo $localize['text_search_admins']; ?></label>
																			<input class="form-control" id="panitia_cari" type="text" value="<?php echo $search_n; ?>">
																			<span class="material-input"></span>
																		</div>
																		<div class="right">
																			<button class="btn btn-success" id="btn_cari"><?php echo $localize['btn_search']; ?></button>
																			<script>
																				$('#btn_cari').click(function(){
																					var key = $('#panitia_cari').val();
																					if(key.length < 3){
																						$('#panitia_cari').focus();
																						return false;
																					}
																					top.location = './panitia.php?kunci='+key;
																				})
																				$('#panitia_cari').keypress(function(e){
																					var key = e.which;
																					if(key == 13){
																						e.preventDefault();
																						$('#btn_cari').click();
																					}
																				})
																				<?php
																					if((strlen($search_n) > 0) && $page == 1)
																						echo "$('#panitia_cari').focus();";
																				?>
																			</script>
																		</div>
																	</div>
																	<div id="pnt_forms" class="display-none">
																		<input type="hidden" id="panitia_id" value="">
																		<div class="form-group label-floating form-success">
																			<label class="control-label"><?php echo $localize['tbl_id_admin']; ?></label>
																			<input class="form-control" id="panitia_noinduk" type="text" value="">
																			<span class="material-input"></span>
																		</div>
																		<div class="form-group label-floating form-success">
																			<label class="control-label"><?php echo $localize['tbl_name']; ?></label>
																			<input class="form-control" id="panitia_nama" type="text" value="">
																			<span class="material-input"></span>
																		</div>
																		<div class="form-group form-success">
																			<select class="form-control" id="panitia_level">
																				<option value="0" id="panitia_level_0">- <?php echo $localize['txt_select_previlege']; ?> -</option>
																				<option value="1" id="panitia_level_1">Administrator</option>
																				<option value="2" id="panitia_level_2">Registrator</option>
																				<option value="3" id="panitia_level_3">Inspector</option>
																			</select>
																		</div>
																		<div class="form-group" id="pass_change_tgl">
																			<div class="togglebutton green">
																				<label>
																					<input type="checkbox" id="pass_change" checked="">
																					<?php echo $localize['text_change_password']; ?>
																				</label>
																			</div>
																			<script>
																				$('#pass_change').change(function(){
																					if($(this).is(':checked'))
																						$('#pass_change_ctn').slideDown(); else
																						$('#pass_change_ctn').slideUp();
																				});
																			</script>
																		</div>
																		<div id="pass_change_ctn">
																			<div class="form-group label-floating form-success">
																				<label class="control-label"><?php echo $localize['text_new_password']; ?></label>
																				<input class="form-control" id="panitia_sandi_1" type="password" value="">
																				<span class="material-input"></span>
																			</div>
																			<div class="form-group label-floating form-success">
																				<label class="control-label"><?php echo $localize['text_new_password_retype']; ?></label>
																				<input class="form-control" id="panitia_sandi_2" type="password" value="">
																				<span class="material-input"></span>
																			</div>
																		</div>
																		<div class="right">
																			<div class="float-left hidden" id="panitia_opt">
																				<div class="dropdown">
																					<button id="btn_option" class="btn btn-simple dropdown-toggle" data-toggle="dropdown">
																				    	<?php echo $localize['btn_options']; ?>
																				    	<b class="caret"></b>
																					</button>
																					<ul class="dropdown-menu">
																						<li><a class="pointer" onclick="panitia('batal')"><?php echo $localize['btn_cancel']; ?></a></li>
																						<li><a class="pointer" onclick="panitia('hapus')"><span class="text-danger"><?php echo $localize['btn_delete']; ?></span></a></li>
																					</ul>
																				</div>
																			</div>
																			<input type="hidden" id="method" value="">
																			<button class="btn btn-default btn-simple" id="btn_batal" onclick="panitia('batal')"><?php echo $localize['btn_cancel']; ?></button>
																			<button class="btn btn-success" id="btn_post" onclick="panitia('post')"><?php echo $localize['btn_add']; ?></button>
																		</div>
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
