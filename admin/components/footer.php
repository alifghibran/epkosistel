				<div class="container-fluid">
                    <nav class="pull-left">
                        <ul>
                            <li>
                                <a class="text-success" href="../?_panitia">
                                    <?php echo $localize['menu_homepage']; ?>
                                </a>
                            </li>
                            <li>
                                <a class="text-success" href="./live.php?_panitia">
                                    <?php echo $localize['menu_live_chart']; ?>
                                </a>
                            </li>
														<li class="dropdown">
                                <a class="text-success pointer" data-toggle="dropdown">
																	<?php echo getCurrentLang(); ?>
                                </a>
																<ul class="dropdown-menu up">
																	<?php echo getListLang("menu", $_COOKIE['el_lang']); ?>
																</ul>
                            </li>
                        </ul>
                    </nav>
                    <p class="copyright pull-right">
                        &copy; 2018
                        <a class="text-success" href="//fauzantrif.wordpress.com/" target="_blank">Tripath Projects</a>, eLection <?php echo getVersion($connection); ?>
                    </p>
                </div>
