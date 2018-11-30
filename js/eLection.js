function showNotif(text,type,icon){
	if(icon === undefined) icon = 'notifications';
	if(type === undefined) type = 'primary';
	$.notify({
        icon: '',
        message: text
    }, {
        type: type,
        timer: 2000,
		    delay: 2000,
        placement: {
            from: 'top',
            align: 'right'
        },
        animate: {
          enter: 'animated fadeInRight',
          exit: 'animated fadeOutRight'
        }
    });
}

var readyVote = true;
function mulaiVote(){
  if(readyVote == false){
    showNotif(localize['alert_access_code_validating'],"warning","warning");
    return false;
  }
  var no_induk = $('#no_induk').val();
  if(no_induk.length < 5){
    $('#no_induk').focus();
    return false;
  }
  readyVote = false;
	$('#no_induk, #btn_pilih').prop("disabled", true);
  var sending = $.post("./admin/ajax/pemilihan.php",
  {
    step: "start",
    kode_akses: no_induk
  });
  sending.fail(function(){
    showNotif(localize['alert_system_error'],'danger','error');
    readyVote = true;
		$('#no_induk, #btn_pilih').prop("disabled", false);
  });
  sending.done(function(data){
    var output = JSON.parse(data);
    switch(output['code']){
      case "403":
        showNotif(output['error'],'danger');
				$('#no_induk, #btn_pilih').prop("disabled", false);
        $('#no_induk').focus();
      break;
      case "200":
        showNotif(localize['alert_access_code_success'],'success');
        setTimeout(function(){
          top.location = './pemilihan.php?_loggedin';
        },1000);
      break;
    }
    readyVote = true;
  });
}
