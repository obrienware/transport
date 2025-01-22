<?php 
require_once 'autoload.php';

use Transport\User;
?>
  <div class="container mt-2">
    <h2>Add Blockout Dates</h2>
    <div>

      <div class="row">
        <div class="col-3">
          
          <div class="mb-3">
            <label for="blockout-from-datetime" class="form-label">Starting</label>
            <input type="datetime-local" class="form-control" id="blockout-from-datetime" value="" min="<?=date('Y-m-d\TH:i')?>">
          </div>

        </div>

        <div class="col-3">

          <div class="mb-3">
            <label for="blockout-to-datetime" class="form-label">Ending</label>
            <input type="datetime-local" class="form-control" id="blockout-to-datetime" value="" min="<?=date('Y-m-d\TH:i')?>">
          </div>

      </div>

      <div class="row">
        <div class="col-3">
          <div class="mb-3">
            <label for="blockout-user" class="form-label">Driver</label>
            <select id="blockout-user" class="form-control">
              <option>
                <?php if ($rows = User::getDrivers()): ?>
                  <?php foreach ($rows as $driver): ?>
                    <option value="<?=$driver->id?>"><?=$driver->first_name?> <?=$driver->last_name?></option>
                  <?php endforeach;?>
                <?php endif; ?>
              </option>
            </select>
          </div>
        </div>

        <div class="col">
          <div class="mb-3">
            <label for="blockout-note" class="form-label">Note</label>
            <input type="text" class="form-control" id="blockout-note" placeholder="Note" value="<?=$blockout->note?>">
          </div>
        </div>
      </div>


      <div class="row my-4">
        <div class="col d-flex justify-content-between">
          <button class="btn btn-primary px-4" id="btn-save-blockout">Save</button>
        </div>
      </div>

    </div>
  </div>

  <script type="text/javascript">

    $(async ƒ => {

      $('#blockout-user').selectpicker();

      $('#btn-save-blockout').off('click').on('click', async ƒ => {
        if ($('#blockout-user').val() == '')return toastr.error('You need to select a driver first', 'Attention');
        const resp = await post('/api/post.save-blockout.php', {
          userId: $('#blockout-user').val(),
          fromDateTime: val('#blockout-from-datetime') ? moment(val('#blockout-from-datetime')).format('YYYY-MM-DD HH:mm:ss') : null,
          toDateTime: val('#blockout-to-datetime') ? moment(val('#blockout-to-datetime')).format('YYYY-MM-DD HH:mm:ss') : null,
          note: cleanVal('#blockout-note')
        });
        if (resp?.result) {
          $(document).trigger('blockoutChange');
          app.closeOpenTab();
          return toastr.success('Blockout dates added.', 'Success')
        }
        toastr.error(resp .result.errors[2], 'Error');
        console.log(resp);
      });

    });

  </script>
