<?php
  if(!file_exists("./admin/inc/conn.php")){
    header("Location: ./install/?_rdr");
    exit();
  }
  include("./admin/inc/conn.php");
  include("./admin/inc/functions.php");
  $default_language = getWebProp($connection, "default_language");
  $localize = getLang("main", $default_language);

  if(isRegistered()){
    echo "$localize[alert_redirecting]<script>setTimeout(function(){top.location='./pemilihan.php?_loggedin';},1000)</script>";
    exit();
  }
  $info = mysqli_query($connection, "SELECT * FROM tb_pengaturan WHERE id = 1");
  $info_row = mysqli_fetch_array($info);
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $info_row['instansi'].": ".$info_row['subjudul']; ?></title>
    <meta name="description" content="Selamat datang di eLection, pemilihan kandidat berbasis online yang dikembangkan oleh Tripath Projects">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <link rel="shortcut icon" href="./favicon.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/shards.min.css">
    <link rel="stylesheet" href="css/shards.tripath.css?v=1.1.0">
</head>

<body>
<div class="loader"><div class="page-loader"></div></div>

<!-- Navbar Section -->
<nav id="navbar" class="navbar navbar-expand-lg navbar-light bg-transparent mb-4">
    <img src="./img/eLection-logo.png" alt="eLection Logo" class="mr-2" height="40px">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="#" id="toTop"><?php echo $localize['menu_home']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="toKandidat"><?php echo $localize['menu_candidate']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="toPilih"><?php echo $localize['menu_vote_now']; ?></a>
            </li>
        </ul>
    </div>
</nav>

<!-- Welcome Section -->
<div class="welcome d-flex justify-content-center flex-column">
    <div class="inner-wrapper mt-auto mb-auto">
        <p class="slide-in"><?php echo $info_row['subjudul']; ?></p>
        <h1 class="slide-in"><?php echo $info_row['judul']; ?></h1>
    </div>
    <div class="product-by slide-in">
        <a href="<?php echo $_SERVER['PHP_SELF']."?_ref=btminstance"; ?>" target="_blank">
            <p class="instansi"><?php echo $info_row['instansi']; ?></p>
            <!--<img src="./img/tripath-logo.png" alt="Tripath Projects">-->
        </a>
    </div>
</div>

<!-- Page Content -->
<div class="page-content">

        <!-- Kandidat -->
        <?php
          $result_out = getResultPoll();
          $kdt_total = count($result_out[0]["kandidat"]);
          $hasil_keluar = true;
          if($result_out == false){
            $hasil_keluar = false;
            $kdt = mysqli_query($connection,"SELECT * FROM tb_kandidat ORDER BY nama ASC");
            $kdt_total = mysqli_num_rows($kdt);
            if($kdt_total){
              $result_out = array();
              $init = 1;
              while($row_kandidat = mysqli_fetch_array($kdt)){
                $hasil_s["kandidat"]["kandidat_$init"]['nama'] = $row_kandidat['nama'];
                $hasil_s["kandidat"]["kandidat_$init"]['kelas'] = $row_kandidat['kelas'];
                $hasil_s["kandidat"]["kandidat_$init"]['fbid'] = $row_kandidat['fbid'];
                $hasil_s["kandidat"]["kandidat_$init"]['bio'] = $row_kandidat['bio'];
                $init++;
              }
              $hasil_s['total_suara'] = intval(getTotal($connection,"suara"));
              $hasil_s['total_pemilih'] = getTotal($connection,"siswa") + getTotal($connection,"guru");
              $hasil_s['date'] = false;
              array_push($result_out,$hasil_s);
            }
          }

          $tot_suara = $result_out[0]['total_suara'];
          $tot_peserta = $result_out[0]['total_pemilih'];
          $diperbarui = $result_out[0]['date'];
          if($diperbarui) $diperbarui = sprintf($localize['text_updated_at'], timeAgo($diperbarui)); else $diperbarui = $localize['text_not_published'];
          ?>
        <div id="kandidat" class="container mb-5 mt-5">
            <div class="text-center section-title col-lg-8 col-md-10 ml-auto mr-auto">
                <h2 class="mb-4"><?php echo $localize['title_candidate']; ?></h2>
                <p><?php echo sprintf($localize['title_candidate_sub'], $tot_suara, $tot_peserta, $diperbarui); ?></p>
            </div>

            <div class="col-md-12 ml-auto mr-auto my-4">
                <div class="row text-center">
                  <?php
                    if($kdt_total){
                      $election = true;
                      for($init=1;$init<=$kdt_total;$init++){
                  ?>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4 ml-auto mr-auto">
                        <div class="card">
                            <img class="card-img-top" src="//graph.facebook.com/<?php echo $result_out[0]['kandidat']['kandidat_'.$init]['fbid']; ?>/picture?type=large&width=320&height=320" alt="Foto profil kandidat">
                            <div class="card-body">
                                <?php
                                  $variasi = array('','info','danger','warning','success');
                                  if($hasil_keluar){
                                    $jum_suara = round(($result_out[0]['kandidat']['kandidat_'.$init]['suara'] / $result_out[0]['total_suara']) * 100, 2);

                                ?>
                                <div class="progress">
                                  <div class="progress-bar bg-<?php echo $variasi[rand(1,4)]; ?>" role="progressbar" style="width: <?php echo $jum_suara; ?>%" aria-valuenow="<?php echo $jum_suara; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $jum_suara; ?>%</div>
                                </div>
                                <?php } ?>
                                <h4 class="card-title"><?php echo $result_out[0]['kandidat']['kandidat_'.$init]['nama']; ?></h4>
                                <h6 class="card-title"><?php echo $result_out[0]['kandidat']['kandidat_'.$init]['kelas']; ?></h6>
                                <p class="card-text"><?php echo $result_out[0]['kandidat']['kandidat_'.$init]['bio']; ?></p>

                            </div>
                        </div>
                    </div>
                  <?php
                  } } else {
                    echo "<div class=\"col-lg-12\">$localize[text_candidate_empty]</div>\n";
                    $election = false;
                  } ?>
                </div>
            </div>
        </div>
        <?php if($election){ ?>
        <div id="pilihsekarang" class="my-5">
          <div class="py-5 container">
            <div class="text-center section-title ml-auto mr-auto">
              <h2 class="mb-4"><?php echo $localize['title_vote_now']; ?></h2>
              <p><?php echo $localize['title_vote_now_sub']; ?></p>
            </div>
            <div class="mt-20 col-md-4 ml-auto mr-auto">
              <div class="input-group">
                <input type="text" id="no_induk" autocomplete="off" class="form-control" placeholder="<?php echo $localize['label_access_code']; ?>">
                <button id="btn_pilih" class="input-group-addon" onclick="mulaiVote()"><i class="fa fa-arrow-right"></i></button>
              </div>
            </div>
          </div>
        </div>
      <?php } else {
        //echo "<div class=\"my-5\"></div>";
      } ?>
    <footer class="main-footer py-5">
        <p class="text-muted text-center small p-0 mb-4">&copy; Copyright 2018 — <strong>tripath</strong>projects</p>
        <div class="social d-table ml-auto mr-auto">
            <a class="twitter mx-3 h4" href="//twitter.com/fauzantrif" target="_blank">
                <i class="fa fa-twitter"></i>
            </a>
            <a class="facebook mx-3 h4" href="//facebook.com/fauzantrif" target="_blank">
                <i class="fa fa-facebook"></i>
            </a>
            <a class="github mx-3 h4" href="//instagram.com/fauzantrif" target="_blank">
                <i class="fa fa-instagram"></i>
            </a>
            <a class="github mx-3 h4" href="//fauzantrif.wordpress.com" target="_blank">
                <i class="fa fa-globe"></i>
            </a>
        </div>
    </footer>
</div>

<!-- JavaScript -->
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/jquery.cookie.js"></script>
<script src="js/tripath.localization.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<!--  Notifications Plugin    -->
<script src="./js/bootstrap-notify.js"></script>

<script src="./js/eLection.js"></script>
<script>
  $(".loader").addClass("hidden").delay(200).remove();
  $(".slide-in").each(function(){
    $(this).addClass("visible");
  });
  $(window).scroll(function (event) {
    var tgtScroll = 50;
    var curScroll = $(window).scrollTop();
    if(curScroll > tgtScroll){
      $('#navbar').removeClass('bg-transparent').addClass('bg-light');
    } else {
      $('#navbar').removeClass('bg-light').addClass('bg-transparent');
    }
  });
  $(window).scroll();

  $('#navbarNavDropdown a.nav-link:not(.dropdown-toggle)').click(function(e){
    e.preventDefault();
    var scrollTo = 0;
    switch(this.id){
      case "toTop":
        scrollTo = 0;
      break;
      case "toPilih":
        scrollTo = $("#pilihsekarang").offset().top - 100;
        setTimeout(function(){
          $('#no_induk').focus();
        },1000);
      break;
      case "toKandidat":
        scrollTo = $("#kandidat").offset().top - 100;
      break;
    }
    $('html, body').animate({
          scrollTop: scrollTo
      }, 1000);
  });
  $('#no_induk').keypress(function(e){
    var key = e.which;
    if(key == 13){
      e.preventDefault();
      mulaiVote();
    }
  });
</script>
</body>
</html>
<?php mysqli_close($connection); ?>
