<?php
  if(!file_exists("./admin/inc/conn.php")){
    header("Location: ./install/?_rdr");
    exit();
  }
  include("./admin/inc/conn.php");
  include("./admin/inc/functions.php");
  $default_language = getWebProp($connection, "default_language");
  $localize = getLang("main", $default_language);
  if(isRegistered() == false){
    echo "$localize[alert_acces_denied]<script>setTimeout(function(){top.location='./?_nosession';},1000)</script>";
    exit();
  }
  $timeNow = strtotime(currentTimestamp());
  $timeExp = strtotime($_SESSION['os_pemilih_kadaluarsa']);
  if($timeNow > $timeExp){
    unset($_SESSION['os_pemilih'],$_SESSION['os_pemilih_kadaluarsa'],$_SESSION['os_pemilih_panitia']);
    echo "$localize[alert_session_expired]<script>setTimeout(function(){top.location='./?_nosession';},1000)</script>";
    exit();
  }
  $countdown = "<span id=\"waktu\" data-kadaluarsa=\"$_SESSION[os_pemilih_kadaluarsa]\">$localize[text_loading]</span>";
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $localize['title_voting']; ?></title>
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


<!-- Page Content -->
<div class="page-content">

        <!-- Kandidat -->
        <?php
          $kdt = mysqli_query($connection,"SELECT * FROM tb_kandidat ORDER BY nama ASC");
          $kdt_total = mysqli_num_rows($kdt);
          ?>
        <div id="kandidat" class="container mb-5 mt-2">
            <div class="text-center section-title col-lg-8 col-md-10 ml-auto mr-auto mb-4">
                <h2 class="mb-2 slide-in"><?php echo $localize['title_voting']; ?></h2>
                <p class="slide-in"><?php echo sprintf($localize['text_countdown'], $countdown); ?></p>
            </div>

            <div class="example col-md-12 ml-auto mr-auto">
                <div class="row text-center">
                  <?php
                    if($kdt_total){
                      while($row_kdt = mysqli_fetch_array($kdt)){
                  ?>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4 ml-auto mr-auto slide-in">
                        <div class="card">
                            <img class="card-img-top" src="//graph.facebook.com/<?php echo $row_kdt['fbid']; ?>/picture?type=large&width=320&height=320">
                            <div class="card-body">
                                <h4 class="card-title" id="kdt_nama_<?php echo $row_kdt['id']; ?>"><?php echo $row_kdt['nama']; ?></h4>
                                <h6 class="card-title"><?php echo $row_kdt['kelas']; ?></h6>
                                <p class="card-text"><?php echo $row_kdt['bio']; ?></p>
                                <button class="btn btn-outline-success btn-pill submitter" value="<?php echo $row_kdt['id']; ?>"><?php echo $localize['btn_vote']; ?></button>
                            </div>
                        </div>
                    </div>
                  <?php
                  } } else {
                    echo "<div class=\"col-lg-12\">$localize[text_candidate_empty]</div>";
                  } ?>
                </div>
            </div>
        </div>
    <footer class="main-footer py-5">
        <p class="text-muted text-center small p-0 mb-4">&copy; Copyright 2018 — <strong>tripath</strong>projects</p>
    </footer>
</div>

<!-- JavaScript -->
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="./js/moment.min.js"></script>
<!--  Notifications Plugin    -->
<script src="./js/bootstrap-notify.js"></script>
<script src="./js/eLection.js"></script>
<script>
  function timeout(){
    var timestamp = $('#waktu').attr('data-kadaluarsa');
    var diff_sec = moment(timestamp,"YYYY-MM-DD HH:mm:ss").diff(moment(), "seconds");
    var diff_min = moment(timestamp,"YYYY-MM-DD HH:mm:ss").diff(moment(), "minutes");
    var secs = "<?php echo $localize['text_time_seconds']; ?>";
    var sec = "<?php echo $localize['text_time_second']; ?>";
    var mins = "<?php echo $localize['text_time_minutes']; ?>";
    var min = "<?php echo $localize['text_time_minute']; ?>";
    var sep = "<?php echo $localize['text_time_separator']; ?>";
    var txt;
    if(diff_sec <= 0){
      clearInterval(gogo);
      alert("<?php echo $localize['alert_session_timeout']; ?>");
      top.location = './pemilihan.php?_expired';
      return false;
    }
    if(diff_sec > 0){
      txt = diff_sec + " " + secs;
      if(diff_sec == 1) txt = diff_sec + " " + sec;
    }
    if(diff_min > 0){
      var sec_in_min = diff_sec - (diff_min * 60);
      if(sec_in_min){
        if(diff_min == 1)
          txt = diff_min + " " + min; else
          txt = diff_min + " " + mins;
        if(sec_in_min == 1)
          txt = txt + sep + " " + sec_in_min + " " + sec; else
          txt = txt + sep + " " + sec_in_min + " " + secs;
      } else {
        if(diff_min == 1)
          txt = diff_min + " " + min; else
          txt = diff_min + " " + mins;
      }
    }
    $('#waktu').text(txt);
  }
  var gogo = setInterval(timeout,1000);
  timeout();

  $('.submitter').click(function(){
    var id_kdt = $(this).attr('value');
    var nama = $('#kdt_nama_'+id_kdt).text();
    var cfr = confirm("<?php echo sprintf($localize['text_confirm_vote_this'], "\"+nama+\""); ?>");
    if(!cfr) return false;
    var sending = $.post("./admin/ajax/pemilihan.php",
    {
      step: "end",
      id_kandidat: id_kdt
    });
    sending.fail(function(){
      showNotif("<?php echo $localize['alert_system_error']; ?>",'danger','error');
    });
    sending.done(function(data){
      var output = JSON.parse(data);
      switch(output['code']){
        case "403":
          showNotif(output['error'],'danger');
        break;
        case "200":
          showNotif("<?php echo $localize['alert_vote_success']; ?>",'success');
          setTimeout(function(){
            top.location = './?_success';
          },1000);
        break;
      }
      readyVote = true;
    });
  });

  $(".loader").addClass("hidden").delay(200).remove();
  $(".slide-in").each(function(){
    $(this).addClass("visible");
  });
</script>
</body>
</html>
<?php mysqli_close($connection); ?>
