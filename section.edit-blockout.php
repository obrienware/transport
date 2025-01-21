<?php
require_once 'autoload.php';

use Transport\Blockout;

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$blockout = new Blockout($id);
$blockoutId = $blockout->getId();
?>
<?php if (isset($_GET['id']) && !$blockoutId): ?>

  <div class="container-fluid text-center">
    <div class="alert alert-danger mt-5 w-50 mx-auto">
      <h1 class="fw-bold">Oh dear!</h1>
      <p class="lead">I can't seem to find that blockout period! <i class="fa-duotone fa-solid fa-face-thinking"></i></p>
    </div>
  </div>

<?php else: ?>

  <div class="container mt-2">
    <?php if ($blockoutId): ?>
      <h2>Edit Blockout Dates</h2>
    <?php else: ?>
      <h2>Add Blockout Dates</h2>
    <?php endif; ?>
    <div>
      <div class="row">
        <div class="col">

          <div class="mb-3">
            <label for="blockout-from-datetime" class="form-label">Starting</label>
            <div
              class="input-group log-event"
              id="datetimepicker3"
              data-td-target-input="nearest"
              data-td-target-toggle="nearest">
              <input
                id="blockout-from-datetime"
                type="text"
                class="form-control"
                data-td-target="#datetimepicker3"
                value="<?=($blockout->fromDateTime) ? Date('m/d/Y h:i A', strtotime($blockout->fromDateTime)) : '' ?>"/>
              <span
                class="input-group-text"
                data-td-target="#datetimepicker3"
                data-td-toggle="datetimepicker">
                <i class="fa-duotone fa-calendar"></i>
              </span>
            </div>
          </div>

        </div>

        <div class="col">

          <div class="mb-3">
            <label for="blockout-to-datetime" class="form-label">Ending</label>
            <div
              class="input-group log-event"
              id="datetimepicker2"
              data-td-target-input="nearest"
              data-td-target-toggle="nearest">
              <input
                id="blockout-to-datetime"
                type="text"
                class="form-control"
                data-td-target="#datetimepicker3"
                value="<?=($blockout->toDateTime) ? Date('m/d/Y h:i A', strtotime($blockout->toDateTime)) : '' ?>"/>
              <span
                class="input-group-text"
                data-td-target="#datetimepicker2"
                data-td-toggle="datetimepicker">
                <i class="fa-duotone fa-calendar"></i>
              </span>
            </div>
          </div>

        </div>

        <div class="col-6">
          <div class="mb-3">
            <label for="blockout-note" class="form-label">Note</label>
            <input type="text" class="form-control" id="blockout-note" placeholder="Note" value="<?=$blockout->note?>">
          </div>
        </div>
      </div>


      <div class="row my-4">
        <div class="col d-flex justify-content-between">
          <?php if ($blockoutId): ?>
            <button class="btn btn-outline-danger px-4" id="btn-delete-blockout">Delete</button>
          <?php endif; ?>
          <button class="btn btn-primary px-4" id="btn-save-blockout">Save</button>
        </div>
      </div>

    </div>
  </div>

  <script type="text/javascript">

    $(async ƒ => {

      const blockoutId = <?=$blockoutId ?: 'null'?>;
      const startDate = new tempusDominus.TempusDominus(document.getElementById('datetimepicker2'), tempusConfigDefaults);
      const endDate = new tempusDominus.TempusDominus(document.getElementById('datetimepicker3'), tempusConfigDefaults);

      $('#btn-save-blockout').off('click').on('click', async ƒ => {
        const resp = await post('/api/post.save-blockout.php', {
          id: blockoutId,
          userId: `<?=$_SESSION['user']->id?>`,
          fromDateTime: val('#blockout-from-datetime') ? moment(val('#blockout-from-datetime'), 'MM/DD/YYYY h:mm A').format('YYYY-MM-DD HH:mm:ss') : null,
          toDateTime: val('#blockout-to-datetime') ? moment(val('#blockout-to-datetime'), 'MM/DD/YYYY h:mm A').format('YYYY-MM-DD HH:mm:ss') : null,
          note: cleanVal('#blockout-note')
        });
        if (resp?.result) {
          $(document).trigger('blockoutChange', {blockoutId});
          app.closeOpenTab();
          if (blockoutId) return toastr.success('Blockout dates saved.', 'Success');
          return toastr.success('Blockout dates added.', 'Success')
        }
        toastr.error(resp .result.errors[2], 'Error');
        console.log(resp);
      });

      $('#btn-delete-blockout').off('click').on('click', async ƒ => {
        if (await ask('Are you sure you want to delete these blockout dates?')) {
          const resp = await get('/api/get.delete-blockout.php', {
            id: blockoutId
          });
          if (resp?.result) {
            $(document).trigger('blockoutChange', {blockoutId});
            app.closeOpenTab();
            return toastr.success('Blockout dates deleted.', 'Success')
          }
          console.log(resp);
          toastr.error('There seems to be a problem deleting blockout dates.', 'Error');
        }
      });

    });

  </script>

<?php endif; ?>