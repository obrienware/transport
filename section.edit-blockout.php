<?php
require_once 'autoload.php';

use Transport\Blockout;
use Transport\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$blockout = new Blockout($id);
$blockoutId = $blockout->getId();

if (!is_null($id) && !$blockoutId) {
  exit(Utils::showResourceNotFound());
}
?>

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
          <input type="datetime-local" class="form-control" id="blockout-from-datetime" value="<?=$blockout->fromDateTime?>" min="<?=date('Y-m-d\TH:i')?>">
        </div>

      </div>

      <div class="col">

        <div class="mb-3">
          <label for="blockout-to-datetime" class="form-label">Ending</label>
          <input type="datetime-local" class="form-control" id="blockout-to-datetime" value="<?=$blockout->toDateTime?>" min="<?=date('Y-m-d\TH:i')?>">
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

<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  $(async ƒ => {

    const blockoutId = <?=$blockoutId ?: 'null'?>;

    $('#btn-save-blockout').off('click').on('click', async ƒ => {
      if (!val('#blockout-from-datetime') || !val('#blockout-to-datetime')) return ui.toastr.error('Please select both start and end dates.', 'Error');
      const resp = await net.post('/api/post.save-blockout.php', {
        id: blockoutId,
        userId: `<?=$_SESSION['user']->id?>`,
        fromDateTime: input.val('#blockout-from-datetime') ? moment(input.val('#blockout-from-datetime')).format('YYYY-MM-DD HH:mm:ss') : null,
        toDateTime: input.val('#blockout-to-datetime') ? moment(input.val('#blockout-to-datetime')).format('YYYY-MM-DD HH:mm:ss') : null,
        note: input.cleanVal('#blockout-note')
      });
      if (resp?.result) {
        $(document).trigger('blockoutChange', {blockoutId});
        app.closeOpenTab();
        if (blockoutId) return ui.toastr.success('Blockout dates saved.', 'Success');
        return ui.toastr.success('Blockout dates added.', 'Success')
      }
      ui.toastr.error(resp .result.errors[2], 'Error');
      console.log(resp);
    });

    $('#btn-delete-blockout').off('click').on('click', async ƒ => {
      if (await ui.ask('Are you sure you want to delete these blockout dates?')) {
        const resp = await net.get('/api/get.delete-blockout.php', {
          id: blockoutId
        });
        if (resp?.result) {
          $(document).trigger('blockoutChange', {blockoutId});
          app.closeOpenTab();
          return ui.toastr.success('Blockout dates deleted.', 'Success')
        }
        console.log(resp);
        ui.toastr.error('There seems to be a problem deleting blockout dates.', 'Error');
      }
    });

  });

</script>
