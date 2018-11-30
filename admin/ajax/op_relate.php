<?php
  include("../inc/conn.php");
  include("../inc/functions.php");
  $localize = getLang("ajax");
  include("../inc/check_ajax.php");
  if(!isLoggedIn()){
    echo json_encode(array('code' => '403','error' => $localize['alert_not_loggedin']));
    exit();
  }
  if($_SESSION['os_level'] != "3"){
    echo json_encode(array('code' => '403','error' => $localize['alert_inspector_previlege']));
    mysqli_close($connection);
    exit();
  }
  $no_induk = netralize_noinduk($_POST['no_induk']);
  $get = mysqli_query($connection,"SELECT * FROM tb_panitia WHERE no_induk = '$no_induk'");
  if(mysqli_num_rows($get) == 1){
    $get_row = mysqli_fetch_array($get);
    if($_SESSION['os_relate'] != $_SESSION['os_id']){
      $txt = sprintf($localize['error_related'], getPanitia($connection,$_SESSION['os_relate'],"nama"));
      $output = array('code' => '403','error' => $txt);
    } else if($get_row['level'] < 3){
      $_SESSION['os_relate'] = $get_row['id'];
      $msg = sprintf($localize['alert_relate_success'], getPanitia($connection,$get_row['id'],"nama"));
      $output = array('code' => '200', 'msg' => $msg);
    } else {
      $output = array('code' => '403','error' => $localize['error_relate_prev']);
    }
  } else {
    $output = array('code' => '403','error' => $localize['error_relate_404']);
  }
  echo json_encode($output);
  mysqli_close($connection);
?>
