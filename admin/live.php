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
	<title><?php echo $localize['title_live']; ?></title>
    <?php include("./components/header.php"); ?>
</head>

<body>
    <div class="wrapper">
        <div class="main-panel width-full">
            <nav class="navbar navbar-transparent navbar-absolute">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="./dashboard.php?_livechart"><i class="material-icons">chevron_left</i> <?php echo $localize['title_dashboard']; ?> </a>
                    </div>
										<div class="collapse navbar-collapse">
											<ul class="nav navbar-nav navbar-right">
												<li><a id="pembaruan" data-tgl="" data-status=""><?php echo $localize['text_updating']; ?></a></li>
											</ul>
										</div>
                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
											<div class="col-lg-6 col-centered" id="kandidat"></div>
										</div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include("./components/bottom_script.php"); ?>
<link href="./css/animate.css" rel="stylesheet" />
<script src="../js/moment.min.js"></script>
<script src="./js/jquery.animatecss.min.js"></script>
<script src="./js/tripath-json.js"></script>
<script>
	moment.locale("<?php echo getCurrentLocale(); ?>");
	function cekAgo(){
		var status = $('#pembaruan').attr('data-status');
		switch(status){
			case "":
				$('#pembaruan').text("<?php echo $localize['text_updating']; ?>");
			break;
			case "0":
				$('#pembaruan').text("<?php echo $localize['text_no_update']; ?>");
			break;
			case "1":
				var timestamp = $('#pembaruan').attr('data-tgl');
		    var timeAgo = moment(timestamp,"YYYY-MM-DD HH:mm:ss").fromNow();
				$('#pembaruan').text("<?php echo $localize['text_updated']; ?> "+timeAgo);
			break;
		}
	}
	setInterval(cekAgo,1000);
	cekAgo();
	cekPembaruanChart();
</script>
</html>
