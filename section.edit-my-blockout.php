<?php
require_once 'autoload.php';

use Transport\Blockout;
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$blockout = new Blockout($id);
$blockoutId = $blockout->getId();

if (!is_null($id) && !$blockoutId)
{
  exit(Utils::showResourceNotFound());
}
?>

<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('myblockout:reloadList');">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>


<div class="">
  <?php if ($blockoutId): ?>
    <h2>Edit Blockout Dates</h2>
    <input type="hidden" id="blockout-id" value="<?= $$blockoutId = $blockout->getId(); ?>">
  <?php else: ?>
    <h2>Add Blockout Dates</h2>
    <input type="hidden" id="blockout-id" value="">
  <?php endif; ?>



  <div class="row">
    <div class="col-12 col-sm-6 col-md-8 col-lg-5 col-xl-4 col-xxl-3">
      <div class="mb-3">
        <label for="blockout-from-datetime" class="form-label">Starting</label>
        <input type="datetime-local" class="form-control" id="blockout-from-datetime" value="<?= $blockout->fromDateTime ?>" min="<?= date('Y-m-d\TH:i') ?>">
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-8 col-lg-5 col-xl-4 col-xxl-3">
      <div class="mb-3">
        <label for="blockout-to-datetime" class="form-label">Ending</label>
        <input type="datetime-local" class="form-control" id="blockout-to-datetime" value="<?= $blockout->toDateTime ?>" min="<?= date('Y-m-d\TH:i') ?>">
      </div>
    </div>

    <div class="col-6">
      <div class="mb-3">
        <label for="blockout-note" class="form-label">Note</label>
        <input type="text" class="form-control" id="blockout-note" placeholder="Note" value="<?= $blockout->note ?>">
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-between mt-3">
    <?php if ($blockoutId): ?>
      <button class="btn btn-outline-danger" onclick="$(document).trigger('buttonDelete:myblockout', <?= $blockoutId ?>)">Delete</button>
    <?php endif; ?>
    <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:myblockout', '<?= $blockoutId ?>')">Save</button>
  </div>

</div>

<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'myBlockouts',
        url: 'section.list-my-blockout-dates.php',
        forceReload: true
      });
    }

    if (!documentEventExists('buttonSave:myblockout')) {
      $(document).on('buttonSave:myblockout', async (e, id) => {
        const blockoutId = id;
        if (!$('#blockout-from-datetime').val() || !$('#blockout-to-datetime').val()) return ui.toastr.error('Please select both start and end dates.', 'Error');
        const resp = await net.post('/api/post.save-blockout.php', {
          id: $('#blockout-id').val(),
          userId: `<?= $_SESSION['user']->id ?>`,
          fromDateTime: $('#blockout-from-datetime').val() ? moment($('#blockout-from-datetime').val()).format('YYYY-MM-DD HH:mm:ss') : null,
          toDateTime: $('#blockout-to-datetime').val() ? moment($('#blockout-to-datetime').val()).format('YYYY-MM-DD HH:mm:ss') : null,
          note: $('#blockout-note').cleanVal()
        });
        if (resp?.result) {
          $(document).trigger('myblockoutChange');
          if (blockoutId) {
            ui.toastr.success('Blockout dates saved.', 'Success');
            return backToList();
          }
          ui.toastr.success('Blockout dates added.', 'Success')
          return backToList();
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.error(resp);
      });
    }

    if (!documentEventExists('buttonSaveAndConfirm:myblockout')) {
      $(document).on('buttonSaveAndConfirm:myblockout', async (e, id) => {
        const blockoutId = id;
        if (await ui.ask('Are you sure you want to delete these blockout dates?')) {
          const resp = await net.get('/api/get.delete-blockout.php', { id: blockoutId });
          if (resp?.result) {
            $(document).trigger('myblockoutChange', );
            ui.toastr.success('Blockout dates deleted.', 'Success');
            return backToList();
          }
          console.error(resp);
          ui.toastr.error('There seems to be a problem deleting blockout dates.', 'Error');
        }
      });
    }

  });
</script>