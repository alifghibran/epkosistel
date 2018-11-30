<div class="modal fade" id="gantiPass" tabindex="-1" role="dialog" aria-labelledby="labelGantiPass" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="labelGantiPass"><?php echo $localize['text_change_password']; ?></h4>
      </div>
      <div class="modal-body">
        <div class="form-group label-floating form-success">
          <label class="control-label"><?php echo $localize['text_current_password']; ?></label>
          <input class="form-control" id="pass_old" type="password">
          <span class="material-input"></span>
        </div>
        <div class="form-group label-floating form-success">
          <label class="control-label"><?php echo $localize['text_new_password']; ?></label>
          <input class="form-control" id="pass_new_1" type="password">
          <span class="material-input"></span>
        </div>
        <div class="form-group label-floating form-success">
          <label class="control-label"><?php echo $localize['text_new_password_retype']; ?></label>
          <input class="form-control" id="pass_new_2" type="password">
          <span class="material-input"></span>
        </div>
      </div>
      <script>
        $('#pass_old, #pass_new_1, #pass_new_2').keypress(function(e){
          var key = e.which;
          if(key == 13) cekPass();
        });
      </script>
      <div class="modal-footer">
        <div class="form-group">
          <div class="togglebutton green float-left">
            <label>
              <input type="checkbox" id="logout_after">
              <?php echo $localize['text_change_password_logout']; ?>
            </label>
          </div>
          <button type="button" class="btn btn-default btn-simple" data-dismiss="modal"><?php echo $localize['btn_cancel']; ?></button>
          <button type="button" class="btn btn-success btn-simple" id="btn_sandi" onclick="gantiPass()"><?php echo $localize['btn_change_pass']; ?></button>
        </div>
      </div>
    </div>
  </div>
  </div>
  <script>
    $('#gantiPass').on('shown.bs.modal',function(e){
      $('#pass_old').focus();
      $('#bodyClick').click();
    });
    $('#gantiPass').on('hide.bs.modal',function(e){
      $('#btn_sandi').prop('disabled',false);
      $('#btn_sandi').html('Change');
      $('#pass_old').val('');
      $('#pass_new_1').val('');
      $('#pass_new_2').val('');
      $('#pass_old, #pass_new_1, #pass_new_2').keyup();
    });
  </script>
