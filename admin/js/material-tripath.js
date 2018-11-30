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
function fetchHakPilih(){
	var sending = $.get("./ajax/hakpilih.php");
	sending.fail(function(){
		showNotif(localize['error_fetch_in_vote'],'error','danger');
	});
	sending.done(function(data){
		var output = JSON.parse(data);
		var setto;
		switch(output['code']){
			case "empty":
				setto = '<tr><td colspan="4" align="center">'+localize['text_empty']+'</td></tr>';
			break;
			case "fill":
				setto = output['text'];
			break;
		}
		$('#update_hakpilih').html(localize['text_updated']+' '+output['date']);
		$('#label_voting, #label_vote').html(localize['text_today']+' '+output['date']);
		$('#num_voting').html(output['voting']);
		$('#num_vote').html(output['suara']);
		$('#list_hakpilih').html(setto);
	});
}
function hakPilih(step,noin){
	switch(step){
		case "reset":
			$('#btn_cari').prop('disabled',false);
			$('#btn_cari').html('Cari');
			$('#ni_pemilih').val('');
			$('#ni_pemilih').focus();
		break;
		case "abort":
			var cfr = confirm(localize['text_confirm_abort_vote']+" "+noin+"?");
			if(cfr){
				var batal = $.post("./ajax/registrasi.php",
				{
					step: "batal",
					noinduk: noin
				});
				batal.fail(function(){
					showNotif(localize['alert_system_error'],'error','danger');
				});
				batal.done(function(data){
					var output = JSON.parse(data);
					switch(output['code']){
						case "404":
							showNotif(localize['alert_vote_access_404'],'error','danger');
						break;
						case "403":
							showNotif(localize['alert_vote_access_no_previlege'],'error','danger');
						break;
						case "500":
							showNotif(localize['alert_server_error']+"<br>"+output['desc'],'error','danger');
						break;
						case "200":
							showNotif(localize['alert_vote_access_aborted']+' '+output['no_induk'],'done','success');
							fetchHakPilih();
						break;
					}
				});
			}
		break;
		case 0:
			$('#inputnis').slideDown();
			$('#datapemilih').slideUp();
			hakPilih('reset');
		break;
		case 1:
			if($('#ni_pemilih').val().length < 4){
				$('#ni_pemilih').focus();
				return false;
			}
			$('#btn_cari').prop('disabled',true);
			$('#btn_cari').html(localize['btn_searching']);
			var sending = $.post("./ajax/registrasi.php",
			{
				step: "1",
				noinduk: $('#ni_pemilih').val()
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
				hakPilih('reset');
			});
			sending.done(function(data){
				console.log(data);
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
						hakPilih('reset');
					break;
					case "200":
						$('#pilih_nama').html(output['nama']);
						$('#pilih_noinduk').html(output['no_induk']+': <span id="tkn_akses">'+output['kode_akses']+'</span>');
						$('#inputnis').slideUp();
						$('#datapemilih').slideDown();
						$('#btn_daftar').focus();
					break;
				}
			});
		break;
		case 2:
			var sending = $.post("./ajax/registrasi.php",
			{
				step: "2",
				noinduk: $('#ni_pemilih').val(),
				kode_akses: $('#tkn_akses').text()
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(localize['alert_system_error']+"<br>"+output['desc'],'error','danger');
					break;
					case "200":
						showNotif(localize['text_register_success'],'done','success');
						if($("#cetakstruk").length){
							cetakStruk($('#tkn_akses').text(), $('#ni_pemilih').val());
						}
						hakPilih(0);
						fetchHakPilih();
					break;
				}
			});
		break;
		case "relate":
			var induk = prompt(localize['text_relate_prompt']);
			if(induk.length > 3){
				var sending = $.post("./ajax/op_relate.php",
				{
					no_induk: induk
				});
				sending.fail(function(){
					showNotif(localize['alert_system_error'],'error','danger');
				});
				sending.done(function(data){
					var output = JSON.parse(data);
					switch(output['code']){
						case "403":
							showNotif(output['error'],'error','danger');
						break;
						case "200":
							showNotif(output['msg'],'done','success');
							fetchHakPilih();
						break;
					}
				});
			}
		break;
	}
}
function kandidat(aksi,id){
	switch(aksi){
		case "batal_edit":
			$("#kd_edit_"+id).slideUp();
			$("#kd_info_"+id).slideDown();
		break;
		case "buka_edit":
			var sending = $.post("./ajax/op_kandidat.php",
			{
				aksi: "fetch",
				id: id
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "404":
						showNotif(localize['alert_candidate_404'],'error','danger');
					break;
					case "200":
						$("#ekand_nama_"+id).val(output['nama']).keyup();
						$("#ekand_kelas_"+id).val(output['kelas']).keyup();
						$("#ekand_fbid_"+id).val(output['fbid']).keyup();
						$("#ekand_bio_"+id).val(output['bio']).keyup();
						$("#kd_edit_"+id).slideDown();
						$("#kd_info_"+id).slideUp();
					break;
				}
			});
		break;
		case "tambah":
			var kand_nama = $('#kand_nama').val();
			var kand_kelas = $('#kand_kelas').val();
			var kand_fbid = $('#kand_fbid').val();
			var kand_bio = $('#kand_bio').val();
			if(kand_nama.length == 0){
				$('#kand_nama').focus();
				return false;
			}
			if(kand_kelas.length == 0){
				$('#kand_kelas').focus();
				return false;
			}
			if(kand_fbid.length == 0){
				$('#kand_fbid').focus();
				return false;
			}
			if(kand_bio.length == 0){
				$('#kand_bio').focus();
				return false;
			}
			var sending = $.post("./ajax/op_kandidat.php",
			{
				aksi: "tambah",
				nama: kand_nama,
				kelas: kand_kelas,
				fbid: kand_fbid,
				bio: kand_bio
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(localize['error_previlege_header']+"<br>"+output['error'],'error','danger');
					break;
					case "301":
						showNotif(localize['error_name'],'error','danger');
						$('#kand_nama').focus();
					break;
					case "302":
						showNotif(localize['error_candidate_grade'],'error','danger');
						$('#kand_kelas').focus();
					break;
					case "303":
						showNotif(localize['error_bio'],'error','danger');
						$('#kand_bio').focus();
					break;
					case "304":
						showNotif(localize['error_fbid'],'error','danger');
						$('#kand_fbid').focus();
					break;
					case "200":
						showNotif(localize['text_candidate_added'],'done','success');
						setTimeout(function(){
							top.location = './kandidat.php?_added';
						}, 1000);
					break;
				}
			});
		break;
		case "edit":
			var kand_nama = $('#ekand_nama_'+id).val();
			var kand_kelas = $('#ekand_kelas_'+id).val();
			var kand_fbid = $('#ekand_fbid_'+id).val();
			var kand_bio = $('#ekand_bio_'+id).val();
			if(kand_nama.length == 0){
				$('#ekand_nama_'+id).focus();
				return false;
			}
			if(kand_kelas.length == 0){
				$('#ekand_kelas_'+id).focus();
				return false;
			}
			if(kand_fbid.length == 0){
				$('#ekand_fbid_'+id).focus();
				return false;
			}
			if(kand_bio.length == 0){
				$('#ekand_bio_'+id).focus();
				return false;
			}
			var sending = $.post("./ajax/op_kandidat.php",
			{
				aksi: "edit",
				id: id,
				nama: kand_nama,
				kelas: kand_kelas,
				fbid: kand_fbid,
				bio: kand_bio
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "404":
						showNotif(localize['alert_candidate_404'],'error','danger');
					break;
					case "403":
						showNotif(localize['error_previlege_header']+"<br>"+output['error'],'error','danger');
					break;
					case "301":
						showNotif(localize['error_name'],'error','danger');
						$('#ekand_nama_'+id).focus();
					break;
					case "302":
						showNotif(localize['error_candidate_grade'],'error','danger');
						$('#ekand_kelas_'+id).focus();
					break;
					case "303":
						showNotif(localize['error_bio'],'error','danger');
						$('#ekand_bio_'+id).focus();
					break;
					case "304":
						showNotif(localize['error_fbid'],'error','danger');
						$('#ekand_fbid_'+id).focus();
					break;
					case "200":
						showNotif(localize['text_candidate_updated'],'done','success');
						$("#inf_nama_"+id).html(output['nama']);
						$("#inf_kelas_"+id).html(output['kelas']);
						$("#inf_photo_"+id).attr("src",output['photo']);
						$("#inf_bio_"+id).html(output['bio']);
						kandidat('batal_edit',output['id']);
					break;
				}
			});
		break;
		case "hapus":
			var kand_nama = $('#inf_nama_'+id).html();
			var cfr = confirm(localize['text_confirm_delete_candidate']+" "+kand_nama+"?");
			if(!cfr) return false;
			var sending = $.post("./ajax/op_kandidat.php",
			{
				aksi: "hapus",
				id: id
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "404":
						showNotif(localize['alert_candidate_404'],'error','danger');
					break;
					case "403":
						showNotif(localize['error_previlege_header']+"<br>"+output['error'],'error','danger');
					break;
					case "200":
						showNotif(localize['text_candidate_deleted'],'done','success');
						$('#card_'+id).hide('medium');
						setTimeout(function(){
							$('#card_'+id).remove();
						},1000);
					break;
				}
			});
		break;
	}
}
function pengaturan(cmd){
	switch(cmd){
		case "umum":
			$('#btn_umum, #btn_umum_2').prop('disabled',true);
			$('#btn_umum, #btn_umum_2').html(localize['btn_saving']);
			var enabled = 0;
			var disabled_text = $('#sett_message').val();
			var judul = $('#sett_judul').val();
			var subjudul = $('#sett_subjudul').val();
			var instansi = $('#sett_instansi').val();
			var timezone = $('#sett_timezone').val();
			var language = $('#sett_lang_default').val();
			if($('#sett_enable').is(':checked'))
				enabled = 1;
			var sending = $.post("./ajax/op_pengaturan.php",
			{
				aksi: "umum",
				enabled: enabled,
				disabled_text: disabled_text,
				judul: judul,
				subjudul: subjudul,
				instansi: instansi,
				timezone: timezone,
				default_lang: language
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
				$('#btn_umum, #btn_umum_2').prop('disabled',false);
				$('#btn_umum, #btn_umum_2').html(localize['btn_save']);
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
					break;
					case "200":
						showNotif(localize['alert_settings_saved'],'done','success');
					break;
				}
				$('#btn_umum, #btn_umum_2').prop('disabled',false);
				$('#btn_umum, #btn_umum_2').html(localize['btn_save']);
			});
		break;
		case "hapus_poll":
			var cfr = confirm(localize['text_confirm_empty_poll']);
			if(!cfr) return false;
			$('#btn_hapus_suara').prop('disabled',true);
			$('#btn_hapus_suara').html(localize['btn_emptying']);
			var sending = $.post("./ajax/op_pengaturan.php",
			{
				aksi: "hapus_poll"
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
				$('#btn_hapus_suara').prop('disabled',false);
				$('#btn_hapus_suara').html(localize['btn_empty_vote']);
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(localize['error_previlege_header']+"<br>"+output['error'],'error','danger');
					break;
					case "200":
						showNotif(localize['alert_vote_result_reset'],'done','success');
						$('#label_suara').html(localize['alert_vote_result_reset']);
					break;
				}
				$('#btn_hapus_suara').prop('disabled',false);
				$('#btn_hapus_suara').html(localize['btn_empty_vote']);
			});
		break;
		case "hapus_hasil":
			var cfr = confirm(localize['text_confirm_vote_result_unpublish']);
			if(!cfr) return false;
			$('#btn_hapus_hasil, #btn_update_hasil').prop('disabled',true);
			$('#btn_hapus_hasil').html(localize['btn_unpublishing']);
			var sending = $.post("./ajax/op_pengaturan.php",
			{
				aksi: "hapus_hasil"
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
				$('#btn_hapus_hasil, #btn_update_hasil').prop('disabled',false);
				$('#btn_hapus_hasil').html(localize['btn_unpublish']);
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(localize['error_previlege_header']+"<br>"+output['error'],'error','danger');
					break;
					case "200":
						showNotif(localize['alert_vote_result_unpublished'],'done','success');
						$('#label_hasil').html(localize['alert_vote_result_unpublished']);
						$('#btn_update_hasil').html(localize['btn_publish']);
						$('#btn_hapus_hasil').html(localize['btn_unpublish']).hide();
					break;
				}
				$('#btn_hapus_hasil, #btn_update_hasil').prop('disabled',false);
			});
		break;
		case "update_hasil":
			$('#btn_update_hasil, #btn_hapus_hasil').prop('disabled',true);
			var btn_label = $('#btn_update_hasil').html();
			$('#btn_update_hasil').html(localize['btn_processing']);
			var sending = $.post("./ajax/op_pengaturan.php",
			{
				aksi: "update_hasil"
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
				$('#btn_update_hasil, #btn_hapus_hasil').prop('disabled',false);
				$('#btn_update_hasil').html(btn_label);
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(localize['error_previlege_header']+"<br>"+output['error'],'error','danger');
					break;
					case "200":
						showNotif(localize['alert_vote_result_published'],'done','success');
						$('#label_hasil').html(localize['alert_vote_result_published_time']);
						$('#btn_hapus_hasil').show();
						$('#btn_update_hasil').html(localize['btn_update']);
					break;
				}
				$('#btn_update_hasil, #btn_hapus_hasil').prop('disabled',false);
			});
		break;
	}
}
function cekPass(){
	var old_pass = $('#pass_old').val();
	var new_pass_1 = $('#pass_new_1').val();
	var new_pass_2 = $('#pass_new_2').val();
	if(old_pass.length < 4){
		if(old_pass.length > 0) showNotif(localize['error_password_current'],"warning","warning");
		$('#pass_old').focus();
		return false;
	}
	if(new_pass_1.length < 4){
		if(new_pass_1.length > 0) showNotif(localize['error_password_new'],"warning","warning");
		$('#pass_new_1').focus();
		return false;
	}
	if(new_pass_1 != new_pass_2){
		if(new_pass_2.length > 0) showNotif(localize['error_password_nomatch'],"warning","warning");
		$('#pass_new_2').focus();
		return false;
	}
	$('#btn_sandi').focus();
	return true;
}
function gantiPass(){
	var old_pass = $('#pass_old').val();
	var new_pass_1 = $('#pass_new_1').val();
	var new_pass_2 = $('#pass_new_2').val();
	if(!cekPass()) return false;
	$('#btn_sandi').prop('disabled',true);
	$('#btn_sandi').html(localize['btn_saving']);
	var sending = $.post("./ajax/op_akun.php",
	{
		sandi_lama: old_pass,
		sandi_baru: new_pass_2
	});
	sending.fail(function(){
		showNotif(localize['alert_system_error'],'error','danger');
		$('#btn_sandi').prop('disabled',false);
		$('#btn_sandi').html(localize['btn_change_pass']);
	});
	sending.done(function(data){
		var output = JSON.parse(data);
		switch(output['code']){
			case "403":
				showNotif(output['error'],'error','danger');
				$('#btn_sandi').prop('disabled',false);
				$('#btn_sandi').html(localize['btn_change_pass']);
			break;
			case "200":
				showNotif(localize['alert_password_changed'],'done','success');
				if($('#logout_after').is(':checked')){
					$('#pass_old').val('');
		      $('#pass_new_1').val('');
		      $('#pass_new_2').val('');
					top.location = './logout.php?gantisandi_';
					return true;
				}
				$('#gantiPass').modal('hide');
			break;
		}
	});
}
function panitia(aksi,id){
	switch(aksi){
		case "show":
			$('#pnt_cari').slideUp();
			$('#pnt_forms').slideDown();
			$('#panitia_noinduk').focus();
		break;
		case "hide":
			$('#pnt_cari').slideDown();
			$('#pnt_forms').slideUp();
		break;
		case "disable_button":
			$('#btn_post, #btn_batal, #btn_option, #panitia_noinduk, #panitia_nama, #panitia_level, #panitia_sandi_1, #panitia_sandi_2').prop('disabled',true);
		break;
		case "enable_button":
			$('#btn_post, #btn_batal, #btn_option, #panitia_noinduk, #panitia_nama, #panitia_level, #panitia_sandi_1, #panitia_sandi_2').prop('disabled',false);
		break;
		case "reset":
			$('#panitia_noinduk, #panitia_nama, #panitia_sandi_1, #panitia_sandi_2').val("").keyup();
			$('#panitia_id').val("");
			$('#panitia_level option').each(function(){
				$(this).prop('selected',false);
			})
			$('#pass_change').prop('checked',true).change();
			$('#pass_change').prop('disabled',true);
			$('#pass_change_tgl, #panitia_opt').addClass('hidden');
			$('#btn_batal').removeClass('hidden');
			$('#btn_post').html(localize['btn_add']);
			$('#method').val("");
		break;
		case "batal":
			panitia('hide');
			setTimeout(function(){
				panitia('reset');
			},400);
		break;
		case "tambah":
			panitia('reset');
			$('#method').val("tambah");
			panitia('show');
		break;
		case "hapus":
			var cfr = confirm(localize['text_confirm_admins_delete']);
			if(cfr){
				$('#method').val("hapus");
				$('#btn_post').text(localize['btn_delete']).click();
			}
		break;
		case "edit":
			if(id === undefined){
				showNotif(localize['alert_system_error'],"error","danger");
				return false;
			}
			var sending = $.post("./ajax/op_panitia.php",
			{
				id: id,
				aksi: 'fetch'
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
					break;
					case "200":
						panitia('reset');
						$('#panitia_id').val(output['id']);
						$('#panitia_noinduk').val(output['no_induk']).keyup();
						$('#panitia_nama').val(output['nama']).keyup();
						$('#panitia_level_'+output['level']).prop('selected',true);
						$('#pass_change').prop('checked',false).change();
						$('#pass_change').prop('disabled',false);
						$('#pass_change_tgl, #panitia_opt').removeClass('hidden');
						$('#btn_batal').addClass('hidden');
						$('#btn_post').html(localize['btn_save']);
						$('#method').val("ubah");
						panitia('show');
					break;
				}
			});
		break;
		case "post":
			var method = $('#method').val().toLowerCase();
			var pt_id = $('#panitia_id').val();
			var pt_noinduk = $('#panitia_noinduk').val();
			var pt_nama = $('#panitia_nama').val();
			var pt_level = $('#panitia_level').val();
			var pt_change_pass = $('#pass_change').is(':checked');
			var pt_sandi_1 = $('#panitia_sandi_1').val();
			var pt_sandi_2 = $('#panitia_sandi_2').val();
			if(pt_noinduk.length == 0){
				$('#panitia_noinduk').focus();
				return false;
			}
			if(pt_nama.length == 0){
				$('#panitia_nama').focus();
				return false;
			}
			if(pt_level == "0"){
				$('#panitia_level').focus();
				return false;
			}
			if(pt_change_pass){
				if(pt_sandi_1.length == 0){
					$('#panitia_sandi_1').focus();
					return false;
				}
				if(pt_sandi_1 != pt_sandi_2){
					$('#panitia_sandi_2').focus();
					showNotif(localize['error_password_nomatch'],"error","danger");
					return false;
				}
			}
			if(method == "ubah"){
				if(pt_id.length == 0){
					showNotif(localize['alert_system_error'],"error","danger");
					return false;
				}
				$('#btn_post').text(localize['btn_saving']);
			} else {
				$('#btn_post').text(localize['btn_processing']);
			}
			if(method == "hapus"){
				if(pt_id.length == 0){
					showNotif(localize['alert_system_error'],"error","danger");
					return false;
				}
				$('#btn_post').text(localize['btn_processing']);
			}
			panitia('disable_button');
			var sending = $.post("./ajax/op_panitia.php",
			{
				id: pt_id,
				no_induk: pt_noinduk,
				nama: pt_nama,
				level: pt_level,
				chpass: pt_change_pass,
				sandi: pt_sandi_1,
				aksi: method
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
				if(method == "ubah")
					$('#btn_post').text(localize['btn_save']); else
					$('#btn_post').text(localize['btn_add']);
				if(method == "hapus") $('#btn_post').text(localize['btn_delete']);
			});
			sending.done(function(data){
				//console.log(data);
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
						if(method == "ubah")
							$('#btn_post').text(localize['btn_save']); else
							$('#btn_post').text(localize['btn_add']);
						if(method == "hapus") $('#btn_post').text(localize['btn_delete']);
						panitia('enable_button');
					break;
					case "200":
						showNotif(output['error'],'done','success');
						$('#btn_post').text(localize['btn_success']);
						panitia('reset');
						panitia('hide');
						setTimeout(function(){
							top.location = './panitia.php?_success';
						}, 1000);
					break;
				}
			});
		break;
	}
}
function siswa(aksi,id){
	switch(aksi){
		case "show":
			$('#card_cari').slideUp();
			$('#card_forms').slideDown();
			if($('#siswa_noinduk').prop('disabled'))
				$('#siswa_nama').focus(); else
				$('#siswa_noinduk').focus();
		break;
		case "hide":
			$('#card_cari').slideDown();
			$('#card_forms').slideUp();
		break;
		case "disable_button":
			$('#btn_post, #btn_batal, #btn_option, #siswa_noinduk, #siswa_nama, #siswa_kelas').prop('disabled',true);
		break;
		case "enable_button":
			$('#btn_post, #btn_batal, #btn_option, #siswa_noinduk, #siswa_nama, #siswa_kelas').prop('disabled',false);
		break;
		case "reset":
			$('#siswa_noinduk, #siswa_nama, #siswa_kelas').val("").keyup().prop('disabled',false);
			$('#siswa_id').val("");
			$('#siswa_opt').addClass('hidden');
			$('#btn_batal').removeClass('hidden');
			$('#btn_post').html(localize['btn_add']);
			$('#method').val("");
		break;
		case "batal":
			siswa('hide');
			setTimeout(function(){
				siswa('reset');
			},400);
		break;
		case "tambah":
			siswa('reset');
			$('#method').val("add");
			siswa('show');
		break;
		case "hapus":
			var cfr = confirm(localize['text_confirm_student_delete']);
			if(cfr){
				$('#method').val("delete");
				$('#btn_post').text(localize['btn_delete']).click();
			}
		break;
		case "edit":
			if(id === undefined){
				showNotif(localize['alert_system_error'],"error","danger");
				return false;
			}
			var sending = $.post("./ajax/op_siswa.php",
			{
				id: id,
				aksi: 'fetch'
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
					break;
					case "200":
						siswa('reset');
						$('#siswa_id').val(output['no_induk']);
						$('#siswa_noinduk').val(output['no_induk']).keyup().prop('disabled',true);
						$('#siswa_nama').val(output['nama']).keyup();
						$('#siswa_kelas').val(output['kelas']).keyup();
						$('#siswa_opt').removeClass('hidden');
						$('#btn_batal').addClass('hidden');
						$('#btn_post').html(localize['btn_save']);
						$('#method').val("update");
						siswa('show');
					break;
				}
			});
		break;
		case "post":
			var method = $('#method').val().toLowerCase();
			var pt_id = $('#siswa_id').val();
			var pt_noinduk = $('#siswa_noinduk').val();
			var pt_nama = $('#siswa_nama').val();
			var pt_kelas = $('#siswa_kelas').val();
			if(pt_noinduk.length == 0){
				$('#siswa_noinduk').focus();
				return false;
			}
			if(pt_nama.length == 0){
				$('#siswa_nama').focus();
				return false;
			}
			if(pt_kelas.length == 0){
				$('#siswa_kelas').focus();
				return false;
			}
			if(method == "update"){
				if(pt_id.length == 0){
					showNotif(localize['alert_system_error'],"error","danger");
					return false;
				}
				$('#btn_post').text(localize['btn_saving']);
			} else {
				$('#btn_post').text(localize['btn_processing']);
			}
			if(method == "delete"){
				if(pt_id.length == 0){
					showNotif(localize['alert_system_error'],"error","danger");
					return false;
				}
				$('#btn_post').text(localize['btn_processing']);
			}
			siswa('disable_button');
			var sending = $.post("./ajax/op_siswa.php",
			{
				id: pt_id,
				no_induk: pt_noinduk,
				nama: pt_nama,
				kelas: pt_kelas,
				aksi: method
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
				if(method == "update")
					$('#btn_post').text(localize['btn_save']); else
					$('#btn_post').text(localize['btn_add']);
				if(method == "delete")
					$('#btn_post').text(localize['btn_delete']);
				siswa('enable_button');
			});
			sending.done(function(data){
				//console.log(data);
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
						if(method == "update")
							$('#btn_post').text(localize['btn_save']); else
							$('#btn_post').text(localize['btn_add']);
						if(method == "delete")
							$('#btn_post').text(localize['btn_delete']);
						siswa('enable_button');
					break;
					case "200":
						if(method == "update")
							$('#btn_post').text(localize['btn_save']); else
							$('#btn_post').text(localize['btn_add']);
						var redir = "&kunci="+output['no_induk'];
						if(method == "delete"){
							redir = "";
							$('#btn_post').text(localize['btn_delete']);
						}
						showNotif(output['error'],'done','success');
						$('#btn_post').text(localize['btn_success']);
						siswa('reset');
						siswa('hide');
						setTimeout(function(){
							top.location = './siswa.php?_success'+redir;
						}, 1000);
					break;
				}
			});
		break;
	}
}
function guru(aksi,id){
	switch(aksi){
		case "show":
			$('#card_cari').slideUp();
			$('#card_forms').slideDown();
			if($('#guru_noinduk').prop('disabled'))
				$('#guru_nama').focus(); else
				$('#guru_noinduk').focus();
		break;
		case "hide":
			$('#card_cari').slideDown();
			$('#card_forms').slideUp();
		break;
		case "disable_button":
			$('#btn_post, #btn_batal, #btn_option, #guru_noinduk, #guru_nama, #guru_jabatan').prop('disabled',true);
		break;
		case "enable_button":
			$('#btn_post, #btn_batal, #btn_option, #guru_noinduk, #guru_nama, #guru_jabatan').prop('disabled',false);
		break;
		case "reset":
			$('#guru_noinduk, #guru_nama, #guru_jabatan').val("").keyup().prop('disabled',false);
			$('#guru_id').val("");
			$('#guru_opt').addClass('hidden');
			$('#btn_batal').removeClass('hidden');
			$('#btn_post').html(localize['btn_add']);
			$('#method').val("");
		break;
		case "batal":
			guru('hide');
			setTimeout(function(){
				guru('reset');
			},400);
		break;
		case "tambah":
			guru('reset');
			$('#method').val("add");
			guru('show');
		break;
		case "hapus":
			var cfr = confirm(localize['text_confirm_staff_delete']);
			if(cfr){
				$('#method').val("delete");
				$('#btn_post').text(localize['btn_delete']).click();
			}
		break;
		case "edit":
			if(id === undefined){
				showNotif(localize['alert_system_error'],"error","danger");
				return false;
			}
			var sending = $.post("./ajax/op_guru.php",
			{
				id: id,
				aksi: 'fetch'
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
					break;
					case "200":
						guru('reset');
						$('#guru_id').val(output['no_induk']);
						$('#guru_noinduk').val(output['no_induk']).keyup().prop('disabled',true);
						$('#guru_nama').val(output['nama']).keyup();
						$('#guru_jabatan').val(output['jabatan']).keyup();
						$('#guru_opt').removeClass('hidden');
						$('#btn_batal').addClass('hidden');
						$('#btn_post').html(localize['btn_save']);
						$('#method').val("update");
						guru('show');
					break;
				}
			});
		break;
		case "post":
			var method = $('#method').val().toLowerCase();
			var pt_id = $('#guru_id').val();
			var pt_noinduk = $('#guru_noinduk').val();
			var pt_nama = $('#guru_nama').val();
			var pt_jabatan = $('#guru_jabatan').val();
			if(pt_noinduk.length == 0){
				$('#guru_noinduk').focus();
				return false;
			}
			if(pt_nama.length == 0){
				$('#guru_nama').focus();
				return false;
			}
			if(pt_jabatan.length == 0){
				$('#guru_jabatan').focus();
				return false;
			}
			if(method == "update"){
				if(pt_id.length == 0){
					showNotif(localize['alert_system_error'],"error","danger");
					return false;
				}
				$('#btn_post').text(localize['btn_saving']);
			} else {
				$('#btn_post').text(localize['btn_processing']);
			}
			if(method == "delete"){
				if(pt_id.length == 0){
					showNotif(localize['alert_system_error'],"error","danger");
					return false;
				}
				$('#btn_post').text(localize['btn_processing']);
			}
			guru('disable_button');
			var sending = $.post("./ajax/op_guru.php",
			{
				id: pt_id,
				no_induk: pt_noinduk,
				nama: pt_nama,
				jabatan: pt_jabatan,
				aksi: method
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
				if(method == "update")
					$('#btn_post').text(localize['btn_save']); else
					$('#btn_post').text(localize['btn_add']);
				if(method == "delete")
					$('#btn_post').text(localize['btn_delete']);
				guru('enable_button');
			});
			sending.done(function(data){
				//console.log(data);
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
						if(method == "update")
							$('#btn_post').text(localize['btn_save']); else
							$('#btn_post').text(localize['btn_add']);
						if(method == "delete")
							$('#btn_post').text(localize['btn_delete']);
						guru('enable_button');
					break;
					case "200":
						if(method == "update")
							$('#btn_post').text(localize['btn_save']); else
							$('#btn_post').text(localize['btn_add']);
						var redir = "&kunci="+output['no_induk'];
						if(method == "delete"){
							redir = "";
							$('#btn_post').text(localize['btn_delete']);
						}
						showNotif(output['error'],'done','success');
						$('#btn_post').text(localize['btn_success']);
						guru('reset');
						guru('hide');
						setTimeout(function(){
							top.location = './guru.php?_success'+redir;
						}, 1000);
					break;
				}
			});
		break;
	}
}
function changeLanguage(lang){
	var curLang = $.cookie("el_lang");
	if(lang == curLang) return false;
	$("html").append("<div id='box-overlay'></div>");
	$("body").addClass("blur");
	$("#box-overlay").fadeIn(500);
	$.cookie("el_lang", lang, {path: '/'});
	setTimeout(function(){
		window.location.reload();
	}, 1000);
}
