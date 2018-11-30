		<div class="sidebar" data-color="green" data-image="./img/sidebar-1.jpg">
            <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

        Tip 2: you can also add an image using data-image tag
    -->
            <div class="logo">
                <a href="../?_ref=logo" class="simple-text">
                    <span class="text-success"><?php echo getWebProp($connection, "judul"); ?></span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li>
                        <a href="./dashboard.php?_">
                            <i class="material-icons">dashboard</i>
                            <p><?php echo $localize['menu_dashboard']; ?></p>
                        </a>
                    </li>
                    <li>
                        <a href="./siswa.php?_">
                            <i class="material-icons">wc</i>
                            <p><?php echo $localize['menu_siswa']; ?></p>
                        </a>
                    </li>
                    <li>
                        <a href="./guru.php?_">
                            <i class="material-icons">business_center</i>
                            <p><?php echo $localize['menu_guru']; ?></p>
                        </a>
                    </li>
					<li>
                        <a href="./kandidat.php?_">
                            <i class="material-icons">supervisor_account</i>
                            <p><?php echo $localize['menu_kandidat']; ?></p>
                        </a>
                    </li>
										<?php if(requireLevel(1)) { ?>
                    <li>
                        <a href="./panitia.php?_">
                            <i class="material-icons">person</i>
                            <p><?php echo $localize['menu_panitia']; ?></p>
                        </a>
                    </li>
                    <li>
                        <a href="./pengaturan.php?_">
                            <i class="material-icons">settings</i>
                            <p><?php echo $localize['menu_pengaturan']; ?></p>
                        </a>
                    </li>
										<?php } ?>
										<li class="separator"></li>
										<li>
											<a data-toggle="modal" data-target="#gantiPass" href="#!/profil.php?gantipass_"><?php echo getName(); ?><small><?php echo getLevel(); ?></small></a>
                    </li>
                    <li>
						<a href="./logout.php"><?php echo $localize['menu_logout']; ?></a>
                     </li>
                </ul>
				<script>
					var curPage = "<?php echo strtolower(basename($_SERVER["SCRIPT_FILENAME"], '.php')); ?>";
					var child = false;
					switch(curPage){
						case "dashboard":
							child = 1;
						break;
						case "siswa":
							child = 2;
						break;
						case "guru":
							child = 3;
						break;
						case "kandidat":
							child = 4;
						break;
						case "panitia":
							child = 5;
						break;
						case "pengaturan":
							child = 6;
						break;
						case "profil":
							child = 7;
						break;
					}
					if(child)
						$(".sidebar-wrapper .nav li:nth-child("+child+")").addClass("active");
				</script>
            </div>
		</div>
