function showNotif(text,icon,type){
	if(icon === undefined) icon = 'notifications';
	if(type === undefined) type = 'primary';
	$.notify({
        icon: icon,
        message: text
    }, {
        type: type,
        timer: 2000,
		delay: 2000,
        placement: {
            from: 'top',
            align: 'right'
        }
    });
}

var currentWelcomeText = 1;
function welcomeText(){
	var welcomes = localize['welcomes'];
	$('#welcome-text').delay(2000).animate({'opacity': 0}, 1000, function () {
    $(this).text(welcomes[currentWelcomeText]);
		currentWelcomeText = currentWelcomeText + 1;
		if(currentWelcomeText >= welcomes.length)
			currentWelcomeText = 0;
		welcomeText();
	}).animate({'opacity': 1}, 1000);
}

function instalasi(step){
  switch(step){
    case "welcome":
	    $("#welcome").fadeIn(500);
	    $("#license").slideUp();
    break;
		case "back_welcome":
			$("#welcome").slideDown();
			$("#license").slideUp();
		break;
    case "lisensi":
      $("#welcome").slideUp();
      $("#license").slideDown();
      $("#mysql").slideUp();
    break;
    case "mysql":
      $("#license").slideUp();
      $("#mysql").slideDown();
      setTimeout(function(){ $("#con_pass").focus(); }, 500);
    break;
    case "mysql_cek":
      $("#mysql_btn_cek").prop("disabled", true).text(localize['btn_connecting']);
      var sending = $.post("./ajax/mysql.php",
      {
        act: "cek",
        host: $("#con_host").val(),
        user: $("#con_user").val(),
        pass: $("#con_pass").val()
      });
      sending.fail(function(){
        showNotif(localize['alert_fatal_error'],'error','danger');
      });
      sending.done(function(data){
        var output = JSON.parse(data);
        switch(output['code']){
          case "404":
            showNotif("<b>"+localize['text_error_occured']+"</b><br>"+output['error'],'error','danger');
          break;
          case "200":
            instalasi("mysql_db");
          break;
        }
        $("#mysql_btn_cek").prop("disabled", false).text(localize['btn_connect']);
      });
    break;
    case "mysql_db":
      $("#mysql_1").slideUp();
      $("#mysql_2").slideDown();
      $("#con_db").focus();
    break;
    case "mysql_db_cek":
      $("#mysql_btn_db").prop("disabled", true).text(localize['btn_checking_db']);
      var sending = $.post("./ajax/mysql.php",
      {
        act: "db_cek",
        host: $("#con_host").val(),
        user: $("#con_user").val(),
        pass: $("#con_pass").val(),
        db: $("#con_db").val()
      });
      sending.fail(function(){
        showNotif(localize['alert_fatal_error'],'error','danger');
      });
      sending.done(function(data){
        var output = JSON.parse(data);
        switch(output['code']){
          case "exist":
            var cfr = confirm($("#con_db").val()+" "+localize['alert_db_exist']);
            if(!cfr){
              $("#mysql_btn_db").prop("disabled", false).text(localize['btn_begin_install']);
              return false;
            }
            instalasi("process");
            dbExist = true;
          break;
          case "ok":
            instalasi("process");
          break;
        }
        $("#mysql_btn_db").prop("disabled", false).text(localize['btn_begin_install']);
      });
    break;
		case "mysql_back":
			$("#mysql_1").slideDown();
			$("#mysql_2").slideUp();
		break;
    case "process":
      $("#mysql").slideUp();
      $("#process").slideDown();
      installstatus();
    break;
		case "zonawaktu":
			$("#process").slideUp();
			$("#information").slideUp();
			$("#zonawaktu").slideDown();
		break;
		case "webinfo":
			$("#information").slideDown();
			$("#zonawaktu").slideUp();
			setTimeout(function(){ $("#sett_judul").focus(); }, 500);
		break;
		case "skip_webinfo":
			var cfr = confirm(localize['alert_skipping']);
			if(cfr){
				instalasi("finish");
			}
		break;
		case "save_webinfo":
		$("#info_back, #sett_judul, #sett_subjudul, #sett_instansi, #info_skip, #info_save").prop("disabled", true);
		$("#info_save").text(localize['btn_saving']);
		var sending = $.post("./ajax/mysql.php",
		{
			act: "save_webinfo",
			timezone: $("#sett_timezone").val(),
			judul: $("#sett_judul").val(),
			subjudul: $("#sett_subjudul").val(),
			instansi: $("#sett_instansi").val()
		});
		sending.fail(function(){
			showNotif(localize['alert_fatal_error'],'error','danger');
		});
		sending.done(function(data){
			console.log(data);
			var output = JSON.parse(data);
			switch(output['code']){
				case "200":
					$("#info_save").text("Saved!");
					instalasi("finish");
				break;
				case "403":
					showNotif("<b>"+localize['text_error_occured']+"</b><br>"+output['error'],'error','danger');
					$("#info_back, #sett_judul, #sett_subjudul, #sett_instansi, #info_skip, #info_save").prop("disabled", false);
					$("#info_save").text(localize['btn_save']);
				break;
			}
		});
		break;
    case "finish":
      $("#information").slideUp();
      $("#success").slideDown();
    break;
  }
}

var dbExist = false;
var errorCount = 0;
function installstatus(){
  if($(".install-status .nav-item.done").length == 0){
    var curProg = $(".install-status .nav-item:nth-child(1)");
  } else {
    if($(".install-status .nav-item.done").length == 5){
      curProg.removeClass("process").addClass("done");
      return true;
    } else {
      var curProg = $(".install-status .nav-item.done:last").next();
    }
  }
  curProg.addClass("process");
  setTimeout(function(){
    switch(curProg.attr("id")){
      case "prog_db":
        if(dbExist == false){
          var sending = $.post("./ajax/mysql.php",
          {
            act: "db_create",
            host: $("#con_host").val(),
            user: $("#con_user").val(),
            pass: $("#con_pass").val(),
            db: $("#con_db").val()
          });
          sending.fail(function(){
            showNotif(localize['alert_fatal_error'],'error','danger');
          });
          sending.done(function(data){
            var output = JSON.parse(data);
            switch(output['code']){
              case "200":
                curProg.removeClass("process").addClass("done");
              break;
              case "404":
								errorCount += 1;
                console.log(localize['text_error_occured']+" "+output['error']);
								curProg.find(".material-icons").removeClass("text-success").addClass("text-danger").text("close");
								curProg.removeClass("process").addClass("done");
              break;
            }
						installstatus();
          });
        } else {
          curProg.removeClass("process").addClass("done");
          installstatus();
        }
      break;
      case "prog_conf":
        var sending = $.post("./ajax/mysql.php",
        {
          act: "conf_create",
          host: $("#con_host").val(),
          user: $("#con_user").val(),
          pass: $("#con_pass").val(),
          db: $("#con_db").val()
        });
        sending.fail(function(){
          showNotif(localize['alert_fatal_error'],'error','danger');
        });
        sending.done(function(data){
          var output = JSON.parse(data);
          switch(output['code']){
            case "200":
              curProg.removeClass("process").addClass("done");
            break;
            case "404":
							errorCount += 1;
							console.log(localize['text_error_occured']+" "+output['error']);
							curProg.find(".material-icons").removeClass("text-success").addClass("text-danger").text("close");
							curProg.removeClass("process").addClass("done");
            break;
          }
					installstatus();
        });
      break;
      case "prog_tbl":
        var sending = $.post("./ajax/mysql.php",
        {
          act: "tb_create"
        });
        sending.fail(function(){
          showNotif(localize['alert_fatal_error'],'error','danger');
        });
        sending.done(function(data){
          var output = JSON.parse(data);
          switch(output['code']){
            case "200":
              curProg.removeClass("process").addClass("done");
            break;
            case "404":
							errorCount += 1;
							console.log(localize['text_error_occured']+" "+output['error']);
							curProg.find(".material-icons").removeClass("text-success").addClass("text-danger").text("close");
							curProg.removeClass("process").addClass("done");
            break;
          }
					installstatus();
        });
      break;
      case "prog_dump":
        var sending = $.post("./ajax/mysql.php",
        {
          act: "tb_dump"
        });
        sending.fail(function(){
          showNotif(localize['alert_fatal_error'],'error','danger');
        });
        sending.done(function(data){
          var output = JSON.parse(data);
          switch(output['code']){
            case "200":
              curProg.removeClass("process").addClass("done");
            break;
            case "404":
							errorCount += 1;
							console.log(localize['text_error_occured']+" "+output['error']);
							curProg.find(".material-icons").removeClass("text-success").addClass("text-danger").text("close");
							curProg.removeClass("process").addClass("done");
            break;
          }
					installstatus();
        });
      break;
      case "prog_alter":
        var sending = $.post("./ajax/mysql.php",
        {
          act: "tb_alter"
        });
        sending.fail(function(){
          showNotif(localize['alert_fatal_error'],'error','danger');
        });
        sending.done(function(data){
          var output = JSON.parse(data);
          switch(output['code']){
            case "200":
              curProg.removeClass("process").addClass("done");
            break;
            case "404":
							errorCount += 1;
							console.log(localize['text_error_occured']+" "+output['error']);
							curProg.find(".material-icons").removeClass("text-success").addClass("text-danger").text("close");
							curProg.removeClass("process").addClass("done");
            break;
          }
					if(errorCount > 0){
						$("#prog_text").text(localize['text_error_install']).addClass("text-danger");
						$("#process_title").text(localize['title_install_error']);
						$("#process_loader").slideUp();
					} else {
						$("#process_title").text(localize['title_install_success']);
						$("#process_loader").slideUp();
						$("#prog_text").slideUp();
						setTimeout(function(){
							instalasi("zonawaktu");
						}, 1000);
					}
        });
      break;
    }
  }, 1000);

}
function changeLanguage(lang){
	var curLang = $.cookie("el_lang");
	if(lang == curLang) return false;
	$("html").append("<div id='box-overlay'></div>");
	$("body").addClass("blur");
	$("#box-overlay").fadeIn(1000);
	$.cookie("el_lang", lang, {path: '/'});
	setTimeout(function(){
		window.location.reload();
	}, 1000);
}
$("#sett_lang").on("change", function(){
	changeLanguage($(this).val());
});
